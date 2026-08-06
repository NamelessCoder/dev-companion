<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Upkeep\Fixture;

/**
 * Asking the installation itself, and what happens on the three ways that fail.
 *
 * There is no real TYPO3 here — this repository has none and never will — so the
 * installations below carry an autoloader shaped like one. The probe is not
 * simulated for that: it is delivered to a real interpreter, boots what the
 * autoloader gives it, and answers as data, which is how the payload, the
 * declared autoloader path, the three states and the attribution evidence all
 * end up held by the same mechanism they run through.
 */
final class Typo3RuntimeTest extends TestCase
{
    private string $root = '';

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        Instance::discoverFrom(null);
        Typo3Cli::useRunner(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }

    #[Test]
    public function theProbeReachesAnInterpreterAndAnswersAsData(): void
    {
        // The whole delivery in one assertion: the payload is base64-encoded
        // into `php -r`, the opening tag is stripped, the subprocess starts in
        // the installation root and prints one JSON object. What it reports
        // here is the missing autoloader, because a fixture has no TYPO3 — that
        // it reports anything at all is the mechanism working.
        $this->discover($this->installationWithAConsole());

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_UNREACHABLE, $answer['state']);
        self::assertStringContainsString('no autoloader at vendor/autoload.php', $answer['reason']);
        self::assertStringContainsString($this->root, $answer['reason'], 'it ran in the installation root');
    }

    #[Test]
    public function theAutoloaderIsTheOneTheInstallationDeclares(): void
    {
        // Relative, and from the declared vendor directory: the extension
        // testing setup puts it below .Build/, and inside a DDEV container no
        // absolute path of this machine exists at all.
        $this->discover($this->installationWithAConsole(['config' => ['vendor-dir' => '.Build/vendor']]));

        self::assertStringContainsString('.Build/vendor/autoload.php', Typo3Runtime::ask()['reason']);
    }

    #[Test]
    public function aStatedConsoleIsKeptAsTheWayInAndPointedAtPhp(): void
    {
        // A stated console is a transport plus a binary. The transport is the
        // part this server could never have worked out, so it is kept and only
        // the binary is exchanged.
        $this->root = $this->installationWithAConsole();
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' /some/where/typo3');
        $this->discover($this->root);

        self::assertStringContainsString('no autoloader at', Typo3Runtime::ask()['reason']);
    }

    #[Test]
    public function aStatedConsoleNoInterpreterCanBeDerivedFromIsSaidOutLoud(): void
    {
        // `env` is a program, and this case is about the answer for a stated
        // console whose first word is one and still names no interpreter.
        // Whether this machine has an `env` is not what it holds — read off
        // the real `PATH` it passed here and failed on a machine carrying
        // only PHP, with the answer for a program that does not exist.
        $ran = self::createStub(CommandRunner::class);
        $ran->method('locate')->willReturnCallback(
            static fn(string $name): ?string => $name === 'env' ? '/usr/bin/env' : null,
        );
        Typo3Cli::useRunner($ran);

        $this->root = $this->installationWithAConsole();
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=env /some/where/cli');
        $this->discover($this->root);

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_UNREACHABLE, $answer['state']);
        self::assertStringContainsString('no interpreter can be derived', $answer['reason']);
    }

    #[Test]
    public function aBootedContainerAnswersWithTheTopicsAndTheirAttribution(): void
    {
        // A TYPO3 shaped like the real one, so the real probe boots it: the
        // registry answers with EXT: sources and the TCA with LLL:EXT: titles,
        // which is what an entry is attributed by on the way back.
        $root = $this->installationWithAConsole();
        Fixture::bootsInto(
            $root,
            ['ext-acme-teaser' => 'EXT:acme/Resources/Public/Icons/teaser.svg', 'actions-add' => 'EXT:core/Resources/Public/Icons/T3Icons/actions/add.svg'],
            ['tx_acme_event' => 'LLL:EXT:acme/Resources/Private/Language/locallang_db.xlf:tx_acme_event'],
            ['acme_teaser' => ['LLL:EXT:acme/Resources/Private/Language/locallang_be.xlf:teaser', 'ext-acme-teaser']],
        );
        $this->discover($root);

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_FULL, $answer['state']);
        self::assertSame('', $answer['reason']);
        self::assertSame(
            ['ext-acme-teaser' => 'EXT:acme/Resources/Public/Icons/teaser.svg', 'actions-add' => 'EXT:core/Resources/Public/Icons/T3Icons/actions/add.svg'],
            $answer['topics']['icons'],
        );
        self::assertArrayHasKey('tx_acme_event', $answer['topics']['tables']);
        self::assertArrayHasKey('acme_teaser', $answer['topics']['contentElements']);
    }

    /**
     * A form data group is a graph, and what the caller wants is what it
     * resolves to. The fixture's ordering service reverses rather than
     * resolves, so what is held here is that the probe reports the service's
     * answer and not the order the registry was written in — a passthrough
     * could not tell those apart, and an ordering written into the probe would
     * be the second implementation it exists to avoid.
     */
    #[Test]
    public function aFormDataGroupComesBackInTheOrderTheInstallationResolved(): void
    {
        $root = $this->installationWithAConsole();
        Fixture::bootsInto($root, formDataGroups: [
            'tcaDatabaseRecord' => [
                'Acme\\First' => [],
                'Acme\\Second' => ['depends' => ['Acme\\First']],
                'Acme\\Third' => ['depends' => ['Acme\\Second'], 'before' => ['Acme\\Fourth']],
            ],
        ]);
        $this->discover($root);

        $groups = Typo3Runtime::topic('formDataGroups');

        self::assertIsArray($groups);
        self::assertArrayNotHasKey('unavailable', $groups);
        self::assertSame(
            ['Acme\\Third', 'Acme\\Second', 'Acme\\First'],
            array_column($groups['groups']['tcaDatabaseRecord'], 'provider'),
        );
        self::assertSame(
            [['Acme\\Second'], ['Acme\\First'], []],
            array_column($groups['groups']['tcaDatabaseRecord'], 'depends'),
        );
        self::assertSame(
            [['Acme\\Fourth'], [], []],
            array_column($groups['groups']['tcaDatabaseRecord'], 'before'),
        );
    }

    #[Test]
    public function aFailsafeContainerIsAReasonRatherThanAResult(): void
    {
        // It answers — with core packages and nothing else, which is the state
        // every extension repository is in and the one that looks complete.
        $root = $this->installationWithAConsole();
        Fixture::bootsInto($root, failsafe: true);
        $this->discover($root);

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_FAILSAFE, $answer['state']);
        self::assertStringContainsString('no essential configuration', $answer['reason']);
        self::assertNull(Typo3Runtime::topic('icons'), 'a subset that looks whole is never handed on');
        self::assertSame($answer['reason'], Typo3Runtime::reason());
    }

    #[Test]
    public function anExtensionIsNamedByTheReferenceAnEntryCarries(): void
    {
        self::assertSame('news', Typo3Runtime::extensionIn('EXT:news/Resources/Public/Icons/list.svg'));
        self::assertSame('news', Typo3Runtime::extensionIn('LLL:EXT:news/Resources/Private/Language/locallang.xlf:plugin'));
        self::assertSame('my_sitepackage', Typo3Runtime::extensionIn('EXT:my_sitepackage/Configuration/Icons.php'));
        // Nothing names an extension: it belongs to the installation.
        self::assertNull(Typo3Runtime::extensionIn('content-news'));
        self::assertNull(Typo3Runtime::extensionIn('impexp.db:tx_impexp_presets'));
    }

    #[Test]
    public function withoutAConsoleTheReasonIsTheConsolesOwn(): void
    {
        // Nothing is invented here: the console said why it could not be
        // invoked, and that sentence is what the caller gets.
        $this->discover($this->installation());

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_UNREACHABLE, $answer['state']);
        self::assertSame(Typo3Cli::reason(), $answer['reason']);
        self::assertNull(Typo3Runtime::topic('icons'), 'no topic is a topic of its own');
    }

    #[Test]
    public function withoutAnInstallationThereIsNothingToBoot(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();

        self::assertStringContainsString('no TYPO3 installation', Typo3Runtime::ask()['reason']);
    }

    /** @param array<string, mixed> $manifest */
    private function installation(array $manifest = []): string
    {
        $root = sys_get_temp_dir() . '/typo3-dev-companion-runtime-' . bin2hex(random_bytes(6));
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

        return $this->root = $root;
    }

    /** @param array<string, mixed> $manifest */
    private function installationWithAConsole(array $manifest = []): string
    {
        $root = $this->installation($manifest);
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");

        return $root;
    }

    private function discover(string $root): void
    {
        Instance::discoverFrom($root);
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }
}
