<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
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

    /** The checkout the current test's console and label files sit in. */
    private string $installationRoot = '';

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

    /**
     * What the per-term counts cannot say: which words have to go. Two of these
     * five had to, and the smallest reach — `yaml`, carried by both entries the
     * query was after — is the one to keep rather than the one to drop.
     */
    #[Test]
    public function anEmptyResultNamesTheLargestPartOfTheQueryThatDoesReach(): void
    {
        $entries = [
            ['key' => 'Deprecation-109412-FormYamlConfigurationRegistration', 'source' => 'Form Yaml Configuration Registration'],
            ['key' => 'Feature-84203-UnifyFormSetupYAMLLoading', 'source' => 'Unify Form Setup YAML Loading'],
            ['key' => 'Breaking-101392-GetIdentifierRemoved', 'source' => 'Get Identifier Removed'],
        ];

        // Both of them, because the one a tie-break picks first here is the
        // YAML loading feature rather than the deprecation being looked for.
        self::assertSame(
            [
                ['terms' => ['form', 'yaml', 'registration'], 'matchCount' => 1],
                ['terms' => ['form', 'set', 'yaml'], 'matchCount' => 1],
            ],
            LabelSearch::largestReachingSubsets($entries, LabelSearch::terms('form set yaml registration deprecated'))
        );
    }

    #[Test]
    public function theSubsetThatNarrowsBestComesFirst(): void
    {
        $entries = [
            ['key' => 'Feature-1-BackendModuleModules', 'source' => 'Backend Module Modules'],
            ['key' => 'Feature-2-BackendModuleModulesAgain', 'source' => 'Backend Module Modules Again'],
            ['key' => 'Feature-3-BackendModuleRegistration', 'source' => 'Backend Module Registration'],
        ];

        self::assertSame(
            [
                ['terms' => ['backend', 'module', 'registration'], 'matchCount' => 1],
                ['terms' => ['backend', 'module', 'modules'], 'matchCount' => 2],
            ],
            LabelSearch::largestReachingSubsets($entries, LabelSearch::terms('backend module registration modules'))
        );
    }

    #[Test]
    public function whereNoTwoWordsMeetInOneEntryThereIsNoSubsetToOffer(): void
    {
        // A single word is what the per-term counts already say, so this adds
        // nothing and says so rather than repeating them.
        $entries = [
            ['key' => 'Feature-1-Alpha', 'source' => 'Alpha'],
            ['key' => 'Feature-2-Beta', 'source' => 'Beta'],
        ];

        self::assertSame([], LabelSearch::largestReachingSubsets($entries, LabelSearch::terms('alpha beta')));
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
    public function aConsoleThatExitsWellAndSaysNothingUsableEstablishesNothing(): void
    {
        // The payload shares stdout with the title the same command prints
        // ahead of it, and the decoder starts at the first brace or bracket.
        // What else can land on that stream is not established — the two
        // obvious candidates were checked and neither reaches it. That is the
        // point rather than a gap in it: exit 0 says nothing about the stream,
        // so the tool may not read it as an installation that answered. Here
        // the payload is intact and the decoder misses it, and read as "none"
        // that is the one wrong answer nothing distinguishes from a right one.
        $this->consoleThatPrints(
            "[note] something reached this stream ahead of the payload\n"
            . "\nLabels in active extensions\n===========================\n\n"
            . (string) json_encode(['items' => [[
                'resource' => 'EXT:core/Resources/Private/Language/locallang.xlf',
                'labels' => [['domain' => 'core.messages', 'reference' => 'labels.save_document', 'label' => 'Save document']],
            ]]], JSON_THROW_ON_ERROR)
        );
        $this->labelFile('Resources/Private/Language/locallang.xlf', ['labels.save_document' => 'Save document']);

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame('packages', $result->data['answeredBy'], 'the console settled nothing, so the files answered');
        self::assertSame(1, $result->data['matchCount']);
        self::assertStringContainsString('the console settled nothing', $result->text);
    }

    #[Test]
    public function aConsoleThatExitsWellAndSaysNothingAtAllIsUnansweredRatherThanEmpty(): void
    {
        // Same failure with nothing behind it to fall back on: what must not
        // happen is a matchCount of 0 under answeredBy "installation", because
        // that is the shape of an installation that really has no such label.
        $this->consoleThatPrints('');

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertArrayNotHasKey('answeredBy', $result->data);
        self::assertStringContainsString('not answerable here', $result->text);
        self::assertStringContainsString(
            'exited successfully with neither a JSON payload nor the warning',
            $result->data['unsupported']['reason']
        );
    }

    #[Test]
    public function aConsoleThatCannotRunIsStillUnanswered(): void
    {
        $this->consoleThatFails('the database is not reachable');

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertArrayNotHasKey('answeredBy', $result->data);
        self::assertStringContainsString('not answerable here', $result->text);
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

    /**
     * Both reports of German written into a source XLF came from a session
     * that called this tool and named labels nowhere else — `R-ANS-015`. So
     * the rule rides on the answer rather than on a query the caller would
     * have had to phrase around labels first.
     */
    #[Test]
    #[DataProvider('whatTheConsoleAnswers')]
    public function aCallerAboutToWriteAUnitIsToldItsSourceLanguage(string $output): void
    {
        $this->consoleThatPrints($output);

        $result = Registry::call('typo3_label_lookup', ['query' => 'testimonial author']);

        self::assertStringContainsString('Write a new trans-unit in English in the unprefixed source file', $result->text);
        self::assertStringContainsString('de.locallang.xlf for locallang.xlf', $result->text);
        self::assertStringContainsString('is a defect to correct in place', $result->text);
        self::assertStringContainsString('an en.-prefixed file is not that correction', $result->text);
    }

    /**
     * The two branches a caller about to author a unit arrives on: nothing
     * matched, and a neighbouring label that may or may not be reusable.
     *
     * @return array<string, array{0: string}>
     */
    public static function whatTheConsoleAnswers(): array
    {
        return [
            'nothing matched' => ["Labels in active extensions\n===\n\n [WARNING] No language resource files found.\n"],
            'a neighbour in the same resource' => [(string) json_encode(['items' => [[
                'resource' => 'EXT:sitepackage/Resources/Private/Language/backend_fields.xlf',
                'labels' => [
                    ['domain' => 'sitepackage.backend_fields', 'reference' => 'testimonial.author', 'label' => 'Autor des Testimonials'],
                ],
            ]]], JSON_THROW_ON_ERROR)],
        ];
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

        self::assertArrayNotHasKey('answeredBy', $result->data);
        self::assertStringContainsString('no TYPO3 schema yet', $result->text);
        self::assertStringContainsString('no TYPO3 schema yet', $result->data['unsupported']['diagnosis']);
    }

    /** @param array<string, string> $units */
    private function labelFile(string $path, array $units): void
    {
        $file = $this->installationRoot . '/typo3/sysext/core/' . $path;
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
        $root = $this->removeAfterwards(sys_get_temp_dir() . '/typo3-cms-mcp-labels-' . bin2hex(random_bytes(6)));
        $this->installationRoot = $root;
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
