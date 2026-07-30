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

    private function install(string $directory, string &$stderr): int
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-cms-mcp', 'install'],
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
}
