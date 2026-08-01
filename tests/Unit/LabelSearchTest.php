<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Search\LabelSearch;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tool\Registry;

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

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame('installation', $result->data['answeredBy']);
        self::assertSame(0, $result->data['matchCount']);
        self::assertStringNotContainsString('could not be asked', $result->text);
        self::assertStringContainsString('No label in', $result->text);
    }

    #[Test]
    public function aConsoleThatCannotRunIsStillUnanswered(): void
    {
        $this->consoleThatFails('the database is not reachable');

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

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

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame(0, $result->data['matchCount']);
        self::assertSame(
            [['term' => 'save', 'matchCount' => 1], ['term' => 'document', 'matchCount' => 1]],
            $result->data['terms']
        );
        self::assertStringContainsString('"save" matches 1 label(s)', $result->text);
    }

    #[Test]
    public function aResourceRestrictsReuseToTheUsageContext(): void
    {
        $this->consoleThatPrints((string) json_encode(['items' => [
            [
                'resource' => 'EXT:backend/Resources/Private/Language/locallang.xlf',
                'labels' => [
                    ['domain' => 'backend.messages', 'reference' => 'action.new', 'label' => 'New'],
                ],
            ],
            [
                'resource' => 'EXT:sitepackage/Resources/Private/Language/Backend/Import.xlf',
                'labels' => [
                    ['domain' => 'sitepackage.backend.import', 'reference' => 'actions.createImport', 'label' => 'New import'],
                ],
            ],
        ]], JSON_THROW_ON_ERROR));

        $resource = 'EXT:sitepackage/Resources/Private/Language/Backend/Import.xlf';
        $result = Registry::call('typo3_label_lookup', [
            'query' => 'new',
            'resource' => $resource,
        ]);

        self::assertSame(1, $result->data['matchCount']);
        self::assertSame($resource, $result->data['resource']);
        self::assertSame('actions.createImport', $result->data['labels'][0]['key']);
        self::assertStringNotContainsString('backend.messages:action.new', $result->text);
        self::assertStringContainsString('Search restricted to the translation resource used', $result->text);
        self::assertStringContainsString($resource, $result->text);
    }

    #[Test]
    public function aConsoleThatCannotBootIsAnsweredFromTheFilesItWouldHaveRead(): void
    {
        // An installed TYPO3 whose database has no schema yet: the console
        // fails on the first query, and the labels are sitting in the XLF files
        // of the same packages the icon lookup already reads.
        $this->consoleThatFails('An exception occurred while executing a query: '
            . "Table 'db.tx_scheduler_task' doesn't exist");
        $this->labelFile('Resources/Private/Language/locallang.xlf', ['labels.save' => 'Save document']);

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame('packages', $result->data['answeredBy']);
        self::assertSame('core.messages:labels.save', $result->data['labels'][0]['ref']);
        self::assertStringContainsString('LANG/resourceOverrides', $result->text);
    }

    #[Test]
    public function aDatabaseWithoutASchemaIsNamedRatherThanLeftAsAStackTrace(): void
    {
        $this->consoleThatFails('An exception occurred while executing a query: '
            . "Table 'db.tx_scheduler_task' doesn't exist");

        // Nothing to fall back on here — this package ships no labels — so the
        // answer is unanswered, and says what to do about it.
        $result = Registry::call('typo3_label_lookup', ['query' => 'save']);

        self::assertSame('nothing', $result->data['answeredBy']);
        self::assertStringContainsString('no TYPO3 schema yet', $result->text);
        self::assertStringContainsString('no TYPO3 schema yet', $result->data['unavailable']['diagnosis']);
    }

    /** @param array<string, string> $units */
    private function labelFile(string $path, array $units): void
    {
        $file = $this->temporaryRoot . '/typo3/sysext/core/' . $path;
        mkdir(dirname($file), 0o777, true);

        $body = '';
        foreach ($units as $id => $source) {
            $body .= sprintf('<trans-unit id="%s"><source>%s</source></trans-unit>', $id, $source);
        }
        file_put_contents($file, '<?xml version="1.0" encoding="UTF-8"?>'
            . '<xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">'
            . '<file source-language="en" datatype="plaintext"><body>' . $body . '</body></file></xliff>');
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
