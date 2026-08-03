<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Server;

use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Paths;

final class Installer
{
    private const SERVER = 'typo3-cms-mcp';
    private const SKILLS = [
        'typo3-backend-module-development',
        'typo3-content-element-development',
        'typo3-core-patch-development',
        'typo3-core-patch-review',
        'typo3-extension-conformance',
        'typo3-extension-documentation',
        'typo3-extension-release',
        'typo3-extension-testing',
        'typo3-extension-upgrade',
    ];
    private const BASE = 'references/base.md';
    private const STATE_DIRECTORY = '.typo3-cms-mcp';
    private const STATE = self::STATE_DIRECTORY . '/state.json';
    /**
     * What a directory this package owns says to git about itself: everything
     * below it, this file included, so the directory is invisible and no line
     * about it is owed to anybody else's file.
     */
    private const IGNORE_ALL = "*\n";
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
    /**
     * What the client still needs before a tool in the entry just written can
     * be called, said beside the line that reports the entry.
     *
     * Writing the file registers the server with nothing. A client that scopes
     * project servers behind an approval has not been asked yet, and a session
     * that was already open when the file was written is running against the
     * configuration it started with — both end with an entry that is entirely
     * correct and no tool in the session, which is where two sessions in one
     * project went. Which of the two applies is the client's property and not
     * this package's, so it is said per client and at the terminal, because the
     * person who can finish the install is looking at one at that moment.
     *
     * Each line is what that client's own documentation says, read on
     * 2026-08-02 and sourced per client in
     * `documentation/clients/installing.md`. A client whose documentation does
     * not answer says that rather than the likely answer: the sentence is acted
     * on by somebody who cannot check it, and there a guess is indistinguishable
     * from a fact. The two clients that need nothing say that too — "nothing is
     * left" is the answer a reader most needs to be able to trust.
     *
     * @var array<string, string>
     */
    private const REMAINING = [
        self::GENERIC => '.mcp.json is read by more than one client and what is left is each '
            . 'one\'s own; install --agent=<client> says it for that client. Claude Code, which '
            . 'reads this file at project scope, reads it when a session starts and asks you to '
            . 'approve a project server the first time it sees one.',
        'claude' => 'Claude Code reads .mcp.json when a session starts, so a session that was '
            . 'already open does not have this entry yet, and it asks you to approve a project '
            . 'server the first time it sees one: approve at that prompt or in /mcp, and run '
            . 'claude mcp reset-project-choices if it was once refused.',
        'amp' => 'Amp requires a workspace server in .amp/settings.json to be approved before it '
            . 'runs: approve at the prompt when it is first detected, or run '
            . 'amp mcp approve typo3-cms-mcp. amp mcp doctor names one still awaiting it.',
        'copilot' => 'VS Code asks you to confirm that you trust a workspace server before it '
            . 'starts it, so start it from the MCP view and confirm there. chat.mcp.autoStart, '
            . 'still experimental, restarts it when this file changes.',
        'cursor' => 'Cursor lists servers under Customize, where one can be toggled off, and asks '
            . 'for approval before using an MCP tool. Its documentation does not say whether a '
            . 'window that was already open reads a new .cursor/mcp.json, so check that list.',
        'junie' => 'Junie enables a server imported from .junie/mcp/mcp.json by default. Its '
            . 'documentation does not say whether an IDE that was already open reads a new one; '
            . 'the list is Settings | Tools | Junie | MCP Settings.',
        'codex' => 'Codex reads MCP servers from a project .codex/config.toml in trusted projects '
            . 'only, so answer the trust prompt for this directory. Its documentation does not say '
            . 'whether a running session reads the file again; codex mcp list reports what it has.',
        'factory' => 'Droid reloads when an mcp.json changes, so this server is available '
            . 'immediately and nothing is left here. Each of its tools is approved on first use, '
            . 'and droid mcp permissions keeps that approval.',
        'kiro' => 'Kiro applies a saved mcp.json and reconnects the server, so nothing is left '
            . 'here. A tool that autoApprove does not name is still asked about on the call.',
        'opencode' => 'opencode.json switches a server off with enabled: false, which this entry '
            . 'does not. Its documentation does not say whether a session that was already open '
            . 'reads the file again.',
        'zed' => 'Zed reads MCP servers from a project .zed/settings.json, but every worktree '
            . 'starts in Restricted Mode, where those settings are not applied and no server is '
            . 'started: trust this directory at the exclamation mark in the title bar, or with '
            . 'workspace::ToggleWorktreeSecurity. Whether a window that was already open reads '
            . 'the file again is not documented.',
        'grok' => 'Grok reads mcp_servers from a project .grok/config.toml, walking up to the git '
            . 'root. Its documentation does not say whether a running session reads the file '
            . 'again; grok mcp doctor reports what it has.',
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
     * Without an agent that is every client `.typo3-cms-mcp/state.json`
     * records, which is the case that matters: a project is usually worked on
     * by more than one client, and naming them one at a time meant remembering
     * which of them the project had — a list nobody keeps, so the second client
     * silently kept the skills of the version it was installed with.
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
     * What the run leaves behind: the clients installed here, in the directory
     * that ignores itself.
     *
     * The record is written once per run rather than per client, because it is
     * one file for the whole project. Writing it inside the loop would let the
     * first client of a run decide what the second one sees.
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
        $this->write($this->project . '/' . self::STATE_DIRECTORY . '/.gitignore', self::IGNORE_ALL);

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
        $written = $mcp['format'] === 'toml'
            ? $this->installTomlConfiguration($mcp['path'], $mcp['key'])
            : $this->installJsonConfiguration($mcp['path'], $mcp['key'], $mcp['shape'] ?? null, $agent);

        return $written . $this->remaining($agent);
    }

    /**
     * The step left, indented under the entry it belongs to.
     *
     * Under, rather than as a line of its own, because the run writes an entry
     * per client and a sentence about one of them floating among nine successes
     * would have to name which. It is said on every run and not only on the run
     * that wrote the file: what is left is a property of the client and the
     * session, and neither is changed by this command having found the entry
     * already correct.
     */
    private function remaining(string $agent): string
    {
        $remaining = self::REMAINING[$agent] ?? '';

        return $remaining === '' ? '' : "\n  " . wordwrap($remaining, 74, "\n  ");
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
        // The directory says to git what it is, rather than the project's own
        // `.gitignore` saying it on its behalf. Everything in here is written
        // by this package and replaced whole on the next run, and the skills
        // beside it — the project's own — are not covered by a word of it.
        $this->write($target . '/.gitignore', self::IGNORE_ALL);

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
        foreach (Finder::create()->files()->in($source)->sortByName() as $file) {
            $contents = file_get_contents($file->getPathname());
            if ($contents === false) {
                throw new \RuntimeException('could not read ' . $file->getPathname());
            }
            $this->write($target . '/' . $file->getRelativePathname(), $contents);
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
        // The finder walks a directory before what is in it, so reversed it
        // hands over the deepest entry first — which is the order the entries
        // can be removed in. A symlink to a directory is unlinked rather than
        // descended into, the way the walk itself leaves it alone.
        $entries = Finder::create()->in($path)->ignoreDotFiles(false)->ignoreVCS(false)->reverseSorting();
        foreach ($entries as $entry) {
            $removed = $entry->isDir() && !$entry->isLink()
                ? rmdir($entry->getPathname())
                : unlink($entry->getPathname());
            if (!$removed) {
                throw new \RuntimeException('could not remove ' . $entry->getPathname());
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
