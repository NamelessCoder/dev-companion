<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Paths;

final class Installer
{
    private const SERVER = 'typo3-dev-companion';

    private const BASE = 'references/base.md';
    /** What a skill declares while it is not to be published. */
    private const DRAFT = 'draft';
    /**
     * Where it declares it. `metadata` is the one field the standard leaves to
     * a client — "a map from string keys to string values" for "additional
     * properties not defined by the Agent Skills spec" — and the six it does
     * define are a closed set the reference validator refuses anything outside
     * of. The key is this server's own name because that same paragraph asks
     * for one unique enough not to collide, and a draft published under
     * `--drafts` sits in a project where other tools write the same map.
     */
    private const DRAFT_KEY = self::SERVER . '-status';
    private const STATE_DIRECTORY = '.typo3-dev-companion';
    private const STATE = self::STATE_DIRECTORY . '/state.json';
    /**
     * The same fact as `outdated()` states, in the length the initialize
     * instructions have room for.
     *
     * Short because it competes for a budget that is already spent: what a
     * client keeps is 2048 characters, the prefix naming excluded tools can
     * take most of what is left, and `R-ANS-013` holds the whole assembly to
     * it. So this says the one thing that has to be acted on and leaves what
     * differs to the line on stderr, which no budget bounds.
     */
    public const NOTICE = 'The task skills installed in this project are stale; run typo3-dev-companion update. ';
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
    /**
     * What a client resolves to the project root in `command` and `args`, where
     * its own documentation says it resolves anything there at all.
     *
     * Two of the eleven do, and both spell it this way — `D-DIS-016`, with the
     * table in `documentation/usage/installing.rst`. It is the whole of what
     * makes a shareable entry possible: a plain relative path would resolve
     * against the working directory the client spawns the process in, which the
     * MCP specification does not define and only one of the eleven documents.
     * The variable is named instead of that directory being depended on, which
     * is also the advice the client this server is used with most gives.
     *
     * `.mcp.json` carries no such value even though Claude Code expands one:
     * `${CLAUDE_PROJECT_DIR}` is set in the spawned server's environment rather
     * than in the client's own, so an entry referring to it needs a
     * `${CLAUDE_PROJECT_DIR:-.}` default — and the default is the working
     * directory again. The file is read by more than one client besides.
     */
    private const WORKSPACE = '${workspaceFolder}';
    /** @var array<string, array{skills: string, mcp?: array{format: string, path: string, key: string, shape?: string, root?: string}}> */
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
            'mcp' => [
                'format' => 'json',
                'path' => '.cursor/mcp.json',
                'key' => 'mcpServers',
                'root' => self::WORKSPACE,
            ],
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
            'mcp' => [
                'format' => 'json',
                'path' => '.vscode/mcp.json',
                'key' => 'servers',
                'root' => self::WORKSPACE,
            ],
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
     * `documentation/usage/installing.rst`. A client whose documentation does
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
            . 'amp mcp approve typo3-dev-companion. amp mcp doctor names one still awaiting it.',
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
     * The skills this server publishes, which is also where a skill starts
     * existing for its readers. An answer that names one names it from here —
     * `knowledge/task-intents.json` routes a task to the skill that owns it,
     * and a name this does not carry is one nobody can load.
     *
     * It is the directory minus the drafts rather than a list beside it. A list
     * is a second place the same fact lives, and the two disagree in the
     * direction nobody notices: a draft that was reviewed and added here while
     * its file still declares itself one is published and reads as unfinished,
     * and one taken out of the list while its file says nothing reads as ready
     * and is loadable by nobody. Now publishing is one edit — the declaration
     * comes out of the front matter — and the file in somebody else's project
     * is the same file that decided it would go there.
     *
     * Sorted, because it is written into `.typo3-dev-companion/state.json` and
     * compared against what the last run left; a listing whose order moved
     * would read as a change.
     *
     * @return array<int, string>
     */
    public static function skills(): array
    {
        return self::declaring(false);
    }

