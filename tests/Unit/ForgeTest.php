<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Http\Recent;

/**
 * The tracker is somebody else's host, so what is held here is what this side
 * does with what comes back: the fields the answer is composed of, the journal
 * the decision sits in, the relation that names two issues and means one, the
 * title a search hit carries its triage state in, and the page a bot protection
 * answers 200 with.
 */
final class ForgeTest extends TestCase
{
    #[After]
    public function forgetWhatWasHeld(): void
    {
        Recent::forget();
    }

    /**
     * A search answer as the tracker sends it, in the shape measured on
     * 2026-08-03: the hits, then the envelope saying how many there were.
     */
    private const HITS = [
        'results' => [
            [
                'id' => 105403,
                'title' => 'Bug #105403 (Under Review): f:image and cache busting issue',
                'type' => 'issue',
                'url' => 'https://forge.typo3.org/issues/105403',
                'description' => 'Since 13.4 the generated image URI carries a timestamp.',
            ],
            [
                'id' => 107869,
                'title' => 'Feature #107869 (Closed): Add option to not add cache busting to generated URIs',
                'type' => 'issue-closed',
                'url' => 'https://forge.typo3.org/issues/107869',
                'description' => 'There are legitimate cases where not adding cache busting is required.',
            ],
        ],
        'total_count' => 2,
        'offset' => 0,
        'limit' => 15,
    ];

    /**
     * The issues those two hits are, as `/issues.json` answers a list of ids:
     * fields where a search hit has a title, which is what makes the area, the
     * assignee and the two dates answerable at all.
     */
    private const FIELDS = [
        'issues' => [
            [
                'id' => 105403,
                'subject' => 'f:image and cache busting issue',
                'tracker' => ['name' => 'Bug'],
                'status' => ['name' => 'Under Review'],
                'category' => ['name' => 'Fluid'],
                'author' => ['id' => 2737, 'name' => 'Nicole Zingg'],
                'assigned_to' => ['name' => 'Andreas Kienast'],
                'created_on' => '2024-10-23T08:42:11Z',
                'updated_on' => '2026-08-02T17:24:18Z',
            ],
            [
                'id' => 107869,
                'subject' => 'Add option to not add cache busting to generated URIs',
                'tracker' => ['name' => 'Feature'],
                'status' => ['name' => 'Closed'],
                'created_on' => '2025-10-27T19:48:27Z',
                'updated_on' => '2025-12-02T12:04:41Z',
            ],
        ],
        'total_count' => 2,
    ];

    private const ISSUE = [
        'id' => 110348,
        'subject' => 'Rework AdminPanel "imagesOnPage" feature',
        'status' => ['name' => 'Resolved'],
        'tracker' => ['name' => 'Task'],
        'priority' => ['name' => 'Should have'],
        'fixed_version' => ['name' => '15.0'],
        'created_on' => '2026-07-30T10:00:00Z',
        'updated_on' => '2026-08-02T20:40:50Z',
        'description' => "The feature is older than git.\n",
        'custom_fields' => [
            ['name' => 'PHP Version', 'value' => '8.4'],
            ['name' => 'TYPO3 Version', 'value' => '15'],
        ],
        'relations' => [
            ['issue_id' => 105403, 'issue_to_id' => 110348, 'relation_type' => 'relates'],
            ['issue_id' => 110348, 'issue_to_id' => 105953, 'relation_type' => 'duplicates'],
        ],
        'attachments' => [
            [
                'id' => 34363,
                'filename' => 'ckeditor-3-p-tags.png',
                'filesize' => 15472,
                'content_type' => 'image/png',
                'created_on' => '2019-06-13T13:35:40Z',
                'content_url' => 'https://forge.typo3.org/attachments/download/34363/ckeditor-3-p-tags.png',
            ],
            ['id' => 37897, 'filename' => 'db_field_value.jpg', 'content_url' => 'https://forge.typo3.org/attachments/download/37897/db_field_value.jpg'],
        ],
        'journals' => [
            ['user' => ['name' => 'Gerrit Code Review'], 'created_on' => '2026-07-31T19:23:24Z', 'notes' => 'Patch set 1 has been pushed.'],
            ['user' => ['name' => 'Somebody'], 'created_on' => '2026-08-01T06:13:04Z', 'notes' => ''],
            ['user' => ['name' => 'Benni Mack'], 'created_on' => '2026-08-02T20:40:50Z', 'notes' => 'Closing, the rework landed.'],
        ],
    ];

    #[Test]
    public function anIssueIsReadIntoTheFieldsAQuestionAboutItIsAsked(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode(['issue' => self::ISSUE]));

        $issue = $forge->issue('#110348')['issue'];

