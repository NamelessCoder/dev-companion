<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Contribution\Forge;
use Typo3CmsMcp\Http\Recent;

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
        $asked = '';
        $forge = new Forge(function (string $url) use (&$asked): string {
            $asked = $url;

            return (string) json_encode(self::HITS);
        });

        $answer = $forge->search('  cache busting  ', 3);

        self::assertStringContainsString('q=cache%20busting', $asked);
        self::assertStringContainsString('issues=1', $asked);
        self::assertStringContainsString('limit=3', $asked);
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
}
