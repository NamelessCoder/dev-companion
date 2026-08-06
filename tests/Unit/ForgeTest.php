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
     * (`feedback/2026-08-05-033846`).
     */
    #[Test]
    public function theFilesHangingOffAnIssueAreNamedRatherThanFetched(): void
    {
        $asked = '';
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked = $url;

            return (string) json_encode(['issue' => self::ISSUE]);
        });

        $attachments = $forge->issue('110348')['issue']['attachments'];

        self::assertStringContainsString('include=journals,relations,attachments', $asked);
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
     * asks about age, which no title carries.
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
     * outage. The hits stand with the fields the title carried.
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
     * The two calls an enumeration makes, answered by the URL that was asked
     * for: the project carries the areas, the issues carry the page.
     *
     * @param list<string> $asked
     * @return \Closure(string): string
     */
    private static function tracker(array &$asked): \Closure
    {
        return function (string $url) use (&$asked): string {
            $asked[] = $url;

            return (string) json_encode(str_contains($url, '/issues.json') ? self::PAGE : self::PROJECT);
        };
    }

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
                'assigned_to' => ['name' => 'Sacha Vorbeck'],
                'created_on' => '2005-07-11T10:22:33Z',
                'updated_on' => '2026-01-23T08:11:00Z',
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
     * fields, which is why the two dates are answerable here at all.
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
     * A page is not the set, and only the tracker's own count says which of the
     * two the caller is holding.
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
     * substring match answers "rte" with every category carrying "Reporter".
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

        $issues = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/issues.json')));
        self::assertCount(1, $issues);
        self::assertStringContainsString('category_id=971%7C972', $issues[0]);
        self::assertSame(['Backend API', 'Backend User Interface'], $answer['categoriesUsed']);
    }

    /**
     * A word naming no area is an answer about the word. Sent on unfiltered it
     * would come back as the whole backlog, which reads as "everything is about
     * the RTE" and is the one mistake this path can make.
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
     * category the core adds is one this can filter by without a release.
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
}