    /**
     * The drafts, which is the rest of the same directory.
     *
     * They are published to nobody by default and to a project that asks for
     * them by name, because a draft has one reader before it is finished: the
     * person who has to say whether the workflow is the one they actually run.
     * `documentation/contributing/writing-a-skill.rst` makes that a step, and a step
     * nobody can carry out is one that gets skipped — reading a skill in the
     * repository is not reading it where it loads, and the questions it exists
     * to raise are raised by using it.
     *
     * Kept apart from the published set rather than merged into it under a
     * flag, so that everything answering "what does this server publish" keeps
     * answering it: `knowledge/task-intents.json` routes to a name from there,
     * and a draft reachable by routing is one nobody chose.
     *
     * @return array<int, string>
     */
    public static function drafts(): array
    {
        return self::declaring(true);
    }

    /**
     * What a publication of this set would write, as one string.
     *
     * The record holds the names of what was published, and a name is what does
     * not move when a skill is rewritten: a body edited in this package leaves
     * a project with the old workflow under the current name, and every listing
     * on both sides keeps saying the same twelve words. So the record holds this
     * beside them, and a run that finds it different from what the package would
     * write now is the run that says so.
     *
     * It covers what decides the published bytes: each skill's own files, and
     * `skills/base.md`, which is copied into every one of them. The `.gitignore`
     * each published directory carries is a constant of this class and moves
     * only when this class does.
     *
     * The set is part of it rather than beside it — a project that asked for
     * the drafts and a project that did not are two publications, and the digest
     * of one must not read as current in the other.
     */
    public static function digest(bool $drafts): string
    {
        $digest = hash_init('sha256');
        hash_update_file($digest, Paths::root() . '/skills/' . basename(self::BASE));
        foreach (self::publishSet($drafts) as $skill) {
            hash_update($digest, "\0" . $skill . "\0");
            foreach (Finder::create()->files()->in(Paths::root() . '/skills/' . $skill)->sortByName() as $file) {
                hash_update($digest, $file->getRelativePathname() . "\0");
                hash_update_file($digest, $file->getPathname());
            }
        }

        return hash_final($digest);
    }

    /**
     * Whether what a project has is what this server publishes now, said as the
     * line somebody can act on — null where there is nothing to say.
     *
     * A published skill is a copy, so it goes stale the moment this package
     * moves and nothing on either side notices: the client loads the file it
     * finds, the workflow it carries reads exactly as current, and a tool name
     * that has since been renamed fails at the call rather than at the load.
     * `update` was always the answer and nothing ever said it was due.
     *
     * The record is what this reads, so a project this package never installed
     * into is silent rather than wrong. Two things make it speak: a skills
     * directory that no longer holds what was published there — `R-DIS-024` has
     * those ignoring themselves, which is also what `git clean -xdf` takes with
     * it — and a digest that no longer matches. A record written before the
     * digest existed counts as the second, because "not established" and
     * "current" are what this exists to keep apart.
     */
    public static function outdated(string $project): ?string
    {
        $state = self::readState($project);
        if ($state['agents'] === [] && $state['skills'] === []) {
            return null;
        }

        $reasons = [];
        foreach (self::unpublished($project, $state) as $path) {
            $reasons[] = 'nothing is published at ' . $path;
        }
        if ($state['digest'] === '') {
            $reasons[] = 'the record predates this check and says nothing about what was published';
        } elseif ($state['digest'] !== self::digest($state['drafts'] !== [])) {
            $reasons[] = 'this server publishes something other than what was published here';
        }
        if ($reasons === []) {
            return null;
        }

        // The drafts are a per-run choice, so an update that does not ask for
        // them takes them out. Somebody acting on this line wants the project
        // it has, not the one a shorter command would leave.
        $drafts = $state['drafts'] === []
            ? ''
            : ' --drafts is what keeps the unreviewed drafts this project has: ' . implode(', ', $state['drafts']) . '.';

        return 'the task skills in this project are not the ones this server publishes now — '
            . implode('; ', $reasons) . '. Run typo3-dev-companion update' . ($drafts === '' ? '' : ' --drafts') . '.'
            . $drafts;
    }

