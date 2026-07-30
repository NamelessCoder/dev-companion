<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Scenarios;

final class ScenariosTest extends TestCase
{
    private string $runs = '';

    protected function tearDown(): void
    {
        if ($this->runs === '' || !is_dir($this->runs)) {
            return;
        }
        foreach (glob($this->runs . '/*.json') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->runs);
    }

    #[Test]
    public function everyScenarioIsReadableAsData(): void
    {
        $scenarios = Scenarios::load();
        $environments = Scenarios::vocabulary('Id');
        $statuses = Scenarios::vocabulary('Mark');

        self::assertNotSame([], $scenarios);
        self::assertSame($this->headings(), array_keys($scenarios), 'a scenario heading was not parsed, or was parsed twice');

        foreach ($scenarios as $id => $scenario) {
            self::assertNotSame('', $scenario['title'], $id . ' has no title');
            self::assertNotSame('', $scenario['prompt'], $id . ' has no prompt');
            self::assertNotSame([], $scenario['outcomes'], $id . ' says nothing about what has to come out of it');
            self::assertNotSame([], $scenario['failures'], $id . ' says nothing about how it fails');
            self::assertContains($scenario['environment'], $environments, $id . ' runs in no environment the readme names');
            self::assertContains($scenario['status'], $statuses, $id . ' carries no mark the readme names');
        }
    }

    #[Test]
    public function everyRecordedRunHoldsUpToItsScenario(): void
    {
        $problems = [];
        foreach (Scenarios::runs() as $recorded) {
            $problems = [...$problems, ...$recorded['problems']];
        }

        self::assertSame([], $problems);
    }

    #[Test]
    public function aRunThatMeetsEveryCriterionIsTheStatusItsScenarioClaims(): void
    {
        $recorded = $this->record('SITE-07', static fn(array $run): array => $run);

        self::assertSame('covered', $recorded['verdict']);
        self::assertSame([], $recorded['problems']);
    }

    #[Test]
    public function anUnjudgedRunIsNotAResult(): void
    {
        // What `bin/scenarios record` writes before the session happens. It is
        // in the same shape as a finished run and worth nothing, so the check
        // has to be the thing that knows the difference.
        $recorded = $this->record('SITE-07', static function (array $run): array {
            $run['outcomes'] = array_map(static fn(): array => ['met' => null, 'evidence' => ''], $run['outcomes']);

            return $run;
        });

        self::assertSame('', $recorded['verdict']);
        self::assertContains('scenarios/runs/SITE-07.json leaves outcomes 1 unjudged', $recorded['problems']);
        self::assertContains('scenarios/runs/SITE-07.json gives no evidence for outcomes 1', $recorded['problems']);
    }

    #[Test]
    public function aRunThatMissesACriterionContradictsAScenarioThatClaimsToBeCovered(): void
    {
        $recorded = $this->record('SITE-07', static function (array $run): array {
            $run['outcomes'][0]['met'] = false;

            return $run;
        });

        self::assertSame('partial', $recorded['verdict']);
        self::assertContains(
            'scenarios/runs/SITE-07.json says SITE-07 is `partial`, and scenarios/site-developer.md stands at `covered`',
            $recorded['problems'],
        );
    }

    #[Test]
    public function aRunJudgedAgainstOlderCriteriaIsNotReadAsAnAnswerToTheCurrentOnes(): void
    {
        $recorded = $this->record('SITE-07', static function (array $run): array {
            $run['criteria'] = 'aaaaaaaaaaaa';

            return $run;
        });

        self::assertNotSame([], $recorded['problems']);
        self::assertStringContainsString('was judged against criteria aaaaaaaaaaaa', $recorded['problems'][0]);
    }

    #[Test]
    public function aRunOfNoScenarioIsNotARun(): void
    {
        $recorded = $this->record('SITE-07', static function (array $run): array {
            $run['scenario'] = 'SITE-99';

            return $run;
        });

        self::assertSame(['scenarios/runs/SITE-99.json records a run of no scenario in scenarios/'], $recorded['problems']);
    }

    /**
     * One recorded run, written to a directory of this test's own: a fixture
     * below scenarios/runs/ would be read as a real result of a real session.
     *
     * @param callable(array<string, mixed>): array<string, mixed> $spoil
     * @return array{file: string, run: array<string, mixed>, verdict: string, problems: array<int, string>}
     */
    private function record(string $id, callable $spoil): array
    {
        $run = Scenarios::skeleton($id, 'testing', 'phpunit', '2026-07-30');
        foreach (['outcomes' => 'met', 'failures' => 'avoided'] as $section => $key) {
            $run[$section] = array_map(
                static fn(): array => [$key => true, 'evidence' => 'what the session did'],
                is_array($run[$section]) ? $run[$section] : [],
            );
        }

        $run = $spoil($run);

        $this->runs = sys_get_temp_dir() . '/typo3-cms-mcp-runs-' . getmypid();
        if (!is_dir($this->runs)) {
            mkdir($this->runs, 0775, true);
        }
        file_put_contents(
            $this->runs . '/' . (is_string($run['scenario'] ?? null) ? $run['scenario'] : $id) . '.json',
            json_encode($run, JSON_PRETTY_PRINT),
        );

        $recorded = Scenarios::runs($this->runs);

        return $recorded[array_key_first($recorded)] ?? self::fail('nothing was read back');
    }

    /**
     * The scenario ids as the markdown writes them, found without the parser
     * under test.
     *
     * @return array<int, string>
     */
    private function headings(): array
    {
        $ids = [];
        foreach (glob(Paths::root() . '/scenarios/*.md') ?: [] as $path) {
            if (basename($path) === 'readme.md') {
                continue;
            }
            preg_match_all('/^## ([A-Z]+-\d+)\b/m', (string) file_get_contents($path), $matches);
            $ids = array_merge($ids, $matches[1]);
        }
        sort($ids);

        return $ids;
    }
}
