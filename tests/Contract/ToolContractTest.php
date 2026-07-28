<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Contract;

use Mcp\Capability\Discovery\SchemaValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Tools;

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
        foreach (Tools::definitions() as $definition) {
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
            self::assertFalse($definition['annotations']['openWorldHint'], $name . ' reaches nothing outside this package');
        }
    }

    #[Test]
    public function onlyTheFeedbackToolWrites(): void
    {
        foreach (Tools::definitions() as $definition) {
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
        $result = Tools::call($name, $arguments);

        self::assertNotSame('', trim($result->text), $name . ' answered with empty text');
        self::assertNotSame([], $result->data, $name . ' answered without data');

        $schema = $this->outputSchema($name);
        $data = json_decode((string) json_encode($result->data, JSON_THROW_ON_ERROR), true);

        $errors = (new SchemaValidator())->validateAgainstJsonSchema($data, $schema);
        self::assertSame([], $errors, $name . ' broke its output schema: ' . json_encode($errors));
    }

    #[Test]
    public function anUnknownToolIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Tools::call('typo3_does_not_exist', []);
    }

    #[Test]
    public function theCommitMessageToolNeedsEitherAMessageOrASummary(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Tools::call('typo3_commit_message_guide', ['issue' => '1']);
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
            'components: list' => ['typo3_component_lookup', []],
            'components: hit' => ['typo3_component_lookup', ['query' => 'badge']],
            'components: miss' => ['typo3_component_lookup', ['query' => 'quantumflux']],
            'icons: list' => ['typo3_icon_lookup', []],
            'icons: hit' => ['typo3_icon_lookup', ['query' => 'warning']],
            'icons: miss' => ['typo3_icon_lookup', ['query' => 'quantumflux']],
            'labels: no query' => ['typo3_label_lookup', []],
            'labels: hit' => ['typo3_label_lookup', ['query' => 'save document']],
            'labels: miss' => ['typo3_label_lookup', ['query' => 'quantumflux']],
            'labels: domains' => ['typo3_label_lookup', ['mode' => 'domains']],
            'labels: domains miss' => ['typo3_label_lookup', ['mode' => 'domains', 'query' => 'quantumflux']],
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

    /** @return array<string, mixed> */
    private function outputSchema(string $name): array
    {
        foreach (Tools::definitions() as $definition) {
            if ($definition['name'] === $name) {
                self::assertNotNull($definition['outputSchema']);

                return $definition['outputSchema'];
            }
        }

        self::fail($name . ' is not registered');
    }
}
