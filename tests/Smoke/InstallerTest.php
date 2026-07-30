<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;

final class InstallerTest extends TestCase
{
    #[Test]
    public function installWritesAnIdempotentConfigurationAndPreservesOtherServers(): void
    {
        $directory = sys_get_temp_dir() . '/typo3-cms-mcp-install-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($directory));
        file_put_contents($directory . '/.mcp.json', json_encode([
            'mcpServers' => ['other' => ['command' => 'other-server']],
            'keep' => true,
        ], JSON_THROW_ON_ERROR));

        try {
            $stderr = '';
            self::assertSame(0, $this->install($directory, $stderr), $stderr);
            $first = (string) file_get_contents($directory . '/.mcp.json');
            self::assertSame(0, $this->install($directory, $stderr), $stderr);
            self::assertSame($first, file_get_contents($directory . '/.mcp.json'));

            $configuration = json_decode($first, true, flags: JSON_THROW_ON_ERROR);
            self::assertTrue($configuration['keep']);
            self::assertSame('other-server', $configuration['mcpServers']['other']['command']);
            self::assertSame([
                'type' => 'stdio',
                'command' => 'php',
                'args' => [Paths::root() . '/bin/typo3-cms-mcp'],
            ], $configuration['mcpServers']['typo3-cms-mcp']);
        } finally {
            @unlink($directory . '/.mcp.json');
            @rmdir($directory);
        }
    }

    #[Test]
    public function installRefusesToReplaceAnotherCommand(): void
    {
        $directory = sys_get_temp_dir() . '/typo3-cms-mcp-install-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($directory));
        $original = '{"mcpServers":{"typo3-cms-mcp":{"command":"somewhere-else"}}}';
        file_put_contents($directory . '/.mcp.json', $original);

        try {
            $stderr = '';
            self::assertSame(1, $this->install($directory, $stderr));
            self::assertStringContainsString('refusing to replace', $stderr);
            self::assertSame($original, file_get_contents($directory . '/.mcp.json'));
        } finally {
            @unlink($directory . '/.mcp.json');
            @rmdir($directory);
        }
    }

    #[Test]
    public function codexInstallAndUpdatePreserveConfigurationAndOwnTheirSkill(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.codex', 0777, true));
        $unrelated = "model = \"gpt-5\"\n\n[features]\nweb_search = true\n";
        file_put_contents($directory . '/.codex/config.toml', $unrelated);

        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);
            $configuration = (string) file_get_contents($directory . '/.codex/config.toml');
            self::assertStringStartsWith($unrelated, $configuration);
            self::assertStringContainsString('[mcp_servers.typo3-cms-mcp]', $configuration);
            self::assertStringContainsString(Paths::root() . '/bin/typo3-cms-mcp', $configuration);

            $skill = $directory . '/.agents/skills/typo3-backend-module-development/SKILL.md';
            $manifest = dirname($skill) . '/.typo3-cms-mcp.json';
            self::assertFileEquals(
                Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
                $skill,
            );
            self::assertFileExists($manifest);

            $before = [
                'configuration' => file_get_contents($directory . '/.codex/config.toml'),
                'skill' => file_get_contents($skill),
                'manifest' => file_get_contents($manifest),
            ];
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);
            self::assertSame(0, $this->execute($directory, ['update', '--agent=codex'], $stderr), $stderr);
            self::assertSame($before['configuration'], file_get_contents($directory . '/.codex/config.toml'));
            self::assertSame($before['skill'], file_get_contents($skill));
            self::assertSame($before['manifest'], file_get_contents($manifest));
        } finally {
            $this->removeCodexFixture($directory);
        }
    }

    #[Test]
    public function ddevProjectUsesTheContainerPhpForMcpAndPublishesTheSkillToTheProject(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.ddev'));
        file_put_contents($directory . '/.ddev/config.yaml', "name: fixture\n");

        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install'], $stderr), $stderr);
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);

            $server = [
                'type' => 'stdio',
                'command' => 'ddev',
                'args' => ['exec', 'php', 'vendor/bin/typo3-cms-mcp'],
            ];
            $mcpConfiguration = json_decode(
                (string) file_get_contents($directory . '/.mcp.json'),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
            self::assertSame($server, $mcpConfiguration['mcpServers']['typo3-cms-mcp']);

            $codexConfiguration = (string) file_get_contents($directory . '/.codex/config.toml');
            self::assertStringContainsString('command = "ddev"', $codexConfiguration);
            self::assertStringContainsString(
                'args = ["exec","php","vendor/bin/typo3-cms-mcp"]',
                $codexConfiguration,
            );
            self::assertFileExists(
                $directory . '/.agents/skills/typo3-backend-module-development/SKILL.md',
            );
        } finally {
            @unlink($directory . '/.mcp.json');
            @unlink($directory . '/.ddev/config.yaml');
            @rmdir($directory . '/.ddev');
            $this->removeCodexFixture($directory);
        }
    }

    #[Test]
    public function codexInstallRefusesAConflictingServerEntry(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.codex', 0777, true));
        $original = "[mcp_servers.typo3-cms-mcp]\ncommand = \"other\"\nargs = [\"elsewhere\"]\n";
        file_put_contents($directory . '/.codex/config.toml', $original);

        try {
            $stderr = '';
            self::assertSame(1, $this->execute($directory, ['install', '--agent=codex'], $stderr));
            self::assertStringContainsString('refusing to replace', $stderr);
            self::assertSame($original, file_get_contents($directory . '/.codex/config.toml'));
            self::assertDirectoryDoesNotExist($directory . '/.agents');
        } finally {
            $this->removeCodexFixture($directory);
        }
    }

    #[Test]
    public function codexUpdateRefusesToOverwriteAModifiedGeneratedSkill(): void
    {
        $directory = $this->directory();
        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);
            $skill = $directory . '/.agents/skills/typo3-backend-module-development/SKILL.md';
            file_put_contents($skill, (string) file_get_contents($skill) . "\nUser change.\n");

            self::assertSame(1, $this->execute($directory, ['update', '--agent=codex'], $stderr));
            self::assertStringContainsString('was modified', $stderr);
            self::assertStringContainsString('User change.', (string) file_get_contents($skill));
        } finally {
            $this->removeCodexFixture($directory);
        }
    }

    private function install(string $directory, string &$stderr): int
    {
        return $this->execute($directory, ['install'], $stderr);
    }

    /** @param list<string> $arguments */
    private function execute(string $directory, array $arguments, string &$stderr): int
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-cms-mcp', ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $directory
        );
        self::assertIsResource($process);
        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process);
    }

    private function directory(): string
    {
        $directory = sys_get_temp_dir() . '/typo3-cms-mcp-install-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($directory));

        return $directory;
    }

    private function removeCodexFixture(string $directory): void
    {
        @unlink($directory . '/.agents/skills/typo3-backend-module-development/agents/openai.yaml');
        @rmdir($directory . '/.agents/skills/typo3-backend-module-development/agents');
        @unlink($directory . '/.agents/skills/typo3-backend-module-development/SKILL.md');
        @unlink($directory . '/.agents/skills/typo3-backend-module-development/.typo3-cms-mcp.json');
        @rmdir($directory . '/.agents/skills/typo3-backend-module-development');
        @rmdir($directory . '/.agents/skills');
        @rmdir($directory . '/.agents');
        @unlink($directory . '/.codex/config.toml');
        @rmdir($directory . '/.codex');
        @rmdir($directory);
    }
}
