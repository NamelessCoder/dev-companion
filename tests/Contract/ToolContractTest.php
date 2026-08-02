<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Contract;

use Mcp\Capability\Discovery\SchemaValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Tool\Registry;

/**
 * What every tool promises its callers: a declared input and output schema,
 * annotations, a text answer, and structured data that validates against the
 * schema — on every path, a miss included.
 */
final class ToolContractTest extends TestCase
{
    #[Test]
    public function everyToolDeclaresSchemasAndAnnotations(): void
    {
        foreach (Registry::definitions() as $definition) {
            $name = $definition['name'];

            self::assertNotSame('', $definition['description'], $name . ' has no description');
            self::assertSame('object', $definition['inputSchema']['type'], $name . ' has no object input schema');
            self::assertNotNull($definition['outputSchema'], $name . ' has no output schema');
            self::assertSame('object', $definition['outputSchema']['type']);

            self::assertSame(
                ['readOnlyHint', 'destructiveHint', 'idempotentHint', 'openWorldHint'],
                array_keys($definition['annotations']),
                $name . ' is missing annotations'
            );
            self::assertSame(
                $name === 'typo3_documentation_lookup',
                $definition['annotations']['openWorldHint'],
                $name . ' has the wrong open-world annotation',
            );
        }
    }

    #[Test]
    public function onlyTheFeedbackToolWrites(): void
    {
        foreach (Registry::definitions() as $definition) {
            self::assertSame(
                $definition['name'] !== 'typo3_feedback_record',
                $definition['annotations']['readOnlyHint'],
                $definition['name'] . ' is annotated with the wrong readOnlyHint'
            );
            self::assertFalse($definition['annotations']['destructiveHint'], $definition['name'] . ' must not destroy anything');
        }
    }

    /** @param array<string, mixed> $arguments */
    #[DataProvider('toolCalls')]
    #[Test]
    public function aToolCallAnswersWithTextAndMatchingData(string $name, array $arguments): void
    {
        $result = Registry::call($name, $arguments);

        self::assertNotSame('', trim($result->text), $name . ' answered with empty text');
        self::assertNotSame([], $result->data, $name . ' answered without data');

        $schema = $this->outputSchema($name);
        $data = json_decode((string) json_encode($result->data, JSON_THROW_ON_ERROR), true);

        $errors = (new SchemaValidator())->validateAgainstJsonSchema($data, $schema);
        self::assertSame([], $errors, $name . ' broke its output schema: ' . json_encode($errors));
    }

    /**
     * The arguments each installation-backed tool is driven with below. A tool
     * that declares answeredBy and is not named here fails the test rather than
     * being skipped: the set is derived from the registry, so a new one joins
     * it by existing.
     *
     * @var array<string, array<string, mixed>>
     */
    private const UNANSWERABLE_CALLS = [
        'typo3_icon_lookup' => ['query' => 'publish'],
        'typo3_label_lookup' => ['query' => 'Publish page'],
        'typo3_configuration_lookup' => ['path' => 'SYS/fluid'],
        'typo3_backend_module_lookup' => ['query' => 'page'],
        'typo3_changelog_lookup' => ['query' => 'deprecation'],
        'typo3_fluid_namespace_list' => [],
        'typo3_project_scope' => [],
        'typo3_extension_scope' => ['key' => 'news'],
    ];

    /**
     * A count of 0 and a false say the installation does not have it. Neither
     * is said about an installation nothing asked, so every claim an
     * installation-backed tool makes is declared nullable — R-ANS-001.
     */
    #[Test]
    public function everyClaimAnInstallationBackedToolMakesCanBeWithheld(): void
    {
        foreach (self::installationBackedSchemas() as $name => $schema) {
            foreach ($schema['properties'] as $field => $property) {
                $type = (array) ($property['type'] ?? []);
                if (array_intersect($type, ['integer', 'boolean']) === []) {
                    continue;
                }

                self::assertContains(
                    'null',
                    $type,
                    $name . ' declares ' . $field . ' as a bare ' . implode('|', $type)
                        . '. It is a statement about the installation, so it has to be withholdable.'
                );
            }
        }
    }

    /**
     * Driven where there is nothing to ask, the answer says so and states
     * nothing else. The reason travels as data, and it names where discovery
     * looked — the two halves R-ANS-002 and META-02 ask for.
     */
    #[Test]
    public function nothingIsClaimedAboutAnInstallationThatWasNeverAsked(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();

        foreach (self::installationBackedSchemas() as $name => $schema) {
            self::assertArrayHasKey($name, self::UNANSWERABLE_CALLS, $name . ' answers from the installation and is not driven here');

            $data = Registry::call($name, self::UNANSWERABLE_CALLS[$name])->data;

            self::assertSame('nothing', $data['answeredBy'], $name . ' answered without an installation');
            self::assertNotSame('', $data['unavailable']['reason'] ?? '', $name . ' gave no reason');
            self::assertArrayHasKey('searched', $data['unavailable'], $name . ' does not say where it looked');

            foreach ($schema['properties'] as $field => $property) {
                if (array_intersect((array) ($property['type'] ?? []), ['integer', 'boolean']) === []) {
                    continue;
                }

                self::assertArrayHasKey($field, $data, $name . ' dropped ' . $field);
                self::assertNull($data[$field], $name . ' claims ' . $field . ' about an installation nothing asked');
            }
        }
    }

