<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Process;

/**
 * The one place this server leaves its own process, as a seam a test can take.
 *
 * Everything here that reaches a console, a container or a checkout ends in one
 * `proc_open`, and for a long time it ended in one no test could stand between:
 * a test that had to say what `ddev describe` answers wrote a `ddev` into a
 * temporary directory and put that directory on the `PATH`. A unit test mocks
 * what is outside it rather than starting it (`R-COD-003`), and this interface
 * is what makes that possible — `SystemRunner` is the real one, a test hands in
 * a fake, and nothing in the suite has to have anything running.
 */
interface CommandRunner
{
    /**
     * Runs one command and waits for it, with both its streams read.
     *
     * The child does not inherit this process's stdin unless it is asked for.
     * On the stdio server that stream is the client's JSON-RPC: a request
     * written while a console command runs would be read by the console
     * command, and the client would wait forever for an answer to a request
     * the server never saw. git is the exception and says so at its call.
     *
     * @param array<int, string> $command the executable and its arguments, unquoted — no shell is involved
     * @param ?string $workingDirectory where to run it, or null for this process's own
     * @param ?int $timeoutSeconds how long to wait before terminating it, or null to wait
     * @param bool $inheritStdin let the child read this process's stdin, which only git wants
     *
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    public function run(
        array $command,
        ?string $workingDirectory = null,
        ?int $timeoutSeconds = null,
        bool $inheritStdin = false,
    ): array;

    /**
     * Where an executable of this name is, or null where the machine has none.
     *
     * On the same seam as `run()` because it is the same boundary: asking
     * whether `ddev` exists is asking the machine, and a test that has to
     * arrange for one on the `PATH` is a test that depends on the machine
     * having a writable, executable temporary directory. Answering it here is
     * what lets the whole question be stubbed.
     */
    public function locate(string $name): ?string;
}
