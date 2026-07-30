<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

final class Installer
{
    private const SERVER = 'typo3-cms-mcp';
    private const SKILL = 'typo3-backend-module-development';
    private const MANIFEST = '.typo3-cms-mcp.json';

    public function __construct(
        private readonly string $project,
        private readonly string $entrypoint,
    ) {
    }

    public function install(?string $agent): string
    {
        if ($agent === null) {
            return $this->installMcpJson();
        }
        if ($agent !== 'codex') {
            throw new \RuntimeException('unsupported agent "' . $agent . '"; supported: codex');
        }

        $configuration = $this->installCodexConfiguration();
        $skill = $this->publishCodexSkill();

        return $configuration . "\n" . $skill;
    }

    public function update(string $agent): string
    {
        if ($agent !== 'codex') {
            throw new \RuntimeException('unsupported agent "' . $agent . '"; supported: codex');
        }

        $this->assertCodexConfiguration();

        return $this->publishCodexSkill();
    }

    private function installMcpJson(): string
    {
        $path = $this->project . '/.mcp.json';
        $configuration = ['mcpServers' => []];
        if (is_file($path)) {
            try {
                $decoded = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                throw new \RuntimeException('.mcp.json is not valid JSON: ' . $exception->getMessage());
            }
            if (!is_array($decoded)) {
                throw new \RuntimeException('.mcp.json must contain a JSON object');
            }
            $configuration = $decoded;
            $configuration['mcpServers'] ??= [];
            if (!is_array($configuration['mcpServers'])) {
                throw new \RuntimeException('mcpServers in .mcp.json must be an object');
            }
        }

        $server = $this->jsonServer();
        $existing = $configuration['mcpServers'][self::SERVER] ?? null;
        if ($existing !== null && $existing !== $server) {
            throw new \RuntimeException('.mcp.json already has a different typo3-cms-mcp server; refusing to replace it');
        }
        $configuration['mcpServers'][self::SERVER] = $server;
        $this->write($path, json_encode(
            $configuration,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ) . "\n");

        return 'Configured typo3-cms-mcp in ' . $path . '.';
    }

    /** @return array{type: string, command: string, args: list<string>} */
    private function jsonServer(): array
    {
        if (is_file($this->project . '/.ddev/config.yaml')) {
            return [
                'type' => 'stdio',
                'command' => 'ddev',
                'args' => ['exec', 'php', 'vendor/bin/typo3-cms-mcp'],
            ];
        }

        return ['type' => 'stdio', 'command' => 'php', 'args' => [$this->entrypoint]];
    }

    private function installCodexConfiguration(): string
    {
        $directory = $this->project . '/.codex';
        $path = $directory . '/config.toml';
        $configuration = is_file($path) ? (string) file_get_contents($path) : '';
        $section = $this->codexSection($configuration);
        if ($section !== null) {
            $this->assertMatchingCodexSection($section);

            return 'Reused typo3-cms-mcp in ' . $path . '.';
        }

        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException('could not create ' . $directory);
        }
        $separator = $configuration === '' || str_ends_with($configuration, "\n\n")
            ? ''
            : (str_ends_with($configuration, "\n") ? "\n" : "\n\n");
        $this->write($path, $configuration . $separator . $this->expectedCodexSection());

        return 'Configured typo3-cms-mcp in ' . $path . '.';
    }

    private function assertCodexConfiguration(): void
    {
        $path = $this->project . '/.codex/config.toml';
        $configuration = is_file($path) ? (string) file_get_contents($path) : '';
        $section = $this->codexSection($configuration);
        if ($section === null) {
            throw new \RuntimeException('Codex has no typo3-cms-mcp entry; run install --agent=codex first');
        }
        $this->assertMatchingCodexSection($section);
    }

    private function expectedCodexSection(): string
    {
        $server = $this->jsonServer();

        return sprintf(
            "[mcp_servers.%s]\ncommand = %s\nargs = %s\n",
            self::SERVER,
            json_encode($server['command'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            json_encode($server['args'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        );
    }

    private function codexSection(string $configuration): ?string
    {
        if (preg_match(
            '/^\[mcp_servers\.typo3-cms-mcp\]\R(?:(?!^\[).*(?:\R|$))*/m',
            $configuration,
            $matches,
        ) !== 1) {
            return null;
        }

        return $matches[0];
    }

    private function assertMatchingCodexSection(string $section): void
    {
        $server = $this->jsonServer();
        $command = preg_quote(
            json_encode($server['command'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            '/',
        );
        $arguments = preg_quote(
            json_encode($server['args'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            '/',
        );
        $commandMatches = preg_match('/^command\s*=\s*' . $command . '\s*$/m', $section) === 1;
        $argumentMatches = preg_match('/^args\s*=\s*' . $arguments . '\s*$/m', $section) === 1;
        if (!$commandMatches || !$argumentMatches) {
            throw new \RuntimeException(
                '.codex/config.toml already has a different typo3-cms-mcp server; refusing to replace it',
            );
        }
    }

    private function publishCodexSkill(): string
    {
        $source = Paths::root() . '/skills/' . self::SKILL;
        $target = $this->project . '/.agents/skills/' . self::SKILL;
        $files = ['SKILL.md', 'agents/openai.yaml'];
        $manifestPath = $target . '/' . self::MANIFEST;
        $manifest = $this->readManifest($manifestPath);

        foreach ($files as $file) {
            $targetFile = $target . '/' . $file;
            if (!is_file($targetFile)) {
                continue;
            }
            $actual = hash_file('sha256', $targetFile);
            $owned = $manifest['files'][$file]['installedHash'] ?? null;
            $sourceHash = hash_file('sha256', $source . '/' . $file);
            if (($owned === null && $actual !== $sourceHash) || ($owned !== null && $actual !== $owned)) {
                throw new \RuntimeException($targetFile . ' was not generated by this package or was modified; refusing to replace it');
            }
        }

        foreach ($files as $file) {
            $directory = dirname($target . '/' . $file);
            if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException('could not create ' . $directory);
            }
            $contents = (string) file_get_contents($source . '/' . $file);
            $this->write($target . '/' . $file, $contents);
            $hash = hash('sha256', $contents);
            $manifest['files'][$file] = ['sourceHash' => $hash, 'installedHash' => $hash];
        }
        $manifest['version'] = 1;
        $this->write($manifestPath, json_encode(
            $manifest,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ) . "\n");

        return 'Published ' . self::SKILL . ' in ' . $target . '.';
    }

    /** @return array{version?: int, files: array<string, array{sourceHash?: string, installedHash?: string}>} */
    private function readManifest(string $path): array
    {
        if (!is_file($path)) {
            return ['files' => []];
        }
        try {
            $manifest = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \RuntimeException($path . ' is not a valid ownership manifest: ' . $exception->getMessage());
        }
        if (!is_array($manifest) || !is_array($manifest['files'] ?? null)) {
            throw new \RuntimeException($path . ' is not a valid ownership manifest');
        }

        return $manifest;
    }

    private function write(string $path, string $contents): void
    {
        if (file_put_contents($path, $contents) === false) {
            throw new \RuntimeException('could not write ' . $path);
        }
    }
}
