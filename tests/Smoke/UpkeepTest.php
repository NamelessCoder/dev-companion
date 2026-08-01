<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;

/**
 * That `bin/cli` still runs, driven the way a session drives it.
 *
 * The logic behind each subject is covered a class at a time, and that is what
 * left the gap this closes: a command can be held to its rules and still be
 * unreachable, because what it reads is resolved from where its own file sits.
 * Moving the subjects one directory deeper broke four of them at once and every
 * test stayed green, since none of them went through the entrypoint. These do.
 */
final class UpkeepTest extends TestCase
{
    /**
     * Every command that only reports. The ones that write — index, record,
     * archive, checkouts update — are left out on purpose: a test suite that
     * rewrites the repository it is run in is worse than the gap.
     *
     * @return array<string, array{0: array<int, string>}>
     */
    public static function readingCommands(): array
    {
        return [
            'the help, which is what an empty invocation prints' => [[]],
            'requirements check' => [['requirements', 'check']],
            'requirements list' => [['requirements', 'list']],
            'decisions check' => [['decisions', 'check']],
            'decisions list' => [['decisions', 'list']],
            'scenarios check' => [['scenarios', 'check']],
            'catalog check' => [['catalog', 'check']],
            'hints coverage' => [['hints', 'coverage']],
            'backlog list' => [['backlog', 'list']],
            'todo list' => [['todo', 'list']],
            'feedback list' => [['feedback', 'list']],
            'next' => [['next']],
        ];
    }

    /**
     * That it reaches its own code and reports, which is a different question
     * from whether it liked what it found. The exit code is the command's to
     * decide — `hints coverage` reports a gap and says so with a 1, an empty
     * invocation is a usage error and says so with a 2 — so what is held here
     * is that nothing died on the way: no uncaught error, and an answer.
     *
     * @param array<int, string> $arguments
     */
    #[DataProvider('readingCommands')]
    #[Test]
    public function everyReadingCommandRuns(array $arguments): void
    {
        $stdout = '';
        $stderr = '';

        $exit = $this->execute($arguments, $stdout, $stderr);

        self::assertStringNotContainsString('Fatal error', $stderr, 'died rather than answered');
        self::assertStringNotContainsString('Uncaught', $stderr, 'died rather than answered');
        self::assertNotSame(255, $exit, $stderr);
        // Either stream: the usage an empty invocation prints is a message to
        // whoever typed nothing, and goes where a message goes.
        self::assertNotSame('', trim($stdout . $stderr), 'answered with nothing');
    }

    /** @param array<int, string> $arguments */
    private function execute(array $arguments, string &$stdout, string &$stderr): int
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/cli', ...$arguments],
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