    /**
     * The skills directories a recorded client reads that no longer hold what
     * was published into them.
     *
     * One `is_dir` per recorded skill rather than a comparison of what is in
     * it: the question here is whether the publication is still there at all,
     * and what its files say is the digest's half.
     *
     * @param array{skills: list<string>, drafts: list<string>, agents: list<string>, digest: string} $state
     * @return list<string>
     */
    private static function unpublished(string $project, array $state): array
    {
        $unpublished = [];
        foreach ($state['agents'] as $agent) {
            $path = self::definition($agent)['skills'];
            if (in_array($path, $unpublished, true)) {
                continue;
            }
            foreach ([...$state['skills'], ...$state['drafts']] as $skill) {
                if (!is_dir($project . '/' . $path . '/' . $skill)) {
                    $unpublished[] = $path;

                    break;
                }
            }
        }

        return $unpublished;
    }

    /**
     * The skills a run publishes, which is this package's own set and the
     * drafts where the run asked for them.
     *
     * @return array<int, string>
     */
    private static function publishSet(bool $drafts): array
    {
        $publish = [...self::skills(), ...($drafts ? self::drafts() : [])];
        sort($publish);

        return $publish;
    }

    /**
     * The skills directory, split on the one line that decides where a skill
     * goes. Two walks over one directory asking opposite questions is one walk
     * with the answer as its argument.
     *
     * @return array<int, string>
     */
    private static function declaring(bool $draft): array
    {
        $skills = [];
        foreach (Finder::create()->directories()->in(Paths::root() . '/skills')->depth(0)->sortByName() as $skill) {
            $body = $skill->getPathname() . '/SKILL.md';
            if (is_file($body) && self::draft((string) file_get_contents($body)) === $draft) {
                $skills[] = $skill->getFilename();
            }
        }

        return $skills;
    }

    /**
     * Whether a skill says it is not to be published.
     *
     * Read with a YAML parser rather than matched with a pattern, so that what
     * this sees and what a client reading the same file sees are one reading. A
     * pattern agrees with the parser on the line it was written for and parts
     * from it on the rest of YAML: a quoted value, a mapping written inline, a
     * key that repeats. Front matter no parser can read is not a declaration
     * either, which is why that is false rather than an exception.
     */
    public static function draft(string $body): bool
    {
        if (preg_match('/\A---\R(.*?)\R---\R/s', $body, $block) !== 1) {
            return false;
        }

        try {
            $matter = Yaml::parse($block[1]);
        } catch (ParseException) {
            return false;
        }

        return is_array($matter)
            && is_array($matter['metadata'] ?? null)
            && ($matter['metadata'][self::DRAFT_KEY] ?? null) === self::DRAFT;
    }

    /**
     * The clients `--agent=` accepts, for the entrypoint's own help.
     *
     * @return array<int, string>
     */
    public static function agents(): array
    {
        return array_keys(self::AGENTS);
    }

    public function install(?string $agent, bool $drafts = false): string
    {
        return $this->setUp([$agent ?? self::GENERIC], $drafts);
    }

