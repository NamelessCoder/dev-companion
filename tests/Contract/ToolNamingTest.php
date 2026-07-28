<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Contract;

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
