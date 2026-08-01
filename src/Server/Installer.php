<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Server;

use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Paths;

final class Installer
{
    private const SERVER = 'typo3-cms-mcp';
    private const SKILLS = [
        'typo3-backend-module-development',
        'typo3-content-element-development',
        'typo3-extension-conformance',
        'typo3-extension-documentation',
        'typo3-extension-release',
        'typo3-extension-testing',
        'typo3-extension-upgrade',
    ];
    private const BASE = 'references/base.md';
    private const STATE = 'typo3-cms-mcp.json';
    private const IGNORE_BEGIN = '# BEGIN typo3-cms-mcp (generated)';
    private const IGNORE_END = '# END typo3-cms-mcp';
    /**
     * The setup that names no client: the entry every client reads, and the
     * skills at the path the clients that agreed on one share. It is a client
     * of the installation like any other and is recorded like one — it is only
     * `--agent=` that does not take it, because it is nobody's name.
     */
    private const GENERIC = 'generic';
    /** @var array{skills: string, mcp: array{format: string, path: string, key: string}} */
    private const GENERIC_DEFINITION = [
        'skills' => '.agents/skills',
        'mcp' => ['format' => 'json', 'path' => '.mcp.json', 'key' => 'mcpServers'],
    ];
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
    ) {}

    /**
     * The clients `--agent=` accepts, for the entrypoint's own help.
     *
     * @return array<int, string>
     */
    public static function agents(): array
    {
        return array_keys(self::AGENTS);
    }

    public function install(?string $agent): string
    {
        return $this->setUp([$agent ?? self::GENERIC]);
    }

    /**
     * Bring the clients installed here up to date.
     *
     * Without an agent that is every client `typo3-cms-mcp.json` records, which
     * is the case that matters: a project is usually worked on by more than one
     * client, and naming them one at a time meant remembering which of them the
     * project had — a list nobody keeps, so the second client silently kept the
     * skills of the version it was installed with.
     *
     * The setup that named no client is one of them, so it is refreshed the
     * same way and needs no case of its own.
     */
    public function update(?string $agent): string
    {
        $update = $agent !== null ? [$agent] : $this->readState()['agents'];
        if ($update === []) {
            throw new \RuntimeException(
                'nothing is installed here; run install, or install --agent=<client> for a client of its own',
            );
        }

        return $this->setUp($update);
    }

    /**
     * What both commands do, for the clients they were given.
     *
     * They are the same work: the entry each client reads, the skills at the
     * path it reads them from, and the record of both. `install` names one
     * client, `update` the ones already recorded — and the entry is written on
     * either, because what belongs in it is a property of the project rather
     * than of the run. A project that required this server after it was first
     * installed, or that gained a DDEV configuration since, needs a different
     * entry than the one that is there; an update that only checked it left the
     * project with a message and no command that would fix it, because
     * `install` refuses an entry it did not just write.
     *
     * @param list<string> $names
     */
    private function setUp(array $names): string
    {
        $state = $this->readState();

        $messages = [];
        $published = [];
        foreach ($names as $name) {
            $definition = $this->definition($name);
            if (isset($definition['mcp'])) {
                $messages[] = $this->installAgentConfiguration($name, $definition['mcp']);
            }
            // Clients that share a skills directory — .agents/skills is four of
            // them — are one publication, not four identical ones.
            if (in_array($definition['skills'], $published, true)) {
                continue;
            }
            $published[] = $definition['skills'];
            $messages[] = $this->publishSkills($definition['skills'], $state['skills']);
        }

        return implode("\n", $this->record($state, $names, $messages));
    }

    /**
     * What the run leaves behind: the clients installed here, and the ignores
     * that follow from them.
     *
     * Both are written once per run rather than per client, because both are
     * one file for the whole project. Writing them inside the loop would let
     * the first client of a run decide what the second one sees.
     *
     * @param array{skills: list<string>, agents: list<string>} $state
     * @param list<string> $installed
     * @param list<string> $messages
     * @return list<string>
     */
    private function record(array $state, array $installed, array $messages): array
    {
        $agents = array_values(array_unique([...$state['agents'], ...$installed]));
        sort($agents);
        $this->writeJson($this->project . '/' . self::STATE, [
            'version' => 1,
            'agents' => $agents,
            'skills' => self::SKILLS,
        ]);
        $messages[] = $this->ignoreGenerated($agents);

        return $messages;
    }

    /**
     * What to write for a name the project recorded, which is a client's or the
     * one the generic setup goes by.
     *
     * @return array{skills: string, mcp?: array{format: string, path: string, key: string, shape?: string}}
     */
    private function definition(string $name): array
    {
        return $name === self::GENERIC ? self::GENERIC_DEFINITION : $this->agent($name);
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

        $target = & $configuration;
        foreach (explode('.', $key) as $segment) {
            $target[$segment] ??= [];
            if (!is_array($target[$segment])) {
                throw new \RuntimeException($key . ' in ' . $relativePath . ' must be an object');
            }
            $target = & $target[$segment];
        }

        $existing = $target[self::SERVER] ?? null;
        if ($existing !== null && !$this->namesThisServer($this->commandWords($existing))) {
            throw new \RuntimeException(
                $relativePath . ' already has a different typo3-cms-mcp server; refusing to replace it',
            );
        }
        $target[self::SERVER] = $this->jsonServer($shape);

        return $this->message($this->writeJson($path, $configuration), $path);
    }

    /**
     * Whether the entry that is already there is this server's.
     *
     * That is the line between an entry this installer may rewrite and one it
     * must leave alone, and it is drawn at the server being started rather than
     * at the exact command: which command starts this server is a property of
     * the project, and it changes when the project requires the package or
     * gains a DDEV configuration. An entry that names something else is
     * somebody's own and is refused, whatever key it sits under.
     *
     * @param list<string> $words
     */
    private function namesThisServer(array $words): bool
    {
        foreach ($words as $word) {
            if (basename($word) === self::SERVER) {
                return true;
            }
        }

        return false;
    }

    /**
     * The command an entry starts, as words: the command and its arguments,
     * whichever of the two shapes the client writes them in.
     *
     * @return list<string>
     */
    private function commandWords(mixed $entry): array
    {
        $words = [];
        foreach (['command', 'args'] as $field) {
            $value = is_array($entry) ? ($entry[$field] ?? null) : null;
            foreach (is_array($value) ? $value : [$value] as $word) {
                if (is_string($word)) {
                    $words[] = $word;
                }
            }
        }

        return $words;
    }

    /**
     * @return array{type: string, command: string, args: list<string>}
     *     |array{type: string, enabled: bool, command: list<string>}
     */
    private function jsonServer(?string $shape = null): array
    {
        $command = 'php';
        $args = [$this->entrypoint];
        $installed = $this->installedEntrypoint();
        if ($installed !== null && is_file($this->project . '/.ddev/config.yaml')) {
            $command = 'ddev';
            $args = ['exec', 'php', $installed];
        }
        if ($shape === 'opencode') {
            return ['type' => 'local', 'enabled' => true, 'command' => [$command, ...$args]];
        }

        return ['type' => 'stdio', 'command' => $command, 'args' => $args];
    }

    /**
     * This server's entrypoint inside the project, relative to its root.
     *
     * A DDEV project is started through the container PHP, and the container
     * sees the project directory rather than the host — so the entrypoint has
     * to be named relative to the root, at the bin directory the project
     * declares. `vendor/bin` was written unconditionally, which is right until
     * a project moves it, and a TYPO3 extension repository routinely does
     * (`"bin-dir": ".build/bin"`). The entry then pointed at a file that does
     * not exist, and nothing said so until a client tried to start the server.
     *
     * Null means the server is not a dependency of this project at all — it is
     * being run from a checkout elsewhere, which the container cannot see
     * either, so the absolute entrypoint is the only path that exists for it.
     */
    private function installedEntrypoint(): ?string
    {
        $directories = [Typo3Cli::binDirectory($this->project), 'vendor/bin'];
        foreach (array_unique(array_filter($directories)) as $directory) {
            if (is_file($this->project . '/' . $directory . '/' . self::SERVER)) {
                return $directory . '/' . self::SERVER;
            }
        }

        return null;
    }

    private function installTomlConfiguration(string $relativePath, string $key): string
    {
        $path = $this->project . '/' . $relativePath;
        $configuration = is_file($path) ? (string) file_get_contents($path) : '';
        $section = $this->tomlSection($configuration, $key);
        if ($section !== null) {
            preg_match_all('/"([^"]*)"/', $section, $quoted);
            if (!$this->namesThisServer($quoted[1])) {
                throw new \RuntimeException(
                    $relativePath . ' already has a different typo3-cms-mcp server; refusing to replace it',
                );
            }
            // The blank lines below the section separate it from whatever
            // follows and belong to the file, not to the section, so they are
            // put back as they were found.
            $trailing = substr($section, strlen(rtrim($section, "\n")));
            $configuration = str_replace(
                $section,
                rtrim($this->expectedTomlSection($key), "\n") . ($trailing === '' ? "\n" : $trailing),
                $configuration,
            );
        } else {
            $separator = $configuration === '' || str_ends_with($configuration, "\n\n")
                ? ''
                : (str_ends_with($configuration, "\n") ? "\n" : "\n\n");
            $configuration .= $separator . $this->expectedTomlSection($key);
        }

        return $this->message($this->write($path, $configuration), $path);
    }

    private function message(bool $written, string $path): string
    {
        return ($written ? 'Configured' : 'Reused') . ' typo3-cms-mcp in ' . $path . '.';
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

    /** @param list<string> $previousSkills */
    private function publishSkills(string $skillsPath, array $previousSkills): string
    {
        $messages = [];
        foreach (self::SKILLS as $skill) {
            $messages[] = $this->publishSkill($skillsPath, $skill);
        }
        foreach (array_diff($previousSkills, self::SKILLS) as $skill) {
            $this->removeDirectory($this->project . '/' . $skillsPath . '/' . $skill);
            $messages[] = 'Removed stale ' . $skill . ' from ' . $this->project . '/' . $skillsPath . '.';
        }

        return implode("\n", $messages);
    }

    /**
     * The ignore block, between its markers, written whole every time.
     *
     * What it has to ignore follows from which clients are installed and which
     * skills exist, and both change. Adding the missing lines left the ones
     * that had become wrong — a skill that was renamed, a client nobody
     * publishes to any more — in a file the project shares, where a line that
     * ignores nothing looks exactly like one that ignores something. The
     * markers are what makes replacing it safe: everything between them is
     * this installer's and goes, everything outside is the project's and stays,
     * and neither has to be recognised by what it says.
     *
     * @param list<string> $agents
     */
    private function ignoreGenerated(array $agents): string
    {
        $path = $this->project . '/.gitignore';
        $contents = is_file($path) ? (string) file_get_contents($path) : '';
        $lines = preg_split('/\R/', $contents);
        if ($lines === false) {
            throw new \RuntimeException('could not read ' . $path);
        }

        $block = [self::IGNORE_BEGIN, '/' . self::STATE];
        foreach ($agents as $agent) {
            foreach (self::SKILLS as $skill) {
                $block[] = '/' . trim($this->definition($agent)['skills'], '/') . '/' . $skill . '/';
            }
        }
        $block = array_values(array_unique($block));
        $block[] = self::IGNORE_END;

        $kept = $this->withoutGeneratedBlock($lines);
        $rewritten = ($kept === [] ? '' : implode("\n", $kept) . "\n\n") . implode("\n", $block) . "\n";

        return ($this->write($path, $rewritten) ? 'Wrote' : 'Reused')
            . ' generated skill ignores in ' . $path . '.';
    }

    /**
     * The file without the block, and without the gap it leaves behind.
     *
     * The blank line that separated the block from what is above it is the
     * block's, so a run that takes the block out and puts it back has to leave
     * the file it found — otherwise every run adds an empty line.
     *
     * @param list<string> $lines
     * @return list<string>
     */
    private function withoutGeneratedBlock(array $lines): array
    {
        $kept = [];
        $inside = false;
        $closed = false;
        foreach ($lines as $line) {
            if ($inside) {
                $inside = $line !== self::IGNORE_END;
                $closed = !$inside;
                continue;
            }
            if ($line === self::IGNORE_BEGIN) {
                $inside = true;
                continue;
            }
            if ($closed && $line === '' && ($kept === [] || end($kept) === '')) {
                continue;
            }
            $closed = false;
            $kept[] = $line;
        }
        while ($kept !== [] && end($kept) === '') {
            array_pop($kept);
        }

        return $kept;
    }

    private function publishSkill(string $skillsPath, string $skill): string
    {
        $source = Paths::root() . '/skills/' . $skill;
        $target = $this->project . '/' . $skillsPath . '/' . $skill;
        $this->removeDirectory($target);
        $this->copyDirectory($source, $target);
        // The order every skill starts in, written once and carried into each
        // of them. A copy rather than a shared file, because a published skill
        // lands in somebody else's project on its own: a reference pointing out
        // of its own directory would resolve here and nowhere it is used.
        $this->write(
            $target . '/' . self::BASE,
            (string) file_get_contents(Paths::root() . '/skills/' . basename(self::BASE)),
        );

        return 'Published ' . $skill . ' in ' . $target . '.';
    }

    /** @param array<string, mixed> $configuration */
    private function writeJson(string $path, array $configuration): bool
    {
        return $this->write($path, json_encode(
            $configuration,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ) . "\n");
    }

    /**
     * What the last run left here: the skills it published, and the clients it
     * published them for.
     *
     * A file written before clients were recorded has no `agents`, and so does
     * one from a project set up with the generic `.mcp.json` alone. Both are an
     * empty list rather than an error — nothing is wrong there, there is just
     * nothing an `update` without an agent could act on.
     *
     * @return array{skills: list<string>, agents: list<string>}
     */
    private function readState(): array
    {
        $path = $this->project . '/' . self::STATE;
        if (!is_file($path)) {
            return ['skills' => [], 'agents' => []];
        }
        try {
            $state = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \RuntimeException(self::STATE . ' is not valid JSON: ' . $exception->getMessage());
        }
        if (!is_array($state) || !is_array($state['skills'] ?? null)) {
            throw new \RuntimeException(self::STATE . ' must contain a skills array');
        }
        $skills = array_values(array_filter(
            $state['skills'],
            static fn(mixed $skill): bool => is_string($skill) && $skill !== '',
        ));
        $agents = array_values(array_filter(
            is_array($state['agents'] ?? null) ? $state['agents'] : [],
            static fn(mixed $agent): bool => is_string($agent)
                && (isset(self::AGENTS[$agent]) || $agent === self::GENERIC),
        ));

        return ['skills' => $skills, 'agents' => $agents];
    }

    private function copyDirectory(string $source, string $target): void
    {
        if (!is_dir($source)) {
            throw new \RuntimeException('skill source does not exist: ' . $source);
        }
        if (!mkdir($target, 0777, true) && !is_dir($target)) {
            throw new \RuntimeException('could not create ' . $target);
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
        );
        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $relative = $files->getSubPathName();
            $contents = file_get_contents($file->getPathname());
            if ($contents === false) {
                throw new \RuntimeException('could not read ' . $file->getPathname());
            }
            $this->write($target . '/' . $relative, $contents);
        }
    }

    private function removeDirectory(string $path): void
    {
        if (is_link($path)) {
            if (!unlink($path)) {
                throw new \RuntimeException('could not remove ' . $path);
            }

            return;
        }
        if (!is_dir($path)) {
            return;
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($files as $file) {
            $removed = $file->isDir()
                ? rmdir($file->getPathname())
                : unlink($file->getPathname());
            if (!$removed) {
                throw new \RuntimeException('could not remove ' . $file->getPathname());
            }
        }
        if (!rmdir($path)) {
            throw new \RuntimeException('could not remove ' . $path);
        }
    }

    /** Whether the file changed; a file that already says this is left alone. */
    private function write(string $path, string $contents): bool
    {
        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException('could not create ' . $directory);
        }
        if (is_file($path) && file_get_contents($path) === $contents) {
            return false;
        }
        if (file_put_contents($path, $contents) === false) {
            throw new \RuntimeException('could not write ' . $path);
        }

        return true;
    }
}
