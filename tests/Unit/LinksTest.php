<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Upkeep\Links;

/**
 * The paths this repository writes between its own files are the one thing it
 * says about itself that nothing read back. Nineteen decision files were
 * renamed in one pass and 58 references rewritten with them; a missed one is a
 * link that goes nowhere and reports nothing.
 */
final class LinksTest extends TestCase
{
    #[Test]
    public function everyPathThisRepositoryWritesToItselfResolves(): void
    {
        $dead = Links::dead();

        self::assertSame(
            [],
            $dead,
            'dead links: ' . implode(', ', array_map(
                static fn(array $link): string => $link['file'] . ':' . $link['line'] . ' → ' . $link['link'],
                $dead,
            )),
        );
    }

    /**
     * The check above passes on a repository with no links at all, so this is
     * what says it is reading them: one file, one link that resolves, one that
     * does not, and the anchors and URLs that are deliberately not held.
     */
    #[Test]
    public function aPathThatIsNotThereIsFoundAndTheRestIsLeftAlone(): void
    {
        $directory = sys_get_temp_dir() . '/links-' . bin2hex(random_bytes(6));
        mkdir($directory);
        file_put_contents($directory . '/there.md', '# there');
        file_put_contents($directory . '/reader.md', implode("\n", [
            '[a file that is here](there.md)',
            '[the same file, at a heading](there.md#a-heading-that-moved)',
            '[somebody else](https://docs.typo3.org/)',
            '[a file that is not](gone.md)',
            '[gone-by-reference]: also-gone.md',
        ]));

        $dead = Links::deadIn($directory . '/reader.md');

        self::assertSame(['gone.md', 'also-gone.md'], array_column($dead, 'link'));
        self::assertSame([4, 5], array_column($dead, 'line'));

        unlink($directory . '/reader.md');
        unlink($directory . '/there.md');
        rmdir($directory);
    }
}
