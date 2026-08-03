<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Tool\CommitMessageGuide;

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
}
