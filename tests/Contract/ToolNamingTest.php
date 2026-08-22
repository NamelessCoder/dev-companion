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
     *
     * The records join them where a name is a claim about today: what
     * `documentation/` publishes, what a requirement demands, what the queue
     * says the next step is, and what a scenario says has to come out of a
     * prompt. All four were clean when this was widened, so it holds a boundary
     * rather than reporting a breach.
     *
     * Two corpora stay out, and neither is an oversight. `feedback/` is a
     * session's own report and `scenarios/runs/` is a trace of the calls one
     * made, both on a date — a rename there would edit the evidence, so each
     * run carries a line naming the current spelling instead.
     *
     * `decisions/` is read by the test below rather than by this one, because
     * it is the one corpus where a superseded name is sometimes the subject of
     * the sentence — `D-DOC-040`, `D-SCO-011`, `D-KNW-035`.
     */
    #[Test]
    public function everyToolNameWrittenInTheKnowledgeBaseIsRegistered(): void
    {
        $known = array_column(Registry::definitions(), 'name');

        $unknown = [];
        foreach ([...$this->knowledgeFiles(), ...$this->skillFiles(), ...$this->recordFiles()] as $file) {
            preg_match_all('/typo3_[a-z_]+/', (string) file_get_contents($file), $matches);
            foreach (array_unique($matches[0]) as $name) {
                if (!in_array($name, $known, true)) {
                    $unknown[] = basename($file) . ': ' . $name;
                }
            }
        }

        self::assertSame([], $unknown, 'named where the name is a claim about today, but not registered');
    }

    /**
     * A decision names a tool in backticks, or is not naming a tool.
     *
     * The corpus went stale unwatched: 157 mentions of three tools renamed
     * weeks earlier, across 56 entries, while this file read the knowledge base
     * and the skills alone. Guarding it took no list of what those names used
     * to be, only the distinction a reader wants anyway — a name in backticks
     * is one to call, and a name being talked about is written plainly.
     * `Rejected: typo3_debrief_guide` keeps the name a later session searches
     * for when the demand comes back, and offers it to nobody.
     *
     * Matched is the whole backticked token in the tool shape, a subject and
     * one of the verbs above. So the TER's own `typo3_versions` field is not a
     * tool name, and neither is the `typo3_logo.png` inside a Fluid example —
     * `D-DOC-040`.
     */
    #[Test]
    public function everyToolADecisionOffersInBackticksIsRegistered(): void
    {
        $known = array_column(Registry::definitions(), 'name');
        $verbs = implode('|', array_keys(self::VERBS));

        $stale = [];
        foreach (Finder::create()->files()->in(dirname(__DIR__, 2) . '/decisions')->name('*.md')->sortByName() as $file) {
            preg_match_all('/`(typo3_[a-z]+(?:_[a-z]+)*_(?:' . $verbs . '))`/', (string) file_get_contents($file->getPathname()), $matches);
            foreach (array_unique($matches[1]) as $name) {
                if (!in_array($name, $known, true)) {
                    $stale[] = $file->getBasename() . ': ' . $name;
                }
            }
        }

        self::assertSame([], $stale, 'offered in backticks by a decision, and no tool has the name');
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
     * The records that state what is true now rather than what was called once.
     *
     * @return array<int, string>
     */
    private function recordFiles(): array
    {
        $root = dirname(__DIR__, 2);

        $files = [];
        foreach (['documentation', 'requirements', 'todo', 'scenarios'] as $directory) {
            $found = Finder::create()->files()->in($root . '/' . $directory)->sortByName();
            if ($directory === 'scenarios') {
                $found->notPath('runs');
            }
            foreach ($found as $file) {
                $files[] = $file->getPathname();
            }
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
