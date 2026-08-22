<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Upkeep\Command\DocumentationPreview;

/**
 * What `bin/cli documentation:preview` is for is the order, so the order is what
 * this holds.
 *
 * Each step is correct on its own and none of the orders is free: the renderer
 * publishes the copy rather than the sources, so a render before the copy is
 * written renders the previous one, and the theme's finish step reads the pages
 * the renderer has just written.
 *
 * Nothing here fetches a renderer or starts one. The constructor takes the
 * runner (`R-COD-003`), and what the cases assert on is the sequence the stub
 * was asked for.
 */
final class DocumentationPreviewTest extends TestCase
{
    /** Where the site is built, which is never the `.site` of this checkout. */
    private string $into = '';

    /** @var list<string> every command the stub was asked for, in order */
    private array $ran = [];

    /** Which command the stub answers with a failure, or none. */
    private ?string $fails = null;

    protected function setUp(): void
    {
        $this->into = sys_get_temp_dir() . '/dev-companion-preview-' . getmypid();
        $this->ran = [];
        $this->fails = null;
    }

    protected function tearDown(): void
    {
        Directory::remove($this->into);
    }

    private function preview(): DocumentationPreview
    {
        $runner = self::createStub(CommandRunner::class);
        $runner->method('run')->willReturnCallback(function (array $command): array {
            $this->ran[] = implode(' ', $command);
            $ok = $this->fails === null || !str_contains(implode(' ', $command), $this->fails);

            return ['ok' => $ok, 'exitCode' => $ok ? 0 : 1, 'output' => '', 'error' => $ok ? '' : 'what went wrong'];
        });

        return new DocumentationPreview($runner);
    }

    /** The whole of it in one call, and each step over what the one before it wrote. */
    #[Test]
    public function onePreviewFetchesTheRendererThenRendersThenFinishes(): void
    {
        $output = new BufferedOutput();

        self::assertSame(0, ($this->preview())($output, $this->into));

        self::assertStringContainsString('composer require', implode("\n", $this->ran));
        $render = array_search(true, array_map(
            static fn(string $ran): bool => str_contains($ran, 'vendor/bin/guides'),
            $this->ran,
        ), true);
        $finish = array_search(true, array_map(
            static fn(string $ran): bool => str_contains($ran, 'soul-finish.js'),
            $this->ran,
        ), true);
        self::assertIsInt($render);
        self::assertIsInt($finish);
        self::assertGreaterThan($render, $finish, 'the finish step read pages the renderer had not written');
        // The copy is written by this process rather than by a command, so what
        // says it ran first is the file the renderer was pointed at —
        // `D-DOC-028`.
        self::assertFileExists($this->into . '/source/index.rst');
        self::assertStringContainsString('read it: php -S', $output->fetch());
    }

    /** A preview is run again after every paragraph, so the fetch happens once. */
    #[Test]
    public function aRendererThatIsAlreadyThereIsNotFetchedAgain(): void
    {
        $binary = $this->into . '/renderer/vendor/bin';
        mkdir($binary, 0777, true);
        touch($binary . '/guides');

        ($this->preview())(new BufferedOutput(), $this->into);

        self::assertStringNotContainsString('composer', implode("\n", $this->ran));
    }

    /** A step that failed stops the rest and is quoted with what it said. */
    #[Test]
    public function aFailedStepStopsThePreviewAndQuotesTheCommand(): void
    {
        $this->fails = 'vendor/bin/guides';
        $output = new BufferedOutput();

        self::assertSame(1, ($this->preview())($output, $this->into));
        self::assertStringContainsString('what went wrong', $output->fetch());
        self::assertStringNotContainsString('soul-finish.js', implode("\n", array_slice($this->ran, -1)));
    }
}
