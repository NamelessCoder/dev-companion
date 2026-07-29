<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Support;

use PHPUnit\Framework\Attributes\After;

/**
 * Builds the two installation layouts on disk and takes them away again.
 *
 * What an installation is, this server reads from the files Composer and the
 * core monorepo leave behind — so a test about installations has to have those
 * files, and there are only two shapes of them.
 */
trait TemporaryInstallation
{
    private string $temporaryRoot = '';

    #[After]
    public function removeTemporaryInstallation(): void
    {
        if ($this->temporaryRoot === '') {
            return;
        }

        $entries = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->temporaryRoot, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($entries as $entry) {
            $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
        }
        rmdir($this->temporaryRoot);
        $this->temporaryRoot = '';
    }

    /** A core monorepo checkout holding the core and backend system extensions. */
    private function coreCheckout(): string
    {
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', '{"name": "typo3/cms", "type": "typo3-cms-core"}');
        foreach (['core', 'backend'] as $key) {
            $path = $root . '/typo3/sysext/' . $key;
            mkdir($path . '/Classes/Controller', 0o777, true);
            file_put_contents($path . '/composer.json', json_encode([
                'name' => 'typo3/cms-' . $key,
                'type' => 'typo3-cms-framework',
                'extra' => ['typo3/cms' => ['extension-key' => $key]],
            ], JSON_THROW_ON_ERROR));
        }

        return $root;
    }

    /** A Composer project with one system extension and one of its own. */
    private function composerProject(string $vendorDirectory = 'vendor'): string
    {
        $root = $this->temporaryDirectory();
        $vendor = $root . '/' . $vendorDirectory;
        if ($vendorDirectory !== 'vendor') {
            file_put_contents($root . '/composer.json', json_encode(
                ['name' => 'acme/extension', 'config' => ['vendor-dir' => $vendorDirectory]],
                JSON_THROW_ON_ERROR
            ));
        }
        mkdir($vendor . '/typo3/cms-core', 0o777, true);
        mkdir($root . '/packages/my_sitepackage', 0o777, true);
        mkdir($vendor . '/composer', 0o777, true);
        file_put_contents($vendor . '/composer/installed.json', json_encode(['packages' => [
            [
                'name' => 'typo3/cms-core',
                'type' => 'typo3-cms-framework',
                'install-path' => '../typo3/cms-core',
                'extra' => ['typo3/cms' => ['extension-key' => 'core']],
            ],
            [
                'name' => 'acme/my-sitepackage',
                'type' => 'typo3-cms-extension',
                // Composer writes install-path relative to the vendor
                // directory it actually used, so a moved one is deeper.
                'install-path' => str_repeat('../', substr_count($vendorDirectory, '/') + 2)
                    . 'packages/my_sitepackage',
                'extra' => ['typo3/cms' => ['extension-key' => 'my_sitepackage']],
            ],
            ['name' => 'symfony/console', 'type' => 'library', 'install-path' => '../symfony/console'],
        ]], JSON_THROW_ON_ERROR));

        return $root;
    }

    private function temporaryDirectory(): string
    {
        $this->temporaryRoot = sys_get_temp_dir() . '/typo3-cms-mcp-instance-' . bin2hex(random_bytes(6));
        mkdir($this->temporaryRoot, 0o777, true);

        return $this->temporaryRoot;
    }
}
