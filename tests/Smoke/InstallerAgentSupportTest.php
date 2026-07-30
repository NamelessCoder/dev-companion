<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Typo3CmsMcp\Paths;

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

    #[Test]
    public function itInstallsEverySupportedAgent(): void
    {
        foreach (self::AGENTS as $agent => $paths) {
            $directory = sys_get_temp_dir() . '/typo3-cms-mcp-agent-' . bin2hex(random_bytes(8));
            self::assertTrue(mkdir($directory));

            try {
                $stderr = '';
                self::assertSame(0, $this->execute($directory, ['install', '--agent=' . $agent], $stderr), $stderr);
                self::assertSame(0, $this->execute($directory, ['update', '--agent=' . $agent], $stderr), $stderr);
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
                $this->removeDirectory($directory);
            }
        }
    }

    /** @param list<string> $arguments */
    private function execute(string $directory, array $arguments, string &$stderr): int
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-cms-mcp', ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $directory,
        );
        self::assertIsResource($process);
        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process);
    }

    private function removeDirectory(string $directory): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($directory);
    }
}