    /**
     * Bring the clients installed here up to date.
     *
     * Without an agent that is every client `.typo3-dev-companion/state.json`
     * records, which is the case that matters: a project is usually worked on
     * by more than one client, and naming them one at a time meant remembering
     * which of them the project had — a list nobody keeps, so the second client
     * silently kept the skills of the version it was installed with.
     *
     * The setup that named no client is one of them, so it is refreshed the
     * same way and needs no case of its own.
     *
     * The drafts are the one thing an update does not carry over. They are a
     * per-run choice on both commands, so an update that does not ask for them
     * takes them out again and says so — which is the way back off a draft, and
     * the reason there is no second flag for it. Sticky would be the more
     * convenient reading and the wrong one: an unreviewed workflow that stays
     * in a project because somebody once tried it is exactly what publishing is
     * a deliberate edit to prevent.
     *
     * A project with nothing installed is told so and is not a failure. This is
     * the command a project wires into Composer's `post-update-cmd`, a script
     * that exits non-zero fails the whole Composer run, and the record ignores
     * itself (`R-DIS-024`) — so every colleague who never ran `install` would
     * have their `composer update` fail over a dev tool they do not use.
     * `D-DIS-014` is what that was priced against.
     */
    public function update(?string $agent, bool $drafts = false): string
    {
        $update = $agent !== null ? [$agent] : self::readState($this->project)['agents'];
        if ($update === []) {
            return 'Nothing is installed here, so there is nothing to update. Run install, or '
                . 'install --agent=<client> for a client of its own.';
        }

        return $this->setUp($update, $drafts);
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
    private function setUp(array $names, bool $drafts): string
    {
        $state = self::readState($this->project);

        $messages = [];
        $published = [];
        foreach ($names as $name) {
            $definition = self::definition($name);
            if (isset($definition['mcp'])) {
                $messages[] = $this->installAgentConfiguration($name, $definition['mcp']);
            }
            // Clients that share a skills directory — .agents/skills is four of
            // them — are one publication, not four identical ones.
            if (in_array($definition['skills'], $published, true)) {
                continue;
            }
            $published[] = $definition['skills'];
            $messages[] = $this->publishSkills(
                $definition['skills'],
                [...$state['skills'], ...$state['drafts']],
                $drafts,
            );
        }

        return implode("\n", $this->record($state, $names, $messages, $drafts));
    }

    /**
     * What the run leaves behind: the clients installed here, in the directory
     * that ignores itself.
     *
     * The record is written once per run rather than per client, because it is
     * one file for the whole project. Writing it inside the loop would let the
     * first client of a run decide what the second one sees.
     *
     * @param array{skills: list<string>, drafts: list<string>, agents: list<string>, digest: string} $state
     * @param list<string> $installed
     * @param list<string> $messages
     * @return list<string>
     */
    private function record(array $state, array $installed, array $messages, bool $drafts): array
    {
        $agents = array_values(array_unique([...$state['agents'], ...$installed]));
        sort($agents);
        $this->writeJson($this->project . '/' . self::STATE, [
            'version' => 1,
            'agents' => $agents,
            'skills' => self::skills(),
            // Their own key rather than the same list, because what is in this
            // project unreviewed is the question somebody opens this file to
            // answer, and a name among the published ones does not answer it.
            'drafts' => $drafts ? self::drafts() : [],
            // What the names cannot say: which version of them is down there.
            'digest' => self::digest($drafts),
        ]);
        $this->write($this->project . '/' . self::STATE_DIRECTORY . '/.gitignore', self::IGNORE_ALL);

        return $messages;
    }

    /**
     * What to write for a name the project recorded, which is a client's or the
     * one the generic setup goes by.
     *
     * @return array{skills: string, mcp?: array{format: string, path: string, key: string, shape?: string, root?: string}}
     */
    private static function definition(string $name): array
    {
        return $name === self::GENERIC ? self::GENERIC_DEFINITION : self::agent($name);
    }

    /** @return array{skills: string, mcp?: array{format: string, path: string, key: string, shape?: string, root?: string}} */
    private static function agent(string $agent): array
    {
        if (!isset(self::AGENTS[$agent])) {
            throw new \RuntimeException(
                'unsupported agent "' . $agent . '"; supported: ' . implode(', ', array_keys(self::AGENTS)),
            );
        }

        return self::AGENTS[$agent];
    }

    /** @param array{format: string, path: string, key: string, shape?: string, root?: string} $mcp */
    private function installAgentConfiguration(string $agent, array $mcp): string
    {
        $written = $mcp['format'] === 'toml'
            ? $this->installTomlConfiguration($mcp['path'], $mcp['key'])
            : $this->installJsonConfiguration($agent, $mcp);

        return $written . $this->remaining($agent, $mcp['root'] ?? null);
    }

    /**
     * That the entry just written is true on this machine and nowhere else.
     *
     * Every file it goes into is documented by its own client as the shared,
     * committed one, and the command in this entry is an absolute host path. So
     * it is said rather than fixed, and said where the person who can act on it
     * is looking.
     *
     * Two things spare a client the sentence, and both replace the host path
     * with one the project can share: `ddev exec`, which runs in the container's
     * project root, and a client that resolves `self::WORKSPACE`. Where neither
     * is available nothing else can be written — a plain relative path resolves
     * against the working directory the client spawns the process in, and the
     * MCP specification defines none — so this is the answer rather than the
     * fallback. `D-DIS-016` is the reading, per client.
     */
    private const HOST_SPECIFIC = 'The command in this entry is this checkout\'s absolute path, valid on '
        . 'this machine only, while the file it is in is the one that client documents as shared and '
        . 'committed. Add it to the project\'s .gitignore, or let each person run this install.';

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
    private function remaining(string $agent, ?string $root): string
    {
        $lines = array_filter([self::REMAINING[$agent] ?? '', $this->hostSpecific($root) ? self::HOST_SPECIFIC : '']);

        return implode('', array_map(
            static fn(string $line): string => "\n  " . wordwrap($line, 74, "\n  "),
            $lines,
        ));
    }

    /**
     * Whether the entry names this checkout rather than a path inside the
     * project.
     *
     * Asked of what was written rather than of the conditions that decided it,
     * so the sentence and the entry cannot come apart: a client that gains a
     * shareable shape stops being told to ignore the file in the same edit.
     */
    private function hostSpecific(?string $root): bool
    {
        return $this->startedBy($root)['args'] === [$this->entrypoint];
    }

    /** @param array{format: string, path: string, key: string, shape?: string, root?: string} $mcp */
    private function installJsonConfiguration(string $agent, array $mcp): string
    {
        $relativePath = $mcp['path'];
        $key = $mcp['key'];
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
                $relativePath . ' already has a different typo3-dev-companion server; refusing to replace it',
            );
        }
        $target[self::SERVER] = $this->jsonServer($mcp['shape'] ?? null, $mcp['root'] ?? null)
            + $this->carriedOver($existing);

