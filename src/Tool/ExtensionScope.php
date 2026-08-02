<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Installation\Extension;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Runtime;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Result\Unanswered;

/**
 * What one extension registers, from its own files.
 */
final class ExtensionScope extends ReadOnlyTool
{
    /**
     * The fields the extension schema requires, empty. A miss answers with the
     * same shape as a hit, so a client never has to branch on which it got.
     *
     * answeredBy is not among them, because the two misses this fills are not
     * the same answer: an installation that has other extensions and not this
     * one has answered, and a directory with no installation has not. Sharing
     * the value made every miss report "nothing", which says the installation
     * could not be asked about an installation that just listed 27 packages.
     *
     * @var array<string, mixed>
     */
    private const MISS_FIELDS = [
        'path' => null,
        'origin' => null,
        'composerName' => null,
        'description' => null,
        'requires' => [],
        'tcaTables' => [],
        'tcaOverrides' => [],
        'contentElements' => [],
        'backendModules' => [],
        'backendRoutes' => [],
        'icons' => [],
        'siteSets' => [],
        'middlewares' => [],
        'serviceTags' => [],
        'fluidRoots' => [],
        'fluidNamespaces' => [],
        'typoScript' => [],
        'classes' => [],
        'files' => [],
        'notReadStatically' => [],
        'artifacts' => ['manual' => null, 'readme' => null, 'tests' => [], 'languageFiles' => []],
    ];

    public static function name(): string
    {
        return 'typo3_extension_scope';
    }

