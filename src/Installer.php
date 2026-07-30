<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

final class Installer
{
    private const SERVER = 'typo3-cms-mcp';
    private const SKILL = 'typo3-backend-module-development';
    private const MANIFEST = '.typo3-cms-mcp.json';
    /** @var array<string, array{skills: string, mcp?: array{format: string, path: string, key: string, shape?: string}}> */
    private const AGENTS = [
        'amp' => [
            'skills' => '.agents/skills',
            'mcp' => ['format' => 'json', 'path' => '.amp/settings.json', 'key' => 'amp.mcpServers'],
        ],
        'junie' => [
            'skills' => '.junie/skills',
            'mcp' => ['format' => 'json', 'path' => '.junie/mcp/mcp.json', 'key' => 'mcpServers'],
        ],
        'cursor' => [
            'skills' => '.cursor/skills',
            'mcp' => ['format' => 'json', 'path' => '.cursor/mcp.json', 'key' => 'mcpServers'],
        ],
        'claude' => [
            'skills' => '.claude/skills',
            'mcp' => ['format' => 'json', 'path' => '.mcp.json', 'key' => 'mcpServers'],
        ],
        'codex' => [
            'skills' => '.agents/skills',
            'mcp' => ['format' => 'toml', 'path' => '.codex/config.toml', 'key' => 'mcp_servers'],
        ],
        'copilot' => [
            'skills' => '.github/skills',
            'mcp' => ['format' => 'json', 'path' => '.vscode/mcp.json', 'key' => 'servers'],
        ],
        'factory' => [
            'skills' => '.factory/skills',
            'mcp' => [
                'format' => 'json',
                'path' => '.factory/mcp.json',
                'key' => 'mcpServers',
                'shape' => 'stdio',
            ],
        ],
        'kiro' => [
            'skills' => '.kiro/skills',
            'mcp' => ['format' => 'json', 'path' => '.kiro/settings/mcp.json', 'key' => 'mcpServers'],
        ],
        'opencode' => [
            'skills' => '.agents/skills',
            'mcp' => ['format' => 'json', 'path' => 'opencode.json', 'key' => 'mcp', 'shape' => 'opencode'],
        ],
        'antigravity' => ['skills' => '.agents/skills'],
        'zed' => [
            'skills' => '.agents/skills',
            'mcp' => ['format' => 'json', 'path' => '.zed/settings.json', 'key' => 'context_servers'],
        ],
        'pi' => ['skills' => '.pi/skills'],
        'grok' => [
            'skills' => '.grok/skills',
            'mcp' => ['format' => 'toml', 'path' => '.grok/config.toml', 'key' => 'mcp_servers'],
        ],
    ];

    public function __construct(
        private readonly string $project,
        private readonly string $entrypoint,
    ) {
    }

    public function install(?string $agent): string
    {
        if ($agent === null) {
            return $this->installJsonConfiguration('.mcp.json', 'mcpServers');
        }
        $definition = $this->agent($agent);

        $messages = [];
        if (isset($definition['mcp'])) {
            $messages[] = $this->installAgentConfiguration($agent, $definition['mcp']);
        }
        $messages[] = $this->publishSkill($definition['skills']);

        return implode("\n", $messages);
    }

    public function update(string $agent): string
    {
        $definition = $this->agent($agent);
        if (isset($definition['mcp'])) {
            $this->assertAgentConfiguration($definition['mcp']);
        }

        return $this->publishSkill($definition['skills']);
    }

    /** @return array{skills: string, mcp?: array{format: string, path: string, key: string, shape?: string}} */
    private function agent(string $agent): array
    {
        if (!isset(self::AGENTS[$agent])) {
            throw new \RuntimeException(
                'unsupported agent "' . $agent . '"; supported: ' . implode(', ', array_keys(self::AGENTS)),
            );
        }

        return self::AGENTS[$agent];
    }

    /** @param array{format: string, path: string, key: string, shape?: string} $mcp */
    private function installAgentConfiguration(string $agent, array $mcp): string
    {
        if ($mcp['format'] === 'toml') {
            return $this->installTomlConfiguration($mcp['path'], $mcp['key']);
        }

        return $this->installJsonConfiguration($mcp['path'], $mcp['key'], $mcp['shape'] ?? null, $agent);
    }

