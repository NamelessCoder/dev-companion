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
 * on its own: the renderer publishes the copy rather than the sources, so a
 * render before the copy is written renders the previous one; the theme's
 * finish step reads the pages the renderer has just written, so run before it
 * there is nothing to finish. `D-DOC-020` is why they are one command, and this
 * is what keeps them in the one order that works.
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

    protected function setUp(): void
    {
        $this->into = sys_get_temp_dir() . '/dev-companion-render-' . getmypid();
        $this->ran = [];
        $this->fails = null;

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
        Directory::remove($this->into);
    }

    /**
     * The whole of it in one call, and the theme's finish step runs over pages
     * the renderer has already written.
     */
    #[Test]
    public function oneCallInstallsWhatIsMissingThenBuildsThenRendersThenFinishes(): void
    {
        $output = new BufferedOutput();

        self::assertSame(0, (new DocumentationRender())($output, $this->into));

        $ran = array_map(static fn(array $command): string => implode(' ', $command), $this->ran);
        $render = array_search(implode(' ', Site::RENDER), $ran, true);
        $finish = array_search(implode(' ', Site::finish($this->into . '/html')), $ran, true);
        self::assertIsInt($render);
        self::assertIsInt($finish);
        self::assertLessThan($finish, $render);
        // Whatever the machine is missing is installed before either of them.
        self::assertSame(count(Site::installs()), $render);
    }

    /** The copy and the pages are built where the caller said, not in this checkout. */
    #[Test]
    public function everythingTheSiteIsMadeOfIsWrittenInOnePlace(): void
    {
        $output = new BufferedOutput();

        self::assertSame(0, (new DocumentationRender())($output, $this->into));

        self::assertFileExists($this->into . '/source/index.md');
        $written = $output->fetch();
        self::assertStringContainsString($this->into . '/html', $written);
        self::assertStringContainsString('php -S localhost:8000 -t ' . $this->into . '/html', $written);
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
        // The render stopped there: the finish step was never asked for.
        $ran = array_map(static fn(array $command): string => implode(' ', $command), $this->ran);
        self::assertNotContains(implode(' ', Site::finish($this->into . '/html')), $ran);
    }

    /**
     * The finish step is where the stylesheet, the script and the search index
     * come from, so a failure there is a site nothing would style and is not a
     * render that succeeded.
     */
    #[Test]
    public function aSiteThatWasNeverFinishedIsAFailure(): void
    {
        $this->fails = 'soul-finish.js';
        $output = new BufferedOutput();

        self::assertSame(1, (new DocumentationRender())($output, $this->into));
        self::assertStringContainsString(
            implode(' ', Site::finish($this->into . '/html')) . ' failed',
            $output->fetch(),
        );
    }
}
