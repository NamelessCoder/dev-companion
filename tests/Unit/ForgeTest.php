<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Contribution\Forge;

/**
 * The tracker is somebody else's host, so what is held here is what this side
 * does with what comes back: the fields the answer is composed of, the journal
 * the decision sits in, the relation that names two issues and means one, and
 * the page a bot protection answers 200 with.
 */
final class ForgeTest extends TestCase
{
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
}