    private function installJsonConfiguration(
        string $relativePath,
        string $key,
        ?string $shape = null,
        ?string $agent = null,
    ): string {
        $path = $this->project . '/' . $relativePath;
        $configuration = $agent === 'opencode' ? ['$schema' => 'https://opencode.ai/config.json'] : [];
        if (is_file($path)) {
            try {
                $decoded = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                throw new \RuntimeException($relativePath . ' is not valid JSON: ' . $exception->getMessage());
            }
            if (!is_array($decoded)) {
                throw new \RuntimeException($relativePath . ' must contain a JSON object');
            }
            $configuration = $decoded;
        }

        $target =& $configuration;
        foreach (explode('.', $key) as $segment) {
            $target[$segment] ??= [];
            if (!is_array($target[$segment])) {
                throw new \RuntimeException($key . ' in ' . $relativePath . ' must be an object');
            }
            $target =& $target[$segment];
        }

        $server = $this->jsonServer($shape);
        $existing = $target[self::SERVER] ?? null;
        if ($existing !== null && $existing !== $server) {
            throw new \RuntimeException(
                $relativePath . ' already has a different typo3-cms-mcp server; refusing to replace it',
            );
        }
        $target[self::SERVER] = $server;
        $this->writeJson($path, $configuration);

        return 'Configured typo3-cms-mcp in ' . $path . '.';
    }

    /**
     * @param array{format: string, path: string, key: string, shape?: string} $mcp
     */
    private function assertAgentConfiguration(array $mcp): void
    {
        $path = $this->project . '/' . $mcp['path'];
        if ($mcp['format'] === 'toml') {
            $configuration = is_file($path) ? (string) file_get_contents($path) : '';
            $section = $this->tomlSection($configuration, $mcp['key']);
            if ($section === null) {
                throw new \RuntimeException($mcp['path'] . ' has no typo3-cms-mcp entry; run install first');
            }
            $this->assertMatchingTomlSection($section);

            return;
        }

        $configuration = [];
        if (is_file($path)) {
            try {
                $decoded = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                throw new \RuntimeException($mcp['path'] . ' is not valid JSON: ' . $exception->getMessage());
            }
            $configuration = $decoded;
        }
        foreach (explode('.', $mcp['key']) as $segment) {
            $configuration = is_array($configuration) ? ($configuration[$segment] ?? null) : null;
        }
        if (!is_array($configuration) || ($configuration[self::SERVER] ?? null) !== $this->jsonServer($mcp['shape'] ?? null)) {
            throw new \RuntimeException($mcp['path'] . ' has a different or missing typo3-cms-mcp entry');
        }
    }

    /**
     * @return array{type: string, command: string, args: list<string>}
     *     |array{type: string, enabled: bool, command: list<string>}
     */
    private function jsonServer(?string $shape = null): array
    {
        $command = 'php';
        $args = [$this->entrypoint];
        if (is_file($this->project . '/.ddev/config.yaml')) {
            $command = 'ddev';
            $args = ['exec', 'php', 'vendor/bin/typo3-cms-mcp'];
        }
        if ($shape === 'opencode') {
            return ['type' => 'local', 'enabled' => true, 'command' => [$command, ...$args]];
        }

        return ['type' => 'stdio', 'command' => $command, 'args' => $args];
    }

    private function installTomlConfiguration(string $relativePath, string $key): string
    {
        $path = $this->project . '/' . $relativePath;
        $configuration = is_file($path) ? (string) file_get_contents($path) : '';
        $section = $this->tomlSection($configuration, $key);
        if ($section !== null) {
            $this->assertMatchingTomlSection($section);

            return 'Reused typo3-cms-mcp in ' . $path . '.';
        }

        $separator = $configuration === '' || str_ends_with($configuration, "\n\n")
            ? ''
            : (str_ends_with($configuration, "\n") ? "\n" : "\n\n");
        $this->write($path, $configuration . $separator . $this->expectedTomlSection($key));

        return 'Configured typo3-cms-mcp in ' . $path . '.';
    }

    private function expectedTomlSection(string $key): string
    {
        $server = $this->jsonServer();

        return sprintf(
            "[%s.%s]\ncommand = %s\nargs = %s\n",
            $key,
            self::SERVER,
            json_encode($server['command'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            json_encode($server['args'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        );
    }

    private function tomlSection(string $configuration, string $key): ?string
    {
        if (preg_match(
            '/^\[' . preg_quote($key, '/') . '\.typo3-cms-mcp\]\R(?:(?!^\[).*(?:\R|$))*/m',
            $configuration,
            $matches,
        ) !== 1) {
            return null;
        }

        return $matches[0];
    }

    private function assertMatchingTomlSection(string $section): void
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

    private function publishSkill(string $skillsPath): string
    {
        $source = Paths::root() . '/skills/' . self::SKILL;
        $target = $this->project . '/' . $skillsPath . '/' . self::SKILL;
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

    /** @param array<string, mixed> $configuration */
    private function writeJson(string $path, array $configuration): void
    {
        $this->write($path, json_encode(
            $configuration,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ) . "\n");
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
        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException('could not create ' . $directory);
        }
        if (file_put_contents($path, $contents) === false) {
            throw new \RuntimeException('could not write ' . $path);
        }
    }
}
