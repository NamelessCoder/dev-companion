<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\LabelSearch;
use Typo3CmsMcp\Tools;
use Typo3CmsMcp\Typo3Cli;

/**
 * What a label query means, and what an empty answer to one means.
 *
 * Both were wrong in the same call: "save document" could never match, because
 * the console searched for that string rather than for those words, and the
 * empty result it printed came back as an unreachable installation.
 */
final class LabelSearchTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        Instance::discoverFrom(null);
        Typo3Cli::forget();
    }

    #[Test]
    public function aQueryOfSeveralWordsAsksForAllOfThemAtOnce(): void
    {
        // One console call, not one per word: a call boots TYPO3.
        self::assertSame('--regex=/(save|document)/i', LabelSearch::consoleOption(LabelSearch::terms('save document')));
    }

    #[Test]
    public function aWordThatLooksLikeAPatternIsStillAWord(): void
    {
        self::assertSame('--regex=/(labels\.title)/i', LabelSearch::consoleOption(LabelSearch::terms('labels.title')));
    }

    #[Test]
    public function aLabelAnswersOnlyWhenItCarriesEveryWord(): void
    {
        $labels = [
            ['key' => 'labels.save_document', 'source' => 'Save document'],
            ['key' => 'labels.save', 'source' => 'Save'],
            ['key' => 'labels.document', 'source' => 'Document'],
        ];

        $matching = LabelSearch::carryingEvery($labels, LabelSearch::terms('save document'));

        self::assertSame(['labels.save_document'], array_column($matching, 'key'));
    }

    #[Test]
    public function theWordsMayComeInAnyOrderAndAnyCase(): void
    {
        $labels = [['key' => 'labels.save_document', 'source' => 'Save document']];

        self::assertCount(1, LabelSearch::carryingEvery($labels, LabelSearch::terms('Document SAVE')));
    }

    #[Test]
    public function aWordInsideATransUnitIdCountsAlthoughNoWordBoundaryPrecedesIt(): void
    {
        // An underscore is a word character, so anchoring the match would drop
        // exactly the ids a caller searches by.
        $labels = [['key' => 'labels.save_document', 'source' => 'Speichern']];

        self::assertCount(1, LabelSearch::carryingEvery($labels, LabelSearch::terms('document')));
    }

    #[Test]
    public function anEmptyResultSaysHowFarEachWordReachesOnItsOwn(): void
    {
        $labels = [
            ['key' => 'labels.save', 'source' => 'Save'],
            ['key' => 'labels.save_all', 'source' => 'Save all'],
            ['key' => 'labels.document', 'source' => 'Document'],
        ];

        self::assertSame(
            [['term' => 'save', 'matchCount' => 2], ['term' => 'document', 'matchCount' => 1]],
            LabelSearch::perTermCounts($labels, LabelSearch::terms('save document'))
        );
    }

    #[Test]
    public function aConsoleThatFoundNothingIsAnAnswerRatherThanAFailure(): void
    {
        // The console prints "[WARNING] No language resource files found." and
        // exits successfully. Reading that as an unreachable installation sent
        // the caller to typo3_server_scope instead of to a narrower query.
        $this->consoleThatPrints("Labels in active extensions\n===\n\n [WARNING] No language resource files found.\n");

        $result = Tools::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame('installation', $result->data['answeredBy']);
        self::assertSame(0, $result->data['matchCount']);
        self::assertStringNotContainsString('could not be asked', $result->text);
        self::assertStringContainsString('No label in', $result->text);
    }

    #[Test]
    public function aConsoleThatCannotRunIsStillUnanswered(): void
    {
        $this->consoleThatFails('the database is not reachable');

        $result = Tools::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame('nothing', $result->data['answeredBy']);
        self::assertStringContainsString('could not be asked', $result->text);
    }

    #[Test]
    public function whatEachWordReachesOnItsOwnIsInTheAnswerRatherThanOnlyInTheText(): void
    {
        $this->consoleThatPrints((string) json_encode(['items' => [[
            'resource' => 'EXT:backend/Resources/Private/Language/locallang.xlf',
            'labels' => [
                ['domain' => 'backend.messages', 'reference' => 'labels.save', 'label' => 'Save'],
                ['domain' => 'backend.messages', 'reference' => 'labels.document', 'label' => 'Document'],
            ],
        ]]], JSON_THROW_ON_ERROR));

        $result = Tools::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame(0, $result->data['matchCount']);
        self::assertSame(
            [['term' => 'save', 'matchCount' => 1], ['term' => 'document', 'matchCount' => 1]],
            $result->data['terms']
        );
        self::assertStringContainsString('"save" matches 1 label(s)', $result->text);
    }

    /** A console that answers every call with $output and exits successfully. */
    private function consoleThatPrints(string $output): void
    {
        $this->console(sprintf('<?php echo %s;', var_export($output, true)));
    }

    /** A console that fails the way a broken installation does. */
    private function consoleThatFails(string $reason): void
    {
        $this->console(sprintf('<?php fwrite(STDERR, %s); exit(1);', var_export($reason, true)));
    }

    private function console(string $script): void
    {
        $root = sys_get_temp_dir() . '/typo3-cms-mcp-labels-' . bin2hex(random_bytes(6));
        $this->temporaryRoot = $root;
        mkdir($root . '/typo3/sysext/core', 0o777, true);
        file_put_contents($root . '/composer.json', json_encode(
            ['name' => 'typo3/cms', 'type' => 'typo3-cms-core'],
            JSON_THROW_ON_ERROR
        ));
        file_put_contents($root . '/typo3/sysext/core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root . '/console.php', $script);

        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' ' . $root . '/console.php');
        Instance::discoverFrom($root);
        Typo3Cli::forget();
    }
}
