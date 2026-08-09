<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Upkeep\Command\DocumentationRender;
use TYPO3\DevCompanion\Upkeep\Site;

/**
 * What `bin/cli documentation:render` is for is the order, so the order is what
 * this holds.
 *
 * The steps were six commands a person ran by hand and each of them is correct
 * on its own: the assets carry a hash the layout reads while it renders, so
 * built after the render they are the previous build's names; the two files
 * that go beside the pages are written into a directory the renderer creates,
 * so written before it they are swept with everything else. `D-DOC-020` is why
 * they are one command, and this is what keeps them in the one order that
 * works.
 *
 * Nothing here runs a renderer or an install. `Site::useRunner()` is the seam
 * (`R-COD-003`), and what the cases assert on is the sequence the stub was
 * asked for.
 */
final class DocumentationRenderTest extends TestCase
{
    /** Where the site is built, which is never the `.site` of this checkout. */
    private string $into = '';

    /** @var array<int, array<int, string>> every command the stub was asked for, in order */
    private array $ran = [];

    /** Which command the stub answers with a failure, or none. */
    private ?string $fails = null;

    /** What the theme's build would have written, since nothing here runs it. */
    private string $built = '';

    protected function setUp(): void
    {
        $this->into = sys_get_temp_dir() . '/dev-companion-render-' . getmypid();
        $this->ran = [];
        $this->fails = null;

        // The stub runs no build, so what the build would have written is
        // handed in instead — a machine that has never built the theme is
        // otherwise a machine where these cases fail for the wrong reason.
        $this->built = $this->into . '-built';
        mkdir($this->built, 0777, true);
        file_put_contents($this->built . '/site.1234abcd.css', 'body{}');
        Site::useBuilt($this->built);

        $runner = self::createStub(CommandRunner::class);
        $runner->method('run')->willReturnCallback(function (array $command): array {
            $this->ran[] = $command;
            $ok = $this->fails === null || !str_contains(implode(' ', $command), $this->fails);

            return ['ok' => $ok, 'exitCode' => $ok ? 0 : 1, 'output' => '', 'error' => $ok ? '' : 'what went wrong'];
        });
        Site::useRunner($runner);
    }

    protected function tearDown(): void
    {
        Site::useRunner(null);
        Site::useBuilt(null);
        Directory::remove($this->into);
        Directory::remove($this->built);
    }

    /**
     * The whole of it in one call, and the two hashed files are built before
     * the render reads their names.
     */
    #[Test]
    public function oneCallInstallsWhatIsMissingThenBuildsThenRenders(): void
    {
        $output = new BufferedOutput();

        self::assertSame(0, (new DocumentationRender())($output, $this->into));

        $ran = array_map(static fn(array $command): string => implode(' ', $command), $this->ran);
        $assets = array_search(implode(' ', Site::BUILD_ASSETS), $ran, true);
        $render = array_search(implode(' ', Site::RENDER), $ran, true);
        self::assertIsInt($assets);
        self::assertIsInt($render);
        self::assertLessThan($render, $assets);
        // Whatever the machine is missing is installed before either of them.
        self::assertSame(count(Site::installs()), $assets);
    }

    /** The copy, the assets and the index all land where the site is built. */
    #[Test]
    public function everythingTheSiteIsMadeOfIsWrittenInOnePlace(): void
    {
        $output = new BufferedOutput();

        self::assertSame(0, (new DocumentationRender())($output, $this->into));

        self::assertFileExists($this->into . '/source/index.md');
        self::assertFileExists($this->into . '/html/' . Site::SEARCH);
        self::assertDirectoryExists($this->into . '/html/' . Site::ASSETS);
        self::assertStringContainsString('php -S localhost:8000 -t ' . $this->into . '/html', $output->fetch());
    }

    /**
     * A render that dies has to say which step. The output of a failed step is
     * the only thing that says why, and it is a `composer install` or a
     * renderer somebody has to read.
     */
    #[Test]
    public function aFailedStepStopsTheRenderAndQuotesTheCommand(): void
    {
        $this->fails = 'vendor/bin/guides';
        $output = new BufferedOutput();

        self::assertSame(1, (new DocumentationRender())($output, $this->into));

        $written = $output->fetch();
        self::assertStringContainsString(implode(' ', Site::RENDER) . ' failed', $written);
        self::assertStringContainsString('what went wrong', $written);
        // The render stopped there: what goes beside the pages was never written.
        self::assertFileDoesNotExist($this->into . '/html/' . Site::SEARCH);
    }

    /** Nothing built is a site served unstyled, so it is a failure and not a site. */
    #[Test]
    public function aSiteThatWouldBeServedUnstyledIsAFailure(): void
    {
        Site::useBuilt($this->into . '-absent');
        $output = new BufferedOutput();

        self::assertSame(1, (new DocumentationRender())($output, $this->into));
        self::assertStringContainsString('served unstyled', $output->fetch());
        self::assertFileDoesNotExist($this->into . '/html/' . Site::SEARCH);
    }
}