    /**
     * answeredBy: "nothing" is written in one place, so it cannot be reached by
     * a path that has no reason to hand over. typo3_extension_scope reported
     * every miss that way, including one against an installation that had just
     * listed its packages.
     */
    #[Test]
    public function onlyTheUnansweredResultReportsThatNothingAnswered(): void
    {
        $sources = [];
        foreach (Finder::create()->files()->in(dirname(__DIR__, 2) . '/src')->name('*.php') as $file) {
            if (str_contains((string) file_get_contents($file->getPathname()), "'answeredBy' => 'nothing'")) {
                $sources[] = $file->getRelativePathname();
            }
        }

        self::assertSame(['Result/Unanswered.php'], $sources);
    }

    #[Test]
    public function anUnknownToolIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Registry::call('typo3_does_not_exist', []);
    }

    #[Test]
    public function theCommitMessageToolNeedsEitherAMessageOrASummary(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Registry::call('typo3_commit_message_guide', ['issue' => '1']);
    }

    /**
     * Every tool, on a hit and on a miss: the shape has to hold for both, since
     * a miss is where a client is most likely to look at the data.
     *
     * @return array<string, array{0: string, 1: array<string, mixed>}>
     */
    public static function toolCalls(): array
    {
        return [
            'scope' => ['typo3_server_scope', []],
            'rules: hit' => ['typo3_rule_lookup', ['query' => 'deprecation']],
            'rules: miss' => ['typo3_rule_lookup', ['query' => 'quantum entanglement pineapple']],
            'scripts: hit' => ['typo3_script_lookup', ['task' => 'functional tests']],
            'scripts: miss' => ['typo3_script_lookup', ['task' => 'quantum entanglement pineapple']],
            'brief: with area' => ['typo3_task_guide', [
                'task' => 'Deprecate a public method',
                'area' => 'typo3/sysext/core/Classes/Utility/GeneralUtility.php',
                'changeType' => 'cleanup',
            ]],
            'brief: task only' => ['typo3_task_guide', ['task' => 'Add a badge to the list module']],
            'runTests: all' => ['typo3_test_run_guide', []],
            'runTests: hit' => ['typo3_test_run_guide', ['query' => 'phpstan']],
            'runTests: miss' => ['typo3_test_run_guide', ['query' => 'quantumflux']],
            'runTests: narrowed by paths' => ['typo3_test_run_guide', [
                'query' => 'what do I have to run',
                'paths' => ['Build/Sources/Sass/component/_card.scss'],
            ]],
            'architecture: path' => ['typo3_architecture_lookup', ['paths' => ['typo3/sysext/core/Classes/DataHandling/DataHandler.php']]],
            'architecture: topic' => ['typo3_architecture_lookup', ['task' => 'sass build']],
            'architecture: miss' => ['typo3_architecture_lookup', ['task' => 'quantumflux']],
            'documentation: unsupported version' => ['typo3_documentation_lookup', [
                'queries' => ['page title event'],
                'targetVersion' => '999',
            ]],
            'components: list' => ['typo3_component_lookup', []],
            'components: hit' => ['typo3_component_lookup', ['query' => 'badge']],
            'components: miss' => ['typo3_component_lookup', ['query' => 'quantumflux']],
            'system extensions: hit' => ['typo3_system_extension_lookup', ['query' => 'impexp']],
            'system extensions: miss' => ['typo3_system_extension_lookup', ['query' => 'typo3/cms-content-blocks']],
            'system extensions: everything' => ['typo3_system_extension_lookup', []],
            'domain: EXT reference' => ['typo3_translation_domain_lookup', [
                'path' => 'EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf',
            ]],
            'domain: checkout path' => ['typo3_translation_domain_lookup', [
                'path' => 'typo3/sysext/core/Resources/Private/Language/locallang.xlf',
            ]],
            'domain: miss' => ['typo3_translation_domain_lookup', ['path' => 'somewhere/else.xlf']],
            // No installation is discovered in a test run, so this exercises
            // the path where the answer is unanswered rather than empty.
            'labels: no installation' => ['typo3_label_lookup', ['query' => 'save']],
            'icons: no installation' => ['typo3_icon_lookup', ['query' => 'actions-open']],
            'icons: no installation, no query' => ['typo3_icon_lookup', []],
            'modules: no installation' => ['typo3_backend_module_lookup', []],
            'namespaces: no installation' => ['typo3_fluid_namespace_list', []],
            'configuration: no installation' => ['typo3_configuration_lookup', ['path' => 'SYS/fluid']],
            'catalog scope' => ['typo3_catalog_scope', []],
            'commit: from parts' => ['typo3_commit_message_guide', [
                'changeType' => 'BUGFIX',
                'summary' => 'Show hidden records in the import preview',
                'issue' => '106123',
            ]],
            'commit: from a message' => ['typo3_commit_message_guide', [
                'message' => "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main",
            ]],
        ];
    }

    /**
     * The tools whose answer belongs to an installation, which is the ones that
     * declare answeredBy, keyed by name with the schema that says what they
     * promise.
     *
     * @return array<string, array{properties: array<string, mixed>}>
     */
    private static function installationBackedSchemas(): array
    {
        $schemas = [];
        foreach (Registry::definitions() as $definition) {
            $properties = $definition['outputSchema']['properties'] ?? [];
            if (isset($properties['answeredBy'])) {
                $schemas[$definition['name']] = ['properties' => $properties];
            }
        }

        self::assertNotSame([], $schemas, 'no tool answers from the installation');

        return $schemas;
    }

    /** @return array<string, mixed> */
    private function outputSchema(string $name): array
    {
        foreach (Registry::definitions() as $definition) {
            if ($definition['name'] === $name) {
                self::assertNotNull($definition['outputSchema']);

                return $definition['outputSchema'];
            }
        }

        self::fail($name . ' is not registered');
    }
}
