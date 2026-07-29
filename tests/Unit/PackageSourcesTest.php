<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\InstalledFluidNamespaces;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tools;
use Typo3CmsMcp\Typo3Cli;

/**
 * What the installed packages can answer when the console cannot.
 *
 * Booting TYPO3 needs a migrated database, and a project spends a lot of its
 * life without one. Where the answer is a file in a package rather than the
 * container's assembled state, the question does not have to end there.
 */
final class PackageSourcesTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        Instance::discoverFrom(null);
        Typo3Cli::forget();
    }

    #[Test]
    public function fluidNamespacesAreReadFromThePackagesThatDeclareThem(): void
    {
        $root = $this->coreCheckout();
        $this->namespaceFile($root . '/typo3/sysext/core', ['core' => ['TYPO3\\CMS\\Core\\ViewHelpers']]);
        $this->namespaceFile($root . '/typo3/sysext/backend', ['f' => ['TYPO3\\CMS\\Backend\\ViewHelpers']]);
        Instance::discoverFrom($root);

        self::assertSame(
            ['core' => ['TYPO3\CMS\Core\ViewHelpers'], 'f' => ['TYPO3\CMS\Backend\ViewHelpers']],
            InstalledFluidNamespaces::all()
        );
    }

    #[Test]
    public function withoutAConsoleTheDeclarationsAreTheAnswerAndSaySoAsOne(): void
    {
        $root = $this->coreCheckout();
        $this->namespaceFile($root . '/typo3/sysext/core', ['core' => ['TYPO3\\CMS\\Core\\ViewHelpers']]);
        Instance::discoverFrom($root);
        Typo3Cli::forget();

        $result = Tools::call('typo3_fluid_namespace_list', []);

        self::assertSame('packages', $result->data['answeredBy']);
        self::assertSame('core', $result->data['namespaces'][0]['prefix']);
        self::assertStringContainsString('not what the container assembled', $result->text);
    }

    #[Test]
    public function aPackageThatDeclaresNothingContributesNothing(): void
    {
        Instance::discoverFrom($this->coreCheckout());

        self::assertSame([], InstalledFluidNamespaces::all());
    }

    /** @param array<string, array<int, string>> $namespaces */
    private function namespaceFile(string $packagePath, array $namespaces): void
    {
        $path = $packagePath . '/Configuration/Fluid';
        mkdir($path, 0o777, true);

        $entries = '';
        foreach ($namespaces as $prefix => $phpNamespaces) {
            $entries .= sprintf("    '%s' => [\n", $prefix);
            foreach ($phpNamespaces as $phpNamespace) {
                $entries .= sprintf("        '%s',\n", str_replace('\\', '\\\\', $phpNamespace));
            }
            $entries .= "    ],\n";
        }

        file_put_contents($path . '/Namespaces.php', "<?php\n\nreturn [\n" . $entries . "];\n");
    }
}
