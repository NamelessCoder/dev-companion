<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Contract;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Tools;

/**
 * The naming schema every tool follows: typo3_<subject>_<verb>, with the verb
 * taken from a closed list. This test is the list — see AGENTS.md for the rule
 * a new tool is named by.
 */
final class ToolNamingTest extends TestCase
{
    /**
     * What the verb promises about the answer.
     *
     * @var array<string, string>
     */
    private const VERBS = [
        'lookup' => 'a query goes in, matching entries come out, and finding nothing is a legitimate answer',
        'guide' => 'an answer composed for the task at hand, which always exists',
        'list' => 'an enumeration of what is there, no query needed',
        'scope' => 'what a source covers and where its boundary runs',
        'record' => 'the tool writes something',
    ];

    /** Segments that separate nothing, because they hold for every tool here. */
    private const EMPTY_SEGMENTS = ['core', 'typo3'];

    #[Test]
    public function everyToolIsNamedSubjectThenVerb(): void
    {
        foreach (Tools::definitions() as $definition) {
            $name = $definition['name'];

            self::assertMatchesRegularExpression(
                '/^typo3_[a-z]+(_[a-z]+)+$/',
                $name,
                $name . ' is not typo3_<subject>_<verb> in lowercase words'
            );

            $segments = explode('_', $name);
            $verb = array_pop($segments);
            array_shift($segments);

            self::assertArrayHasKey(
                $verb,
                self::VERBS,
                $name . ' ends in "' . $verb . '", which is not one of: ' . implode(', ', array_keys(self::VERBS))
            );
            self::assertNotSame([], $segments, $name . ' has no subject between the prefix and the verb');
            foreach ($segments as $segment) {
                self::assertNotContains(
                    $segment,
                    self::EMPTY_SEGMENTS,
                    $name . ' carries "' . $segment . '", which separates it from nothing'
                );
            }
        }
    }

    /**
     * A rename that misses one prose string leaves an answer that tells an
     * agent to call a tool this server does not have. The tool call fails, and
     * it fails in exactly the part of the answer meant to steer the next step.
     */
    #[Test]
    public function everyToolNameWrittenInTheKnowledgeBaseIsRegistered(): void
    {
        $known = array_column(Tools::definitions(), 'name');

        $unknown = [];
        foreach ($this->knowledgeFiles() as $file) {
            preg_match_all('/typo3_[a-z_]+/', (string) file_get_contents($file), $matches);
            foreach (array_unique($matches[0]) as $name) {
                if (!in_array($name, $known, true)) {
                    $unknown[] = basename($file) . ': ' . $name;
                }
            }
        }

        self::assertSame([], $unknown, 'named in the knowledge base but not registered');
    }

    /** @param array<string, mixed> $arguments */
    #[DataProvider('toolCalls')]
    #[Test]
    public function everyToolNameAnAnswerNamesIsRegistered(string $tool, array $arguments): void
    {
        $known = array_column(Tools::definitions(), 'name');
        $result = Tools::call($tool, $arguments);
        $answer = $result->text . ' ' . json_encode($result->data, JSON_THROW_ON_ERROR);

        preg_match_all('/typo3_[a-z_]+/', $answer, $matches);
        self::assertSame(
            [],
            array_values(array_diff(array_unique($matches[0]), $known)),
            $tool . ' points at a tool that is not registered'
        );
    }

    /** @return array<string, array{0: string, 1: array<string, mixed>}> */
    public static function toolCalls(): array
    {
        return ToolContractTest::toolCalls();
    }

    /** @return array<int, string> */
    private function knowledgeFiles(): array
    {
        $files = [];
        $directory = new \RecursiveDirectoryIterator(dirname(__DIR__, 2) . '/knowledge');
        foreach (new \RecursiveIteratorIterator($directory) as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }
        sort($files);

        return $files;
    }

    /**
     * The verb names the answer shape, so the same shape has to answer to the
     * same verb — otherwise the name stops predicting what comes back.
     */
    #[Test]
    public function toolsSharingAnOutputSchemaShareTheirVerb(): void
    {
        $verbsPerSchema = [];
        foreach (Tools::definitions() as $definition) {
            $schema = json_encode($definition['outputSchema'], JSON_THROW_ON_ERROR);
            $segments = explode('_', $definition['name']);
            $verbsPerSchema[$schema][(string) array_pop($segments)][] = $definition['name'];
        }

        foreach ($verbsPerSchema as $verbs) {
            self::assertCount(
                1,
                $verbs,
                'One output schema, several verbs: ' . json_encode($verbs, JSON_THROW_ON_ERROR)
            );
        }
    }
}
