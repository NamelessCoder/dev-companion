<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Cli;

use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Scenarios;

/**
 * Runs a scenario from scenarios/ in the only sense a script can: it hands over
 * what has to be pasted, takes back what the run established, and refuses the
 * result when it does not add up. The session itself happens in a client, in
 * the environment the scenario names, and no part of that is automated here.
 *
 * What this replaces is a judgment that was only ever in somebody's head. A
 * scenario carries a `Status today`, and that line is the whole record of the
 * last forward run — so it goes stale the moment the server changes, and
 * nothing about the file looks any different.
 */
final class Scenario implements Subject
{
    public static function about(): string
    {
        return 'forward runs, and the targeted contract cases';
    }

    public static function commands(): array
    {
        return [
            'show' => ['<id>', 'the environment, the prompt to paste verbatim, and the numbered criteria', self::show(...)],
            'contract' => ['<id>', 'the same for a targeted case, which is read rather than run forward', self::contract(...)],
            'record' => ['<id> <client>', 'write the empty run to scenarios/runs/, to be filled in after the session', self::record(...)],
            'check' => ['', 'hold every recorded run to the scenario it claims to answer', self::check(...)],
        ];
    }

    /**
     * What has to be pasted, and what the session is judged against.
     *
     * @param array<int, string> $arguments
     */
    private static function show(array $arguments): int
    {
        $id = strtoupper($arguments[0] ?? '');
        if ($id === '') {
            return Cli::usage(self::class, 'show');
        }

        $scenario = Scenarios::load()[$id] ?? null;
        if ($scenario === null) {
            fwrite(STDERR, isset(Scenarios::contracts()[$id])
                ? sprintf("%s is a targeted contract case: bin/cli scenarios contract %s\n", $id, $id)
                : sprintf("There is no forward review %s.\n", $id));

            return 2;
        }

        return self::render($scenario, 'Status today');
    }

    /**
     * The same for a targeted case, which is read rather than run forward.
     *
     * @param array<int, string> $arguments
     */
    private static function contract(array $arguments): int
    {
        $id = strtoupper($arguments[0] ?? '');
        if ($id === '') {
            return Cli::usage(self::class, 'contract');
        }

        $case = Scenarios::contracts()[$id] ?? null;
        if ($case === null) {
            fwrite(STDERR, isset(Scenarios::load()[$id])
                ? sprintf("%s is an open forward review: bin/cli scenarios show %s\n", $id, $id)
                : sprintf("There is no contract case %s.\n", $id));

            return 2;
        }

        return self::render($case, 'Contract');
    }

    /**
     * @param array{id: string, title: string, file: string, environment: string, status: string, requirements: array<int, string>, heldBy: string, prompt: string, needs: array<int, string>, outcomes: array<int, string>, failures: array<int, string>, criteria: string} $scenario
     */
    private static function render(array $scenario, string $label): int
    {
        printf("%s — %s\n%s\n\n", $scenario['id'], $scenario['title'], $scenario['file']);
        printf("Environment  %s\n", $scenario['environment']);
        printf("%-12s %s%s\n", $label, $scenario['status'], $scenario['requirements'] === [] ? '' : ' — ' . implode(', ', $scenario['requirements']));
        if ($scenario['heldBy'] !== '') {
            // A case nobody runs claims its state on the strength of this line.
            printf("Held by      %s\n", str_replace('`', '', $scenario['heldBy']));
        }
        printf("Criteria     %s\n", $scenario['criteria']);

        // Verbatim, on its own, with nothing around it: a prompt read off a screen
        // that also explains what it is testing is no longer the prompt.
        printf("\nPaste this and add nothing:\n\n%s\n", $scenario['prompt']);

        if ($scenario['needs'] !== []) {
            printf("\nWhat the agent needs from this server\n");
            foreach ($scenario['needs'] as $need) {
                printf("  - %s\n", $need);
            }
        }

        foreach ([['outcomes', 'What has to come out of it'], ['failures', 'How it fails']] as [$section, $heading]) {
            printf("\n%s\n", $heading);
            foreach ($scenario[$section] as $index => $criterion) {
                printf("  %s %d  %s\n", $section === 'outcomes' ? 'met' : 'avoided', $index + 1, $criterion);
            }
        }

        return 0;
    }

    /**
     * The empty run, ready to be filled in after the session.
     *
     * @param array<int, string> $arguments
     */
    private static function record(array $arguments): int
    {
        $id = strtoupper($arguments[0] ?? '');
        $client = $arguments[1] ?? '';
        if (trim($client) === '') {
            return Cli::usage(self::class, 'record');
        }

        if (isset(Scenarios::contracts()[$id])) {
            // Not an oversight to be worked around: a case that names its own task
            // shape cannot be evidence that an agent found it.
            fwrite(STDERR, sprintf("%s is a targeted contract case and is not run forward.\n", $id));

            return 2;
        }

        try {
            $run = Scenarios::skeleton($id, self::server(), $client, date('Y-m-d'));
        } catch (\InvalidArgumentException $exception) {
            fwrite(STDERR, $exception->getMessage() . "\n");

            return 2;
        }

        $directory = Scenarios::runsDirectory();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            fwrite(STDERR, sprintf("Cannot create %s.\n", $directory));

            return 1;
        }

        $file = $directory . '/' . $run['scenario'] . '.json';
        $existed = file_exists($file);
        file_put_contents($file, json_encode($run, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

        printf(
            "%s the run of %s in scenarios/runs/%s.json\n",
            $existed ? 'Replaced' : 'Wrote',
            $run['scenario'],
            $run['scenario'],
        );
        printf("Judge it against: bin/cli scenarios show %s\n", $run['scenario']);

        return 0;
    }

    /**
     * Every recorded run against the scenario it claims to answer: judged in
     * full, evidenced, run in the right environment, judged against the criteria
     * as they read now, and adding up to the status the scenario claims.
     * `composer test` runs the same check through ScenarioTest; this is the
     * readable half.
     */
    public static function check(): int
    {
        $runs = Scenarios::runs();
        $problems = 0;

        foreach ($runs as $id => $recorded) {
            $state = $recorded['verdict'];
            if ($state === '' && Scenarios::isOpen($recorded['run'])) {
                $state = 'open';
            }
            printf(
                "%-10s %-8s %-10s %s\n",
                $id,
                $state === '' ? '—' : $state,
                is_string($recorded['run']['date'] ?? null) ? $recorded['run']['date'] : '',
                $recorded['problems'] === [] ? 'ok' : '',
            );
            foreach ($recorded['problems'] as $problem) {
                ++$problems;
                printf("  %s\n", $problem);
            }
        }

        // Not a failure. Most scenarios have never been run forward, and a suite
        // that fails for that would be a suite nobody could add a scenario to.
        $unrun = array_values(array_diff(array_keys(Scenarios::load()), array_keys($runs)));
        printf("\n%d of %d forward reviews have a recorded run.\n", count($runs), count($runs) + count($unrun));
        if ($unrun !== []) {
            printf("Never run forward: %s\n", implode(', ', $unrun));
        }

        return $problems === 0 ? 0 : 1;
    }

    /** The server the run happened against, as precisely as this checkout can say. */
    private static function server(): string
    {
        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = @proc_open(['git', 'rev-parse', '--short', 'HEAD'], $descriptors, $pipes, dirname(__DIR__, 2), null);
        if (!is_resource($process)) {
            return 'unknown';
        }

        $commit = trim((string) stream_get_contents($pipes[1]));
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process) === 0 && $commit !== '' ? $commit : 'unknown';
    }
}
