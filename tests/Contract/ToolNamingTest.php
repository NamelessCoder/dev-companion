<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Contract;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Tool\Registry;

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
        // `scope` and `describe` are the pair a reader gets wrong: a scope
        // answers for a source and states what it covers, a describe answers
        // for one thing the caller named and states what it is — D-SCO-010.
        'lookup' => 'a query goes in, matching entries come out, and finding nothing is a legitimate answer',
        'guide' => 'an answer composed for the task at hand, which always exists',
        'list' => 'an enumeration of what is there, no query needed',
        'scope' => 'what a source covers and where its boundary runs',
        'describe' => 'what one thing the caller names is and what it registers',
        // Where, not just that: writing into this server's own checkout is not
        // writing into the installation it read, and one word for both is how
        // the feedback channel got read as a hole in the read-only posture —
        // D-FBK-042.
        'record' => 'the tool writes into this server\'s own checkout',
    ];

    /** Segments that separate nothing, because they hold for every tool here. */
    private const EMPTY_SEGMENTS = ['core', 'typo3'];

    #[Test]
    public function everyToolIsNamedSubjectThenVerb(): void
    {
        foreach (Registry::definitions() as $definition) {
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
     *
     * The skills are read the same way and for the same reason. They are almost
     * nothing but tool names in an order, they are installed into somebody
     * else's project, and there a stale name is not corrected by the next
     * release of this server.
     */
    #[Test]
    public function everyToolNameWrittenInTheKnowledgeBaseIsRegistered(): void
    {
        $known = array_column(Registry::definitions(), 'name');

        $unknown = [];
        foreach ([...$this->knowledgeFiles(), ...$this->skillFiles()] as $file) {
            preg_match_all('/typo3_[a-z_]+/', (string) file_get_contents($file), $matches);
            foreach (array_unique($matches[0]) as $name) {
                if (!in_array($name, $known, true)) {
                    $unknown[] = basename($file) . ': ' . $name;
                }
            }
        }

        self::assertSame([], $unknown, 'named in the knowledge base or in a skill, but not registered');
    }

    /**
     * The other direction, and the one nothing watched: a tool that exists and
     * is described nowhere outward.
     *
     * `readme.md` is one of the four things that describe this server to
     * somebody else, and its tool list reads as the list — it ends without an
     * "and more" and the orientation bullet above it promises that a shorter
     * list than this one has a reason a client can read. Five tools had been
     * added since anybody checked.
     */
    #[Test]
    public function everyToolIsDescribedInTheReadme(): void
    {
        $readme = (string) file_get_contents(dirname(__DIR__, 2) . '/readme.md');

        $undescribed = [];
        foreach (array_column(Registry::definitions(), 'name') as $name) {
            if (!str_contains($readme, '`' . $name . '`')) {
                $undescribed[] = $name;
            }
        }

        self::assertSame([], $undescribed, 'registered and named nowhere in readme.md');
    }

    /** @param array<string, mixed> $arguments */
    #[DataProvider('toolCalls')]
    #[Test]
    public function everyToolNameAnAnswerNamesIsRegistered(string $tool, array $arguments): void
    {
        $known = array_column(Registry::definitions(), 'name');
        $result = Registry::call($tool, $arguments);
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
        foreach (Finder::create()->files()->in(dirname(__DIR__, 2) . '/knowledge')->sortByName() as $file) {
            $files[] = $file->getPathname();
        }

        return $files;
    }

    /** @return array<int, string> */
    private function skillFiles(): array
    {
        // A skill is its SKILL.md and what that loads on demand, and nothing
        // else the directory happens to carry.
        $skills = Finder::create()->files()->in(dirname(__DIR__, 2) . '/skills')->depth(1)->name('SKILL.md');
        $references = Finder::create()->files()->in(dirname(__DIR__, 2) . '/skills')->depth(2)->path('references/')->name('*.md');

        $files = [];
        foreach (Finder::create()->append($skills)->append($references)->sortByName() as $file) {
            $files[] = $file->getPathname();
        }

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
        foreach (Registry::definitions() as $definition) {
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