        self::assertSame('Rework AdminPanel "imagesOnPage" feature', $issue['subject']);
        self::assertSame('Resolved', $issue['status']);
        self::assertSame('Task', $issue['tracker']);
        self::assertSame('15.0', $issue['targetVersion']);
        // The version an issue was reported against is a custom field, found by
        // the name it carries rather than by a position in a list.
        self::assertSame('15', $issue['typo3Version']);
        self::assertSame('8.4', $issue['phpVersion']);
    }

    /**
     * What decides an issue is in its journal — a closure, a "we will not do
     * this", a reassignment — and never in the description, which is what the
     * reporter wrote before any of that happened.
     */
    #[Test]
    public function theCommentsComeBackAndTheFieldChangesDoNot(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode(['issue' => self::ISSUE]));

        $issue = $forge->issue('110348')['issue'];

        self::assertSame(2, $issue['noteCount']);
        self::assertSame(['Gerrit Code Review', 'Benni Mack'], array_column($issue['notes'], 'author'));
        self::assertSame('Closing, the rework landed.', $issue['notes'][1]['note']);
    }

    /**
     * The files are named, and the bytes are the caller's to read.
     *
     * Redmine writes an inline image into a comment as `!name.jpg!`, so the
     * text of such a comment is a bare filename referring to something the
     * answer otherwise never mentions exists. On #88556 two attachments decided
     * the triage and the text alone was actively misleading
     * (`feedback/2026-08-05-033846`) — `D-ANS-057`.
     */
    #[Test]
    public function theFilesHangingOffAnIssueAreNamedRatherThanFetched(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return (string) json_encode(['issue' => self::ISSUE]);
        });

        $attachments = $forge->issue('110348')['issue']['attachments'];

        self::assertStringContainsString('include=journals,relations,attachments', $asked[0]);
        self::assertSame(['ckeditor-3-p-tags.png', 'db_field_value.jpg'], array_column($attachments, 'filename'));
        self::assertSame('image/png', $attachments[0]['contentType']);
        self::assertSame(15472, $attachments[0]['size']);
        self::assertSame(
            'https://forge.typo3.org/attachments/download/34363/ckeditor-3-p-tags.png',
            $attachments[0]['url'],
        );
        // A field the tracker did not carry is absent rather than guessed at,
        // and the file is still named.
        self::assertSame('', $attachments[1]['contentType']);
        self::assertSame(0, $attachments[1]['size']);
    }

    /**
     * A relation names both issues, so which one is the other depends on who
     * filed it. Reading one field reports the issue as related to itself, which
     * is what the first live call did.
     */
    #[Test]
    public function aRelationNamesTheOtherIssueAndNeverThisOne(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode(['issue' => self::ISSUE]));

        $relations = $forge->issue('110348')['issue']['relations'];

        self::assertSame([105403, 105953], array_column($relations, 'issue'));
        self::assertSame(['relates', 'duplicates'], array_column($relations, 'relation'));
    }

    /**
     * A number and a word cost one issue read to evaluate, so a caller holding
     * four of them evaluates none — and on 15984 the one that answered what a
     * fix would cost was among them (`D-ANS-064`). What makes it a fix rather
     * than a trade is that the whole set is filled in one call.
     */
    #[Test]
    public function aRelationCarriesEnoughOfTheOtherIssueToJudgeWhetherToReadIt(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return (string) json_encode(str_contains($url, 'issue_id=') ? self::RELATED : ['issue' => self::ISSUE]);
        });

        $relations = $forge->issue('110348')['issue']['relations'];

        self::assertCount(2, $asked);
        self::assertStringContainsString('issue_id=105403%2C105953', $asked[1]);
        // A relation is usually to something closed, and the default of that
        // endpoint is the open issues.
        self::assertStringContainsString('status_id=%2A', $asked[1]);
        self::assertSame('Massive Memory Leak in 4.5.8+ / 4.6', $relations[0]['subject']);
        self::assertSame('Bug', $relations[0]['tracker']);
        self::assertSame('Closed', $relations[0]['status']);
        self::assertSame('https://forge.typo3.org/issues/105953', $relations[1]['url']);
    }

    /**
     * An issue that answered is not turned into an outage by a second call that
     * did not.
     */
    #[Test]
    public function aRelationTheFillCouldNotReachIsStillTheRelationThatWasFiled(): void
    {
        $forge = new Forge(fn(string $url): ?string => str_contains($url, 'issue_id=')
            ? null
            : (string) json_encode(['issue' => self::ISSUE]));

        $relations = $forge->issue('110348')['issue']['relations'];

        self::assertSame([105403, 105953], array_column($relations, 'issue'));
        self::assertSame(['', ''], array_column($relations, 'subject'));
    }

    /**
     * The journal of 15984, in the wording measured on 2026-08-08: the bot
     * names both handles, a human names the number alone three months later,
     * and one comment is a query for a topic rather than a change.
     */
    private const REVIEWED = [
        'id' => 15984,
        'journals' => [
            ['user' => ['name' => 'Steffen Kamper'], 'created_on' => '2011-03-17T11:32:20Z', 'notes' => 'https://review.typo3.org/#q,status:open+project:TYPO3v4/Core+topic:3129,n,z'],
            ['user' => ['name' => 'Mr. Hudson'], 'created_on' => '2011-04-10T12:19:56Z', 'notes' => "Patch set 3 of change I98ea123ccdf1e370f28103546191b0a7234076f4 has been pushed to the review server.\nIt is available at http://review.typo3.org/1186"],
            ['user' => ['name' => 'Mr. Hudson'], 'created_on' => '2011-06-06T17:17:34Z', 'notes' => "Patch set 1 of change I459aa01a8aba89ce361accd3dd84ea0329c5d1e4 has been pushed to the review server.\nIt is available at http://review.typo3.org/2545"],
            ['user' => ['name' => 'Markus Klein'], 'created_on' => '2011-10-06T01:13:23Z', 'notes' => "Patch for 4.5 is still pending, but has enough votes!\n\nhttps://review.typo3.org/2545"],
        ],
    ];

    /**
     * A change reference is in the payload already and only inside a sentence,
     * where it reads as history rather than as a handle: the session that
     * triaged this issue never loaded `typo3_gerrit_lookup`'s schema.
     */
    #[Test]
    public function aReviewChangeIsLiftedOutOfTheProseThatCarriesIt(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode(['issue' => self::REVIEWED]));

        $reviews = $forge->issue('15984')['issue']['reviews'];

        self::assertSame([1186, 2545], array_column($reviews, 'change'));
        self::assertSame([3, 1], array_column($reviews, 'patchSet'));
        self::assertSame('I98ea123ccdf1e370f28103546191b0a7234076f4', $reviews[0]['changeId']);
        self::assertSame('https://review.typo3.org/c/1186', $reviews[0]['url']);
        // The last note naming it, which is how old the reference is — and the
        // change id the bot gave it three months earlier is still on it.
        self::assertSame('2011-10-06T01:13:23Z', $reviews[1]['on']);
        self::assertSame('I459aa01a8aba89ce361accd3dd84ea0329c5d1e4', $reviews[1]['changeId']);
    }

    /**
     * The journal is the most valuable thing in the payload and it is also why
     * a second issue cannot be afforded, and neither half is wrong
     * (`D-ANS-064`). So the bound is asked for: a caller reading one issue
     * keeps exactly what it had.
     */
    #[Test]
    public function theJournalComesBackWholeUnlessACallerAsksForLessOfIt(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode(['issue' => self::REVIEWED]));

        $issue = $forge->issue('15984')['issue'];

        self::assertCount(4, $issue['notes']);
        self::assertSame(4, $issue['noteCount']);
        self::assertSame(2, $issue['botNoteCount']);
    }

    /**
     * The patch-set pings are half the volume of 14858 and carry nothing a
     * reader was going to use — the change numbers in them are a field of their
     * own by the time they are dropped.
     */
    #[Test]
    public function thePingsAreWhatALimitedReaderDropsAndTheChangesSurviveThem(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode(['issue' => self::REVIEWED]));

        $issue = $forge->issue('15984', 'people')['issue'];

        self::assertSame(['Steffen Kamper', 'Markus Klein'], array_column($issue['notes'], 'author'));
        // The total is what the issue carries and not what came back, so the
        // two counts together say what was dropped.
        self::assertSame(4, $issue['noteCount']);
        self::assertSame(2, $issue['botNoteCount']);
        self::assertSame([1186, 2545], array_column($issue['reviews'], 'change'));
    }

    /**
     * `review.typo3.org/#q,…+topic:3129,n,z` names a topic. Matching digits
     * anywhere in a review URL would report it as change 3129, which is a
     * change that exists and is about something else.
     */
    #[Test]
    public function aQueryUrlNamesNoChangeAndIsNotReportedAsOne(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode(['issue' => self::REVIEWED]));

        self::assertNotContains(3129, array_column($forge->issue('15984')['issue']['reviews'], 'change'));
    }

    /** The two relations of the fixture, as `/issues.json` answers a list of ids. */
    private const RELATED = [
        'issues' => [
            [
                'id' => 105403,
                'subject' => 'Massive Memory Leak in 4.5.8+ / 4.6',
                'tracker' => ['name' => 'Bug'],
                'status' => ['name' => 'Closed'],
            ],
            [
                'id' => 105953,
                'subject' => 'Rework AdminPanel',
                'tracker' => ['name' => 'Task'],
                'status' => ['name' => 'New'],
            ],
        ],
        'total_count' => 2,
    ];

    /**
     * The protection in front of the tracker answers a browser-shaped request
     * with 200 and a challenge page. Reading that as "no such issue" would be a
     * false answer rather than a missing one, so it is neither.
     */
    #[Test]
    public function aChallengePageIsNotAnIssueAndNotAnAbsence(): void
    {
        $calls = 0;
        $forge = new Forge(function () use (&$calls): string {
            $calls++;

            return '<!doctype html><title>Making sure you are not a bot!</title>';
        });

        $answer = $forge->issue('110348');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-parseable', $answer['cause']);
        // Asked twice: the retry is the plainer agent, because that is the way
        // past a protection that challenges what looks like a browser.
        self::assertSame(2, $calls);
    }

    #[Test]
    public function aTrackerThatDoesNotAnswerIsSaidRatherThanGuessedAt(): void
    {
        $forge = new Forge(fn(): ?string => null);

        $answer = $forge->issue('110348');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-answering', $answer['cause']);
    }

    /**
     * The words are what is searched for, and `issues=1` is what keeps a wiki
     * page out of an answer whose entries are issue numbers.
     */
    #[Test]
    public function theSearchAsksTheTrackerForIssuesMatchingTheWords(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return (string) json_encode(str_contains($url, '/issues.json') ? self::FIELDS : self::HITS);
        });

        $answer = $forge->search('  cache busting  ', 3);

        self::assertStringContainsString('q=cache%20busting', $asked[0]);
        self::assertStringContainsString('issues=1', $asked[0]);
        self::assertStringContainsString('limit=3', $asked[0]);
        // The query comes back as it was asked, because a caller holding two
        // hits has to see which words produced them before concluding anything
        // from how few there are.
        self::assertSame('cache busting', $answer['query']);
        self::assertSame('answered', $answer['status']);
    }

    /**
     * The title carries the tracker and the triage state — `Bug #105403 (Under
     * Review): …` — so a set of hits says what kind each one is and where it
     * stands without a call per hit.
     */
    #[Test]
    public function aHitIsReadOutOfTheTitleTheTrackerWordsItIn(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode(self::HITS));

        $results = $forge->search('cache busting')['results'];

        self::assertSame([105403, 107869], array_column($results, 'issue'));
        self::assertSame(['Bug', 'Feature'], array_column($results, 'tracker'));
        // A triage state of two words, and the closing bracket is what ends it
        // rather than the first space.
        self::assertSame(['Under Review', 'Closed'], array_column($results, 'status'));
        self::assertSame('f:image and cache busting issue', $results[0]['subject']);
        self::assertSame('https://forge.typo3.org/issues/105403', $results[0]['url']);
    }

    /**
     * A title in some other shape is a hit with less read off it, not a broken
     * one: the number and the URL are fields of their own, and the title stands
     * as the subject.
     */
    #[Test]
    public function aTitleThatIsNotInThatShapeStillAnswersWithTheIssue(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode([
            'results' => [['id' => 105403, 'title' => 'f:image and cache busting issue']],
        ]));

        $hit = $forge->search('cache busting')['results'][0];

        self::assertSame(105403, $hit['issue']);
        self::assertSame('f:image and cache busting issue', $hit['subject']);
        self::assertSame('', $hit['tracker']);
        self::assertSame('https://forge.typo3.org/issues/105403', $hit['url']);
    }

    /**
     * The fields a hit is not made of, read for the whole page in one call.
     *
     * A record carrying them empty is a false statement rather than a missing
     * one: 50 of 50 rows read as issues nobody has categorised and nothing has
     * moved on, when every one of them had an area and a date
     * (`feedback/2026-08-05-033902`). The search path is also where a triage
     * asks about age, which no title carries — `D-ANS-056`.
     */
    #[Test]
    public function aSearchHitIsFilledFromTheIssuesTheHitsAre(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return (string) json_encode(str_contains($url, '/issues.json') ? self::FIELDS : self::HITS);
        });

        $results = $forge->search('cache busting')['results'];

        // One further call for the page, not one per hit.
        self::assertCount(2, $asked);
        self::assertStringContainsString('issue_id=105403%2C107869', $asked[1]);
        // A search answers with closed issues, and the default of that endpoint
        // is the open ones.
        self::assertStringContainsString('status_id=%2A', $asked[1]);

        self::assertSame('Fluid', $results[0]['category']);
        self::assertSame('Nicole Zingg', $results[0]['reportedBy']);
        self::assertSame('Andreas Kienast', $results[0]['assignedTo']);
        self::assertSame('2024-10-23T08:42:11Z', $results[0]['createdOn']);
        self::assertSame('2026-08-02T17:24:18Z', $results[0]['updatedOn']);
        // What the title already carried stands: the tracker words its own
        // titles, and the fields are what the title did not carry.
        self::assertSame('Under Review', $results[0]['status']);
        self::assertSame('Bug', $results[0]['tracker']);
    }

    /**
     * A second call that did not answer does not turn a search that did into an
     * outage. The hits stand with the fields the title carried — `D-ANS-056`.
     */
    #[Test]
    public function aPageThatCouldNotBeFilledIsStillTheHitsThatMatched(): void
    {
        $forge = new Forge(static fn(string $url): ?string => str_contains($url, '/issues.json')
            ? null
            : (string) json_encode(self::HITS));

        $answer = $forge->search('cache busting');

        self::assertSame('answered', $answer['status']);
        self::assertSame([105403, 107869], array_column($answer['results'], 'issue'));
        self::assertSame('', $answer['results'][0]['category']);
    }

    /**
     * Nothing matching is an answer about the words. It is the reading
     * `D-ANS-038` is written against — an empty search taken for "nobody
     * reported this" — so it is `empty` rather than an absence with no cause,
     * and never `unavailable`.
     */
    #[Test]
    public function wordsThatMatchNothingAreEmptyRatherThanUnavailable(): void
    {
        $forge = new Forge(fn(): string => (string) json_encode([
            'results' => [],
            'total_count' => 0,
            'offset' => 0,
            'limit' => 15,
        ]));

        $answer = $forge->search('quantumflux transponder');

        self::assertSame('empty', $answer['status']);
        self::assertSame([], $answer['results']);
        self::assertNull($answer['cause']);
    }

    /**
     * The protection sits in front of the whole host, so the search meets the
     * challenge page the issue read does, and answers it the same way.
     */
    #[Test]
    public function aChallengePageIsNotASearchThatFoundNothing(): void
    {
        $calls = 0;
        $forge = new Forge(function () use (&$calls): string {
            $calls++;

            return '<!doctype html><title>Making sure you are not a bot!</title>';
        });

        $answer = $forge->search('cache busting');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-parseable', $answer['cause']);
        self::assertSame(2, $calls);
    }

    #[Test]
    public function aTrackerThatDoesNotAnswerASearchIsSaidRatherThanGuessedAt(): void
    {
        $forge = new Forge(fn(): ?string => null);

        $answer = $forge->search('cache busting');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-answering', $answer['cause']);
    }

    /**
     * The four calls an enumeration makes, answered by the URL that was asked
     * for: the project carries the areas, the issues carry the page, the id
     * list fills the relations of the whole page, and the review server says
     * which rows have a change.
     *
     * @param list<string> $asked
     * @return \Closure(string): string
     */
    private static function tracker(array &$asked): \Closure
    {
        return function (string $url) use (&$asked): string {
            $asked[] = $url;
            if (str_contains($url, 'review.typo3.org')) {
                return self::CHANGES;
            }
            if (str_contains($url, 'issue_id=')) {
                return (string) json_encode(self::RELATED_ROWS);
            }
            if (str_contains($url, '/memberships.json')) {
                return (string) json_encode(self::MEMBERS);
            }

            return (string) json_encode(str_contains($url, '/issues.json') ? self::PAGE : self::PROJECT);
        };
    }

    /** The issue the first row of the page is filed against. */
    private const RELATED_ROWS = [
        'issues' => [
            [
                'id' => 90676,
                'subject' => 'Clipboard related bugs and features',
                'tracker' => ['name' => 'Epic'],
                'status' => ['name' => 'Accepted'],
            ],
        ],
        'total_count' => 1,
    ];

    /**
     * What one batched query answers, in the shape review.typo3.org sends: the
     * change that names the first row, and the change whose own number is the
     * second row's — which is the false positive the `message:` index answers
     * with whatever it was asked.
     */
    private const CHANGES = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[FEATURE] Make the copy mode configurable",'
        . '"status":"NEW","_number":38419,"current_revision_number":3,"current_revision":"a1","revisions":{'
        . '"a1":{"commit":{"message":"[FEATURE] Make the copy mode configurable\n\nResolves: #14858\nChange-Id: I1\n"}}}},'
        . '{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Something else entirely",'
        . '"status":"MERGED","_number":23633,"current_revision_number":9,"current_revision":"b2","revisions":{'
        . '"b2":{"commit":{"message":"[BUGFIX] Something else entirely\n\nResolves: #106318\nReviewed-on: https://review.typo3.org/c/Packages/TYPO3.CMS/+/23633\n"}}}}]';

    private const PROJECT = [
        'project' => [
            'id' => 27,
            'identifier' => 'typo3cms-core',
            'issue_categories' => [
                ['id' => 971, 'name' => 'Backend API'],
                ['id' => 972, 'name' => 'Backend User Interface'],
                ['id' => 1001, 'name' => 'RTE (rtehtmlarea + ckeditor)'],
                ['id' => 977, 'name' => 'Frontend'],
                // Carries "rte" as a substring and not as a word, which is what
                // separates the two matchers.
                ['id' => 1005, 'name' => 'Performance Reporter'],
            ],
        ],
    ];

    /** A page of `/issues.json`, whose entries are fields rather than a title. */
    private const PAGE = [
        'issues' => [
            [
                'id' => 14858,
                'subject' => 'extended clipboard: setCopyMode can`t be set to copy by default',
                'tracker' => ['name' => 'Bug'],
                'status' => ['name' => 'New', 'is_closed' => false],
                'category' => ['name' => 'Backend User Interface'],
                'author' => ['id' => 52, 'name' => 'Frank Nägler'],
                'assigned_to' => ['name' => 'Sacha Vorbeck'],
                'created_on' => '2005-07-11T10:22:33Z',
                'updated_on' => '2026-01-23T08:11:00Z',
                // What `include=relations,attachments` adds to a row, which the
                // index answers for nothing where the call asks for it.
                'relations' => [
                    ['issue_id' => 14858, 'issue_to_id' => 90676, 'relation_type' => 'relates'],
                ],
                'attachments' => [
                    [
                        'id' => 1277,
                        'filename' => 'clipboard.png',
                        'filesize' => 2048,
                        'content_type' => 'image/png',
                        'created_on' => '2005-07-11T10:22:33Z',
                        'content_url' => 'https://forge.typo3.org/attachments/download/1277/clipboard.png',
                    ],
                ],
            ],
            [
                'id' => 23633,
                'subject' => 'regex in TCA eval function',
                'tracker' => ['name' => 'Feature'],
                'status' => ['name' => 'New', 'is_closed' => false],
                'category' => ['name' => 'Backend API'],
                'created_on' => '2010-09-28T11:00:00Z',
                'updated_on' => '2023-07-04T09:00:00Z',
            ],
        ],
        'total_count' => 479,
        'offset' => 0,
        'limit' => 2,
    ];

    /**
     * The filters go to the tracker and the entries come back as fields.
     *
     * What this holds against the search above is where a triage state is read
     * from: a search hit carries it in a title and an enumeration carries it in
     * fields, which is why the two dates are answerable here at all —
     * `D-ANS-054`.
     */
    #[Test]
    public function theEnumerationAsksForTheOpenIssuesAndReadsThemAsFields(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $answer = $forge->open('stale', 'Bug', '', '2015-01-01', '2020-01-01', 2);

        $issues = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/issues.json')));
        self::assertStringContainsString('status_id=open', $issues[0]);
        self::assertStringContainsString('sort=updated_on%3Aasc', $issues[0]);
        self::assertStringContainsString('tracker_id=1', $issues[0]);
        self::assertStringContainsString('created_on=%3C%3D2015-01-01', $issues[0]);
        self::assertStringContainsString('updated_on=%3C%3D2020-01-01', $issues[0]);

        self::assertSame('answered', $answer['status']);
        self::assertSame([14858, 23633], array_column($answer['results'], 'issue'));
        self::assertSame('Backend User Interface', $answer['results'][0]['category']);
        self::assertSame('Sacha Vorbeck', $answer['results'][0]['assignedTo']);
        // Who holds an issue is what says whether it is free to take, and
        // nobody holding it is a state rather than a missing field.
        self::assertSame('', $answer['results'][1]['assignedTo']);
        self::assertSame('2005-07-11T10:22:33Z', $answer['results'][0]['createdOn']);
        self::assertSame('2026-01-23T08:11:00Z', $answer['results'][0]['updatedOn']);
    }

    /**
     * The relations and the files come back with the page, and cost nothing
     * beyond the call already made.
     *
     * A triage narrows a page of thirty to the few worth reading whole, and
     * `D-ANS-069` measured what it was narrowing on: over 36 stale Bugs, 19
     * carried a relation and 6 carried a file, and the answer dropped all of
     * them.
     */
    #[Test]
    public function aRowCarriesWhatTheOneCallAlreadyAnsweredAboutIt(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $results = $forge->open('stale', '', '', '', '', 2)['results'];

        self::assertStringContainsString('include=relations%2Cattachments', $asked[1]);
        self::assertSame([90676], array_column($results[0]['relations'], 'issue'));
        self::assertSame(['clipboard.png'], array_column($results[0]['attachments'], 'filename'));
        self::assertSame('image/png', $results[0]['attachments'][0]['contentType']);
        // A row the tracker answered nothing for carries neither, which is the
        // issue having none rather than the call not asking.
        self::assertSame([], $results[1]['relations']);
        self::assertSame([], $results[1]['attachments']);
    }

    /**
     * A relation on a row is judged the way a relation on an issue is, and by
     * the same bulk read — `R-ANS-029`. One call for the whole page, whatever
     * the rows carry between them.
     */
    #[Test]
    public function theRelationsOfAWholePageAreFilledInOneCall(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $relation = $forge->open('stale', '', '', '', '', 2)['results'][0]['relations'][0];

        $filling = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, 'issue_id=')));
        self::assertCount(1, $filling);
        self::assertStringContainsString('issue_id=90676', $filling[0]);
        self::assertSame('Clipboard related bugs and features', $relation['subject']);
        self::assertSame('Epic', $relation['tracker']);
        self::assertSame('Accepted', $relation['status']);
        self::assertSame('https://forge.typo3.org/issues/90676', $relation['url']);
    }

    /**
     * Whether somebody has already pushed a patch is the signal a triage stops
     * on, and no row carried it: the change reference lives in the journal, and
     * the index answers no journal however it is asked (`D-ANS-069`).
     *
     * One query for the page and not one per row, and the false positive the
     * `message:` index answers with — a change whose own number was asked for —
     * is dropped by the rule a single-issue lookup already applies.
     */
    #[Test]
    public function aRowSaysWhetherTheReviewServerHoldsAChangeThatNamesIt(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $results = $forge->open('stale', '', '', '', '', 2)['results'];

        $review = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, 'review.typo3.org')));
        self::assertCount(1, $review);
        self::assertStringContainsString('q=message%3A14858%20OR%20message%3A23633', $review[0]);
        // The commit message is what the answer is held against, so it is asked
        // for here as it is for a single issue.
        self::assertStringContainsString('o=CURRENT_COMMIT', $review[0]);

        self::assertSame([38419], array_column($results[0]['reviews'], 'change'));
        self::assertSame('https://review.typo3.org/c/Packages/TYPO3.CMS/+/38419', $results[0]['reviews'][0]['url']);
        // Change 23633 is the second row's own number wearing a change's shape,
        // and its message names another issue entirely.
        self::assertSame([], $results[1]['reviews']);
    }

    /**
     * A page that answered is not turned into an outage by a second host that
     * did not, which is what the two fills above already promise of the
     * tracker's own.
     */
    #[Test]
    public function aReviewServerThatDidNotAnswerLeavesTheRowsAsTheyCameBack(): void
    {
        $asked = [];
        $tracker = self::tracker($asked);
        $forge = new Forge(static fn(string $url): ?string => str_contains($url, 'review.typo3.org')
            ? null
            : $tracker($url));

        $answer = $forge->open('stale', '', '', '', '', 2);

        self::assertSame('answered', $answer['status']);
        self::assertSame([14858, 23633], array_column($answer['results'], 'issue'));
        self::assertSame([], $answer['results'][0]['reviews']);
    }

    /**
     * A page is not the set, and only the tracker's own count says which of the
     * two the caller is holding — `D-ANS-054`.
     */
    #[Test]
    public function theCountOfEverythingThatMatchedComesBackWithThePage(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $answer = $forge->open('oldest', '', '', '', '', 2);

        self::assertSame(479, $answer['total']);
        self::assertCount(2, $answer['results']);
    }

    /**
     * A date the tracker cannot read is dropped rather than sent. Redmine
     * answers an unparseable filter with the unfiltered set, which is a set
     * about everything wearing the shape of a set about one thing.
     */
    #[Test]
    public function onlyADateReachesTheDateFilter(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $forge->open('oldest', '', '', 'last year', '', 2);

        $issues = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/issues.json')));
        self::assertStringNotContainsString('created_on=', $issues[0]);
    }

    /**
     * Nobody types "RTE (rtehtmlarea + ckeditor)", so the caller's own word is
     * matched against the project's names — at a word boundary, because a
     * substring match answers "rte" with every category carrying "Reporter" —
     * `D-ANS-054`.
     */
    #[Test]
    public function anAreaIsNamedInTheCallersWordsAndMatchedAtAWordBoundary(): void
    {
        $categories = [
            'Backend API' => 971,
            'Backend User Interface' => 972,
            'RTE (rtehtmlarea + ckeditor)' => 1001,
            'Performance Reporter' => 1005,
        ];

        self::assertSame(['RTE (rtehtmlarea + ckeditor)'], Forge::named($categories, 'rte'));
        // Every word beats one word: an exact name is not widened by the
        // fallback that makes a half-remembered one work at all.
        self::assertSame(['Backend API'], Forge::named($categories, 'backend api'));
        // And a word carried by several selects them all rather than guessing
        // which was meant.
        self::assertSame(
            ['Backend API', 'Backend User Interface'],
            Forge::named($categories, 'backend ui'),
        );
        self::assertSame([], Forge::named($categories, 'quantumflux'));
    }

    /** The areas selected reach the tracker as its own alternation, in one call. */
    #[Test]
    public function anAreaNamingSeveralCategoriesIsOneCallAndSaysWhichItUsed(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $answer = $forge->open('oldest', '', 'backend', '', '', 2);

        $issues = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/projects/typo3cms-core/issues.json')));
        self::assertCount(1, $issues);
        self::assertStringContainsString('category_id=971%7C972', $issues[0]);
        self::assertSame(['Backend API', 'Backend User Interface'], $answer['categoriesUsed']);
    }

    /**
     * A word naming no area is an answer about the word. Sent on unfiltered it
     * would come back as the whole backlog, which reads as "everything is about
     * the RTE" and is the one mistake this path can make — `D-ANS-054`.
     */
    #[Test]
    public function awordThatNamesNoAreaReadsNothingAndSaysWhichAreasExist(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $answer = $forge->open('oldest', '', 'quantumflux', '', '', 2);

        self::assertSame([], array_filter($asked, static fn(string $url): bool => str_contains($url, '/issues.json')));
        self::assertSame('empty', $answer['status']);
        self::assertSame([], $answer['categoriesUsed']);
        self::assertContains('RTE (rtehtmlarea + ckeditor)', $answer['categories']);
    }

    /**
     * The areas are read from the project rather than written down here, so a
     * category the core adds is one this can filter by without a release —
     * `D-ANS-054`.
     */
    #[Test]
    public function theAreasAreReadFromTheProjectAndHeldRatherThanCopied(): void
    {
        $calls = 0;
        $forge = new Forge(function (string $url) use (&$calls): string {
            $calls++;

            return (string) json_encode(str_contains($url, '/issues.json') ? self::PAGE : self::PROJECT);
        });

        self::assertArrayHasKey('RTE (rtehtmlarea + ckeditor)', $forge->categories());
        $forge->categories();

        self::assertSame(1, $calls, 'the project was read again for a list that changes between releases');
    }

    /**
     * The two pages of memberships the project answers with, in the shape
     * measured on 2026-08-19: 185 members, a hundred to a page.
     */
    private const MEMBERS = [
        'memberships' => [
            ['id' => 193, 'user' => ['id' => 320, 'name' => 'Benni Mack'], 'roles' => [['id' => 7, 'name' => 'Leader']]],
            ['id' => 210, 'user' => ['id' => 52, 'name' => 'Frank Nägler'], 'roles' => [['id' => 4, 'name' => 'Member']]],
            ['id' => 244, 'user' => ['id' => 61, 'name' => 'Daniel Goerz'], 'roles' => [['id' => 4, 'name' => 'Member']]],
            ['id' => 245, 'user' => ['id' => 77, 'name' => 'Daniel Siepmann'], 'roles' => [['id' => 4, 'name' => 'Member']]],
            // A group holds no issues, and reading its name as a person's is a
            // filter that answers nothing.
            ['id' => 300, 'group' => ['id' => 9, 'name' => 'Security Team'], 'roles' => [['id' => 4, 'name' => 'Member']]],
        ],
        'total_count' => 4,
    ];

    /**
     * The tracker takes a numeric user id and answers no public user list, so
     * the name a caller holds is resolved here or the question cannot be asked
     * at all — `D-ANS-089`.
     */
    #[Test]
    public function aPersonIsResolvedAgainstTheProjectsOwnMembers(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $answer = $forge->open(reportedBy: 'Frank Nägler', assignedTo: 'Benni Mack', limit: 2);

        $issues = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/projects/typo3cms-core/issues.json')));
        self::assertStringContainsString('author_id=52', $issues[0]);
        self::assertStringContainsString('assigned_to_id=320', $issues[0]);
        self::assertSame(
            [
                ['filter' => 'reportedBy', 'asked' => 'Frank Nägler', 'name' => 'Frank Nägler', 'id' => 52, 'candidates' => []],
                ['filter' => 'assignedTo', 'asked' => 'Benni Mack', 'name' => 'Benni Mack', 'id' => 320, 'candidates' => []],
            ],
            $answer['people'],
        );
    }

    /**
     * Half a name is not a person. Merging two people into one backlog is a
     * wrong answer nothing about it says is wrong, so neither is chosen and
     * both are named — which is what a caller asks again with — `D-ANS-089`.
     */
    #[Test]
    public function aNameCarriedByTwoPeopleResolvesToNeitherAndAnswersWithBoth(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $answer = $forge->open(reportedBy: 'daniel', limit: 2);

        self::assertSame([], array_filter($asked, static fn(string $url): bool => str_contains($url, 'author_id=')));
        self::assertSame('empty', $answer['status']);
        self::assertSame(['Daniel Goerz', 'Daniel Siepmann'], $answer['people'][0]['candidates']);
    }

    /**
     * A quarter of the reporters hold no membership — 24 of the 100 most
     * recently filed issues on 2026-08-19 — so the members alone would answer
     * "no such person" about people who have filed dozens — `D-ANS-089`.
     */
    #[Test]
    public function aNameNoMemberCarriesIsResolvedFromTheIssuesThatNameIt(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked[] = $url;
            if (str_contains($url, '/memberships.json')) {
                return (string) json_encode(self::MEMBERS);
            }
            if (str_contains($url, '/search.json')) {
                return (string) json_encode(self::HITS);
            }
            if (str_contains($url, 'issue_id=')) {
                return (string) json_encode(self::FIELDS_BY_A_REPORTER);
            }

            return (string) json_encode(self::PAGE);
        });

        $answer = $forge->open(reportedBy: 'Nicole Zingg', limit: 2);

        self::assertSame('Nicole Zingg', $answer['people'][0]['name']);
        self::assertSame(2737, $answer['people'][0]['id']);
        $issues = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/projects/typo3cms-core/issues.json')));
        self::assertStringContainsString('author_id=2737', $issues[0]);
    }

    /** The issues a search for a name matched, filed by the person named. */
    private const FIELDS_BY_A_REPORTER = [
        'issues' => [
            [
                'id' => 105403,
                'subject' => 'f:image and cache busting issue',
                'tracker' => ['name' => 'Bug'],
                'status' => ['name' => 'Under Review'],
                'author' => ['id' => 2737, 'name' => 'Nicole Zingg'],
            ],
        ],
        'total_count' => 1,
    ];

    /**
     * A name nothing here carries is an answer about the name. Sent on
     * unfiltered it would be the backlog of everybody, which is the mistake the
     * word naming no area is guarded against making — `D-ANS-089`.
     */
    #[Test]
    public function aNameNothingHereCarriesReadsNothingRatherThanTheWholeBacklog(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked[] = $url;
            if (str_contains($url, '/memberships.json')) {
                return (string) json_encode(self::MEMBERS);
            }
            if (str_contains($url, '/search.json')) {
                return (string) json_encode(['results' => [], 'total_count' => 0]);
            }

            return (string) json_encode(self::PROJECT);
        });

        $answer = $forge->open(reportedBy: 'Konrad Michalik', limit: 2);

        self::assertSame([], array_filter($asked, static fn(string $url): bool => str_contains($url, '/projects/typo3cms-core/issues.json')));
        self::assertSame('empty', $answer['status']);
        self::assertSame([], $answer['people'][0]['candidates']);
        self::assertSame(0, $answer['people'][0]['id']);
    }

    /**
     * What somebody has filed over the years is mostly closed, and the
     * enumeration the tracker answers by default hides all of it — `D-ANS-089`.
     */
    #[Test]
    public function theStatusIsWhatPutsWhatAPersonAlreadyFiledInReach(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $forge->open(status: 'all', reportedBy: 'Frank Nägler', limit: 2);
        $forge->open(status: 'closed', reportedBy: 'Frank Nägler', limit: 2);

        $issues = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/projects/typo3cms-core/issues.json')));
        self::assertStringContainsString('status_id=%2A', $issues[0]);
        self::assertStringContainsString('status_id=closed', $issues[1]);
    }

    /**
     * The dimension the filter selects on is answered on the row as well, so a
     * page says who is reporting it without a call per row — `D-ANS-089`.
     */
    #[Test]
    public function aRowSaysWhoFiledIt(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        $results = $forge->open('oldest', '', '', '', '', 2)['results'];

        self::assertSame('Frank Nägler', $results[0]['reportedBy']);
        // A row the tracker named nobody on carries none, which is the answer
        // it gave rather than a call that was not made.
        self::assertSame('', $results[1]['reportedBy']);
    }

    /**
     * A page holds a hundred and the project has more, so a member on the
     * second page would otherwise be a person nobody can name.
     */
    #[Test]
    public function theMembersAreReadUntilTheProjectsOwnCountIsCovered(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return (string) json_encode(['memberships' => [], 'total_count' => 185]);
        });

        $forge->people();

        self::assertCount(2, $asked);
        self::assertStringContainsString('offset=0', $asked[0]);
        self::assertStringContainsString('offset=100', $asked[1]);
    }

    /**
     * The members are read once and held: a membership is added when somebody
     * joins, and a session filtering by person asks for the same names on every
     * call it makes.
     */
    #[Test]
    public function theMembersAreHeldRatherThanReadPerCall(): void
    {
        $calls = 0;
        $forge = new Forge(function (string $url) use (&$calls): string {
            $calls++;

            return (string) json_encode(self::MEMBERS);
        });

        $forge->people();
        $forge->people();

        self::assertSame(1, $calls, 'the memberships were read again for a list that changes between releases');
    }

    /**
     * The tracker ANDs its filters, so the question somebody actually says —
     * everything of this person's — is two reads and a merge here or two calls
     * and a merge in the caller (`feedback/2026-08-19-134706`) — `D-ANS-090`.
     */
    #[Test]
    public function aUnionIsTwoReadsMergedAndCountedWithoutTheIssuesBothCarry(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked[] = $url;
            if (str_contains($url, '/memberships.json')) {
                return (string) json_encode(self::MEMBERS);
            }
            if (str_contains($url, 'review.typo3.org') || str_contains($url, 'issue_id=')) {
                return (string) json_encode(['issues' => [], 'total_count' => 0]);
            }
            if (str_contains($url, 'author_id=52&assigned_to_id=52') || str_contains($url, 'assigned_to_id=52&author_id=52')) {
                return (string) json_encode(['issues' => [], 'total_count' => 3]);
            }

            return (string) json_encode(str_contains($url, 'author_id=') ? self::FILED : self::HELD);
        });

        $answer = $forge->open(involving: 'Frank Nägler', limit: 5);

        $reads = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/projects/typo3cms-core/issues.json')));
        self::assertCount(3, $reads, 'the union was not two reads and the count of what they share');
        // The issue both reads carry is one row and counted once.
        self::assertSame([14858, 23633, 89326], array_column($answer['results'], 'issue'));
        self::assertSame(9, $answer['total'], '6 and 6 less the 3 both carry');
        self::assertSame('involving', $answer['people'][0]['filter']);
    }

    /** What the author read answers, one row of it shared with the other. */
    private const FILED = [
        'issues' => [
            ['id' => 14858, 'subject' => 'One', 'created_on' => '2005-07-11T10:22:33Z', 'updated_on' => '2026-01-23T08:11:00Z'],
            ['id' => 23633, 'subject' => 'Two', 'created_on' => '2010-09-28T11:00:00Z', 'updated_on' => '2023-07-04T09:00:00Z'],
        ],
        'total_count' => 6,
    ];

    /** What the assignee read answers. */
    private const HELD = [
        'issues' => [
            ['id' => 23633, 'subject' => 'Two', 'created_on' => '2010-09-28T11:00:00Z', 'updated_on' => '2023-07-04T09:00:00Z'],
            ['id' => 89326, 'subject' => 'Three', 'created_on' => '2019-10-01T07:00:00Z', 'updated_on' => '2023-11-09T10:00:00Z'],
        ],
        'total_count' => 6,
    ];

    /**
     * A person's history has no other words to narrow it by, so a page of 50
     * out of 621 leaves the rest reachable by nothing — what answers it is how
     * the set is distributed (`feedback/2026-08-19-134651`) — `D-ANS-090`.
     */
    #[Test]
    public function aBreakdownCountsTheWholeSetRatherThanAPageOfIt(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked[] = $url;
            if (str_contains($url, '/memberships.json')) {
                return (string) json_encode(self::MEMBERS);
            }
            if (!str_contains($url, '/issues.json')) {
                return (string) json_encode(self::PROJECT);
            }

            return (string) json_encode(self::COUNTED);
        });

        $answer = $forge->open(status: 'all', reportedBy: 'Frank Nägler', breakdown: true);

        $reads = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/projects/typo3cms-core/issues.json')));
        // Nothing that decides which row to read, because no row comes back.
        self::assertStringNotContainsString('include=', $reads[0]);
        self::assertSame([], $answer['results']);
        self::assertSame(3, $answer['breakdown']['read']);
        self::assertTrue($answer['breakdown']['complete']);
        self::assertSame(
            [
                ['dimension' => 'status', 'buckets' => [['name' => 'Closed', 'count' => 2], ['name' => 'New', 'count' => 1]], 'withheldBuckets' => 0, 'withheldCount' => 0],
                ['dimension' => 'tracker', 'buckets' => [['name' => 'Bug', 'count' => 2], ['name' => 'Task', 'count' => 1]], 'withheldBuckets' => 0, 'withheldCount' => 0],
                // An issue filed under no area is a bucket rather than a row
                // left out, so the areas add up to what was read.
                ['dimension' => 'category', 'buckets' => [['name' => 'Backend API', 'count' => 1], ['name' => 'Fluid', 'count' => 1], ['name' => 'none', 'count' => 1]], 'withheldBuckets' => 0, 'withheldCount' => 0],
                ['dimension' => 'year', 'buckets' => [['name' => '2015', 'count' => 2], ['name' => '2024', 'count' => 1]], 'withheldBuckets' => 0, 'withheldCount' => 0],
            ],
            $answer['breakdown']['counts'],
        );
    }

    /** What a counted read answers: the fields the four dimensions are read off. */
    private const COUNTED = [
        'issues' => [
            [
                'id' => 1,
                'status' => ['name' => 'Closed'],
                'tracker' => ['name' => 'Bug'],
                'category' => ['name' => 'Fluid'],
                'created_on' => '2015-03-01T10:00:00Z',
            ],
            [
                'id' => 2,
                'status' => ['name' => 'Closed'],
                'tracker' => ['name' => 'Task'],
                'created_on' => '2015-06-01T10:00:00Z',
            ],
            [
                'id' => 3,
                'status' => ['name' => 'New'],
                'tracker' => ['name' => 'Bug'],
                'category' => ['name' => 'Backend API'],
                'created_on' => '2024-01-01T10:00:00Z',
            ],
        ],
        'total_count' => 3,
    ];

    /**
     * A hundred rows is what one request answers, and a set larger than that is
     * read page by page rather than counted off the first of them —
     * `D-ANS-090`.
     */
    #[Test]
    public function theCountedReadPagesUntilTheWholeMatchedSetIsRead(): void
    {
        $asked = [];
        $forge = new Forge(function (string $url) use (&$asked): string {
            if (str_contains($url, '/issues.json')) {
                $asked[] = $url;

                return (string) json_encode(['issues' => [], 'total_count' => 150]);
            }

            return (string) json_encode(self::PROJECT);
        });

        $forge->open(breakdown: true);

        self::assertCount(2, $asked);
        self::assertStringContainsString('limit=100', $asked[0]);
        self::assertStringContainsString('offset=100', $asked[1]);
    }

    /**
     * A read that stops is a shape of one end of the set, and a caller reading
     * proportions off it would be reading them off the oldest thousand —
     * `D-ANS-090`.
     */
    #[Test]
    public function aBreakdownSaysWhereTheBoundCutTheRead(): void
    {
        $reads = 0;
        $forge = new Forge(function (string $url) use (&$reads): string {
            if (str_contains($url, '/memberships.json')) {
                return (string) json_encode(self::MEMBERS);
            }
            if (!str_contains($url, '/issues.json')) {
                return (string) json_encode(self::PROJECT);
            }
            $reads++;

            return (string) json_encode([
                'issues' => [['id' => $reads, 'status' => ['name' => 'Closed'], 'created_on' => '2015-01-01T00:00:00Z']],
                'total_count' => 5000,
            ]);
        });

        $answer = $forge->open(status: 'all', reportedBy: 'Frank Nägler', breakdown: true);

        self::assertSame(10, $reads, 'the read is not bounded');
        self::assertFalse($answer['breakdown']['complete']);
        self::assertSame(5000, $answer['total']);
        self::assertSame(10, $answer['breakdown']['read']);
    }

    /**
     * The tail of an area count is subsystems holding one issue each, and what
     * it says is already said by the head. What it is not is silently dropped —
     * `D-ANS-090`.
     */
    #[Test]
    public function theLargestBucketsAreAnsweredAndTheTailIsCounted(): void
    {
        $issues = [];
        foreach (range(1, 20) as $number) {
            $issues[] = [
                'id' => $number,
                'category' => ['name' => 'Area ' . $number],
                'created_on' => '2015-01-01T00:00:00Z',
            ];
        }
        $forge = new Forge(static fn(string $url): string => (string) json_encode(
            str_contains($url, '/issues.json') ? ['issues' => $issues, 'total_count' => 20] : self::PROJECT,
        ));

        $areas = $forge->open(breakdown: true)['breakdown']['counts'][2];

        self::assertCount(12, $areas['buckets']);
        self::assertSame(8, $areas['withheldBuckets']);
        self::assertSame(8, $areas['withheldCount']);
    }

    /**
     * The areas are 54 names, and on a call that passed no category they are 54
     * names nobody asked for — three times over in one session
     * (`feedback/2026-08-19-134717`) — `D-ANS-090`.
     */
    #[Test]
    public function theAreasComeBackOnlyWhereAWordOfTheCallersNeedsCorrecting(): void
    {
        $asked = [];
        $forge = new Forge(self::tracker($asked));

        self::assertSame([], $forge->open('oldest', '', '', '', '', 2)['categories']);
        self::assertSame([], $forge->open('oldest', '', 'rte', '', '', 2)['categories'], 'a word that resolved to one area');
        // The two it does the work on: a word naming none, and a word naming
        // several, where what to ask instead is what the list answers.
        self::assertContains('Frontend', $forge->open('oldest', '', 'quantumflux', '', '', 2)['categories']);
        self::assertContains('Frontend', $forge->open('oldest', '', 'backend', '', '', 2)['categories']);
    }
}
