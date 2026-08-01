<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * What holds the shape of the source tree itself, rather than what any one
 * class does.
 */
final class StructureTest extends TestCase
{
    /**
     * A second class in a file is invisible to PSR-4: the file is found through
     * the first class's name, so the second one loads only where something has
     * already loaded the first. It works in the file that wrote it and fails as
     * a missing class from anywhere else, which is the kind of failure that
     * arrives long after the commit.
     */
    #[Test]
    public function everyFileDeclaresOneClass(): void
    {
        foreach (self::sources() as $file) {
            $tokens = \PhpToken::tokenize((string) file_get_contents($file));

            $declarations = [];
            foreach ($tokens as $index => $token) {
                if (!$token->is([T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM])) {
                    continue;
                }
                // `Foo::class` and an anonymous `new class` are not
                // declarations, and only a name makes one.
                $next = $tokens[$index + 2] ?? null;
                $previous = $tokens[$index - 1] ?? null;
                if ($next?->is(T_STRING) === true && $previous?->is(T_DOUBLE_COLON) !== true) {
                    $declarations[] = $next->text;
                }
            }

            self::assertCount(
                1,
                $declarations,
                sprintf('%s declares %s.', $file, implode(', ', $declarations) ?: 'nothing'),
            );
        }
    }

    /**
     * Every class of this package, which is every PHP file below src/ except
     * the ones that are deliberately not classes: the bootstrap, and the probe
     * that runs inside somebody else's installation.
     *
     * @return array<int, string>
     */
    private static function sources(): array
    {
        $root = dirname(__DIR__, 2) . '/src';
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));

        $sources = [];
        foreach ($files as $file) {
            if (!$file instanceof \SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }
            if (in_array($file->getFilename(), ['bootstrap.php', 'probe.php'], true)) {
                continue;
            }
            $sources[] = $file->getPathname();
        }

        sort($sources);

        return $sources;
    }
}
