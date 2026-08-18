<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Upkeep\Checkouts;

/**
 * What a core checkout carries beyond what `checkouts:update` put there.
 *
 * `tools:record` refuses one that carries anything, because the recording is
 * evidence about the checkout that command makes — `D-DOC-034`. What can be
 * wrong here is the reading of git's answer, so that is what this holds; the
 * git call itself is the seam a test hands a runner to.
 */
final class CheckoutsTest extends TestCase
{
    #[After]
    protected function forgetTheRunner(): void
    {
        Checkouts::useRunner(null);
    }

    #[Test]
    public function everyEntryGitReportsIsCarried(): void
    {
        // What `composer install` leaves in a core checkout, as git reported it
        // in .checkouts/14.3 on 2026-08-18. All six are ignored, which is why
        // the status is asked with --ignored and why a plain one calls this
        // tree clean.
        $this->answering(0, "!! .cache/\n!! bin/\n!! index.php\n!! typo3/sysext/core/bin/\n!! typo3temp/\n!! vendor/\n");

        self::assertSame(
            ['.cache/', 'bin/', 'index.php', 'typo3/sysext/core/bin/', 'typo3temp/', 'vendor/'],
            Checkouts::beyondIndex('/checkouts/14.3'),
        );
    }

    #[Test]
    public function aCheckoutAsTheCommandMakesItCarriesNothing(): void
    {
        $this->answering(0, '');

        self::assertSame([], Checkouts::beyondIndex('/checkouts/14.3'));
    }

    #[Test]
    public function aGitThatCannotAnswerReportsNoDifference(): void
    {
        // Not a repository, or no git at all. Either way nothing was found to
        // carry, and a refusal on that would stop a recording over a question
        // that was never asked.
        $this->answering(128, "fatal: not a git repository\n");

        self::assertSame([], Checkouts::beyondIndex('/somewhere/else'));
    }

    #[Test]
    public function bothKindsOfChangeAreCarried(): void
    {
        // A tracked file somebody edited breaks the recording exactly as an
        // installed console does: neither is in what checkouts:update makes.
        $this->answering(0, " M composer.json\n?? notes.md\n!! vendor/\n");

        self::assertSame(['composer.json', 'notes.md', 'vendor/'], Checkouts::beyondIndex('/checkouts/14.3'));
    }

    private function answering(int $exitCode, string $output): void
    {
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturn(
            ['ok' => $exitCode === 0, 'exitCode' => $exitCode, 'output' => $output, 'error' => ''],
        );
        Checkouts::useRunner($git);
    }
}
