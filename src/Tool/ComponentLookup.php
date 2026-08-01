<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Knowledge\Catalog\Components;
use Typo3CmsMcp\Knowledge\Catalog\InstalledComponents;
use Typo3CmsMcp\Knowledge\Catalog\Meta as CatalogMeta;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Result\Provenance;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;

/**
 * TYPO3 backend UI components: their markup, classes and custom properties.
 */
final class ComponentLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_component_lookup';
    }

    public static function description(): string
    {
        return 'Look up TYPO3 backend UI components by name or topic. Where the target is the active installation, its backend CSS, JavaScript, and installed styleguide templates supply the component contract; the curated catalog supplies the searchable names and fallback markup. Without usable installed sources, the bundled version-bound snapshot answers. Returns markup, classes, custom properties, and every source used.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Component name, class, or topic, for example badge, card, search box, or input-group. Omit to list the catalog.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the markup has to hold for, for example "13.4" or "14". Components not verified there are withheld. Defaults to the version of the installation this server was started in; where there is none, the whole catalog is returned and every entry carries the versions it was verified on.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'query' => Schema::nullableString(),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major the answer was composed for — stated by the caller, or read from the installation. Null means nothing was withheld and every entry carries the versions it was verified on.'],
            'matchCount' => Schema::integer('How many components hold on the target version. Ones withheld for it are in withheld, not here.'),
            'components' => Schema::listOf(Schema::object([
                'name' => Schema::string(),
                'title' => Schema::string(),
                'summary' => Schema::string(),
                'rootClass' => Schema::string(),
                'variants' => Schema::listOf(Schema::string()),
                'modifiers' => Schema::listOf(Schema::string()),
                'subComponents' => Schema::listOf(Schema::string()),
                'customProperties' => Schema::listOf(Schema::string()),
                'markup' => Schema::string('Canonical markup of the component.'),
                'examples' => Schema::listOf(Schema::string()),
                'sassPath' => Schema::nullableString('Primary Sass source in the core checkout; null for a web component that carries its own styles.'),
                'sassPaths' => Schema::listOf(Schema::string(), 'Every Sass source the component spans. A component can be split across several files.'),
                'demoPath' => Schema::nullableString('Styleguide demo in the core checkout, if there is one.'),
                'matchedIn' => Schema::listOf(Schema::string(), 'Where the query matched: name, keywords, sub-component classes, description.'),
                'classes' => Schema::listOf(Schema::string(), 'Every class of this component found in the installed backend CSS. Empty for a bundled fallback or a custom element without external CSS.'),
                'sourceFiles' => Schema::listOf(Schema::string(), 'Installed package files consulted for the component contract. Empty for the bundled fallback.'),
                'markupSource' => ['type' => 'string', 'enum' => ['installation', 'catalog'], 'description' => 'Whether markup came from an installed styleguide example or the bundled curated fallback.'],
                'contractVersion' => Schema::string('TYPO3 version whose classes and custom properties this entry describes.'),
                'describesVersion' => Schema::string('TYPO3 version whose markup this entry describes. It can differ from contractVersion when the installed styleguide has no matching example and bundled markup is the fallback.'),
            ] + Schema::verifiedOn(), ['name', 'title', 'rootClass', 'sassPath', 'demoPath', 'classes', 'sourceFiles', 'markupSource', 'contractVersion', 'describesVersion', 'verifiedOn'])),
            'withheld' => Schema::withheldComponents(),
            'checklist' => Schema::object([
                'title' => Schema::string(),
                'intro' => Schema::string(),
                'items' => Schema::listOf(Schema::string()),
            ], ['title', 'items']),
            'componentSource' => ['type' => 'string', 'enum' => ['installation', 'catalog'], 'description' => 'installation when the class and custom-property contract was read from the active TYPO3 packages; catalog when the bundled snapshot answered.'],
            'catalog' => Schema::catalogProvenance(),
        ], ['matchCount', 'components', 'withheld', 'componentSource', 'catalog']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);
        $installed = InstalledComponents::isAvailable($target);
        $split = Components::splitByTarget(Components::find($query, $target), $target);
        $components = $split['holds'];
        $withheld = $split['withheld'];
        $withheldNote = Provenance::withheldNote($withheld, $target);
        $sourceNote = Provenance::sourceNote($installed);

        if ($components === []) {
            return ToolResult::create(
                sprintf(
                    "No TYPO3 component%s matched \"%s\". Try a component name (badge, card), a class (input-group), or a topic (search box). %s\n%s%s",
                    $withheld === [] ? '' : sprintf(' verified on TYPO3 v%d', (int) $target),
                    (string) $query,
                    $installed
                        ? 'The installed packages were checked, but the searchable component index remains curated; inspect the installed backend CSS for an uncatalogued class.'
                        : Provenance::MISS_NOTE,
                    $withheldNote === '' ? '' : $withheldNote . "\n",
                    $sourceNote,
                ),
                [
                    'query' => $query,
                    'targetVersion' => $target,
                    'matchCount' => 0,
                    'components' => [],
                    'withheld' => Provenance::withheldRecords($withheld),
                    'componentSource' => $installed ? 'installation' : 'catalog',
                    'catalog' => Provenance::catalogRecord($installed),
                ],
            );
        }

        // A specific query returns the best matches; an empty query lists names.
        if ($query === null || trim($query) === '') {
            $names = implode("\n", array_map(
                static fn(array $c): string => '- ' . $c['name'] . ' — ' . $c['title'],
                $components
            ));

            return ToolResult::create(
                "TYPO3 backend component catalog:\n" . $names
                    . ($withheldNote === '' ? '' : "\n\n" . $withheldNote)
                    . "\n\n" . $sourceNote,
                [
                    'query' => $query,
                    'targetVersion' => $target,
                    'matchCount' => count($components),
                    // The listing is an overview, so the entries stay lean; query a
                    // component by name for its markup and class contract.
                    'components' => array_map(static fn(array $c): array => [
                        'name' => $c['name'],
                        'title' => $c['title'],
                        'summary' => $c['summary'],
                        'rootClass' => $c['rootClass'],
                        'sassPath' => $c['sassPath'],
                        'sassPaths' => $c['sassPaths'],
                        'demoPath' => $c['demoPath'],
                    ] + Provenance::sourceRecord($c) + Provenance::verifiedRecord($c), $components),
                    'withheld' => Provenance::withheldRecords($withheld),
                    'componentSource' => $installed ? 'installation' : 'catalog',
                    'catalog' => Provenance::catalogRecord($installed),
                ],
            );
        }

        // Only the best matches are described in full; the rest stay in the count.
        $described = array_slice($components, 0, 3);

        $blocks = array_map(static function (array $c): string {
            $lines = ['## ' . $c['title'] . ' (`' . $c['rootClass'] . '`)'];
            if (($c['matchedIn'] ?? []) !== []) {
                $lines[] = 'Matched in: ' . implode(', ', $c['matchedIn']);
                // A component reached only through a sub-component class or a
                // word in its description is a neighbour of what was asked
                // for, not an answer to it.
                if (array_intersect(['name', 'keywords'], $c['matchedIn']) === []) {
                    $lines[] = 'Related, not the component you asked for: it matched through '
                        . implode(' and ', $c['matchedIn']) . ' only.';
                }
            }
            if ($c['summary'] !== '') {
                $lines[] = $c['summary'];
            }

            $lines[] = '';
            $lines[] = 'Markup:';
            $lines[] = '```html';
            $lines[] = $c['markup'];
            $lines[] = '```';

            $appendList = static function (string $label, array $items) use (&$lines): void {
                if ($items !== []) {
                    $lines[] = $label . ': ' . implode(', ', $items);
                }
            };
            $appendList('Variants', $c['variants']);
            $appendList('Modifiers', $c['modifiers']);
            $appendList('Sub-components', $c['subComponents']);
            $appendList('Custom properties', $c['customProperties']);
            if (($c['_installed'] ?? false) === true) {
                $cataloguedClasses = array_merge(
                    [$c['rootClass']],
                    $c['variants'],
                    $c['modifiers'],
                    $c['subComponents'],
                );
                $appendList('Other installed classes', array_values(array_diff($c['classes'], $cataloguedClasses)));
            }

            $lines[] = $c['sassPaths'] === []
                ? 'Sass source: none — this is a web component and carries its styles in its element source.'
                : (($c['_installed'] ?? false) ? 'Curated Sass source path' : 'Sass source')
                    . (count($c['sassPaths']) > 1 ? 's' : '') . ': ' . implode(', ', $c['sassPaths']);
            $lines[] = 'Styleguide demo: ' . ($c['demoPath'] ?? 'none (not a styleguide component)');
            if (($c['_installed'] ?? false) === true) {
                $lines[] = 'Installed sources: ' . implode(', ', $c['sourceFiles']);
                $lines[] = $c['markupSource'] === 'installation'
                    ? 'Markup source: installed styleguide template.'
                    : 'Markup source: bundled TYPO3 ' . CatalogMeta::read()['source']['version']
                        . ' fallback; no matching example was available in the installed packages.';
            }
            $label = Versions::label($c['since'], $c['until']);
            if ($label !== '') {
                // Beside the markup rather than in the block at the end: a
                // client that renders one component shows the classes without
                // any statement of where they exist.
                $lines[] = 'Verified on: ' . $label . '.';
            }

            if ($c['examples'] !== []) {
                $lines[] = '';
                $lines[] = 'Examples:';
                foreach ($c['examples'] as $example) {
                    $lines[] = '```html';
                    $lines[] = $example;
                    $lines[] = '```';
                }
            }

            return implode("\n", $lines);
        }, $described);

        if ($withheldNote !== '') {
            $blocks[] = $withheldNote;
        }

        $checklist = Components::checklist();
        $checklistLines = ['## ' . $checklist['title']];
        if ($checklist['intro'] !== '') {
            $checklistLines[] = $checklist['intro'];
        }
        foreach ($checklist['items'] as $item) {
            $checklistLines[] = '- [ ] ' . $item;
        }
        $blocks[] = implode("\n", $checklistLines);
        if ($target !== null) {
            $blocks[] = $installed
                ? sprintf(
                    'Answered from installed TYPO3 v%d package evidence; an indexed component absent there is withheld.',
                    $target,
                )
                : sprintf(
                    'Answered for TYPO3 v%d: every component above was verified there, and one that was not is '
                        . 'withheld instead. The snapshot below says which revision the catalog was read from, not '
                        . 'which versions an entry holds on — each entry states that itself.',
                    $target,
                );
        }
        $blocks[] = $sourceNote;

        return ToolResult::create(implode("\n\n", $blocks), [
            'query' => $query,
            'targetVersion' => $target,
            'matchCount' => count($components),
            'components' => array_map(static fn(array $c): array => [
                'name' => $c['name'],
                'title' => $c['title'],
                'summary' => $c['summary'],
                'rootClass' => $c['rootClass'],
                'variants' => $c['variants'],
                'modifiers' => $c['modifiers'],
                'subComponents' => $c['subComponents'],
                'customProperties' => $c['customProperties'],
                'markup' => $c['markup'],
                'examples' => $c['examples'],
                'sassPath' => $c['sassPath'],
                'sassPaths' => $c['sassPaths'],
                'demoPath' => $c['demoPath'],
                'matchedIn' => $c['matchedIn'] ?? [],
            ] + Provenance::sourceRecord($c) + Provenance::verifiedRecord($c), $described),
            'withheld' => Provenance::withheldRecords($withheld),
            'checklist' => $checklist,
            'componentSource' => $installed ? 'installation' : 'catalog',
            'catalog' => Provenance::catalogRecord($installed),
        ]);
    }
}
