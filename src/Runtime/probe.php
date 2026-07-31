<?php

/**
 * What the running installation says about itself.
 *
 * This file is never included by the server. It is read as text and handed to
 * the installation's own interpreter as a subprocess — through DDEV where the
 * project runs there. Everything below therefore executes on the other side of
 * a process boundary, with the installation's autoloader, its PHP version and
 * its extensions, and a fatal error here is an exit code rather than a dead MCP
 * session.
 *
 * Two properties are load-bearing and both look like omissions:
 *
 * - No `declare(strict_types=1)`. The body is delivered through `php -r`, which
 *   wraps it, and a declare is only legal as the very first statement of a
 *   script. Typo3Runtime strips the opening tag for the same reason.
 * - The autoloader path is relative and is substituted into the literal below
 *   before delivery. The two sides of DDEV do not share absolute paths: the
 *   subprocess is started with the installation root as its working directory,
 *   and inside the container that same root is /var/www/html.
 *
 * It prints one JSON object on stdout and nothing else. TYPO3's own output
 * buffer is discarded first, because an extension that echoes during boot would
 * otherwise sit in front of the payload.
 */

$answer = ['state' => 'unreachable', 'reason' => '', 'topics' => []];

try {
    // Replaced by Typo3Runtime before delivery; a literal so the file stays
    // valid PHP that can be linted and read on its own.
    $autoload = 'vendor/autoload.php';
    if (!is_file($autoload)) {
        $answer['reason'] = 'no autoloader at ' . $autoload . ' below ' . getcwd();
        throw new RuntimeException('', 1);
    }

    $classLoader = require $autoload;
    TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::run(
        0,
        TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_CLI
    );
    $container = TYPO3\CMS\Core\Core\Bootstrap::init($classLoader);

    // A system without essential configuration boots into a failsafe container:
    // core packages only, no ext_localconf.php, no TCA. Its registries answer,
    // and what they answer is a subset that looks like the whole. Naming that
    // state is the entire point of asking.
    if ($container instanceof TYPO3\CMS\Core\DependencyInjection\FailsafeContainer) {
        $answer['state'] = 'failsafe';
        $answer['reason'] = 'the installation has no essential configuration yet, so TYPO3 booted failsafe '
            . 'with core packages only and no extension registrations';
        throw new RuntimeException('', 1);
    }

    $answer['state'] = 'full';

    $registry = $container->get(TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $icons = [];
    foreach ($registry->getAllRegisteredIconIdentifiers() as $identifier) {
        $identifier = (string) $identifier;
        $configuration = $registry->getIconConfigurationByIdentifier($identifier);
        $options = is_array($configuration['options'] ?? null) ? $configuration['options'] : [];
        // The source is what says which extension an identifier belongs to:
        // EXT:news/Resources/Public/Icons/… is the only attribution the
        // registry carries, and a bitmap or sprite icon names it differently.
        $source = $options['source'] ?? ($options['name'] ?? '');
        $icons[$identifier] = is_string($source) ? $source : '';
    }
    $answer['topics']['icons'] = $icons;
    $answer['topics']['deprecatedIcons'] = array_keys($registry->getDeprecatedIcons());

    // TCA as it is after every extension has had its say, which is where the
    // tables an extension adds through a PHP call and the content elements
    // registered from a variable exist at all.
    $tca = is_array($GLOBALS['TCA'] ?? null) ? $GLOBALS['TCA'] : [];
    $answer['topics']['tables'] = array_keys($tca);

    $contentElements = [];
    foreach ($tca['tt_content']['columns']['CType']['config']['items'] ?? [] as $item) {
        if (!is_array($item)) {
            continue;
        }
        // Keyed since v12, positional before it, and both shapes are in the
        // wild because an extension is written for the line it supports.
        $value = $item['value'] ?? ($item[1] ?? null);
        if (is_string($value) && $value !== '' && $value !== '--div--') {
            $contentElements[] = $value;
        }
    }
    $answer['topics']['contentElements'] = $contentElements;
} catch (Throwable $failure) {
    if ($answer['reason'] === '') {
        $answer['state'] = 'unreachable';
        $answer['reason'] = get_class($failure) . ': ' . $failure->getMessage();
    }
}

while (ob_get_level() > 0) {
    ob_end_clean();
}
fwrite(STDOUT, (string) json_encode($answer));
