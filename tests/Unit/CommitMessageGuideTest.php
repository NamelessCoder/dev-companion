<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Tool\CommitMessageGuide;

/**
 * What the caller reads, rather than what the class behind it returns: the
 * answer drops "no issues found" where something complained, and it may drop
 * nothing else — R-GUI-007.
 */
final class CommitMessageGuideTest extends TestCase
{
    #[Test]
    public function aCheckedMessageStillSaysWhatTheWrappingJoined(): void
    {
        $result = CommitMessageGuide::answer([
            'message' => "Fix the thing\n\nExecuted commands:\n./Build/Scripts/runTests.sh -s cgl -n\n\n"
                . "Resolves: #1\nReleases: main\n",
        ]);

        $codes = array_column($result->data['checks'], 'code');
        self::assertContains('missing-keyword', $codes, 'the subject carries no keyword');
        self::assertContains('body-lines-reflowed', $codes);
        self::assertNotContains('no-issues-found', $codes);
    }

    /**
     * The caveat survives the drop, because the answer it qualifies is the one
     * with something else in it — R-GUI-011.
     */
    #[Test]
    public function aCheckedMessageSaysTheClassificationWasAssumed(): void
    {
        $result = CommitMessageGuide::answer([
            'message' => "Fix the thing\n\nBody.\n\nResolves: #1\nReleases: main\n",
            // The changelog obligation this is about is the core's, and the
            // core is stated since `D-GUI-010`.
            'workflow' => 'core',
        ]);

        $codes = array_column($result->data['checks'], 'code');
        self::assertContains('missing-keyword', $codes);
        self::assertNotContains('no-issues-found', $codes);
        self::assertContains('breaking-not-assessed', $codes);
    }

    /**
     * The line under the draft and the draft itself say the same thing: the
     * project workflow demands no trailer, and writes the one the call passed —
     * `D-GUI-017`.
     */
    #[Test]
    public function theProjectAnswerSaysTheIssueItWroteIsTheOneThatWasPassed(): void
    {
        $result = CommitMessageGuide::answer([
            'changeType' => 'TASK',
            'summary' => 'Update the frontend build to current dependencies',
            'issue' => '348',
        ]);

        self::assertSame('project', $result->data['workflow']);
        self::assertStringContainsString("\nResolves: #348", $result->data['message']);
        self::assertStringContainsString('Resolves: and Related: lines carry the issues this call passed', $result->text);
    }

    /** An answer the caller gave in the call wins over the one the subject withholds. */
    #[Test]
    public function anIsBreakingTheCallerPassedAnswersItEvenWhenItIsFalse(): void
    {
        $result = CommitMessageGuide::answer([
            'message' => "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main\n",
            'isBreaking' => false,
        ]);

        self::assertNotContains('breaking-not-assessed', array_column($result->data['checks'], 'code'));
    }
}
