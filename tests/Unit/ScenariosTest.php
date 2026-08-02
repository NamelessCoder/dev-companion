<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tests\Support\Directory;
use Typo3CmsMcp\Upkeep\Scenarios;

final class ScenariosTest extends TestCase
{
    private string $runs = '';

    protected function tearDown(): void
    {
        if ($this->runs === '') {
            return;
        }
        Directory::remove($this->runs);
    }

    #[Test]
    public function everyForwardReviewIsReadableAsData(): void
    {
        $reviews = Scenarios::load();

        self::assertNotSame([], $reviews);
        self::assertSame(
            $this->headings('/scenarios/forward'),
            array_keys($reviews),
            'a forward review heading was not parsed, or was parsed twice',
        );
        $this->assertReadable($reviews, Scenarios::vocabulary('Mark'));
    }

    #[Test]
    public function everyContractCaseIsReadableAsData(): void
    {
        $cases = Scenarios::contracts();

        self::assertNotSame([], $cases);
        self::assertSame(
            $this->headings('/scenarios/contracts'),
            array_keys($cases),
            'a contract case heading was not parsed, or was parsed twice',
        );
        $this->assertReadable($cases, Scenarios::vocabulary('Contract'));
    }

    /**
     * One case is one file. A file holding a second prompt is a file where
     * nobody can tell which criteria a judgment answered — and the runner would
     * print two prompts under one id.
     */
    #[Test]
    public function everyCaseHasAFileOfItsOwn(): void
    {
        $files = [];
        foreach ([...Scenarios::load(), ...Scenarios::contracts()] as $id => $scenario) {
            $files[$scenario['file']][] = $id;
        }

        self::assertSame(
            [],
            array_filter($files, static fn(array $ids): bool => count($ids) > 1),
        );
    }

    /**
     * A contract case is never run, so its state is a claim no session ever
     * answers. What holds it therefore has to be named beside it — and a test
     * named there that does not exist is worth less than saying nothing.
     */
    #[Test]
    public function everyContractCaseNamesWhatHoldsIt(): void
    {
        $tests = $this->testMethods();

        foreach (Scenarios::contracts() as $id => $case) {
            self::assertNotSame('', $case['heldBy'], $id . ' does not say what holds it');

            preg_match_all('/`(\w+Test::\w+)`/', $case['heldBy'], $matches);
            self::assertTrue(
                $matches[1] !== [] || str_contains($case['heldBy'], 'not guarded'),
                $id . ' names neither a test nor that it is not guarded',
            );
            foreach ($matches[1] as $test) {
                self::assertContains($test, $tests, $id . ' names ' . $test . ', which no test declares');
            }
        }
    }

    /**
     * Every test method in this suite as `Class::method`, read from the files
     * rather than from reflection: a case may name a test in any of the three
     * directories, and loading them all to ask would be the heavier half.
     *
     * @return array<int, string>
     */
    private function testMethods(): array
    {
        $methods = [];
        foreach (['Unit', 'Contract', 'Smoke'] as $suite) {
            foreach (Finder::create()->files()->in(Paths::root() . '/tests/' . $suite)->depth(0)->name('*Test.php')->sortByName() as $file) {
                preg_match_all('/public function (\w+)\(/', (string) file_get_contents($file->getPathname()), $matches);
                foreach ($matches[1] as $method) {
                    $methods[] = $file->getBasename('.php') . '::' . $method;
                }
            }
        }

        return $methods;
    }

