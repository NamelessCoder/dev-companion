<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PhpCsFixer\ConfigInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Tests\Support\Editorconfig;

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
     * A skipped test is how a test stops holding anything without stopping the
     * suite, and the summary that reports it is the one nobody reads twice.
     *
     * Every precondition this suite has is a property of this repository — it
     * is a standalone checkout, its feedback archive is committed, its
     * knowledge base is on disk — so each is an assertion instead, and a
     * precondition that stopped being true is a failure with a sentence rather
     * than a test that quietly went away. The two skips this replaced had both
     * been true since the day they were written.
     *
     * A genuinely environment-dependent case would need a way past this. It
     * would also need this paragraph rewritten, which is the point.
     */
    #[Test]
    public function noTestSkipsItselfInsteadOfHolding(): void
    {
        $skipping = [];
        foreach (self::testFiles() as $file) {
            $contents = (string) file_get_contents($file);
            foreach (['markTestSkipped', 'markTestIncomplete'] as $escape) {
                if (str_contains($contents, $escape . '(')) {
                    $skipping[] = basename($file) . ' calls ' . $escape . '()';
                }
            }
        }

        self::assertSame([], $skipping);
    }

    /**
     * One question, one way of asking it. A directory listing was `glob()` in
     * the flat case and a `RecursiveDirectoryIterator` in the deep one, and the
     * second shape ran to a dozen lines that said what one Finder call says.
     * Both are `symfony/finder` now (D-COD-3), and a returning `glob()` is the
     * split coming back rather than a style slip.
     */
    #[Test]
    public function everyDirectoryIsReadThroughTheFinder(): void
    {
        $found = [];
        $files = Finder::create()->files()->in([dirname(__DIR__, 2) . '/src', dirname(__DIR__, 2) . '/bin', dirname(__DIR__)])
            ->notName('StructureTest.php')->sortByName();
        foreach ($files as $file) {
            preg_match_all(
                '/\b(glob|scandir|opendir|readdir)\s*\(|\bRecursive(?:Directory|Iterator)Iterator\b|\bFilesystemIterator\b/',
                (string) file_get_contents($file->getPathname()),
                $matches,
            );
            foreach ($matches[0] as $call) {
                $found[] = $file->getFilename() . ' uses ' . rtrim($call, ' (');
            }
        }

        self::assertSame([], $found);
    }

    /**
     * `.editorconfig` is what an editor obeys while a file is being typed, and
     * php-cs-fixer is what rewrites it afterwards. Where the two disagree, each
     * undoes the other: a line typed at the stated indentation comes back
     * reindented, and nobody looks for the argument in a config file.
     *
     * The fixer states its indentation by not stating one — PER-CS 3.0 is four
     * spaces and `Config` defaults to it — so this asks the config rather than
     * the rule list.
     */
    #[Test]
    public function editorconfigTypesPhpTheWayTheFixerRewritesIt(): void
    {
        $config = require dirname(__DIR__, 2) . '/.php-cs-fixer.dist.php';

        self::assertInstanceOf(ConfigInterface::class, $config);
        self::assertSame(
            strlen($config->getIndent()),
            Editorconfig::indentFor('Paths.php'),
            '.php-cs-fixer.dist.php and .editorconfig disagree about how PHP is indented',
        );
    }

    /** @return array<int, string> */
    private static function testFiles(): array
    {
        $tests = [];
        foreach (Finder::create()->files()->in(dirname(__DIR__))->name('*Test.php')->sortByName() as $file) {
            $tests[] = $file->getPathname();
        }

        return $tests;
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
        $sources = [];
        $files = Finder::create()->files()->in(dirname(__DIR__, 2) . '/src')->name('*.php')
            ->notName('bootstrap.php')->notName('probe.php')->sortByName();
        foreach ($files as $file) {
            $sources[] = $file->getPathname();
        }

        return $sources;
    }
}
