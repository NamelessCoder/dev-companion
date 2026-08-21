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
 * What a caller asked for is substituted the same way, as one array. The topics
 * that read it are the ones no other reading wants: TYPO3_CONF_VARS is around
 * 50 kB of JSON before an extension has added to it, and a flex field costs a
 * resolution nobody who asked about an icon has a use for.
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
    // What the call asked for, substituted the same way. Empty unless a caller
    // asked a topic that takes an argument, and then the only reason that topic
    // is read at all.
    $parameters = [];
    $configurationPath = (string) ($parameters['configurationPath'] ?? '');
    $flexForm = is_array($parameters['flexForm'] ?? null) ? $parameters['flexForm'] : null;
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

    // One path out of TYPO3_CONF_VARS as it stands after every extension has
    // had its say. `ArrayUtility` is the core's own reading of such a path and
    // is what `configuration:show --type=active` traverses with, so a caller
    // gets the same value on a line that has that command and on the two that
    // do not.
    //
    // In a try of its own: a failure here is one topic.
    if ($configurationPath !== '') {
        try {
            $found = TYPO3\CMS\Core\Utility\ArrayUtility::isValidPath(
                $GLOBALS['TYPO3_CONF_VARS'],
                $configurationPath,
            );
            $answer['topics']['configuration'] = [
                'found' => $found,
                'value' => $found ? TYPO3\CMS\Core\Utility\ArrayUtility::getValueByPath(
                    $GLOBALS['TYPO3_CONF_VARS'],
                    $configurationPath,
                ) : null,
            ];
        } catch (Throwable $failure) {
            $answer['topics']['configuration'] = [
                'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
            ];
        }
    }

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

    // One type=flex column resolved the way FormEngine resolves it, which is
    // the two calls TcaFlexPrepare makes and nothing else: the identifier, and
    // the structure that identifier parses to. Everything between them is the
    // installation's — the events a package listens to, the file a sheet is
    // held in, the migration and the preparation — and none of it is in the
    // file the TCA points at.
    //
    // The row is the caller's. FlexFormTools needs one to find the key with,
    // and nothing here loads one.
    //
    // Read only where a caller asked, for the reason the configuration path is.
    if ($flexForm !== null) {
        $table = (string) ($flexForm['table'] ?? '');
        $field = (string) ($flexForm['field'] ?? '');
        $record = is_array($flexForm['record'] ?? null) ? $flexForm['record'] : [];
        $tableTca = is_array($tca[$table] ?? null) ? $tca[$table] : null;
        $columnTca = is_array($tableTca['columns'][$field] ?? null) ? $tableTca['columns'][$field] : null;
        $configuration = is_array($columnTca['config'] ?? null) ? $columnTca['config'] : [];
        $declared = $configuration['ds'] ?? null;

        $flexColumns = [];
        foreach ($tableTca['columns'] ?? [] as $column => $other) {
            if (($other['config']['type'] ?? '') === 'flex') {
                $flexColumns[] = (string) $column;
            }
        }

        // What a caller can put in the record to reach another structure, read
        // off the declaration in the shape this installation writes it in. An
        // array of structures is keyed by the pointer fields; a single one is
        // overridden per record type. Those are the two mechanisms the covered
        // majors differ by, and the core's own resolution branches on the same
        // shape rather than on a version.
        $keys = [];
        $pointerFields = [];
        if (is_array($declared)) {
            $keys = array_map('strval', array_keys($declared));
            $pointerFields = array_values(array_filter(array_map(
                'trim',
                explode(',', (string) ($configuration['ds_pointerField'] ?? '')),
            )));
        } elseif (is_string($declared) && $declared !== '') {
            $keys = ['default'];
            foreach ($tableTca['types'] ?? [] as $type => $override) {
                if (is_string($override['columnsOverrides'][$field]['config']['ds'] ?? null)) {
                    $keys[] = (string) $type;
                }
            }
        }

        $topic = [
            'table' => $table,
            'field' => $field,
            'tableFound' => $tableTca !== null,
            'type' => (string) ($configuration['type'] ?? ''),
            'flexFields' => $flexColumns,
            'recordTypeField' => (string) ($tableTca['ctrl']['type'] ?? ''),
            'keys' => array_values(array_unique($keys)),
            'pointerFields' => $pointerFields,
            'identifier' => '',
            'decoded' => null,
            'sheets' => [],
            'failure' => '',
        ];

        // What a caller writing or reading a FlexForm needs of an element,
        // rather than the prepared TCA of it: the same fields the backend form
        // labels an input with, and not the rest of what the preparation left
        // on it.
        $summarize = static function (array $elements) use (&$summarize): array {
            $fields = [];
            foreach ($elements as $name => $element) {
                if (!is_array($element)) {
                    continue;
                }
                $config = is_array($element['config'] ?? null) ? $element['config'] : [];
                $items = [];
                foreach ($config['items'] ?? [] as $item) {
                    if (is_array($item)) {
                        $items[] = [
                            'value' => (string) ($item['value'] ?? ($item[1] ?? '')),
                            'label' => (string) ($item['label'] ?? ($item[0] ?? '')),
                        ];
                    }
                }
                // A section holds container types and each of those holds
                // fields, which is the one nesting a data structure has.
                $section = ($element['section'] ?? '') === '1';
                $containers = [];
                $inContainers = $section && is_array($element['el'] ?? null) ? $element['el'] : [];
                foreach ($inContainers as $container => $inside) {
                    $containers[] = [
                        'container' => (string) $container,
                        'title' => (string) ($inside['title'] ?? ''),
                        'fields' => $summarize(is_array($inside['el'] ?? null) ? $inside['el'] : []),
                    ];
                }
                $default = $config['default'] ?? null;
                $fields[] = [
                    'field' => (string) $name,
                    // A field carries a label and a section carries a title,
                    // which is the same line to a reader of the answer.
                    'label' => (string) ($element['label'] ?? ($element['title'] ?? '')),
                    'description' => (string) ($element['description'] ?? ''),
                    'type' => $section ? 'section' : (string) ($config['type'] ?? ''),
                    'renderType' => (string) ($config['renderType'] ?? ''),
                    'required' => (bool) ($config['required'] ?? false),
                    'default' => is_scalar($default) ? $default : null,
                    'items' => $items,
                    'containers' => $containers,
                ];
            }

            return $fields;
        };

        // In a try of its own, and its failure is the answer rather than a
        // missing topic: an empty ds, a column that is not type=flex and a
        // record type nothing is registered for are all reported by throwing,
        // and what they throw is what the caller has to read.
        try {
            $tools = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class,
            );
            // The installation is asked what its own signature is rather than
            // told from a version number. TYPO3 v14 resolves against a TcaSchema
            // handed in and throws where it is null; v12 and v13 read the global
            // TCA and have no such parameter.
            $wantsSchema = (new ReflectionMethod($tools, 'getDataStructureIdentifier'))->getNumberOfParameters() > 4;
            $schema = null;
            if ($wantsSchema) {
                $factory = $container->get(TYPO3\CMS\Core\Schema\TcaSchemaFactory::class);
                $schema = $factory->has($table) ? $factory->get($table) : null;
            }

            $identifier = $wantsSchema
                ? $tools->getDataStructureIdentifier($columnTca ?? [], $table, $field, $record, $schema)
                : $tools->getDataStructureIdentifier($columnTca ?? [], $table, $field, $record);
            $parsed = $wantsSchema
                ? $tools->parseDataStructureByIdentifier($identifier, $schema)
                : $tools->parseDataStructureByIdentifier($identifier);

            $topic['identifier'] = (string) $identifier;
            $topic['decoded'] = json_decode((string) $identifier, true);
            foreach ($parsed['sheets'] ?? [] as $sheet => $definition) {
                $root = is_array($definition['ROOT'] ?? null) ? $definition['ROOT'] : [];
                $topic['sheets'][] = [
                    'sheet' => (string) $sheet,
                    'title' => (string) ($root['sheetTitle'] ?? ''),
                    'description' => (string) ($root['sheetDescription'] ?? ''),
                    'fields' => $summarize(is_array($root['el'] ?? null) ? $root['el'] : []),
                ];
            }
        } catch (Throwable $failure) {
            $topic['failure'] = get_class($failure) . ' (' . $failure->getCode() . '): ' . $failure->getMessage();
        }

        $answer['topics']['flexForm'] = $topic;
    }

    // The columns TYPO3 adds to a table by itself, which is what an
    // ext_tables.sql may leave out. DefaultTcaSchema is handed one empty table
    // per TCA table — it throws where one is missing — so everything it comes
    // back with was derived rather than declared. It reaches the ConnectionPool
    // for the platform of each table, which the MySQL, MariaDB and PostgreSQL
    // drivers ask the server for and the SQLite one does not (D-DIS-012).
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
    // A form data group is a dependency graph and not a list: every provider
    // declares `depends` and `before`, and what orders the run is what the core
    // resolves from those. The raw registry hands a reader the inputs and calls
    // it the answer — tcaDatabaseRecord has 61 providers, and the pair any one
    // question is about sits far apart in it with no edge between them.
    //
    // Ordered by the core's own service, with the two keys
    // `Form\FormDataGroup\OrderedProviderList` passes it. A second
    // implementation on the other side would answer confidently and, the day
    // the resolution changes, differently.
    //
    // In a try of its own, for the reason the enrichment above has one.
    try {
        $groups = [];
        $registry = $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup'] ?? [];
        $ordering = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            TYPO3\CMS\Core\Service\DependencyOrderingService::class,
        );
        foreach (is_array($registry) ? $registry : [] as $group => $providers) {
            if (!is_array($providers)) {
                continue;
            }
            $ordered = [];
            foreach ($ordering->orderByDependencies($providers, 'before', 'depends') as $provider => $declared) {
                $declared = is_array($declared) ? $declared : [];
                $ordered[] = [
                    'provider' => (string) $provider,
                    'depends' => array_values(array_map('strval', (array) ($declared['depends'] ?? []))),
                    'before' => array_values(array_map('strval', (array) ($declared['before'] ?? []))),
                ];
            }
            $groups[(string) $group] = $ordered;
        }
        $answer['topics']['formDataGroups'] = ['groups' => $groups];
    } catch (Throwable $failure) {
        $answer['topics']['formDataGroups'] = [
            'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
        ];
    }
    // The module tree as the registry resolved it. Two of the values exist
    // nowhere else: `navigationComponent` is inherited from the parent module,
    // so a Modules.php says nothing about whether a module is page-tree
    // navigated, and the routes beyond the module's own path are assembled per
    // module rather than declared. Reading the files instead is not a weaker
    // answer here, it is a wrong one — and EXT:backend's own Modules.php
    // references enum constants and cannot be included outside a booted core.
    //
    // The package and the labels are not on the registry's API, so the raw
    // configuration is read beside it. That is the same low-level access the
    // core's own debug:backend:modules takes, for the reason it states there.
    //
    // In a try of its own, for the reason the enrichment above has one.
    try {
        $registry = $container->get(TYPO3\CMS\Backend\Module\ModuleRegistry::class);
        $declared = $container->get('backend.modules')->getArrayCopy();
        $language = $GLOBALS['LANG'] = $container->get(
            TYPO3\CMS\Core\Localization\LanguageServiceFactory::class,
        )->create('en');

        $modules = [];
        foreach ($registry->getModules() as $module) {
            $identifier = $module->getIdentifier();
            $configuration = is_array($declared[$identifier] ?? null) ? $declared[$identifier] : [];

            $parents = [];
            for ($above = $module->getParentModule(); $above !== null; $above = $above->getParentModule()) {
                array_unshift($parents, $above->getIdentifier());
            }

            // What ModuleRegistry::registerRoutesForModules() registers for
            // this module, worked out its way: a first-level module that is not
            // standalone gets none, `_default` goes under the module identifier
            // and every other route under `<module>.<name>` below the module's
            // path. A module with no routable default throws rather than
            // answering, and that is a module with no routes.
            $routes = [];
            if ($module->hasParentModule() || $module->isStandalone()) {
                try {
                    $declaredRoutes = $module->getDefaultRouteOptions();
                } catch (Throwable $unroutable) {
                    $declaredRoutes = [];
                }
                foreach ($declaredRoutes as $name => $options) {
                    $name = (string) $name;
                    $below = (string) (($options['path'] ?? false) ?: ('/' . $name));
                    $routes[] = [
                        'name' => $name,
                        'identifier' => $name === '_default' ? $identifier : $identifier . '.' . $name,
                        'path' => $name === '_default' ? $module->getPath() : $module->getPath() . $below,
                        // The options carry the module object itself, so the
                        // fields are named rather than handed over whole.
                        'target' => is_string($options['target'] ?? null) ? $options['target'] : '',
                    ];
                }
            }

            $labels = $configuration['labels'] ?? '';
            if (is_array($labels)) {
                $labels = (string) ($labels['title'] ?? '');
            }
            $position = $module->getPosition();

            $modules[] = [
                'identifier' => $identifier,
                'parents' => $parents,
                'extension' => (string) ($configuration['packageName'] ?? ''),
                'labels' => trim($language->sL($module->getTitle()) . ' [' . $labels . ']'),
                'path' => $module->getPath(),
                'position' => $position === [] ? '' : (string) json_encode($position),
                'navigationComponent' => $module->getNavigationComponent(),
                'access' => $module->getAccess(),
                'routes' => $routes,
            ];
        }
        $answer['topics']['modules'] = ['modules' => $modules];
    } catch (Throwable $failure) {
        $answer['topics']['modules'] = [
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
