<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Support;

/**
 * An installation whose autoloader answers like a booted TYPO3.
 *
 * `Typo3Runtime` cannot be held against a real core: this repository has none
 * and the whole point of the probe is that it runs somewhere else. What it can
 * be held against is an autoloader that defines the four classes the probe
 * touches, because then the real probe really is delivered to a real
 * interpreter, really boots, and really returns the topics — and everything
 * built on those topics, attribution included, is exercised on the way back.
 *
 * The fake is deliberately shaped like the thing it stands in for: a registry
 * whose icons carry `EXT:<key>/` sources, and a TCA whose titles and item
 * labels carry `LLL:EXT:<key>/`, because that is what an entry is attributed by.
 */
trait FakeInstallation
{
    /**
     * Writes an autoloader that boots into a full container.
     *
     * @param array<string, string> $icons identifier => source, as the registry resolves it
     * @param array<string, string> $tables table => ctrl title
     * @param array<string, array{0: string, 1: string}> $contentElements CType => [label, icon]
     */
    private function fakeTypo3(
        string $root,
        array $icons = [],
        array $tables = [],
        array $contentElements = [],
        bool $failsafe = false,
    ): void {
        $items = [];
        foreach ($contentElements as $value => [$label, $icon]) {
            $items[] = ['label' => $label, 'value' => $value, 'icon' => $icon];
        }

        $state = var_export([
            'icons' => $icons,
            'tables' => $tables,
            'items' => $items,
            'failsafe' => $failsafe,
        ], true);

        if (!is_dir($root . '/vendor')) {
            mkdir($root . '/vendor', 0o777, true);
        }
        file_put_contents($root . '/vendor/autoload.php', <<<PHP
            <?php
            namespace {
                \$GLOBALS['FAKE'] = {$state};
                \$GLOBALS['TCA'] = ['tt_content' => ['columns' => ['CType' => ['config' => [
                    'items' => \$GLOBALS['FAKE']['items'],
                ]]]]];
                foreach (\$GLOBALS['FAKE']['tables'] as \$table => \$title) {
                    \$GLOBALS['TCA'][\$table] = ['ctrl' => ['title' => \$title]];
                }
            }
            namespace TYPO3\\CMS\\Core\\Core {
                class SystemEnvironmentBuilder {
                    public const REQUESTTYPE_CLI = 2;
                    public static function run(int \$level = 0, int \$type = 0): void {}
                }
                class Bootstrap {
                    public static function init(\$classLoader) {
                        return \$GLOBALS['FAKE']['failsafe']
                            ? new \\TYPO3\\CMS\\Core\\DependencyInjection\\FailsafeContainer()
                            : new \\Fake\\Container();
                    }
                }
            }
            namespace TYPO3\\CMS\\Core\\DependencyInjection {
                class FailsafeContainer {}
            }
            namespace TYPO3\\CMS\\Core\\Imaging {
                class IconRegistry {
                    public function getAllRegisteredIconIdentifiers(): array {
                        return array_keys(\$GLOBALS['FAKE']['icons']);
                    }
                    public function getIconConfigurationByIdentifier(string \$identifier): array {
                        return ['options' => ['source' => \$GLOBALS['FAKE']['icons'][\$identifier] ?? '']];
                    }
                    public function getDeprecatedIcons(): array { return []; }
                }
            }
            namespace Fake {
                class Container {
                    public function get(string \$id) { return new \$id(); }
                }
            }
            namespace {
                return new \\Fake\\Container();
            }
            PHP);
    }
}
