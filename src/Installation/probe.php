<?php

declare(strict_types=1);

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
    //
    // What TCA does not carry is which extension an entry belongs to, and an
    // answer about one extension cannot use a list belonging to all of them.
    // So each entry travels with what names an extension in it: a label or a
    // ctrl title is `LLL:EXT:<key>/…`, and an item's icon resolves through the
    // registry to `EXT:<key>/…`. Both are read here, attributed on the other
    // side, and where neither names anything the entry is the installation's
    // rather than a package's.
    $tca = is_array($GLOBALS['TCA'] ?? null) ? $GLOBALS['TCA'] : [];
    $tables = [];
    foreach ($tca as $table => $configuration) {
        $title = $configuration['ctrl']['title'] ?? '';
        $tables[(string) $table] = is_string($title) ? $title : '';
    }
    $answer['topics']['tables'] = $tables;

    $contentElements = [];
    foreach ($tca['tt_content']['columns']['CType']['config']['items'] ?? [] as $item) {
        if (!is_array($item)) {
            continue;
        }
        // Keyed since v12, positional before it, and both shapes are in the
        // wild because an extension is written for the line it supports.
        $value = $item['value'] ?? ($item[1] ?? null);
        if (!is_string($value) || $value === '' || $value === '--div--') {
            continue;
        }
        $label = $item['label'] ?? ($item[0] ?? '');
        $icon = $item['icon'] ?? ($item[2] ?? '');
        $contentElements[$value] = [
            'label' => is_string($label) ? $label : '',
            'icon' => is_string($icon) ? $icon : '',
        ];
    }
    $answer['topics']['contentElements'] = $contentElements;

    // The columns TYPO3 adds to a table by itself, which is what an
    // ext_tables.sql may leave out. DefaultTcaSchema is handed one empty table
    // per TCA table — it throws where one is missing — so everything it comes
    // back with was derived rather than declared. It reaches the ConnectionPool
    // for the platform of each table, which needs a database server that
    // answers and not a schema in it (D-DIS-008).
    //
    // In a try of its own: a failure here is one topic, and the icons and the
    // TCA above have already been read.
    try {
        $tables = [];
        foreach (array_keys($tca) as $table) {
            $tables[(string) $table] = new Doctrine\DBAL\Schema\Table((string) $table);
        }
        $derived = [];
        $enriched = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            TYPO3\CMS\Core\Database\Schema\DefaultTcaSchema::class,
        )->enrich($tables);
        foreach ($enriched as $table => $definition) {
            $columns = [];
            foreach ($definition->getColumns() as $column) {
                $default = $column->getDefault();
                $columns[] = [
                    'name' => $column->getName(),
                    'type' => Doctrine\DBAL\Types\Type::lookupName($column->getType()),
                    'notnull' => $column->getNotnull(),
                    'default' => is_scalar($default) || $default === null ? $default : (string) $default,
                    'length' => $column->getLength(),
                ];
            }
            // A table the enrichment created rather than enriched is an MM
            // table: it exists because a relation asked for it, and no
            // ext_tables.sql needs to declare it at all.
            $derived[(string) $table] = [
                'columns' => $columns,
                'relationTable' => !array_key_exists((string) $table, $tca),
            ];
        }
        $answer['topics']['derivedColumns'] = ['tables' => $derived];
    } catch (Throwable $failure) {
        $answer['topics']['derivedColumns'] = [
            'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
        ];
    }
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