    public static function description(): string
    {
        return 'Describe what one installed extension registers: the tables its TCA defines and the ones it extends, the content elements it adds to tt_content and the Fluid template each renders through, its backend modules and routes, its icons, its site sets, the service tags it hangs into the container, its middlewares, its Fluid roots and namespaces, and the shape of its Classes/ directory — and what it ships beside all of that: its manual, its README, the test layers it has, and its XLF files with the source language each one declares. Those four are answered even when they are not there, because the absence of a manual or a translation is what a file listing cannot show. The tables, content elements and icons are read from the booted installation where there is one and attributed to this extension by the EXT: reference each entry carries, so a list built in a loop or a table added by a PHP call is in the answer; everything else is read from that extension\'s own files, parsed and never executed, so it answers on a fresh clone and for a third-party extension as well as for the project\'s own. answeredBy says which of the two answered, and where it says packages the answer names what that leaves out. typo3_project_scope names the extensions this can be called for.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'extension' => ['type' => 'string', 'minLength' => 1, 'description' => 'The extension key, as typo3_project_scope reports it, for example "my_sitepackage" or "news".'],
            ],
            'required' => ['extension'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'key' => Schema::string('The extension key that was asked for.'),
            'path' => Schema::nullableString('Absolute path of the extension. Null when the installation does not have it.'),
            'origin' => ['type' => ['string', 'null'], 'enum' => ['system', 'project', 'third-party', 'fixture', null], 'description' => 'system: TYPO3\'s own. project: inside the repository. third-party: installed as a dependency. fixture: below a Tests/ directory, so it belongs to the test setup.'],
            'composerName' => Schema::nullableString('The Composer package name it declares.'),
            'description' => Schema::nullableString('What its composer.json says it is.'),
            'requires' => Schema::listOf(Schema::object([
                'package' => Schema::string(),
                'constraint' => Schema::string(),
            ], ['package', 'constraint']), 'What it requires, which is where a version conflict during an upgrade comes from.'),
            'tcaTables' => Schema::listOf(Schema::string(), 'Tables its Configuration/TCA/ defines, by file name.'),
            'tcaOverrides' => Schema::listOf(Schema::string(), 'Tables it extends below Configuration/TCA/Overrides/.'),
            'contentElements' => Schema::listOf(Schema::object([
                'identifier' => Schema::string('The CType value, read from an addTcaSelectItem() call in one of those override files. An identifier assembled at runtime or taken from a constant is not among them.'),
                'templateName' => Schema::nullableString('The Fluid template it renders through, from tt_content.<identifier>.templateName in this extension\'s TypoScript. Null where its TypoScript does not set one — another extension or the site configuration may.'),
                'source' => Schema::nullableString('The TypoScript file that set it, relative to the extension.'),
            ], ['identifier', 'templateName', 'source']), 'The content elements it adds to tt_content, and where each renders.'),
            'backendModules' => Schema::listOf(Schema::string(), 'Module identifiers from Configuration/Backend/Modules.php.'),
            'backendRoutes' => Schema::listOf(Schema::string(), 'Route names from Configuration/Backend/Routes.php and AjaxRoutes.php.'),
            'icons' => Schema::listOf(Schema::string(), 'Identifiers from Configuration/Icons.php. typo3_icon_lookup searches every package at once.'),
            'siteSets' => Schema::listOf(Schema::object([
                'name' => Schema::string('The composer-style set name a site depends on.'),
                'path' => Schema::string('Relative to the extension.'),
            ], ['name', 'path'])),
            'middlewares' => Schema::listOf(Schema::string(), 'Middleware identifiers from Configuration/RequestMiddlewares.php, across the request scopes.'),
            'serviceTags' => Schema::listOf(Schema::string(), 'Tags its Services.yaml carries, such as data.processor, event.listener or console.command.'),
            'fluidRoots' => Schema::listOf(Schema::string(), 'Which of Resources/Private/Templates, Partials and Layouts exist.'),
            'fluidNamespaces' => Schema::listOf(Schema::string(), 'Prefixes it registers globally in Configuration/Fluid/Namespaces.php.'),
            'typoScript' => Schema::listOf(Schema::string(), 'Files below Configuration/TypoScript/.'),
            'classes' => Schema::listOf(Schema::object([
                'kind' => Schema::string('The Classes/ subdirectory, for example EventListener or DataProcessing.'),
                'files' => Schema::integer('PHP files below it.'),
            ], ['kind', 'files'])),
            'files' => Schema::listOf(Schema::string(), 'Registration files it ships, from ext_localconf.php to Initialisation/data.t3d.'),
            'notReadStatically' => Schema::listOf(Schema::string(), 'Registration files that are there but whose entries do not stand in their own text: each assembles its list while it runs, so what it registers is missing from the lists above rather than absent. The booted installation is what answers for them; an empty list here means every file that exists was read.'),
            'artifacts' => Schema::object([
                'manual' => Schema::nullableString('Its manual entry point, "Documentation/" where the directory exists without one, null where the extension ships no manual at all.'),
                'readme' => Schema::nullableString('The README it ships, null where there is none.'),
                'tests' => Schema::listOf(Schema::string(), 'The layers below Tests/, for example Unit and Functional. Empty where the extension ships no tests.'),
                'languageFiles' => Schema::listOf(Schema::object([
                    'path' => Schema::string('Relative to the extension.'),
                    'sourceLanguage' => Schema::nullableString('The source-language its own <file> element declares, null where it declares none. This is what the file says, not what it should say.'),
                    'translations' => Schema::listOf(Schema::string(), 'Locales of the prefixed files beside it, such as de for de.messages.xlf.'),
                ], ['path', 'sourceLanguage', 'translations'])),
            ], ['manual', 'readme', 'tests', 'languageFiles'], 'What it ships beside its registrations. Every key is present even when the artifact is not, because the absence of a manual, a test or a translation is the answer a file listing cannot give.'),
            'installed' => Schema::listOf(Schema::string(), 'On a miss: the extension keys this installation does have.'),
            'answeredBy' => Schema::answeredBy(),
            'unavailable' => Schema::unavailable(),
        ], ['key', 'path', 'origin', 'tcaTables', 'tcaOverrides', 'contentElements', 'backendModules', 'icons', 'siteSets', 'serviceTags', 'files', 'notReadStatically', 'artifacts', 'answeredBy']);
    }

    public static function answer(array $args): ToolResult
    {
        $key = trim((string) ($args['extension'] ?? ''));
        $extension = $key === '' ? null : Extension::describe($key);
        if ($extension === null) {
            return self::miss($key);
        }

        $lines = [sprintf(
            '%s (%s) — %s%s',
            $extension['key'],
            $extension['origin'],
            $extension['path'],
            $extension['description'] === null ? '' : "\n" . $extension['description'],
        )];

        $sections = [
            'TCA tables it defines' => $extension['tcaTables'],
            'TCA tables it extends' => $extension['tcaOverrides'],
            'Backend modules' => $extension['backendModules'],
            'Backend routes' => $extension['backendRoutes'],
            'Icons' => $extension['icons'],
            'Middlewares' => $extension['middlewares'],
            'Service tags' => $extension['serviceTags'],
            'Fluid roots' => $extension['fluidRoots'],
            'Fluid namespaces declared globally' => $extension['fluidNamespaces'],
            'TypoScript files' => $extension['typoScript'],
            'Registration files' => $extension['files'],
        ];
        foreach ($sections as $heading => $entries) {
            if ($entries !== []) {
                $lines[] = '';
                $lines[] = $heading . ': ' . implode(', ', $entries);
            }
        }

        if ($extension['contentElements'] !== []) {
            $lines[] = '';
            $lines[] = 'Content elements it adds:';
            foreach ($extension['contentElements'] as $element) {
                $lines[] = $element['templateName'] === null
                    ? sprintf(
                        '- %s — no templateName in this extension\'s TypoScript; another extension or the site '
                        . 'may set it',
                        $element['identifier'],
                    )
                    : sprintf(
                        '- %s — renders through %s (%s)',
                        $element['identifier'],
                        $element['templateName'],
                        $element['source'],
                    );
            }
            $lines[] = 'The identifiers come from the addRecordType() and addTcaSelectItem() calls below '
                . 'Configuration/TCA/Overrides/ and the templates from tt_content.<identifier>.templateName in its '
                . 'TypoScript. A value the file assigns to a variable once is followed there; one a call puts '
                . 'together at runtime, takes from a constant, or reads from a variable that file assigns more than '
                . 'once, is in neither.';
        }

        if ($extension['siteSets'] !== []) {
            $lines[] = '';
            $lines[] = 'Site sets:';
            foreach ($extension['siteSets'] as $set) {
                $lines[] = '- ' . $set['name'] . ' (' . $set['path'] . ')';
            }
        }

        if ($extension['classes'] !== []) {
            $lines[] = '';
            $lines[] = 'Classes: ' . implode(', ', array_map(
                static fn(array $kind): string => $kind['kind'] . ' (' . $kind['files'] . ')',
                $extension['classes'],
            ));
        }

        if ($extension['requires'] !== []) {
            $lines[] = '';
            $lines[] = 'Requires: ' . implode(', ', array_map(
                static fn(array $requirement): string => $requirement['package'] . ' ' . $requirement['constraint'],
                $extension['requires'],
            ));
        }

        // Always rendered, present or not. Everything above is found by reading
        // further; these are the ones a caller finds by being told, because a
        // manual nobody wrote leaves no file to notice.
        $artifacts = $extension['artifacts'];
        $lines[] = '';
        $lines[] = 'Ships: ' . implode(', ', [
            'manual ' . ($artifacts['manual'] ?? 'none'),
            'readme ' . ($artifacts['readme'] ?? 'none'),
            'tests ' . ($artifacts['tests'] === [] ? 'none' : implode('+', $artifacts['tests'])),
        ]);
        if ($artifacts['languageFiles'] !== []) {
            foreach ($artifacts['languageFiles'] as $file) {
                $lines[] = sprintf(
                    '- %s — source-language %s, %s',
                    $file['path'],
                    $file['sourceLanguage'] ?? 'not declared',
                    $file['translations'] === []
                        ? 'no translations beside it'
                        : 'translated into ' . implode(', ', $file['translations']),
                );
            }
            $lines[] = 'The source language is what each file declares, not what it should declare — '
                . 'typo3_architecture_lookup owns that rule.';
        }

        $lines[] = '';
        // The boundary of this answer, stated rather than implied — and it is
        // not the same boundary in both cases, which is why it is not one
        // sentence with a clause bolted on.
        $lines[] = Extension::answeredBy() === 'installation'
            ? 'The tables, content elements and icons are what the booted installation has, attributed to this '
                . 'extension by the EXT: reference each entry carries; everything else is read from its files. '
                . 'What a hook or an event listener changes at request time is in neither.'
            : 'Read from the files, so this is what the extension declares — not what it does at runtime. '
                . 'Registrations made in ext_localconf.php with a PHP call, a table or an icon list built in a '
                . 'loop, and anything a hook or an event listener changes, are not in this list; the files that '
                . 'could hold them are named above. The installation itself was not asked: '
                . Typo3Runtime::reason() . '.';

        if ($extension['notReadStatically'] !== []) {
            // Which files the degradation cost, beside the reason it names: an
            // omitted section is otherwise the same silence as a file that is
            // not there at all.
            $lines[] = 'Nothing could be read statically from ' . implode(', ', $extension['notReadStatically'])
                . ': each is there, and each assembles its list while it runs, so what it registers is missing '
                . 'from the lists above rather than absent.';
        }

        return ToolResult::create(implode("\n", $lines), $extension + ['answeredBy' => Extension::answeredBy()]);
    }

    /** The keys there are, so a miss is a question a caller can ask again. */
    private static function miss(string $key): ToolResult
    {
        $installed = array_keys(Instance::packages());
        if ($installed === []) {
            return Unanswered::because(
                'no TYPO3 installation was found, so there are no extensions to describe',
                self::MISS_FIELDS + ['key' => $key],
            );
        }

        return ToolResult::create(
            $key === ''
                ? 'Name the extension to describe. This installation has: ' . implode(', ', $installed) . '.'
                : sprintf(
                    'This installation has no extension "%s". It has: %s.',
                    $key,
                    implode(', ', $installed),
                ),
            // The package list is what answered, and it is read from the
            // metadata the installed packages ship rather than from the booted
            // container — which is what "packages" says.
            self::MISS_FIELDS + ['key' => $key, 'installed' => $installed, 'answeredBy' => 'packages'],
        );
    }
}
