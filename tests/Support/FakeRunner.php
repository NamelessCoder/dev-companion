<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Support;

use Typo3CmsMcp\Process\CommandRunner;

/**
 * What a unit test hands in where the code under test would leave the process.
 *
 * It answers from a table keyed by the command line, so a test says what
 * `ddev describe -j` returns rather than arranging for a `ddev` to exist. It
 * also keeps every command it was asked for, which is the other half: a test
 * that used to prove the invocation by watching a shell receive it now reads
 * it off `commands()`.
 *
 * A command nothing was written for answers as one that could not be started,
 * because that is the honest stand-in for "this machine has no such thing" and
 * because a silent empty success is how a test passes over a call it never
 * meant to allow.
 */
final class FakeRunner implements CommandRunner
{
    /** @var array<int, array{command: array<int, string>, cwd: ?string}> */
    private array $asked = [];

    /**
     * @param array<string, array{ok?: bool, exitCode?: int, output?: string, error?: string}> $answers
     *     keyed by the command as one space-joined line, or by a prefix of it
     */
    public function __construct(private array $answers = []) {}

    /**
     * @param array{ok?: bool, exitCode?: int, output?: string, error?: string} $answer
     */
    public function answer(string $commandLine, array $answer): self
    {
        $this->answers[$commandLine] = $answer;

        return $this;
    }

    /**
     * @param array<int, string> $command
     *
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    public function run(
        array $command,
        ?string $workingDirectory = null,
        ?int $timeoutSeconds = null,
        bool $inheritStdin = false,
    ): array {
        $this->asked[] = ['command' => $command, 'cwd' => $workingDirectory];
        $line = implode(' ', $command);

        $answer = $this->answers[$line] ?? null;
        if ($answer === null) {
            // Longest prefix wins, so a test can answer `ddev exec` once
            // without writing out every argument that follows it.
            $best = '';
            foreach ($this->answers as $key => $candidate) {
                if (str_starts_with($line, $key) && strlen($key) > strlen($best)) {
                    $best = $key;
                    $answer = $candidate;
                }
            }
        }

        if ($answer === null) {
            return ['ok' => false, 'exitCode' => -1, 'output' => '', 'error' => 'could not start ' . ($command[0] ?? '')];
        }

        $exitCode = $answer['exitCode'] ?? (($answer['ok'] ?? true) ? 0 : 1);

        return [
            'ok' => $answer['ok'] ?? $exitCode === 0,
            'exitCode' => $exitCode,
            'output' => $answer['output'] ?? '',
            'error' => $answer['error'] ?? '',
        ];
    }

    /**
     * Every command it was handed, oldest first, as space-joined lines.
     *
     * @return array<int, string>
     */
    public function commands(): array
    {
        return array_map(
            static fn(array $asked): string => implode(' ', $asked['command']),
            $this->asked,
        );
    }

    /** The working directory one command was run in, by its position. */
    public function directoryOf(int $index): ?string
    {
        return $this->asked[$index]['cwd'] ?? null;
    }

    /** @var array<int, string> */
    private array $present = [];

    /**
     * Which executables this machine is to be said to have.
     *
     * Nothing by default, so a test that needs `ddev` to exist says so and one
     * that does not is not quietly answered by whatever the machine running
     * the suite happens to have installed.
     */
    public function with(string ...$names): self
    {
        foreach ($names as $name) {
            $this->present[] = $name;
        }

        return $this;
    }

    public function locate(string $name): ?string
    {
        return in_array($name, $this->present, true) ? '/usr/local/bin/' . $name : null;
    }
}
