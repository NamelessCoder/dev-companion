<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tests\Support\Directory;

/**
 * What a project records about the clients installed in it.
 *
 * A project is worked on by more than one client, and which ones is knowledge
 * only the project has. It keeps it in `.typo3-cms-mcp/state.json`, so that an
 * update needs no list from whoever runs it, and so that a skill this package
 * has stopped shipping can be taken out of every client it reached.
 */
final class InstallerRecordTest extends TestCase
{
    private const SKILL = 'typo3-backend-module-development';

    #[Test]
    public function updateWithoutAnAgentRefreshesEveryClientInstalledHere(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=claude'], $stdout, $stderr), $stderr);
            self::assertSame(0, $this->execute($directory, ['install', '--agent=copilot'], $stdout, $stderr), $stderr);
            self::assertSame(
                ['claude', 'copilot'],
                $this->state($directory)['agents'],
            );

            $skills = [
                $directory . '/.claude/skills/' . self::SKILL . '/SKILL.md',
                $directory . '/.github/skills/' . self::SKILL . '/SKILL.md',
            ];
            foreach ($skills as $skill) {
                file_put_contents($skill, "User change.\n");
            }

            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            foreach ($skills as $skill) {
                self::assertFileEquals(Paths::root() . '/skills/' . self::SKILL . '/SKILL.md', $skill);
            }
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function clientsSharingASkillDirectoryArePublishedOnce(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=codex'], $stdout, $stderr), $stderr);
            self::assertSame(0, $this->execute($directory, ['install', '--agent=amp'], $stdout, $stderr), $stderr);

            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            self::assertSame(
                1,
                substr_count($stdout, 'Published ' . self::SKILL . ' in ' . $directory . '/.agents/skills'),
                $stdout,
            );
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function namingNoClientInstallsTheSkillsEveryClientFindsOnItsOwn(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install'], $stdout, $stderr), $stderr);

            $skill = $directory . '/.agents/skills/' . self::SKILL . '/SKILL.md';
            self::assertFileEquals(Paths::root() . '/skills/' . self::SKILL . '/SKILL.md', $skill);
            self::assertFileExists($directory . '/.mcp.json');
            // It is recorded like any other client, so it is refreshed like
            // one — the setup that names nobody needs no case of its own.
            self::assertSame(['generic'], $this->state($directory)['agents']);
            self::assertSame(
                "*\n",
                file_get_contents($directory . '/.agents/skills/' . self::SKILL . '/.gitignore'),
            );

            file_put_contents($skill, "User change.\n");
            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            self::assertFileEquals(Paths::root() . '/skills/' . self::SKILL . '/SKILL.md', $skill);
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function generalIsNotAClientOptionOfItsOwn(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(1, $this->execute($directory, ['install', '--agent=whatever'], $stdout, $stderr));
            self::assertStringContainsString('unsupported agent "whatever"', $stderr);
            self::assertStringNotContainsString('generic', $stderr);
        } finally {
            Directory::remove($directory);
        }
    }

    #[Test]
    public function updateSaysSoWhereNothingIsInstalledAtAll(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(1, $this->execute($directory, ['update'], $stdout, $stderr));
            self::assertStringContainsString('nothing is installed here', $stderr);
            self::assertStringContainsString('install --agent=', $stderr);
            self::assertFileDoesNotExist($directory . '/.typo3-cms-mcp/state.json');
        } finally {
            Directory::remove($directory);
        }
    }

    /**
     * The project's `.gitignore` is the project's, on a run that has every
     * reason to touch it.
     *
     * This is the one file an install used to write into, and the case that
     * would show it doing so again is an `update` in a project that has one:
     * nine skills are republished, the record is rewritten, and what the
     * project wrote stays byte for byte what it was.
     */
    #[Test]
    public function neitherCommandWritesIntoTheProjectsGitignore(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            file_put_contents($directory . '/.gitignore', "/vendor/\n\n/.idea/\n");
            self::assertSame(0, $this->execute($directory, ['install', '--agent=claude'], $stdout, $stderr), $stderr);
            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);

            self::assertSame("/vendor/\n\n/.idea/\n", file_get_contents($directory . '/.gitignore'));
            self::assertSame(
                "*\n",
                file_get_contents($directory . '/.claude/skills/' . self::SKILL . '/.gitignore'),
            );
            self::assertSame("*\n", file_get_contents($directory . '/.typo3-cms-mcp/.gitignore'));
        } finally {
            Directory::remove($directory);
        }
    }

    /** @return array{agents: list<string>, skills: list<string>} */
    private function state(string $directory): array
    {
        return json_decode(
            (string) file_get_contents($directory . '/.typo3-cms-mcp/state.json'),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
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

    private function directory(): string
    {
        $directory = sys_get_temp_dir() . '/typo3-cms-mcp-record-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($directory));

        return $directory;
    }

}
