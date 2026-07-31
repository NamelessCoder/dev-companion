<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;

/**
 * What a project records about the clients installed in it.
 *
 * A project is worked on by more than one client, and which ones is knowledge
 * only the project has. It keeps it in `typo3-cms-mcp.json`, so that an update
 * needs no list from whoever runs it, and so that the ignores can be written
 * for the clients that are actually there.
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
            $this->removeDirectory($directory);
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
            $this->removeDirectory($directory);
        }
    }

    #[Test]
    public function aGenericSetupIsUpdatedByConfirmingItsEntry(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install'], $stdout, $stderr), $stderr);
            $installed = (string) file_get_contents($directory . '/.mcp.json');

            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);
            self::assertStringContainsString('Confirmed typo3-cms-mcp', $stdout);
            self::assertStringContainsString('install --agent=', $stdout);
            // Naming no client is a setup, not a half-finished one: it writes
            // the entry every client reads and owns no skills, so an update
            // has nothing to publish and nothing to record.
            self::assertSame($installed, file_get_contents($directory . '/.mcp.json'));
            self::assertFileDoesNotExist($directory . '/typo3-cms-mcp.json');
            self::assertFileDoesNotExist($directory . '/.gitignore');
        } finally {
            $this->removeDirectory($directory);
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
            self::assertFileDoesNotExist($directory . '/typo3-cms-mcp.json');
        } finally {
            $this->removeDirectory($directory);
        }
    }

    #[Test]
    public function theIgnoreBlockIsReplacedBetweenItsMarkersAndNothingElseIs(): void
    {
        $directory = $this->directory();

        try {
            $stdout = '';
            $stderr = '';
            self::assertSame(0, $this->execute($directory, ['install', '--agent=claude'], $stdout, $stderr), $stderr);
            // A block from a run that published a skill this version no longer
            // ships, into a client this project no longer has.
            file_put_contents($directory . '/.gitignore', "/vendor/\n\n"
                . "# BEGIN typo3-cms-mcp (generated)\n"
                . "/typo3-cms-mcp.json\n"
                . '/.claude/skills/obsolete-typo3-skill/' . "\n"
                . '/.github/skills/' . self::SKILL . '/' . "\n"
                . "# END typo3-cms-mcp\n\n"
                . "/.idea/\n");

            self::assertSame(0, $this->execute($directory, ['update'], $stdout, $stderr), $stderr);

            $gitignore = (string) file_get_contents($directory . '/.gitignore');
            self::assertStringStartsWith("/vendor/\n\n/.idea/\n\n", $gitignore);
            self::assertSame(1, substr_count($gitignore, '# BEGIN typo3-cms-mcp'));
            self::assertSame(1, substr_count($gitignore, '# END typo3-cms-mcp'));
            self::assertStringEndsWith("# END typo3-cms-mcp\n", $gitignore);
            self::assertStringContainsString('/.claude/skills/' . self::SKILL . "/\n", $gitignore);
            // The skill this version no longer ships, and the client that is
            // not installed here: both were lines that ignored nothing.
            self::assertStringNotContainsString('obsolete-typo3-skill', $gitignore);
            self::assertStringNotContainsString('/.github/skills/', $gitignore);
        } finally {
            $this->removeDirectory($directory);
        }
    }

    /** @return array{agents: list<string>, skills: list<string>} */
    private function state(string $directory): array
    {
        return json_decode(
            (string) file_get_contents($directory . '/typo3-cms-mcp.json'),
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

    private function removeDirectory(string $directory): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
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
