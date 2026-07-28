<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Typo3Cli;

/**
 * Whether the installation's console can be invoked, and — when it cannot —
 * whether the caller is told something it can act on.
 */
final class Typo3CliTest extends TestCase
{
    private string $temporaryRoot = '';

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();
        if ($this->temporaryRoot !== '') {
            self::removeDirectory($this->temporaryRoot);
            $this->temporaryRoot = '';
        }
    }

    #[Test]
    public function withoutAnInstallationThereIsNothingToRun(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('no TYPO3 installation', Typo3Cli::reason());
    }

    #[Test]
    public function anInstallationWithoutAConsoleSaysSo(): void
    {
        $this->discover($this->installation());

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('bin/typo3', Typo3Cli::reason());
    }

    #[Test]
    public function aPhpBelowWhatTheInstallationPinsIsNotUsed(): void
    {
        // Composer pins the platform, so a lower interpreter aborts in
        // platform_check.php before TYPO3 is reached. Saying that beats a fatal.
        $root = $this->installation(['config' => ['platform' => ['php' => '99.0.0']]]);
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        $this->discover($root);

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('requires PHP 99.0.0', Typo3Cli::reason());
    }

    #[Test]
    public function aDdevProjectThatIsNotRunningIsReportedRatherThanStarted(): void
    {
        // An agent asking about a label must not bring containers up as a side
        // effect, so the answer names the command the caller may choose to run.
        $root = $this->installation(['config' => ['platform' => ['php' => '99.0.0']]]);
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        $this->discover($root);

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('ddev', Typo3Cli::reason());
    }

    #[Test]
    public function runningWithoutAConsoleFailsWithTheReasonRatherThanThrowing(): void
    {
        $this->discover($this->installation());

        $result = Typo3Cli::run(['language:domain:list']);

        self::assertFalse($result['ok']);
        self::assertSame(Typo3Cli::reason(), $result['error']);
    }

    /** @param array<string, mixed> $manifest */
    private function installation(array $manifest = []): string
    {
        $root = sys_get_temp_dir() . '/typo3-cms-mcp-cli-' . bin2hex(random_bytes(6));
        $this->temporaryRoot = $root;
        mkdir($root . '/typo3/sysext/core', 0o777, true);
        file_put_contents($root . '/composer.json', json_encode(
            $manifest + ['name' => 'typo3/cms', 'type' => 'typo3-cms-core'],
            JSON_THROW_ON_ERROR
        ));
        file_put_contents($root . '/typo3/sysext/core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ], JSON_THROW_ON_ERROR));

        return $root;
    }

    private function discover(string $root): void
    {
        Instance::discoverFrom($root);
        Typo3Cli::forget();
    }

    private static function removeDirectory(string $path): void
    {
        $entries = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($entries as $entry) {
            $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
        }
        rmdir($path);
    }
}
