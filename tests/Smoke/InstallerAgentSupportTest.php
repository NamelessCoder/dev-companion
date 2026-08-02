<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tests\Support\Directory;

final class InstallerAgentSupportTest extends TestCase
{
    /** @var array<string, array{skills: string, mcp?: string}> */
    private const AGENTS = [
        'amp' => ['skills' => '.agents/skills', 'mcp' => '.amp/settings.json'],
        'junie' => ['skills' => '.junie/skills', 'mcp' => '.junie/mcp/mcp.json'],
        'cursor' => ['skills' => '.cursor/skills', 'mcp' => '.cursor/mcp.json'],
        'claude' => ['skills' => '.claude/skills', 'mcp' => '.mcp.json'],
        'codex' => ['skills' => '.agents/skills', 'mcp' => '.codex/config.toml'],
        'copilot' => ['skills' => '.github/skills', 'mcp' => '.vscode/mcp.json'],
        'factory' => ['skills' => '.factory/skills', 'mcp' => '.factory/mcp.json'],
        'kiro' => ['skills' => '.kiro/skills', 'mcp' => '.kiro/settings/mcp.json'],
        'opencode' => ['skills' => '.agents/skills', 'mcp' => 'opencode.json'],
        'antigravity' => ['skills' => '.agents/skills'],
        'zed' => ['skills' => '.agents/skills', 'mcp' => '.zed/settings.json'],
        'pi' => ['skills' => '.pi/skills'],
        'grok' => ['skills' => '.grok/skills', 'mcp' => '.grok/config.toml'],
    ];
    /** @var array{skills: string, mcp: string} */
    private const GENERIC = ['skills' => '.agents/skills', 'mcp' => '.mcp.json'];

    #[Test]
    public function itInstallsEverySupportedAgent(): void
    {
        foreach (self::AGENTS as $agent => $paths) {
            $directory = sys_get_temp_dir() . '/typo3-cms-mcp-agent-' . bin2hex(random_bytes(8));
            self::assertTrue(mkdir($directory));

            try {
                $stdout = '';
                $stderr = '';
                self::assertSame(
                    0,
                    $this->execute($directory, ['install', '--agent=' . $agent], $stdout, $stderr),
                    $stderr,
                );
                self::assertSame(
                    0,
                    $this->execute($directory, ['update', '--agent=' . $agent], $stdout, $stderr),
                    $stderr,
                );
                self::assertFileExists(
                    $directory . '/' . $paths['skills']
                    . '/typo3-backend-module-development/SKILL.md',
                );
                $gitignore = (string) file_get_contents($directory . '/.gitignore');
                self::assertStringContainsString("/typo3-cms-mcp.json\n", $gitignore);
                self::assertStringContainsString(
                    '/' . $paths['skills'] . "/typo3-backend-module-development/\n",
                    $gitignore,
                );
                if (isset($paths['mcp'])) {
                    self::assertFileExists($directory . '/' . $paths['mcp']);
                    self::assertStringNotContainsString('/' . $paths['mcp'], $gitignore);
                }
            } finally {
                Directory::remove($directory);
            }
        }
    }

    /**
     * Every client that gets an entry is told what is still between that entry
     * and a callable tool.
     *
     * Writing the file registers the server with nothing, and what is left —
     * an approval the client has not asked for yet, a session that was already
     * open — belongs to the client rather than to this package. So what can be
     * held here is that something is said, per client, on both commands and
     * beside the entry it is about; whether it is true of that client is what
     * `documentation/clients/installing.md` sources, and no test reaches it.
     */
    #[Test]
    public function everyClientWithAnEntryIsToldWhatIsLeftBeforeAToolCanBeCalled(): void
    {
        // The setup that names no client writes an entry too, and is the one a
        // reader is most likely to run.
        $clients = ['generic' => self::GENERIC] + self::AGENTS;
        foreach ($clients as $client => $paths) {
            if (!isset($paths['mcp'])) {
                continue;
            }
            $directory = sys_get_temp_dir() . '/typo3-cms-mcp-remaining-' . bin2hex(random_bytes(8));
            self::assertTrue(mkdir($directory));

            try {
                $arguments = $client === 'generic' ? [] : ['--agent=' . $client];
                foreach ([['install', ...$arguments], ['update', ...$arguments]] as $command) {
                    $stdout = '';
                    $stderr = '';
                    self::assertSame(0, $this->execute($directory, $command, $stdout, $stderr), $stderr);
                    $lines = explode("\n", $stdout);
                    $entry = $this->entryLine($lines, $directory . '/' . $paths['mcp']);
                    self::assertMatchesRegularExpression(
                        '/^ {2}\S/',
                        $lines[$entry + 1] ?? '',
                        $client . ' says nothing after ' . $lines[$entry] . ' (' . $command[0] . ')',
                    );
                }
            } finally {
                Directory::remove($directory);
            }
        }
    }

    /**
     * Which line reported the entry, so that what follows it is read as being
     * about that entry rather than about the run.
     *
     * @param list<string> $lines
     */
    private function entryLine(array $lines, string $path): int
    {
        foreach ($lines as $number => $line) {
            if ($line === 'Configured typo3-cms-mcp in ' . $path . '.'
                || $line === 'Reused typo3-cms-mcp in ' . $path . '.') {
                return $number;
            }
        }

        self::fail('nothing reported an entry in ' . $path . ":\n" . implode("\n", $lines));
    }

    /** @param list<string> $arguments */
    private function execute(string $directory, array $arguments, string &$stdout, string &$stderr): int
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-cms-mcp', ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $directory,
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
