<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;

/**
 * What the entrypoint does with an argument that is not a command.
 *
 * The server is what runs when nothing is passed, so every other word has to
 * end in a message and an exit code. Falling through to the transport instead
 * leaves whoever typed it in front of a process that reads stdin forever.
 */
final class EntrypointTest extends TestCase
{
    #[Test]
    public function helpNamesTheCommandsAndTheClientsTheyTake(): void
    {
        $stderr = '';
        $stdout = '';
        self::assertSame(0, $this->execute(['help'], $stdout, $stderr), $stderr);
        self::assertStringContainsString('Usage: typo3-cms-mcp', $stdout);
        self::assertStringContainsString('install', $stdout);
        self::assertStringContainsString('update', $stdout);
        self::assertStringContainsString('codex', $stdout);
        self::assertSame('', $stderr);
    }

    #[Test]
    public function unknownCommandFailsInsteadOfStartingTheServer(): void
    {
        $stderr = '';
        $stdout = '';
        self::assertSame(2, $this->execute(['bogus'], $stdout, $stderr));
        self::assertStringContainsString('no such command "bogus"', $stderr);
        self::assertStringContainsString('Usage: typo3-cms-mcp', $stderr);
        self::assertSame('', $stdout);
    }

    /** @param array<int, string> $arguments */
    private function execute(array $arguments, string &$stdout, string &$stderr): int
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-cms-mcp', ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            Paths::root(),
        );
        self::assertIsResource($process);
        fclose($pipes[0]);
        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process);
    }
}