        return $this->message($this->writeJson($path, $configuration), $path);
    }

    /**
     * What an entry already there keeps: every field this package does not
     * write itself.
     *
     * The command and the shape around it are a property of the project and are
     * rewritten on every run, which is what `update` is for. The rest is the
     * caller's, and `env` is why this exists: a `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`
     * written into the entry by hand was replaced away by the next install, and
     * the tools it had taken out came back with nothing said on either side —
     * `D-AUD-005`.
     *
     * @return array<array-key, mixed>
     */
    private function carriedOver(mixed $existing): array
    {
        if (!is_array($existing)) {
            return [];
        }

        return array_diff_key($existing, array_flip(['type', 'command', 'args', 'enabled']));
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
    private function jsonServer(?string $shape = null, ?string $root = null): array
    {
        ['command' => $command, 'args' => $args] = $this->startedBy($root);
        if ($shape === 'opencode') {
            return ['type' => 'local', 'enabled' => true, 'command' => [$command, ...$args]];
        }

        return ['type' => 'stdio', 'command' => $command, 'args' => $args];
    }

    /**
     * What starts this server for one client, and the only place the path in an
     * entry is decided.
     *
     * Three shapes, in the order they are available. A project that does not
     * have this server as a dependency has none of the first two: no path
     * inside it names a checkout somewhere else, so the host path is the only
     * one that exists there.
     *
     * DDEV comes before the variable because it is not only a way of naming the
     * path: the entry has to start the container's PHP, which sees the project
     * directory rather than the host, and a `${...}` expanded to a host path
     * would name a directory that container has never had.
     *
     * @param ?string $root what this client resolves to the project root, null
     *     where its documentation says it resolves nothing there
     * @return array{command: string, args: list<string>}
     */
    private function startedBy(?string $root): array
    {
        $installed = $this->installedEntrypoint();
        if ($installed === null) {
            return ['command' => 'php', 'args' => [$this->entrypoint]];
        }
        if (is_file($this->project . '/.ddev/config.yaml')) {
            return ['command' => 'ddev', 'args' => ['exec', 'php', $installed]];
        }
        if ($root !== null) {
            return ['command' => 'php', 'args' => [$root . '/' . $installed]];
        }

        return ['command' => 'php', 'args' => [$this->entrypoint]];
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
            $header = substr_count(substr($configuration, 0, (int) strpos($configuration, $section)), "\n") + 1;
            $configuration = str_replace(
                $section,
                $this->rewrittenTomlSection($section, $key, $relativePath, $header),
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

    /**
     * The section as it should read, with every line this package does not own
     * kept where the caller wrote it.
     *
     * The two lines this package owns are `command` and `args`, which are a
     * property of the project and are rewritten on every run. Everything else
     * in the section is the caller's — `env` above all, since it is the only
     * place a TOML client can carry `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`, and it was being
     * deleted by the `install` that was supposed to keep the entry current
     * (measured 2026-08-04 in a fixture project, `D-AUD-006`).
     *
     * @param int $number which line of the file the section header is, so a
     *     refusal can name the line in the file rather than in the section
     */
    private function rewrittenTomlSection(string $section, string $key, string $relativePath, int $number): string
    {
        $lines = preg_split('/(?<=\n)/', $section) ?: [];
        $header = (string) array_shift($lines);

        $body = [];
        $words = [];
        foreach ($lines as $index => $line) {
            $name = $this->tomlKey($line, $relativePath, $key, $number + $index + 1);
            if ($name === 'command' || $name === 'args') {
                preg_match_all('/["\']([^"\']*)["\']/', $line, $quoted);
                $words = [...$words, ...$quoted[1]];
            }
            $body[$index] = ['name' => $name, 'text' => $line];
        }
        // An entry starting something else is somebody's own, and which command
        // it starts is the only thing that says so — the caller's own keys say
        // nothing about whose server this is.
        if ($words !== [] && !$this->namesThisServer($words)) {
            throw new \RuntimeException(
                $relativePath . ' already has a different typo3-dev-companion server; refusing to replace it',
            );
        }

        // Each of the two goes back where the caller has it, so the section
        // keeps its order; one the section does not carry goes under the
        // header, which is where a section this writes from scratch has it.
        $ours = $this->ownTomlLines($key);
        $unwritten = $ours;
        $rewritten = '';
        foreach ($body as $line) {
            $name = $line['name'];
            if ($name !== null && isset($ours[$name])) {
                $rewritten .= $unwritten[$name] ?? '';
                unset($unwritten[$name]);

                continue;
            }
            $rewritten .= $line['text'];
        }

        return $header . implode('', $unwritten) . $rewritten;
    }

    /**
     * The key a line in a section assigns, null where it assigns none.
     *
     * A blank line and a comment carry no key and are kept as they are. Every
     * other line has to be a whole `key = value`: this path rewrites two lines
     * of a section and keeps the rest by copying them, and a value continued on
     * the next line would be copied without the key that opens it. Refusing is
     * what is left, because the alternative on record is deleting it.
     */
    private function tomlKey(string $line, string $relativePath, string $key, int $number): ?string
    {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            return null;
        }
        $matched = preg_match('/^([A-Za-z0-9_-]+|"[^"]*")\s*=\s*(\S.*)$/', $trimmed, $assignment) === 1;
        if (!$matched || !$this->isWholeTomlValue($assignment[2])) {
            throw new \RuntimeException(sprintf(
                '%s line %d, in [%s.%s], is not a key and a value on one line, so this cannot rewrite the '
                . 'section without dropping it; refusing. Put the value on one line, or take the section out '
                . 'and run this again',
                $relativePath,
                $number,
                $key,
                self::SERVER,
            ));
        }

        return trim($assignment[1], '"');
    }

    /** Whether a value ends on the line it started on: no open string, array or inline table. */
    private function isWholeTomlValue(string $value): bool
    {
        $bare = (string) preg_replace('/"(?:[^"\\\\]|\\\\.)*"|\'[^\']*\'/', '', $value);
        if (str_contains($bare, '"') || str_contains($bare, "'")) {
            return false;
        }
        $bare = (string) preg_replace('/#.*$/', '', $bare);

        return substr_count($bare, '[') === substr_count($bare, ']')
            && substr_count($bare, '{') === substr_count($bare, '}');
    }

    /**
     * The two lines this package writes, by the key each one assigns.
     *
     * @return array<string, string>
     */
    private function ownTomlLines(string $key): array
    {
        $lines = [];
        foreach (explode("\n", trim($this->expectedTomlSection($key), "\n")) as $line) {
            if (str_contains($line, '=')) {
                $lines[trim(strstr($line, '=', true) ?: '')] = $line . "\n";
            }
        }

        return $lines;
    }

    private function message(bool $written, string $path): string
    {
        return ($written ? 'Configured' : 'Reused') . ' typo3-dev-companion in ' . $path . '.';
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
            '/^\[' . preg_quote($key, '/') . '\.typo3-dev-companion\]\R(?:(?!^\[).*(?:\R|$))*/m',
            $configuration,
            $matches,
        ) !== 1) {
            return null;
        }

        return $matches[0];
    }

    /** @param list<string> $previousSkills */
    private function publishSkills(string $skillsPath, array $previousSkills, bool $drafts): string
    {
        $publish = self::publishSet($drafts);

        $messages = [];
        foreach ($publish as $skill) {
            $messages[] = $this->publishSkill($skillsPath, $skill);
        }
        foreach (array_diff($previousSkills, $publish) as $skill) {
            $this->removeDirectory($this->project . '/' . $skillsPath . '/' . $skill);
            $messages[] = 'Removed stale ' . $skill . ' from ' . $this->project . '/' . $skillsPath . '.';
        }
        if ($drafts && self::drafts() !== []) {
            $messages[] = 'Drafts published here: ' . implode(', ', self::drafts())
                . '. Nobody has reviewed these, and the next update takes them out again unless it is'
                . ' asked for them too.';
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
     * @return array{skills: list<string>, drafts: list<string>, agents: list<string>, digest: string}
     */
    private static function readState(string $project): array
    {
        $path = $project . '/' . self::STATE;
        if (!is_file($path)) {
            return ['skills' => [], 'drafts' => [], 'agents' => [], 'digest' => ''];
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
        // Absent in a state file written before drafts could be installed, and
        // absent is right there: that project has none.
        $drafts = array_values(array_filter(
            is_array($state['drafts'] ?? null) ? $state['drafts'] : [],
            static fn(mixed $draft): bool => is_string($draft) && $draft !== '',
        ));
        $agents = array_values(array_filter(
            is_array($state['agents'] ?? null) ? $state['agents'] : [],
            static fn(mixed $agent): bool => is_string($agent)
                && (isset(self::AGENTS[$agent]) || $agent === self::GENERIC),
        ));
        // Absent in a state file written before the publication was recorded
        // as anything but names, and absent is not current there: what is in
        // the project was never established, and `outdated()` says so rather
        // than reading silence as a match.
        $digest = is_string($state['digest'] ?? null) ? $state['digest'] : '';

        return ['skills' => $skills, 'drafts' => $drafts, 'agents' => $agents, 'digest' => $digest];
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
