<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Process\CommandRunner;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tool\Registry;

/**
 * Whether the installation's console can be invoked, and — when it cannot —
 * whether the caller is told something it can act on.
 */
final class Typo3CliTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        putenv('IS_DDEV_PROJECT');
        Instance::discoverFrom(null);
        Typo3Cli::useRunner(null);
        Typo3Cli::forget();
    }

    #[Test]
    public function aFailureIsDiagnosedOnlyWhereTheMessageDoesNotSayEnough(): void
    {
        self::assertStringContainsString(
            'no TYPO3 schema yet',
            Typo3Cli::diagnose("An exception occurred while executing a query: Table 'db.tx_scheduler_task' doesn't exist")
        );
        self::assertStringContainsString('no TYPO3 schema yet', Typo3Cli::diagnose('SQLSTATE[42S02]: Base table or view not found'));
        self::assertSame('', Typo3Cli::diagnose('php_network_getaddresses: getaddrinfo for db failed'));
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
    public function aConsoleInTheDeclaredBinDirectoryIsFound(): void
    {
        // What the TYPO3 extension testing setup produces: the console exists
        // and runs, but not under either Composer default. Probing only those
        // left five installation-backed tools without an answer in a checkout
        // whose console was one directory away.
        $root = $this->installation([
            'config' => ['bin-dir' => '.build/bin', 'vendor-dir' => '.build/vendor'],
        ]);
        mkdir($root . '/.build/bin', 0o777, true);
        file_put_contents($root . '/.build/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        $this->discover($root);

        // Nothing here says the console can be run — that depends on the
        // machine. It says the reason is no longer that none was found.
        self::assertStringNotContainsString('has no TYPO3 console', Typo3Cli::reason());
    }

    #[Test]
    public function anAbsoluteBinDirectoryBelowTheRootIsTheSameDirectory(): void
    {
        // Composer accepts an absolute bin-dir and installs the binaries there
        // (2.9.5, checked against a project declaring one). Spelled absolutely
        // it names the very directory the relative spelling names, so dropping
        // it lost a console that was there — the failure the declared bin-dir
        // was read for in the first place, under a second spelling.
        $root = $this->installation();
        $root = (string) realpath($root);
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'typo3/cms',
            'type' => 'typo3-cms-core',
            'config' => ['bin-dir' => $root . '/.build/bin', 'vendor-dir' => $root . '/.build/vendor'],
        ], JSON_THROW_ON_ERROR));
        mkdir($root . '/.build/bin', 0o777, true);
        file_put_contents($root . '/.build/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        $this->discover($root);

        self::assertSame('.build/bin', Typo3Cli::binDirectory($root));
        self::assertSame('.build/vendor/autoload.php', Typo3Cli::autoloader($root));
        self::assertStringNotContainsString('has no TYPO3 console', Typo3Cli::reason());
    }

    #[Test]
    public function aBinDirectoryOutsideTheRootIsNamedRatherThanPassedOver(): void
    {
        // It has no form the invocation can use — the console is run relative
        // to the root, and in a container the host path is not there. What the
        // caller gets instead is the declaration it wrote and the setting that
        // replaces it.
        $this->discover($this->installation(['config' => ['bin-dir' => '/opt/typo3/bin']]));

        $reason = Typo3Cli::reason();
        self::assertStringContainsString('/opt/typo3/bin', $reason);
        self::assertStringContainsString(Typo3Cli::CONSOLE_VARIABLE, $reason);
    }

    #[Test]
    public function aMissingConsoleNamesEveryPathThatWasProbed(): void
    {
        $this->discover($this->installation(['config' => ['bin-dir' => '.build/bin']]));

        $reason = Typo3Cli::reason();
        self::assertStringContainsString('.build/bin/typo3', $reason);
        self::assertStringContainsString('vendor/bin/typo3', $reason);
    }

    #[Test]
    public function aStatedCommandIsUsedInsteadOfWorkingOneOut(): void
    {
        // Autodiscovery is a chain of guesses about a machine this server does
        // not control. When a link breaks, this is the whole repair.
        $root = $this->installation(['config' => ['platform' => ['php' => '99.0.0']]]);
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' /some/where/typo3');
        $this->discover($root);

        $console = Typo3Cli::resolve();
        self::assertSame(Typo3Cli::VIA_OVERRIDE, $console['via']);
        self::assertSame([PHP_BINARY, '/some/where/typo3'], $console['command']);
    }

    #[Test]
    public function aStatedCommandThatIsNoProgramIsReportedRatherThanReplaced(): void
    {
        $root = $this->installation();
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=/nowhere/at/all typo3');
        $this->discover($root);

        // Falling through to the discovered console would answer from
        // something other than what the caller named.
        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString(Typo3Cli::CONSOLE_VARIABLE, Typo3Cli::reason());
    }

    #[Test]
    public function aQuotedArgumentInAStatedCommandStaysOneArgument(): void
    {
        $this->discover($this->installation());
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' "-d memory_limit=512M" typo3');
        Typo3Cli::forget();

        self::assertSame(
            [PHP_BINARY, '-d memory_limit=512M', 'typo3'],
            Typo3Cli::resolve()['command']
        );
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

    /**
     * `D-DIS-002`: `ddev exec` runs in the container's configured working
     * directory, so a console named relative to the project root is exit 127 in
     * a project whose `working_dir.web` is the docroot. The container mounts the
     * project at one place whatever that setting says, and that is the place
     * the invocation names.
     *
     * DDEV is stubbed rather than run: what is held here is the command that
     * would be run, which is where the failure was.
     */
    #[Test]
    public function theDdevConsoleIsNamedByAPathTheWorkingDirectoryCannotMove(): void
    {
        $root = $this->installation([
            'config' => ['bin-dir' => '.build/bin', 'vendor-dir' => '.build/vendor'],
        ]);
        mkdir($root . '/.build/bin', 0o777, true);
        file_put_contents($root . '/.build/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatIsRunning());
        $this->discover($root);

        self::assertSame(
            ['ddev', 'exec', '--', '/var/www/html/.build/bin/typo3'],
            Typo3Cli::resolve()['command'],
        );
    }

    /**
     * `ddev exec` joins its arguments into a line and hands that to bash inside
     * the container, so an argument carrying a character bash acts on never
     * reaches the console. `typo3_label_lookup` builds one — the search is a
     * regex, and `--regex=/(save)/i` is a subshell to bash — so against every
     * DDEV installation it came back exit 2 and fell through to reading the
     * package files, reporting `answeredBy: "packages"` as though the console
     * were unreachable. Found by the first recording made against a booted
     * TYPO3 rather than by anything here, which is what `D-EVI-004` said an
     * installation of this repository's own would be for.
     *
     * What this holds is the quoting, which is this class's part: every
     * argument reaches `ddev exec` in the form that survives being joined into
     * a line. That the joining then happens is DDEV's behaviour and is not
     * reproduced here — it was measured against v1.25.1 on 2026-08-02 and the
     * measurement is written into `Typo3Cli::pastTheShell()`.
     *
     * @param array<int, string> $arguments
     */
    #[Test]
    #[DataProvider('argumentsAShellWouldActOn')]
    public function everyArgumentReachesTheContainerInTheFormThatSurvivesItsShell(array $arguments): void
    {
        $root = $this->installation();
        mkdir($root . '/vendor/bin', 0o777, true);
        file_put_contents($root . '/vendor/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatIsRunning($commands));
        $this->discover($root);

        Typo3Cli::run($arguments);

        $expected = array_map(
            escapeshellarg(...),
            array_merge($arguments, ['--no-interaction', '--no-ansi']),
        );
        self::assertContains(
            'ddev exec -- /var/www/html/vendor/bin/typo3 ' . implode(' ', $expected),
            $commands,
        );
    }

    /**
     * @return array<string, array{0: array<int, string>}>
     */
    public static function argumentsAShellWouldActOn(): array
    {
        return [
            'a regex in parentheses, which bash reads as a subshell' => [
                ['language:domain:search', '--regex=/(save)/i'],
            ],
            'a semicolon, which bash reads as the end of the command' => [
                ['language:domain:search', '--regex=/a;b/'],
            ],
            'a dollar, which bash expands' => [
                ['configuration:show', 'BE/$installToolPassword'],
            ],
            'a space, which bash reads as two arguments' => [
                ['language:domain:search', '--regex=/two words/'],
            ],
            'nothing bash acts on, which is quoted the same way' => [
                ['site:list'],
            ],
        ];
    }

    #[Test]
    public function aStoppedProjectReachedThroughHostPhpIsReportedAsTheHalfAnswerItIs(): void
    {
        // The console answers, on an interpreter of this machine, because the
        // project that is meant to run in containers has none running. Every
        // question that boots TYPO3 against its database fails, and "reachable"
        // said none of that.
        $root = $this->installation();
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        $this->discover($root);

        self::assertTrue(Typo3Cli::isAvailable());
        self::assertSame('', Typo3Cli::reason(), 'it can be run, so nothing says it cannot');
        self::assertStringContainsString('database', Typo3Cli::caveat());

        $scope = Registry::call('typo3_server_scope', []);
        self::assertNotNull($scope->data['installation']['console']['caveat']);
        self::assertStringContainsString('Reachable is not the same as ready', $scope->text);
    }

    /**
     * The same **From** one layer down. There the console really did run on an
     * interpreter of this machine and only the database was out of reach; here
     * it never starts at all, and "reachable via host PHP" was still what
     * `typo3_server_scope` said.
     *
     * The manifest is the base distribution's, which is what made this
     * reachable: `"platform": {}` and no `require.php`, because the PHP bound
     * of a TYPO3 project lives in the packages it requires rather than in the
     * file that requires them. `composer install` had already written that
     * bound where it decides anything — the platform check the autoloader
     * includes.
     */
    #[Test]
    public function thePhpBoundComesFromTheInstallWhereTheManifestStatesNone(): void
    {
        $root = $this->installation(['config' => ['platform' => []]]);
        $this->console($root);
        $this->installedRequiring($root, 990000);
        $this->discover($root);

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('requires PHP 99.0.0', Typo3Cli::reason());
    }

    /**
     * Composer leaves the check out when nothing needs a version and deletes it
     * when platform requirements are ignored, so its absence has to mean "no
     * bound to read" rather than "no interpreter will do".
     */
    #[Test]
    public function anInstallWithNoPlatformCheckBoundsNothing(): void
    {
        $root = $this->installation(['config' => ['platform' => []]]);
        $this->console($root);
        mkdir($root . '/vendor/composer', 0o777, true);
        $this->discover($root);

        self::assertTrue(Typo3Cli::isAvailable());
        self::assertSame(Typo3Cli::VIA_PHP, Typo3Cli::resolve()['via']);
    }

    /**
     * `R-DIS-009` one step further in: the caller who reads "start the project"
     * is the one who starts it, and asks again in the same session.
     *
     * Measured on 2026-08-04 in one server process against
     * `.environments/e-site-main`, before the bound was read from the install:
     * host PHP 8.3 satisfied a bound nothing stated, so the resolution
     * succeeded through it and was remembered, and `ddev start` changed nothing
     * until the client was restarted — `META-02`'s third **How it fails**. With
     * the bound at what the packages require, the stopped project has no
     * answer at all, and an answer that was never given is not remembered.
     */
    #[Test]
    public function aStoppedProjectNoInterpreterHereCanRunIsAskedAgainAfterItStarts(): void
    {
        $root = $this->installation(['config' => ['platform' => []]]);
        $this->console($root);
        $this->installedRequiring($root, 990000);
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatStartsAfterTheFirstLook($status));
        $this->discover($root);

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('ddev start', Typo3Cli::reason());

        $status = 'running';

        self::assertTrue(Typo3Cli::isAvailable(), 'the failure was retried rather than remembered');
        self::assertSame(Typo3Cli::VIA_DDEV, Typo3Cli::resolve()['via']);
        self::assertSame('', Typo3Cli::caveat());
    }

    #[Test]
    public function aConsoleAlreadyInsideDdevIsReadyThroughItsDirectPhp(): void
    {
        $root = $this->installation();
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        putenv('IS_DDEV_PROJECT=true');
        $this->discover($root);

        self::assertTrue(Typo3Cli::isAvailable());
        self::assertSame(Typo3Cli::VIA_PHP, Typo3Cli::resolve()['via']);
        self::assertSame('', Typo3Cli::caveat());

        $scope = Registry::call('typo3_server_scope', []);
        self::assertNull($scope->data['installation']['console']['caveat']);
        self::assertStringNotContainsString('Reachable is not the same as ready', $scope->text);
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
        $root = $this->removeAfterwards(sys_get_temp_dir() . '/typo3-cms-mcp-cli-' . bin2hex(random_bytes(6)));
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

    /** A console at the default path, which several cases need and none is about. */
    private function console(string $root): void
    {
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
    }

    /**
     * The platform check `composer install` writes, in the form Composer 2.9.5
     * generates it — the whole file, because what is read out of it is a line
     * among others rather than the file's only content.
     */
    private function installedRequiring(string $root, int $versionId): void
    {
        mkdir($root . '/vendor/composer', 0o777, true);
        file_put_contents($root . '/vendor/composer/platform_check.php', <<<PHP
            <?php

            // platform_check.php @generated by Composer

            \$issues = array();

            if (!(PHP_VERSION_ID >= {$versionId})) {
                \$issues[] = 'Your Composer dependencies require a PHP version ">= 99.0.0". You are running ' . PHP_VERSION . '.';
            }

            if (\$issues) {
                throw new \RuntimeException(
                    'Composer detected issues in your platform: ' . implode(' ', \$issues)
                );
            }

            PHP);
    }

    /**
     * A DDEV whose project comes up between two calls, so what one call
     * remembered is what the next one answers with.
     *
     * `locate` answers for `ddev` alone: the interpreter search asks it for
     * every `php99.x` it might use, and a stub that says yes to all of them
     * would put the describe payload where a PHP version belongs.
     *
     * @param-out string $status
     */
    private function ddevThatStartsAfterTheFirstLook(?string &$status = null): CommandRunner&Stub
    {
        $status = 'stopped';
        $ddev = self::createStub(CommandRunner::class);
        $ddev->method('locate')->willReturnCallback(
            static fn(string $name): ?string => $name === 'ddev' ? '/usr/local/bin/ddev' : null
        );
        $ddev->method('run')->willReturnCallback(
            static function (array $command) use (&$status): array {
                $output = $command === ['ddev', 'describe', '-j']
                    ? sprintf('{"raw": {"status": "%s", "php_version": "8.3"}}', $status)
                    : PHP_VERSION;

                return ['ok' => true, 'exitCode' => 0, 'output' => $output, 'error' => ''];
            },
        );

        return $ddev;
    }

    /**
     * A DDEV that says its project is up, standing in for the real one.
     *
     * Two things are stubbed and both are the machine: that a `ddev` exists at
     * all, and what `ddev describe -j` answers. Neither is arranged on the
     * `PATH` any more — a test that writes an executable into a temporary
     * directory depends on that directory being writable, on `chmod`, and on a
     * `/tmp` nobody mounted `noexec`, none of which is what it is testing.
     */
    /**
     * @param array<int, string>|null $commands filled with every command the code under test ran
     *
     * @param-out array<int, string> $commands
     */
    private function ddevThatIsRunning(?array &$commands = null): CommandRunner&Stub
    {
        $commands = [];
        $ddev = self::createStub(CommandRunner::class);
        $ddev->method('locate')->willReturn('/usr/local/bin/ddev');
        $ddev->method('run')->willReturnCallback(
            static function (array $command) use (&$commands): array {
                $commands[] = implode(' ', $command);

                // The one call this class makes of its own accord is
                // `describe -j`, and everything else here is the console. Both
                // are answered with the description, because what the cases
                // read is the command rather than what came back from it.
                return [
                    'ok' => true,
                    'exitCode' => 0,
                    'output' => '{"raw": {"status": "running", "php_version": "8.3"}}',
                    'error' => '',
                ];
            },
        );

        return $ddev;
    }
}
