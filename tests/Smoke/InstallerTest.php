<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Server\Installer;
use Typo3CmsMcp\Tests\Support\Directory;

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
            Directory::remove($directory);
        }
    }

    /**
     * The entry is the caller's file, and the run owns the command in it and
     * nothing else. `env` is where that matters: an exclusion written in by
     * hand was replaced away by the next install, and the tools it had taken
     * out came back with nothing said — the same silence `D-AUD-005` is about,
     * reached from the other side.
     */
    #[Test]
    public function installKeepsWhatTheCallerPutInTheEntryAndRewritesOnlyTheCommand(): void
    {
        $directory = $this->directory();
        file_put_contents($directory . '/.mcp.json', json_encode([
            'mcpServers' => [
                'typo3-cms-mcp' => [
                    'type' => 'stdio',
                    'command' => 'php',
                    'args' => ['/gone/bin/typo3-cms-mcp'],
                    'env' => ['TYPO3_MCP_EXCLUDE_TOOLS' => 'typo3_icon_lookup'],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        try {
            $stderr = '';
            self::assertSame(0, $this->install($directory, $stderr), $stderr);

            $entry = json_decode(
                (string) file_get_contents($directory . '/.mcp.json'),
                true,
                flags: JSON_THROW_ON_ERROR,
            )['mcpServers']['typo3-cms-mcp'];

            self::assertSame(['TYPO3_MCP_EXCLUDE_TOOLS' => 'typo3_icon_lookup'], $entry['env']);
            self::assertSame([Paths::root() . '/bin/typo3-cms-mcp'], $entry['args'], 'the command is still rewritten');
        } finally {
            Directory::remove($directory);
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
            Directory::remove($directory);
        }
    }

    #[Test]
    public function codexInstallAndUpdatePreserveConfigurationAndTrackTheirSkillsCentrally(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.codex', 0777, true));
        $unrelated = "model = \"gpt-5\"\n\n[features]\nweb_search = true\n";
        file_put_contents($directory . '/.codex/config.toml', $unrelated);
        file_put_contents($directory . '/.gitignore', "/vendor/\n");

        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);
            $configuration = (string) file_get_contents($directory . '/.codex/config.toml');
            self::assertStringStartsWith($unrelated, $configuration);
            self::assertStringContainsString('[mcp_servers.typo3-cms-mcp]', $configuration);
            self::assertStringContainsString(Paths::root() . '/bin/typo3-cms-mcp', $configuration);

            $skill = $directory . '/.agents/skills/typo3-backend-module-development/SKILL.md';
            $state = $directory . '/.typo3-cms-mcp/state.json';
            self::assertFileEquals(
                Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
                $skill,
            );
            self::assertFileDoesNotExist(dirname($skill) . '/.typo3-cms-mcp.json');
            self::assertFileExists($state);
            self::assertSame([
                'version' => 1,
                'agents' => ['codex'],
                'skills' => [
                    'typo3-backend-module-development',
                    'typo3-content-element-development',
                    'typo3-core-patch-development',
                    'typo3-core-patch-review',
                    'typo3-development-installation',
                    'typo3-extension-cleanup',
                    'typo3-extension-conformance',
                    'typo3-extension-documentation',
                    'typo3-extension-testing',
                    'typo3-extension-upgrade',
                ],
            ], json_decode((string) file_get_contents($state), true, flags: JSON_THROW_ON_ERROR));
            // The project's own .gitignore is what it was before the install,
            // and every directory this package wrote says `*` about itself.
            self::assertSame("/vendor/\n", file_get_contents($directory . '/.gitignore'));
            self::assertSame("*\n", file_get_contents($directory . '/.typo3-cms-mcp/.gitignore'));
            foreach (Installer::skills() as $publishedSkill) {
                self::assertSame(
                    "*\n",
                    file_get_contents($directory . '/.agents/skills/' . $publishedSkill . '/.gitignore'),
                );
            }
            foreach (array_diff(Installer::skills(), ['typo3-backend-module-development']) as $publishedSkill) {
                self::assertFileEquals(
                    Paths::root() . '/skills/' . $publishedSkill . '/SKILL.md',
                    $directory . '/.agents/skills/' . $publishedSkill . '/SKILL.md',
                );
            }
            foreach (['checklist', 'phpunit', 'playwright', 'static-quality'] as $reference) {
                self::assertFileEquals(
                    Paths::root() . '/skills/typo3-extension-testing/references/' . $reference . '.md',
                    $directory . '/.agents/skills/typo3-extension-testing/references/' . $reference . '.md',
                );
            }
            // The base is one file here and a copy in every published skill:
            // each of them lands in somebody else's project alone, so a skill
            // pointing out of its own directory would resolve in this
            // repository and nowhere it is actually read.
            foreach (Installer::skills() as $publishedSkill) {
                self::assertFileEquals(
                    Paths::root() . '/skills/base.md',
                    $directory . '/.agents/skills/' . $publishedSkill . '/references/base.md',
                );
            }

            $before = [
                'configuration' => file_get_contents($directory . '/.codex/config.toml'),
                'skill' => file_get_contents($skill),
                'state' => file_get_contents($state),
                'gitignore' => file_get_contents($directory . '/.gitignore'),
            ];
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);
            self::assertSame(0, $this->execute($directory, ['update', '--agent=codex'], $stderr), $stderr);
            self::assertSame($before['configuration'], file_get_contents($directory . '/.codex/config.toml'));
            self::assertSame($before['skill'], file_get_contents($skill));
            self::assertSame($before['state'], file_get_contents($state));
            self::assertSame($before['gitignore'], file_get_contents($directory . '/.gitignore'));
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function ddevProjectUsesTheContainerPhpForMcpAndPublishesTheSkillToTheProject(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.ddev'));
        file_put_contents($directory . '/.ddev/config.yaml', "name: fixture\n");
        $this->installEntrypoint($directory, 'vendor/bin');

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
            Directory::remove($directory);
        }
    }

    #[Test]
    public function ddevProjectNamesTheEntrypointAtTheBinDirectoryItDeclares(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.ddev'));
        file_put_contents($directory . '/.ddev/config.yaml', "name: fixture\n");
        file_put_contents($directory . '/composer.json', json_encode([
            'config' => ['vendor-dir' => '.build/vendor', 'bin-dir' => '.build/bin'],
        ], JSON_THROW_ON_ERROR));
        $this->installEntrypoint($directory, '.build/bin');

        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install'], $stderr), $stderr);

            $configuration = json_decode(
                (string) file_get_contents($directory . '/.mcp.json'),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
            self::assertSame([
                'type' => 'stdio',
                'command' => 'ddev',
                'args' => ['exec', 'php', '.build/bin/typo3-cms-mcp'],
            ], $configuration['mcpServers']['typo3-cms-mcp']);
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function ddevProjectThatNeverRequiredTheServerKeepsTheAbsoluteEntrypoint(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.ddev'));
        file_put_contents($directory . '/.ddev/config.yaml', "name: fixture\n");

        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install'], $stderr), $stderr);

            $configuration = json_decode(
                (string) file_get_contents($directory . '/.mcp.json'),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
            self::assertSame([
                'type' => 'stdio',
                'command' => 'php',
                'args' => [Paths::root() . '/bin/typo3-cms-mcp'],
            ], $configuration['mcpServers']['typo3-cms-mcp']);
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function updateRewritesTheEntryAProjectHasOutgrown(): void
    {
        $directory = $this->directory();
        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install'], $stderr), $stderr);

            // The project requires the server and gains DDEV after the fact,
            // which is what the entry written above no longer says.
            self::assertTrue(mkdir($directory . '/.ddev'));
            file_put_contents($directory . '/.ddev/config.yaml', "name: fixture\n");
            $this->installEntrypoint($directory, 'vendor/bin');

            self::assertSame(0, $this->execute($directory, ['update'], $stderr), $stderr);
            $configuration = json_decode(
                (string) file_get_contents($directory . '/.mcp.json'),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
            self::assertSame([
                'type' => 'stdio',
                'command' => 'ddev',
                'args' => ['exec', 'php', 'vendor/bin/typo3-cms-mcp'],
            ], $configuration['mcpServers']['typo3-cms-mcp']);
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function updateRefusesToReplaceAnotherCommand(): void
    {
        $directory = $this->directory();
        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install'], $stderr), $stderr);
            $original = '{"mcpServers":{"typo3-cms-mcp":{"command":"somewhere-else"}}}';
            file_put_contents($directory . '/.mcp.json', $original);

            self::assertSame(1, $this->execute($directory, ['update'], $stderr));
            self::assertStringContainsString('refusing to replace', $stderr);
            self::assertSame($original, file_get_contents($directory . '/.mcp.json'));
        } finally {
            Directory::remove($directory);
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
            Directory::remove($directory);
        }
    }

    #[Test]
    public function codexUpdateRewritesTheSectionAndKeepsTheRestOfTheFile(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.codex', 0777, true));
        $unrelated = "model = \"gpt-5\"\n\n";
        $stale = "[mcp_servers.typo3-cms-mcp]\ncommand = \"php\"\nargs = [\"/elsewhere/bin/typo3-cms-mcp\"]\n\n";
        $below = "[features]\nweb_search = true\n";
        file_put_contents($directory . '/.codex/config.toml', $unrelated . $stale . $below);
        self::assertTrue(mkdir($directory . '/.typo3-cms-mcp'));
        file_put_contents(
            $directory . '/.typo3-cms-mcp/state.json',
            json_encode(['version' => 1, 'agents' => ['codex'], 'skills' => []], JSON_THROW_ON_ERROR),
        );

        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['update'], $stderr), $stderr);
            $configuration = (string) file_get_contents($directory . '/.codex/config.toml');
            self::assertSame(
                $unrelated
                . "[mcp_servers.typo3-cms-mcp]\ncommand = \"php\"\nargs = [\"" . Paths::root()
                . "/bin/typo3-cms-mcp\"]\n\n"
                . $below,
                $configuration,
            );
        } finally {
            Directory::remove($directory);
        }
    }

    /**
     * The TOML half of what `453e439` fixed for JSON. The section was replaced
     * whole, so the `env` block that is the only place a TOML client can carry
     * `TYPO3_MCP_EXCLUDE_TOOLS` was gone after an `install` — measured
     * 2026-08-04, `D-AUD-006`.
     */
    #[Test]
    public function codexInstallKeepsTheLinesOfTheSectionItDoesNotOwn(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.codex', 0777, true));
        $original = "model = \"gpt-5\"\n\n[mcp_servers.typo3-cms-mcp]\n# kept\ncommand = \"php\"\n"
            . "args = [\"/elsewhere/bin/typo3-cms-mcp\"]\n"
            . "env = { TYPO3_MCP_EXCLUDE_TOOLS = \"typo3_icon_lookup\" }\nstartup_timeout_sec = 30\n\n"
            . "[features]\nweb_search = true\n";
        file_put_contents($directory . '/.codex/config.toml', $original);

        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);
            self::assertSame(
                str_replace('/elsewhere/bin/typo3-cms-mcp', Paths::root() . '/bin/typo3-cms-mcp', $original),
                file_get_contents($directory . '/.codex/config.toml'),
            );
        } finally {
            Directory::remove($directory);
        }
    }

    /**
     * A value continued on the next line is the reason this path replaced the
     * section rather than editing it: keeping a line means knowing where one
     * ends. Refusing is what is left, because the alternative on record is
     * deleting the caller's lines without saying so.
     */
    #[Test]
    public function codexInstallRefusesASectionItCannotRewriteWithoutDropping(): void
    {
        $directory = $this->directory();
        self::assertTrue(mkdir($directory . '/.codex', 0777, true));
        $original = "[mcp_servers.typo3-cms-mcp]\ncommand = \"php\"\nargs = [\n"
            . "  \"/elsewhere/bin/typo3-cms-mcp\"\n]\n";
        file_put_contents($directory . '/.codex/config.toml', $original);

        try {
            $stderr = '';
            self::assertSame(1, $this->execute($directory, ['install', '--agent=codex'], $stderr));
            self::assertStringContainsString('line 3', $stderr);
            self::assertStringContainsString('refusing', $stderr);
            self::assertSame($original, file_get_contents($directory . '/.codex/config.toml'));
            self::assertDirectoryDoesNotExist($directory . '/.agents');
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function codexUpdateReplacesAModifiedGeneratedSkill(): void
    {
        $directory = $this->directory();
        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);
            $skill = $directory . '/.agents/skills/typo3-backend-module-development/SKILL.md';
            file_put_contents($skill, (string) file_get_contents($skill) . "\nUser change.\n");

            self::assertSame(0, $this->execute($directory, ['update', '--agent=codex'], $stderr), $stderr);
            self::assertFileEquals(
                Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
                $skill,
            );
            self::assertStringNotContainsString('User change.', (string) file_get_contents($skill));
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function codexUpdateReplacesTheCompleteGeneratedSkillDirectory(): void
    {
        $directory = $this->directory();
        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);
            $skillDirectory = $directory . '/.agents/skills/typo3-extension-testing';
            $obsolete = $skillDirectory . '/agents/openai.yaml';
            self::assertTrue(mkdir(dirname($obsolete)));
            self::assertNotFalse(file_put_contents($obsolete, "interface: {}\n"));

            self::assertSame(0, $this->execute($directory, ['update', '--agent=codex'], $stderr), $stderr);
            self::assertFileDoesNotExist($obsolete);
            self::assertDirectoryDoesNotExist(dirname($obsolete));
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function codexUpdateRemovesSkillsTrackedByThePreviousCentralState(): void
    {
        $directory = $this->directory();
        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stderr), $stderr);
            $stale = $directory . '/.agents/skills/obsolete-typo3-skill';
            self::assertTrue(mkdir($stale));
            self::assertNotFalse(file_put_contents($stale . '/SKILL.md', "obsolete\n"));
            $state = json_decode(
                (string) file_get_contents($directory . '/.typo3-cms-mcp/state.json'),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
            $state['skills'][] = 'obsolete-typo3-skill';
            file_put_contents(
                $directory . '/.typo3-cms-mcp/state.json',
                json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n",
            );

            self::assertSame(0, $this->execute($directory, ['update', '--agent=codex'], $stderr), $stderr);
            self::assertDirectoryDoesNotExist($stale);
        } finally {
            Directory::remove($directory);
        }
    }

    /**
     * What the ignores are for, asked of git rather than of the files.
     *
     * Everything else here reads what was written; this reads what the tool it
     * was written for makes of it, in a repository of its own, because the
     * property being kept is about `git status` and not about a file's
     * contents. A skill the project wrote itself sits beside the published ones
     * throughout: it is what tells "this package's directories are invisible"
     * apart from "the whole skills directory is".
     */
    #[Test]
    public function gitReportsTheProjectsOwnFilesAndNothingThisPackageWrote(): void
    {
        // A property of this repository rather than of the machine: it is a git
        // checkout, its coverage is fetched by `checkouts:update` and it commits
        // through `.githooks/`. A checkout without git is a failure with a
        // sentence, not a test that quietly goes away.
        $version = '';
        self::assertSame(0, $this->git(sys_get_temp_dir(), ['--version'], $version), 'git does not answer here');

        $directory = $this->directory();
        $output = '';
        self::assertSame(0, $this->git($directory, ['init', '--quiet'], $output), $output);
        self::assertTrue(mkdir($directory . '/.claude/skills', 0777, true));
        file_put_contents($directory . '/.claude/skills/my-own-skill.md', "Mine.\n");
        file_put_contents($directory . '/composer.json', "{}\n");

        try {
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=claude'], $stderr), $stderr);

            self::assertSame(0, $this->git($directory, ['status', '--porcelain', '-uall'], $output), $output);
            $reported = array_values(array_filter(explode("\n", $output)));
            sort($reported);
            self::assertSame([
                '?? .claude/skills/my-own-skill.md',
                '?? .mcp.json',
                '?? composer.json',
            ], $reported, $output);
        } finally {
            Directory::remove($directory);
        }
    }

    /** @param list<string> $arguments */
    private function git(string $directory, array $arguments, string &$output): int
    {
        $process = proc_open(
            ['git', ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $directory,
        );
        if (!is_resource($process)) {
            return 127;
        }
        fclose($pipes[0]);
        $output = (string) stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process);
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

    /** A project that has required this server has its entrypoint below its bin directory. */
    private function installEntrypoint(string $directory, string $binDirectory): void
    {
        self::assertTrue(mkdir($directory . '/' . $binDirectory, 0777, true));
        file_put_contents($directory . '/' . $binDirectory . '/typo3-cms-mcp', "#!/usr/bin/env php\n");
    }

}
