<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Contribution\Forge;
use Typo3CmsMcp\Contribution\Gerrit;
use Typo3CmsMcp\Http\Recent;

/**
 * What is held from a host outside this machine, and what is fetched again.
 *
 * The two sources sit on opposite sides of one question: who can change the
 * answer. Nobody reaches the tracker through this server, so its answers are
 * held; the caller reaches the review server with its own git, and the answer
 * it changes there is the one it asks about next.
 */
final class RecentTest extends TestCase
{
    private int $now = 1_000_000;

    private int $reads = 0;

    #[After]
    public function forgetWhatWasHeld(): void
    {
        Recent::forget();
        Recent::useClock(null);
    }

    #[Test]
    public function anAnsweredIssueIsReadFromTheTrackerOnce(): void
    {
        $forge = new Forge($this->transport('{"issue": {"id": 105403, "subject": "f:image and cache busting"}}'));

        $first = $forge->issue('105403');
        $second = $forge->issue('105403');

        self::assertSame('answered', $first['status']);
        self::assertSame($first, $second);
        self::assertSame(1, $this->reads, 'the second answer came from what was held');
    }

    #[Test]
    public function anIssueIsReadAgainOnceWhatWasHeldIsOld(): void
    {
        $forge = new Forge($this->transport('{"issue": {"id": 105403, "subject": "f:image and cache busting"}}'));

        $forge->issue('105403');
        $this->now += Forge::HELD_FOR;
        $forge->issue('105403');

        self::assertSame(2, $this->reads);
    }

    #[Test]
    public function aBodyTheTrackerDidNotAnswerIsNotHeld(): void
    {
        // The challenge page a protection answers with is a state of this
        // minute. Held, one bad minute would be five.
        $forge = new Forge($this->transport('<html>are you a robot</html>'));

        $forge->issue('105403');
        $second = $forge->issue('105403');

        self::assertSame('unavailable', $second['status']);
        self::assertSame(4, $this->reads, 'both calls read, and each retried with the plain agent');
    }

    #[Test]
    public function aChangeThatExistsIsReadFromTheReviewServerOnce(): void
    {
        $gerrit = new Gerrit($this->transport(")]}'\n" . '[{"_number": 90210, "subject": "[BUGFIX] Do the thing", "project": "Packages/TYPO3.CMS"}]'));

        $first = $gerrit->changesForIssue('105403');
        $second = $gerrit->changesForIssue('105403');

        self::assertSame('answered', $first['status']);
        self::assertSame($first, $second);
        self::assertSame(1, $this->reads);
    }

    #[Test]
    public function noChangeForAnIssueIsAskedEveryTime(): void
    {
        // The caller falsifies this one itself by pushing, and asks again right
        // afterwards. A held "there is none" is what sends somebody to write a
        // patch that is already up.
        $gerrit = new Gerrit($this->transport(")]}'\n[]"));

        $gerrit->changesForIssue('105403');
        $gerrit->changesForIssue('105403');

        self::assertSame(2, $this->reads);
    }

    /** A transport that answers the same body every time and counts the asking. */
    private function transport(string $body): \Closure
    {
        Recent::useClock(fn(): int => $this->now);

        return function () use ($body): string {
            $this->reads++;

            return $body;
        };
    }
}
