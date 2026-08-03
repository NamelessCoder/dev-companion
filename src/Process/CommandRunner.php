<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Process;

/**
 * The one place this server leaves its own process, as a seam a test can take.
 *
 * Everything here that reaches a console, a container or a checkout ends in one
 * `proc_open`, and for a long time it ended in one that no test could stand
 * between. So a unit test that had to say what `ddev describe` answers wrote a
 * `ddev` into a temporary directory, made it executable and put that directory
 * on the `PATH` — a mock, but one that still forks a shell and depends on
 * `chmod`, on how `PATH` is read, and on a `/tmp` that is not mounted `noexec`.
 *
 * A unit test tests a small part, and where it needs something from outside,
 * that is mocked rather than started (`R-COD-003`). This interface is what
 * makes the second half possible: `SystemRunner` is the real one, a test hands
 * in a fake, and nothing in the suite has to have anything running.
 */
interface CommandRunner
{
    /**
     * Runs one command and waits for it, with both its streams read.
     *
     * The child never inherits this process's stdin. On the stdio server that
     * stream is the client's JSON-RPC: a request written while a console
     * command runs would be read by the console command, and the client would
     * wait forever for an answer to a request the server never saw.
     *
     * @param array<int, string> $command the executable and its arguments, unquoted — no shell is involved
     * @param ?string $workingDirectory where to run it, or null for this process's own
     * @param ?int $timeoutSeconds how long to wait before terminating it, or null to wait
     *
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    public function run(array $command, ?string $workingDirectory = null, ?int $timeoutSeconds = null): array;

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
