<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\ToolAnswers;
use TYPO3\DevCompanion\Upkeep\ToolCalls;
use TYPO3\DevCompanion\Upkeep\ToolSurface;

/**
 * The tool reference against the registry it is rendered from.
 *
 * A generated page nothing reads back is a hand-written one that was generated
 * once. This is also where a tool that is offered and described nowhere outward
 * is caught, since the index below is the list: `readme.md` carried a
 * hand-written one until five tools had been added since anybody checked, and
 * what replaced it is generated rather than watched.
 */
final class ToolSurfaceTest extends TestCase
{
    #[Test]
    public function everyPageIsWhatTheRegistryDeclares(): void
    {
        foreach (ToolSurface::pages() as $file => $contents) {
            $named = substr($file, strlen(Paths::root()) + 1);
            self::assertFileExists($file, $named . ' — run bin/cli tools:index');
            self::assertSame(
                $contents,
                (string) file_get_contents($file),
                $named . ' is not what the registry declares — run bin/cli tools:index',
            );
        }
    }

    /**
     * What the comparison above cannot say on its own: that the directory is the
     * whole surface and nothing besides. A renderer that dropped every tool
     * would agree with a directory that had none, and a tool that left the
     * registry leaves a page behind that reads like one it still offers.
     */
    #[Test]
    public function theIndexReachesEveryToolAndTheDirectoryHoldsNoOther(): void
    {
        $pages = ToolSurface::pages();
        $index = (string) file_get_contents(ToolSurface::index());

        foreach (array_column(Registry::definitions(), 'name') as $name) {
            self::assertArrayHasKey(ToolSurface::file($name), $pages, $name . ' is offered and rendered nowhere');
            self::assertStringContainsString('<' . $name . '>`', $index, $name . ' is on no line of the index');
        }

        foreach (ToolSurface::written() as $written) {
            self::assertArrayHasKey(
                $written->getPathname(),
                $pages,
                $written->getFilename() . ' is a page for a tool the registry does not offer',
            );
        }
    }

    /**
     * Every page either carries what the tool answered or says why it has none.
     *
     * Neither happened for the two feedback tools. The head of the surface
     * promised a recording for every tool, the renderer emitted nothing where
     * there was none, and the promise was therefore false in the one direction a
     * reader cannot check: an absent recording and a forgotten one look the
     * same.
     */
    #[Test]
    public function everyToolCarriesItsAnswerOrSaysWhyItHasNone(): void
    {
        foreach (array_column(Registry::definitions(), 'name') as $name) {
            $page = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(ToolSurface::file($name)));

            if (!isset(ToolCalls::undriven()[$name])) {
                self::assertNotSame('', ToolAnswers::recordedIn(ToolSurface::file($name)), $name . ' answered nowhere');

                continue;
            }

            self::assertStringContainsString(
                (string) preg_replace('/\s+/', ' ', ToolCalls::undriven()[$name]),
                $page,
                $name . ' has no recorded answer and its page does not say why',
            );
        }
    }
}
