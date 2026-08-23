<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\TestingFramework;

/**
 * Which harness a statement about testing a project extension was read in.
 *
 * The package releases on its own cycle, so the statements are verified against
 * a tag rather than against a core branch, and which tag that is is derived
 * from the core's own pin — `D-KNW-106`. This holds the one step of that
 * derivation which needs no repository to try; everything else is a git call,
 * and `bin/cli catalog:check` is where it is exercised.
 */
final class TestingFrameworkTest extends TestCase
{
    #[Decision('D-KNW-106')]
    #[Test]
    public function aPinThatNamesOneReleaseLineIsThatLine(): void
    {
        // The pins the four covered branches carry today.
        self::assertSame('8', TestingFramework::line('^8.3.1'));
        self::assertSame('9', TestingFramework::line('^9.2.1'));
        self::assertSame('9', TestingFramework::line('^9.5.0'));
        self::assertSame('main', TestingFramework::line('dev-main'));
    }

    #[Decision('D-KNW-106')]
    #[Test]
    public function aPinThatNamesTwoLinesNamesNone(): void
    {
        // The case the check exists for: a core major that admits two harnesses
        // no longer says which one a statement bound to it was read in, and a
        // line picked out of the two would be a guess wearing a version number.
        self::assertNull(TestingFramework::line('^8.3.1 || ^9.0'));
        self::assertNull(TestingFramework::line('>=8'));
        self::assertNull(TestingFramework::line(''));
    }
}