    /**
     * @param array<string, array{title: string, prompt: string, environment: string, status: string, outcomes: array<int, string>, failures: array<int, string>}> $scenarios
     * @param array<int, string> $states
     */
    private function assertReadable(array $scenarios, array $states): void
    {
        $environments = Scenarios::vocabulary('Id');

        foreach ($scenarios as $id => $scenario) {
            self::assertNotSame('', $scenario['title'], $id . ' has no title');
            self::assertNotSame('', $scenario['prompt'], $id . ' has no prompt');
            self::assertNotSame([], $scenario['outcomes'], $id . ' says nothing about what has to come out of it');
            self::assertNotSame([], $scenario['failures'], $id . ' says nothing about how it fails');
            self::assertContains($scenario['environment'], $environments, $id . ' runs in no environment the readme names');
            self::assertContains($scenario['status'], $states, $id . ' carries no state its readme names');
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
    public function aTargetedContractCaseIsNotSomethingARunCanAnswer(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Scenarios::skeleton('SKILL-07', 'testing', 'phpunit', '2026-07-30');
    }

    #[Test]
    public function aRunAddsUpToTheVerdictItsJudgmentsMake(): void
    {
        $missed = $this->record('REVIEW-01', static function (array $run): array {
            $run['outcomes'][0]['met'] = false;

            return $run;
        });

        self::assertSame('covered', $this->record('REVIEW-01', static fn(array $run): array => $run)['verdict']);
        self::assertSame('partial', $missed['verdict']);
    }

    #[Test]
    public function aHalfJudgedRunIsNotAResult(): void
    {
        $recorded = $this->record('REVIEW-01', static function (array $run): array {
            $run['outcomes'] = array_map(static fn(): array => ['met' => null, 'evidence' => ''], $run['outcomes']);

            return $run;
        });

        self::assertSame('', $recorded['verdict']);
        self::assertContains('scenarios/runs/REVIEW-01.json leaves outcomes 1 unjudged', $recorded['problems']);
        self::assertContains('scenarios/runs/REVIEW-01.json gives no evidence for outcomes 1', $recorded['problems']);
    }

    #[Test]
    public function aRunWhoseSessionHasNotHappenedYetIsOpenRatherThanBroken(): void
    {
        // What `bin/cli scenarios:record` writes. A checker that fails on it stops
        // the repository for as long as a run is open — which is the one time
        // it has to stay usable.
        $skeleton = Scenarios::skeleton('REVIEW-01', 'testing', 'phpunit', '2026-07-30');
        $recorded = $this->record('REVIEW-01', static fn(): array => $skeleton);

        self::assertTrue(Scenarios::isOpen($recorded['run']));
        self::assertSame('', $recorded['verdict']);
        self::assertSame([], $recorded['problems']);
    }

    #[Test]
    public function aRecordedCallSaysWhatItAskedAndNotOnlyWhichToolItAsked(): void
    {
        // A bare name cannot answer what the runs are judged on: whether the
        // conventions lookup was asked once per surface or once broadly,
        // which version it was given, whether a returned id was followed. All
        // three are in the arguments and nowhere else in the record.
        $recorded = $this->record('REVIEW-01', static function (array $run): array {
            $run['toolTrace'] = [
                ['tool' => 'typo3_project_scope', 'arguments' => []],
                ['tool' => 'typo3_architecture_lookup'],
                ['arguments' => ['query' => 'icons']],
            ];

            return $run;
        });

        self::assertSame([
            'scenarios/runs/REVIEW-01.json tool call 2, typo3_architecture_lookup, does not say what it was called with',
            'scenarios/runs/REVIEW-01.json tool call 3 does not name the tool it called',
        ], $recorded['problems']);

        // Recorded as a bare name, the way every run was written before the
        // arguments were part of one.
        $named = $this->record('REVIEW-01', static function (array $run): array {
            $run['toolTrace'] = ['typo3_project_scope'];

            return $run;
        });

        self::assertSame(
            ['scenarios/runs/REVIEW-01.json tool call 1 does not name the tool it called'],
            $named['problems'],
        );
    }

    #[Test]
    public function aRunContradictsAReviewWhoseMarkIsNotWhatTheJudgmentsAddUpTo(): void
    {
        $review = Scenarios::load()['REVIEW-01'];
        // Judged into whichever result REVIEW-01 does not currently claim, so
        // this stays a contradiction whatever its mark says today.
        $met = !in_array($review['status'], ['covered', 'boundary'], true);
        $recorded = $this->record('REVIEW-01', static function (array $run) use ($met): array {
            foreach (['outcomes' => 'met', 'failures' => 'avoided'] as $section => $key) {
                $run[$section] = array_map(
                    static fn(array $entry): array => [$key => $met, 'evidence' => $entry['evidence']],
                    is_array($run[$section]) ? $run[$section] : [],
                );
            }

            return $run;
        });

        self::assertContains(
            sprintf(
                'scenarios/runs/REVIEW-01.json says REVIEW-01 is `%s`, and %s stands at `%s`',
                $met ? 'covered' : 'gap',
                $review['file'],
                $review['status'],
            ),
            $recorded['problems'],
        );
    }

    #[Test]
    public function aRunJudgedAgainstOlderCriteriaIsNotReadAsAnAnswerToTheCurrentOnes(): void
    {
        $recorded = $this->record('REVIEW-01', static function (array $run): array {
            $run['criteria'] = 'aaaaaaaaaaaa';

            return $run;
        });

        self::assertNotSame([], $recorded['problems']);
        self::assertStringContainsString('was judged against criteria aaaaaaaaaaaa', $recorded['problems'][0]);
    }

    #[Test]
    public function aRunOfNoForwardReviewIsNotARun(): void
    {
        $recorded = $this->record('REVIEW-01', static function (array $run): array {
            $run['scenario'] = 'REVIEW-99';

            return $run;
        });

        self::assertSame(
            ['scenarios/runs/REVIEW-99.json records a run of no forward review in scenarios/forward/'],
            $recorded['problems'],
        );
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
     * The ids as the markdown writes them, found without the parser under test.
     *
     * @return array<int, string>
     */
    private function headings(string $directory): array
    {
        $ids = [];
        $files = Finder::create()->files()->in(Paths::root() . $directory)->depth('< 2')
            ->name('*.md')->notName('readme.md')->sortByName();
        foreach ($files as $file) {
            preg_match_all('/^#{1,2} ([A-Z]+-\d+)\b/m', (string) file_get_contents($file->getPathname()), $matches);
            $ids = array_merge($ids, $matches[1]);
        }
        sort($ids);

        return $ids;
    }
}
