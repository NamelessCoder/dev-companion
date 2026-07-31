<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Typo3CmsMcp\Catalog\Components;
use Typo3CmsMcp\Catalog\InstalledComponents;
use Typo3CmsMcp\Catalog\Meta as CatalogMeta;
use Typo3CmsMcp\Catalog\References;
use Typo3CmsMcp\Catalog\SystemExtensions;
use Typo3CmsMcp\Catalog\TranslationDomain;

/**
 * Defines the knowledge tools and builds their answers.
 *
 * Every tool returns a ToolResult: the rendered text and the same answer as
 * data, matching the output schema declared for that tool in ToolSchemas. The
 * text is what makes an answer usable; the data is what makes it composable.
 */
final class Tools
{
    /**
     * The first TYPO3 major that resolves translation domains.
     *
     * Verified against the core: 13.4 has no TranslationDomain* class at all,
     * 14 ships the mapper. A domain written into a label below this renders
     * nothing — the failure is silent and at runtime, which is why this is the
     * one version fact the code carries rather than the knowledge base.
     */
    private const TRANSLATION_DOMAIN_SINCE = 14;

    /** @var array<string, array<int, string>> */
    private const CHANGE_TYPE_CHECKLIST = [
        'bugfix' => [
            'Reproduce the bug first, ideally with a failing test that the fix turns green.',
            'Check whether the bug also affects maintained older release branches.',
        ],
        'feature' => [
            'Add a changelog feature file under typo3/sysext/core/Documentation/Changelog/ for public API additions.',
            'Cover the new behaviour with functional tests, not only unit tests.',
        ],
        'cleanup' => ['Keep the cleanup mechanical; avoid mixing behavioural changes into the same patch.'],
        'test' => ['Confirm the test fails without the fix and passes with it; avoid asserting on incidental output.'],
        'documentation' => ['Run ./Build/Scripts/runTests.sh -s checkRst to validate ReST syntax.'],
        'unknown' => [],
    ];

    /** Appended when a catalog lookup finds nothing at all. */
    private const CATALOG_MISS_NOTE = 'Call typo3_catalog_scope for what this snapshot covers.';

    /**
     * The tool that answers a matched subject from the installation instead of
     * from memory. These are invented or misregistered in bulk when nobody
     * points at them, and fail at runtime rather than at build time.
     *
     * @var array<string, string>
     */
    private const HINT_TOOLS = [
        'backend-modules' => 'typo3_backend_module_lookup, to compare the declaration with modules registered '
            . 'by the active installation',
        'language-files' => 'typo3_label_lookup with the XLF resource used at the consuming code, while writing '
            . 'labels: a matching label elsewhere in the installation is not reusable in that context',
        'icon-usage' => 'typo3_icon_lookup, before writing an icon identifier: an unknown one renders an empty box',
    ];

    /** Extra domain signal carried by the change type itself. */
    private const CHANGE_TYPE_TERMS = [
        'documentation' => 'documentation changelog rst',
        'test' => 'unit test functional test',
        'feature' => 'changelog',
        'bugfix' => '',
        'cleanup' => '',
        'unknown' => '',
    ];

    /**
     * Every tool but typo3_feedback_record only reads bundled knowledge: same
     * arguments, same answer, no side effect, nothing outside this package.
     *
     * @var array<string, bool>
     */
    private const READ_ONLY_ANNOTATIONS = [
        'readOnlyHint' => true,
        'destructiveHint' => false,
        'idempotentHint' => true,
        'openWorldHint' => false,
    ];

    /**
     * @return array<int, array{
     *     name: string,
     *     description: string,
     *     inputSchema: array<string, mixed>,
     *     annotations: array<string, bool>,
     *     outputSchema: array<string, mixed>|null
     * }>
     */
    public static function definitions(): array
    {
        $definitions = [
            [
                'name' => 'typo3_server_scope',
                'description' => 'Orientation for this server: what it covers and at which depth, what it deliberately does not cover, and which tool to call when. Start here when it is unclear whether this server can answer a question at all, or which of the lookups is the right one.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
            ],
            [
                'name' => 'typo3_rule_lookup',
                'description' => 'Search the local TYPO3 core contribution rules and script notes by topic.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'minLength' => 1, 'description' => 'Topic to look up, in English, for example testing, review, deprecation, or code style.'],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name' => 'typo3_script_lookup',
                'description' => 'Find notes for TYPO3 core scripts and commands. They are the core checkout\'s own: a query that reads as a project or third-party extension is answered with the boundary instead of with commands that do not exist there.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'task' => ['type' => 'string', 'minLength' => 1, 'description' => 'The TYPO3 core task, in English, for example unit tests, functional tests, CGL, npm, or dependency install.'],
                    ],
                    'required' => ['task'],
                ],
            ],
            [
                'name' => 'typo3_task_guide',
                'description' => 'Build a task checklist enriched with matching architecture hints and relevant core checks. Built from bundled conventions only: it does not read your checkout, so it also names what you have to establish there yourself and routes to the lookups that fit the task. Work that reads as a project or third-party extension is answered with what transfers only — the core checks, checklist items and steps that name something only the core repository has are left out rather than handed over.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'task' => ['type' => 'string', 'minLength' => 1, 'description' => 'Short description of the TYPO3 core task, in English.'],
                        'area' => ['type' => 'string', 'description' => 'Affected subsystem or extension, if known.'],
                        'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version this task is for, for example "13.4" or "14". Conventions that do not hold there are left out. Defaults to the version of the installation this server was started in.'],
                        'changeType' => ['type' => 'string', 'enum' => ['bugfix', 'feature', 'cleanup', 'test', 'documentation', 'unknown'], 'default' => 'unknown'],
                    ],
                    'required' => ['task'],
                ],
            ],
            [
                'name' => 'typo3_test_run_guide',
                'description' => 'Recommend Build/Scripts/runTests.sh commands by topic. Pass the changed paths and the answer is narrowed to the suites that can actually fail on them — a Sass-only change gets the CSS suites, not the PHP ones. Which suites the script offers changes between majors, so a suite that branch does not have is left out rather than handed over as a command. The script belongs to the core repository, so paths that read as a project or third-party extension get no suite at all rather than commands that cannot run there.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Test or script topic, for example functional, phpstan, TypeScript, composer, or CGL.'],
                        'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'The changed TYPO3 core file paths, relative to the core checkout. Given, only suites touching their domains are returned.'],
                        'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the commands have to run on, for example "13.4" or "14". Suites that branch\'s runTests.sh does not have are left out. Defaults to the version of the installation this server was started in; where there is none, every suite is listed.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_architecture_lookup',
                'description' => 'Return architecture hints for TYPO3 core paths or task topics, grouped by section. Where the paths read as a project or third-party extension the hints still come back — the conventions transfer — but without their core check commands. The "Backend CSS" and "Backend TypeScript" sections describe the TYPO3 backend interface and are withheld, with the reason, where the task names the frontend.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'TYPO3 core file paths related to the task, relative to the core checkout.'],
                        'task' => ['type' => 'string', 'description' => 'Short task description or architecture topic, in English. Matching is lexical against English text, so another language reaches only the loanwords.'],
                        'id' => ['type' => 'string', 'description' => 'Ask for one hint by its id, for example language-files, instead of matching. Every answer that returns no hint lists the ids there are, so a subject that exists can be requested by name rather than guessed at.'],
                        'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the answer has to hold for, for example "13.4" or "14". Statements that do not hold there are left out. Defaults to the version of the installation this server was started in; where there is none, nothing is filtered and every statement carries the versions it holds for.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'default' => 6, 'description' => 'Maximum number of architecture hints.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_documentation_lookup',
                'description' => 'Search or read the official live TYPO3 documentation for a covered TYPO3 line. Search with several short English queries; every result carries a canonical URL. Pass one of those URLs back as page with the same targetVersion to receive that page as text, including headings and code examples. This reaches docs.typo3.org, unlike the bundled convention lookups.',
                'annotations' => [
                    'readOnlyHint' => true,
                    'destructiveHint' => false,
                    'idempotentHint' => true,
                    'openWorldHint' => true,
                ],
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'queries' => [
                            'type' => 'array',
                            'items' => ['type' => 'string', 'minLength' => 1],
                            'minItems' => 1,
                            'description' => 'Short search queries in English. Pass alternatives separately, for example ["page title event", "page title provider"].',
                        ],
                        'page' => [
                            'type' => 'string',
                            'minLength' => 1,
                            'description' => 'Canonical page URL returned by an earlier search. Pass it with the same targetVersion and without queries to read the page as text.',
                        ],
                        'targetVersion' => ['type' => 'string', 'minLength' => 1, 'description' => 'Covered TYPO3 version whose official manual must answer, for example "13.4" or "14". There is no fallback to another release.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'default' => 6],
                    ],
                    'required' => ['targetVersion'],
                    'oneOf' => [
                        ['required' => ['queries']],
                        ['required' => ['page']],
                    ],
                ],
            ],
            [
                'name' => 'typo3_component_lookup',
                'description' => 'Look up TYPO3 backend UI components by name or topic. Where the target is the active installation, its backend CSS, JavaScript, and installed styleguide templates supply the component contract; the curated catalog supplies the searchable names and fallback markup. Without usable installed sources, the bundled version-bound snapshot answers. Returns markup, classes, custom properties, and every source used.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Component name, class, or topic, for example badge, card, search box, or input-group. Omit to list the catalog.'],
                        'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the markup has to hold for, for example "13.4" or "14". Components not verified there are withheld. Defaults to the version of the installation this server was started in; where there is none, the whole catalog is returned and every entry carries the versions it was verified on.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_system_extension_lookup',
                'description' => 'Answer whether an extension is part of the TYPO3 core, and on which versions: the system extensions of every covered TYPO3 line, by extension key and Composer package name, each with what it is for and the range it is shipped on. Independent of any installation, which is the point — the question comes up for a package that is not installed, and "is this core" is otherwise answered from memory. A miss means the name is not a system extension on the covered versions, never that it does not exist.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'An extension key ("theme_camino"), a Composer package name ("typo3/cms-impexp"), or a word from what it does ("redirects"). Omit to list everything the core ships.'],
                        'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version to answer for, for example "13.4" or "14". Restricts the answer to what that line ships. Defaults to the version of the installation this server was started in; where there is none, every entry comes back with the range it is shipped on.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_reference_list',
                'description' => 'List the worked examples the TYPO3 core ships of its own conventions, and what each one is a reference for: the theme extension, the styleguide, the Extbase fixture extension, the content element rendering, the browser test suite, the static analysis setup. Read one of these before inventing a layout or a test harness — they are the version-correct, currently-passing form of what a convention describes, and every hint here is a summary of one. Paths are relative to a core checkout; where the answer names a Composer package, an installation that has it holds the same files below vendor/.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version to list for, for example "13.4" or "14". An example that branch does not have is left out rather than qualified. Defaults to the version of the installation this server was started in; where there is none, every entry comes back with the range it exists on.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_translation_domain_lookup',
                'description' => 'Compute the translation domain an XLF file resolves to, from its path. The domain is the canonical way to reference a label (backend.alt_doc:key) in TCA, LanguageService::sL() and f:translate, and it is registered nowhere — it follows from the path by the rules the core itself applies, which live in TranslationDomainMapper on one branch and TranslationDomainResolver on the next. Because it is computed, it also answers for a file outside the core and for one a patch is about to add. Where the installation being read is older than translation domains, it answers with the full LLL:EXT: reference instead: the domain form renders nothing there and fails at runtime rather than at build time.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'path' => ['type' => 'string', 'minLength' => 1, 'description' => 'The XLF file path, either as an EXT: reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") or relative to a core checkout ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").'],
                    ],
                    'required' => ['path'],
                ],
            ],
            [
                'name' => 'typo3_label_lookup',
                'description' => 'Search the labels registered in the TYPO3 installation you are working in. Reuse is local to the translation resource already used at the consuming code: pass resource whenever it is known, and do not reference a match from another module or package merely because its text is identical. Answered by the installation itself through its console, with the resource overrides it applies. Where the console cannot be reached — an installed TYPO3 whose database has no schema yet is the common case — the same packages\' XLF files are read instead, and answeredBy says which of the two answered.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'minLength' => 1, 'description' => 'Words from the label text or its trans-unit id, for example "save document" or "labels.title". Several words are matched independently, ignoring case and order: a label has to carry every one of them, in its text or in its id. When none carries all of them, the answer says how far each word reaches on its own.'],
                        'extension' => ['type' => 'string', 'description' => 'Restrict the search to the extension that owns the consuming code.'],
                        'resource' => ['type' => 'string', 'description' => 'Restrict the search to the exact XLF resource already used at the consuming code, for example "EXT:my_sitepackage/Resources/Private/Language/Backend/Import.xlf". A match from another resource is not a reuse candidate.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 200, 'default' => 25, 'description' => 'Maximum number of labels to return.'],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name' => 'typo3_fluid_namespace_list',
                'description' => 'List the Fluid ViewHelper namespaces that are globally available in the TYPO3 installation you are working in, so a template knows which prefixes it may use without declaring them. Every other namespace has to be declared per template with an xmlns attribute. Answered by the installation itself; where its console cannot be reached, by the Configuration/Fluid/Namespaces.php the installed packages declare, which answeredBy reports as "packages".',
                'inputSchema' => ['type' => 'object', 'properties' => new \stdClass()],
            ],
            [
                'name' => 'typo3_configuration_lookup',
                'description' => 'Read an effective TYPO3_CONF_VARS value from the installation you are working in — the value as it is at runtime after every extension has had its say, not the shipped default. Use it for configuration whose assembled shape matters, such as SYS/formEngine/formDataGroup, SYS/caching/cacheConfigurations, or SYS/fluid.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'path' => ['type' => 'string', 'minLength' => 1, 'description' => 'Slash-separated path into TYPO3_CONF_VARS, for example "SYS/fluid" or "SYS/formEngine/formDataGroup".'],
                    ],
                    'required' => ['path'],
                ],
            ],
            [
                'name' => 'typo3_backend_module_lookup',
                'description' => 'List the backend modules registered in the TYPO3 installation you are working in, with the extension that declares each one, its place in the module tree, its labels and its route. Answered by the installation, so a project extension\'s modules are in it.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Module identifier, label, route, or extension name to filter by. Omit to list every module.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_icon_lookup',
                'description' => 'Validate or find an icon identifier in the TYPO3 backend icon registry of the installation you are working in. The registry is assembled from the T3Icons set, the Configuration/Icons.php of every installed package, and the flag images, so a project extension\'s icons are in the answer. Identifiers spell shapes rather than intents, so concept words are mapped: "warning" finds actions-exclamation-triangle. Backend only: the identifiers are resolved by IconFactory and rendered by <core:icon>, and a frontend template can use neither.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Identifier, identifier fragment, or concept, for example "actions-open", "delete", or "warning". Omit to list the categories and concept words.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 40, 'description' => 'Maximum number of identifiers to return.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_changelog_lookup',
                'description' => 'Search the TYPO3 changelog of the installation you are working in: one entry per breaking change, deprecation, feature and important note, in the version it was released in. This is the first stop when building on a major you have not built on recently, not only a lookup after the fact — what separates a current answer from a two-major-old one is written down here and almost nowhere else. Answers "what did this version deprecate", "what changed about X", "which release introduced Y". Read from the core package on disk, so it covers exactly the versions that installation ships and grows with a Composer update. Every word of the query has to be carried by an entry; narrow further with type and version.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Words the entry has to carry, matched against its title. Omit to list a version or a type as a whole.'],
                        'type' => ['type' => 'string', 'enum' => ['breaking', 'deprecation', 'feature', 'important'], 'description' => 'Restrict to one kind of change. Breaking and deprecation are what affects existing code.'],
                        'version' => ['type' => 'string', 'description' => 'Restrict to a version, by prefix: "14" covers 14.0 through 14.3.x, "13.4" covers 13.4 and 13.4.x.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20, 'description' => 'Maximum number of entries.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_project_scope',
                'description' => 'Describe the project around the TYPO3 installation this server was started in: its TYPO3 and PHP constraints, the extensions that are its own rather than TYPO3\'s, the sites it configures with the site sets each depends on, and the commands it declares in composer.json and package.json. Read from files only — no console, no database — so it answers on a fresh clone. Call it before recommending a check: the commands listed here are the ones that exist in this repository.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
            ],
            [
                'name' => 'typo3_extension_scope',
                'description' => 'Describe what one installed extension registers: the tables its TCA defines and the ones it extends, the content elements it adds to tt_content and the Fluid template each renders through, its backend modules and routes, its icons, its site sets, the service tags it hangs into the container, its middlewares, its Fluid roots and namespaces, and the shape of its Classes/ directory — and what it ships beside all of that: its manual, its README, the test layers it has, and its XLF files with the source language each one declares. Those four are answered even when they are not there, because the absence of a manual or a translation is what a file listing cannot show. Read from that extension\'s own files — declaration files are parsed, never executed — so it answers on a fresh clone and for a third-party extension as well as for the project\'s own. typo3_project_scope names the extensions this can be called for.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'extension' => ['type' => 'string', 'minLength' => 1, 'description' => 'The extension key, as typo3_project_scope reports it, for example "my_sitepackage" or "news".'],
                    ],
                    'required' => ['extension'],
                ],
            ],
            [
                'name' => 'typo3_catalog_scope',
                'description' => 'Report whether component contracts come from the active installation or the bundled fallback, which TYPO3 core revision the fallback catalogs were taken from, what they cover, and how to re-check them. Call this to judge whether a lookup miss is authoritative: even with installed sources, component names remain a curated index rather than every backend class.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version to report the catalog\'s coverage for, for example "13.4" or "14". Defaults to the version of the installation this server was started in.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_commit_message_guide',
                'description' => 'Draft and check a TYPO3 commit message. Either assemble one from parts (changeType plus summary) or pass an existing message to check and correct it. The returned draft is ready to commit: the body is wrapped at 72 characters, with fenced code, indented blocks, list structure, and long URLs left intact. Defaults to the core contribution rules; pass workflow="project" in a project or extension repository of your own, where the subject and body conventions apply but the Forge issue, the Releases: trailer and the changelog do not.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'message' => ['type' => 'string', 'minLength' => 1, 'description' => 'A complete existing commit message to check, subject and trailers included. Unknown trailers such as Change-Id are kept, so an amended patch set stays valid.'],
                        'workflow' => ['type' => 'string', 'enum' => ['core', 'project'], 'default' => 'core', 'description' => 'Which rules to apply. "core": a patch against the TYPO3 core, with the Forge issue and the Releases: trailer required. "project": any other repository — the keyword, the 52/72 character limits and the wrapping are checked, no trailer is added or demanded, and [SECURITY] is allowed.'],
                        'changeType' => ['type' => 'string', 'enum' => ['BUGFIX', 'FEATURE', 'TASK', 'DOCS', 'SECURITY'], 'description' => 'TYPO3 commit message keyword. [SECURITY] is reserved for the TYPO3 Security Team and is only accepted with workflow="project".'],
                        'summary' => ['type' => 'string', 'minLength' => 1, 'description' => 'Summary text without the TYPO3 keyword prefix.'],
                        'issue' => ['type' => 'string', 'description' => 'Forge issue number, with or without leading #.'],
                        'relatedIssues' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'Optional related Forge issue numbers.'],
                        'releases' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Target releases, for example main or 13.4. Left out, the draft carries a RELEASE_TARGET placeholder and the checks ask for it — the branches a change is released on are not guessed.'],
                        'body' => ['type' => 'string', 'description' => 'Optional commit body. It is wrapped at 72 characters in the draft.'],
                        'isBreaking' => ['type' => 'boolean', 'default' => false, 'description' => 'Whether this is a breaking change requiring [!!!].'],
                        'isDeprecation' => ['type' => 'boolean', 'default' => false, 'description' => 'Whether this is a deprecation.'],
                    ],
                    'anyOf' => [
                        ['required' => ['message']],
                        ['required' => ['changeType', 'summary']],
                    ],
                ],
            ],
        ];

        // The core contribution surface is left out where it cannot be
        // followed — see Profile.
        $definitions = array_values(array_filter(
            $definitions,
            static fn(array $definition): bool => Profile::offers($definition['name']),
        ));

        // Only offered from a standalone checkout — see Feedback.
        if (Feedback::isAvailable()) {
            array_push($definitions, ...self::feedbackDefinitions());
        }

        return array_map(static function (array $definition): array {
            $definition['annotations'] ??= self::READ_ONLY_ANNOTATIONS;
            $definition['outputSchema'] = ToolSchemas::forTool($definition['name']);

            return $definition;
        }, $definitions);
    }

    /** @return array<int, array{name: string, description: string, inputSchema: array<string, mixed>, annotations?: array<string, bool>}> */
    private static function feedbackDefinitions(): array
    {
        return [
            [
                'name' => 'typo3_feedback_record',
                'description' => 'Leave a note about a gap, wrong answer, or missing capability of this knowledge server. The note is stored as markdown in this project so it can be implemented later. Use it whenever an answer was incomplete or a lookup found nothing that should have been there.',
                // The one tool that writes: a new note file per call, never
                // touching an existing one.
                'annotations' => [
                    'readOnlyHint' => false,
                    'destructiveHint' => false,
                    'idempotentHint' => false,
                    'openWorldHint' => false,
                ],
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'observation' => ['type' => 'string', 'minLength' => 1, 'description' => 'What was missing, wrong, or unhelpful. Be specific enough to act on later.'],
                        'category' => ['type' => 'string', 'enum' => Feedback::CATEGORIES, 'default' => 'idea', 'description' => 'missing-knowledge: the knowledge base lacks the answer. wrong-answer: the answer was incorrect. tool-gap: no tool covers the need. bug: the server misbehaved. idea: anything else.'],
                        'tool' => ['type' => ['string', 'array'], 'items' => ['type' => 'string'], 'description' => 'The tool the observation is about, for example typo3_component_lookup. Several tools may be named, as a list or separated by commas.'],
                        'query' => ['type' => 'string', 'description' => 'The query or arguments that produced the unsatisfying result.'],
                        'suggestion' => ['type' => 'string', 'description' => 'What the server should have answered or should be able to do.'],
                    ],
                    'required' => ['observation'],
                ],
            ],
            [
                'name' => 'typo3_feedback_list',
                'description' => 'List improvement notes recorded via typo3_feedback_record, newest first, so they can be worked off. Filter by status, by category, or by the tool a note is about. A note is closed by deleting its file, so status="closed" answers "what became of what I reported" from the commit that removed it.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => ['type' => 'string', 'enum' => ['open', 'closed', 'all'], 'default' => 'open', 'description' => 'open: the notes still in the backlog. closed: the ones already worked off, read from the commits that deleted them, each with the commit subject saying what came of it. all: both. A closed note carries no category and no tools, so those filters answer from the open half alone.'],
                        'category' => ['type' => 'string', 'enum' => Feedback::CATEGORIES, 'description' => 'Restrict the list to one category.'],
                        'tool' => ['type' => 'string', 'description' => 'Restrict the list to the notes about one tool, for example typo3_label_lookup. A note naming several tools is matched by each of them.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20, 'description' => 'Maximum number of notes to return.'],
                    ],
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $args */
    public static function call(string $name, array $args): ToolResult
    {
        return match ($name) {
            'typo3_server_scope' => self::serverScope(),
            'typo3_rule_lookup' => self::ruleLookup($args),
            'typo3_script_lookup' => self::scriptLookup($args),
            'typo3_task_guide' => self::taskGuide($args),
            'typo3_test_run_guide' => self::testRunGuide($args),
            'typo3_architecture_lookup' => self::architectureLookup($args),
            'typo3_documentation_lookup' => self::documentationLookup($args),
            'typo3_component_lookup' => self::componentLookup($args),
            'typo3_system_extension_lookup' => self::systemExtensionLookup($args),
            'typo3_reference_list' => self::referenceList($args),
            'typo3_translation_domain_lookup' => self::translationDomainLookup($args),
            'typo3_label_lookup' => self::labelLookup($args),
            'typo3_fluid_namespace_list' => self::fluidNamespaceList(),
            'typo3_configuration_lookup' => self::configurationLookup($args),
            'typo3_backend_module_lookup' => self::backendModuleLookup($args),
            'typo3_icon_lookup' => self::iconLookup($args),
            'typo3_changelog_lookup' => self::changelogLookup($args),
            'typo3_project_scope' => self::projectScope(),
            'typo3_extension_scope' => self::extensionScope($args),
            'typo3_catalog_scope' => self::catalogScope($args),
            'typo3_commit_message_guide' => self::commitMessageGuide($args),
            'typo3_feedback_record' => self::feedbackRecord($args),
            'typo3_feedback_list' => self::feedbackList($args),
            default => throw new \InvalidArgumentException(sprintf('Unknown tool: %s', $name)),
        };
    }

    /** @param array<string, mixed> $args */
    private static function documentationLookup(array $args): ToolResult
    {
        $queries = is_array($args['queries'] ?? null)
            ? array_values(array_filter($args['queries'], is_string(...)))
            : [];
        $page = is_string($args['page'] ?? null) ? trim($args['page']) : '';
        $statedVersion = is_string($args['targetVersion'] ?? null) ? trim($args['targetVersion']) : '';
        $major = Versions::major($statedVersion);
        $branch = $major === null ? null : Versions::branch($major);
        $limit = is_int($args['limit'] ?? null) ? max(1, min(10, $args['limit'])) : 6;

        if ($statedVersion === '' || ($queries === []) === ($page === '')) {
            throw new \InvalidArgumentException('Pass targetVersion and exactly one of queries or page');
        }

        if ($branch === null) {
            $answer = [
                'mode' => $page === '' ? 'search' : 'page',
                'status' => 'unavailable',
                'targetVersion' => $statedVersion,
                'source' => 'https://docs.typo3.org',
                'queries' => $queries,
                'results' => [],
                'unavailable' => [
                    'reason' => sprintf(
                        'TYPO3 %s is outside the covered versions: %s.',
                        $statedVersion,
                        implode(', ', array_map(static fn(array $version): string => $version['branch'], Versions::covered())),
                    ),
                ],
            ];
        } else {
            $documentation = new Documentation();
            $answer = $page === ''
                ? $documentation->lookup($queries, $branch, $limit)
                : $documentation->page($page, $branch);
        }

        $lines = [
            sprintf('Official TYPO3 documentation for %s.', $answer['targetVersion']),
            'Source: ' . $answer['source'],
        ];
        if ($answer['status'] === 'unavailable') {
            $lines[] = 'Could not answer: ' . $answer['unavailable']['reason'];
        } elseif ($answer['status'] === 'empty') {
            $lines[] = $answer['mode'] === 'page'
                ? 'The selected page answered without a readable main article.'
                : 'No matching section was found. The documentation service answered; narrow or rephrase the queries.';
        } elseif ($answer['mode'] === 'page') {
            $result = $answer['results'][0];
            $lines[] = '';
            $lines[] = '## ' . $result['title'];
            $lines[] = sprintf('%s · %s · %s', $result['document'], $result['documentVersion'], $result['url']);
            $lines[] = '';
            $lines[] = $result['content'];
        } else {
            foreach ($answer['results'] as $result) {
                $lines[] = '';
                $lines[] = '## ' . $result['title'];
                $lines[] = sprintf('%s · %s · %s', $result['document'], $result['documentVersion'], $result['url']);
                if ($result['excerpt'] !== '') {
                    $lines[] = $result['excerpt'];
                }
            }
        }

        return ToolResult::create(implode("\n", $lines), $answer);
    }

    private static function serverScope(): ToolResult
    {
        $scope = Scope::offered();

        $lines = [
            // Before the purpose rather than after it: the purpose describes
            // the whole server, and a client that reads what it holds first and
            // that half of it is missing second has been told and then
            // corrected.
            self::profileLine(),
            '',
            $scope['purpose'],
            '',
            'Covered, and how deeply. Each topic says what its answers are worth outside the core: '
            . 'core-only is the contribution process and the scripts that belong to that repository, '
            . 'transferable is a convention that holds wherever TYPO3 is written, and installation is '
            . 'answered by the installation this server was started in rather than from any snapshot.',
        ];
        foreach ($scope['covers'] as $entry) {
            $lines[] = '## ' . $entry['topic'];
            $lines[] = $entry['depth'];
            $lines[] = 'Tools: ' . implode(', ', $entry['tools']);
            $lines[] = 'Source: ' . $entry['source'] . ' (' . $entry['provenance'] . ')';
        }

        $lines[] = '';
        // Stated here as well as in the initialize instructions, because this
        // is the tool an agent calls when it does not know how to use the
        // server, and a client is free not to surface instructions at all.
        $lines[] = 'Query this server in English, whatever language you are speaking with the user. Its '
            . 'knowledge is written in English and its matching is lexical, so a query in another language '
            . 'reaches only the words the two happen to share and otherwise comes back empty.';

        $lines[] = '';
        $lines[] = 'Versions this knowledge is bound to:';
        foreach (Versions::covered() as $version) {
            $lines[] = '- TYPO3 v' . $version['major'] . ' (' . $version['branch'] . ', ' . $version['status'] . ')';
        }
        $lines[] = 'A statement that does not hold on all of them carries the range it holds on. Pass targetVersion '
            . 'to have the ones that do not apply left out; without it, the version of the installation being read '
            . 'decides, and where there is none nothing is filtered.';

        $lines[] = '';
        // What the list is worth read from the other side. A caller cannot tell
        // a boundary from a gap by the size of an answer, and the two ask for
        // opposite reactions: leave, or say what was missing.
        $lines[] = 'Deliberately not covered — and this list is the boundary: a subject that is not on it is in '
            . 'scope, so a thin answer to it is a gap in the knowledge base rather than a limit of it.'
            . (Feedback::isAvailable() ? ' Record one with typo3_feedback_record instead of going elsewhere.' : '');
        foreach ($scope['doesNotCover'] as $entry) {
            $lines[] = '## ' . $entry['topic'];
            $lines[] = $entry['why'];
            $lines[] = 'Instead: ' . $entry['instead'];
        }

        $lines[] = '';
        $lines[] = 'Which tool to call when:';
        foreach ($scope['routing'] as $entry) {
            $lines[] = '- ' . $entry['when'] . ' → ' . $entry['call'];
        }

        // Which installation is being read is the one thing a caller cannot
        // check for itself, and reading the wrong one would be worse than
        // reading none — so it is stated, with where the search started.
        $instance = Instance::describe();
        $lines[] = '';
        if ($instance === null) {
            $lines[] = 'No TYPO3 installation was found from the directory this server was started in, so every answer '
                . 'comes from the bundled knowledge base alone. Questions about what is registered in an '
                . 'installation — which icon identifiers exist, which labels — cannot be answered here.';
            if (Instance::searched() !== []) {
                // Where it looked is the difference between "this layout cannot
                // be read" and "the client started the server somewhere else",
                // and the caller can check neither without being told.
                $lines[] = 'Looked in: ' . implode(', ', Instance::searched())
                    . ' — none of them declares a TYPO3 core checkout or holds Composer metadata with TYPO3 packages in it.';
            }
            $lines[] = sprintf(
                'Naming it outright is the way out: set %s to the installation root, and %s to the command that '
                . 'reaches its console where that is not a path this server would find on its own.',
                Instance::ROOT_VARIABLE,
                Typo3Cli::CONSOLE_VARIABLE,
            );
        } else {
            $lines[] = sprintf(
                'Found the TYPO3 installation at %s (%s, %s, from %s), which holds %d packages. '
                . 'If that is not the installation you are working on, this server was started in the wrong '
                . 'directory — or set %s to the one you mean.',
                $instance['root'],
                $instance['kind'],
                $instance['via'] === Instance::VIA_ENVIRONMENT
                    ? 'named by ' . Instance::ROOT_VARIABLE
                    : 'found by walking up',
                $instance['startedFrom'],
                count(Instance::packages()),
                Instance::ROOT_VARIABLE,
            );
        }
        if (Instance::misconfiguration() !== '') {
            $lines[] = 'The configuration says otherwise and could not be followed: '
                . Instance::misconfiguration() . '.';
        }

        // What the installation can be asked is a different question from
        // whether one was found, and the answer is actionable often enough to
        // belong here rather than in a failing tool call.
        $console = Typo3Cli::resolve();
        if ($instance !== null && $console === null) {
            $lines[] = 'Its console cannot be run right now, so questions that only the installation can answer — which '
                . 'labels exist, which backend modules are registered — have no answer here: ' . Typo3Cli::reason() . '. '
                . 'Where the command that would work is known, ' . Typo3Cli::CONSOLE_VARIABLE
                . ' states it, for example "ddev exec .build/bin/typo3".';
        }
        if ($instance !== null && $console !== null && $console['via'] === Typo3Cli::VIA_OVERRIDE) {
            $lines[] = sprintf(
                'Its console is invoked as "%s", which %s states, so those answers come from the installation '
                . 'itself rather than from a bundled snapshot.',
                implode(' ', $console['command']),
                Typo3Cli::CONSOLE_VARIABLE,
            );
        }
        if ($instance !== null && $console !== null && $console['via'] !== Typo3Cli::VIA_OVERRIDE) {
            $lines[] = sprintf(
                'Its console is reachable via %s on PHP %s, so those answers come from the installation itself '
                . 'rather than from a bundled snapshot.',
                $console['via'],
                $console['php'] === '' ? 'an unreported version' : $console['php'],
            );
        }
        if ($instance !== null && Typo3Cli::caveat() !== '') {
            $lines[] = 'Reachable is not the same as ready here: ' . Typo3Cli::caveat() . '.';
        }

        $lines[] = '';
        $lines[] = 'Every lookup and guide is read-only. typo3_documentation_lookup reads the official, versioned '
            . 'manuals at docs.typo3.org; apart from that and the installation named above, nothing is fetched, '
            . 'executed, or looked up online.';
        if (Feedback::isAvailable()) {
            // Naming the one write next to the read-only claim, not after it:
            // a blanket "everything is read-only" followed by a tool that
            // creates a file contradicts both the annotations and the behaviour.
            $lines[] = 'The one exception is typo3_feedback_record, this server\'s only write: '
                . 'it creates a new markdown note under feedback/ and touches nothing else. '
                . 'Missing something that belongs here? Leave a note with it.';
        }

        return ToolResult::create(implode("\n", $lines), $scope + [
            'versions' => Versions::covered(),
            'profile' => [
                'active' => Profile::active(),
                'via' => Profile::via(),
                'omits' => Profile::omitted(),
                'variable' => Profile::VARIABLE,
                'misconfiguration' => Profile::misconfiguration() === '' ? null : Profile::misconfiguration(),
            ],
            'installation' => self::installationReport(),
        ]);
    }

    /**
     * Which half of the server this client is being offered, and why.
     *
     * A shorter tool list than the documentation describes is otherwise
     * indistinguishable from a broken server, and the caller has no way to
     * check: it sees the list it was given and nothing else.
     */
    private static function profileLine(): string
    {
        $line = sprintf('Profile "%s", %s. ', Profile::active(), Profile::via() === Profile::VIA_ENVIRONMENT
            ? 'named by ' . Profile::VARIABLE
            : 'following from the installation this server was started in');

        $line .= Profile::omitted() === []
            ? sprintf(
                'Every tool this server has is offered. In a project or extension repository, %s=%s offers the same '
                . 'server without the core contribution surface.',
                Profile::VARIABLE,
                Profile::PROJECT,
            )
            : sprintf(
                'The core contribution surface is not offered here — a project or extension repository has no '
                . 'Build/Scripts/, no Gerrit remote and no Forge issue — so %s are missing from the tool list, and so '
                . 'are the topics they answered. What transfers is still here. %s=%s offers them anyway.',
                implode(', ', Profile::omitted()),
                Profile::VARIABLE,
                Profile::ALL,
            );

        if (Profile::misconfiguration() !== '') {
            $line .= ' The configuration says otherwise and could not be followed: ' . Profile::misconfiguration() . '.';
        }

        return $line;
    }

    /**
     * The installation diagnostic as data.
     *
     * It used to be in the text alone, and a client that renders
     * structuredContent and drops the text block never saw it. What the caller
     * got instead was five tools answering {"matchCount": 0, "answeredBy":
     * "nothing"} — indistinguishable from a registry that really is empty, and
     * read as one: an extension with forty registered icons was reported as
     * registering none, twice.
     *
     * @return array<string, mixed>
     */
    private static function installationReport(): array
    {
        $instance = Instance::describe();
        $console = Typo3Cli::resolve();

        return [
            'found' => $instance !== null,
            'root' => $instance['root'] ?? null,
            'kind' => $instance['kind'] ?? null,
            'via' => $instance['via'] ?? null,
            'startedFrom' => $instance['startedFrom'] ?? null,
            'searched' => Instance::searched(),
            'packageCount' => count(Instance::packages()),
            'misconfiguration' => Instance::misconfiguration() === '' ? null : Instance::misconfiguration(),
            'console' => [
                'reachable' => $console !== null,
                'via' => $console['via'] ?? null,
                'php' => ($console['php'] ?? '') === '' ? null : $console['php'],
                'command' => $console === null ? null : implode(' ', $console['command']),
                'reason' => $console === null ? Typo3Cli::reason() : null,
                // Reachable and ready are two questions, and the second one has
                // its own answer: a console reached through an interpreter on
                // this machine while the project's containers are stopped runs,
                // and cannot boot TYPO3 against a database that is not there.
                'caveat' => Typo3Cli::caveat() === '' ? null : Typo3Cli::caveat(),
            ],
            'settings' => [
                'root' => Instance::ROOT_VARIABLE,
                'console' => Typo3Cli::CONSOLE_VARIABLE,
            ],
        ];
    }

    /** @param array<string, mixed> $args */
    private static function feedbackRecord(array $args): ToolResult
    {
        $file = Feedback::record($args);

        return ToolResult::create(
            sprintf(
                "Thanks — noted in %s.\n\nIt will be picked up when the knowledge base is next improved; "
                . 'nothing about the current answer changes.',
                $file,
            ),
            ['file' => $file],
        );
    }

    /** @param array<string, mixed> $args */
    private static function feedbackList(array $args): ToolResult
    {
        $status = is_string($args['status'] ?? null) ? $args['status'] : 'open';
        $category = is_string($args['category'] ?? null) ? $args['category'] : null;
        $limit = is_int($args['limit'] ?? null) ? $args['limit'] : 20;
        $tool = is_string($args['tool'] ?? null) && trim($args['tool']) !== '' ? trim($args['tool']) : null;

        $notes = Feedback::notes($status, $category, $limit, $tool);

        if ($notes === []) {
            return ToolResult::create(
                sprintf(
                    '%s%s',
                    match ($status) {
                        'open' => 'No open improvement notes',
                        'closed' => 'No improvement note has been worked off yet',
                        default => 'No improvement notes recorded yet',
                    },
                    $tool === null ? '.' : ' about ' . $tool . '.'
                ),
                ['count' => 0, 'notes' => []],
            );
        }

        $lines = array_map(static function (array $note): string {
            $date = substr($note['date'], 0, 10);
            $about = $note['tool'] === '' ? '' : ' — ' . $note['tool'];

            $entry = sprintf(
                "- %s%s%s\n  %s\n  %s",
                $note['category'] === '' ? '' : '[' . $note['category'] . '] ',
                $date,
                $about,
                $note['title'],
                $note['file'],
            );
            if ($note['closedBy'] !== null) {
                $entry .= sprintf(
                    "\n  closed %s in %s: %s",
                    $note['closedBy']['date'],
                    $note['closedBy']['commit'],
                    $note['closedBy']['subject'],
                );
            }

            return $entry;
        }, $notes);

        return ToolResult::create(
            sprintf("%d improvement note(s):\n\n%s", count($notes), implode("\n", $lines)),
            ['count' => count($notes), 'notes' => $notes],
        );
    }

    /** @param array<string, mixed> $args */
    private static function ruleLookup(array $args): ToolResult
    {
        $query = (string) ($args['query'] ?? '');
        $results = Knowledge::search($query);

        // The prose and the architecture hints are two corpora, and which one
        // holds a subject is this server's business, not the caller's: site
        // sets are a hint, the Gerrit workflow is prose, and the question is
        // phrased the same way either way.
        $hints = ArchitectureHints::find([], $query, 3)['matchedHints'];

        if ($results === [] && $hints === []) {
            return self::noKnowledgeMatch($query);
        }

        $text = $results === []
            ? sprintf('No section of the knowledge documents matched "%s".', $query)
            : self::renderSections($results);
        if ($hints !== []) {
            $text .= "\n\nThe architecture hints also cover this — call typo3_architecture_lookup with the id:\n"
                . implode("\n", array_map(
                    static fn(array $hint): string => '- ' . $hint['id'] . ' — ' . $hint['title'],
                    $hints,
                ));
        }

        return ToolResult::create($text, [
            'query' => $query,
            'matchCount' => count($results),
            'matches' => self::matchRecords($results),
            'alsoInHints' => array_map(
                static fn(array $hint): array => ['id' => $hint['id'], 'title' => $hint['title']],
                $hints,
            ),
        ]);
    }

    /** @param array<string, mixed> $args */
    private static function scriptLookup(array $args): ToolResult
    {
        $task = (string) ($args['task'] ?? '');

        // Every command in these notes runs in a core checkout. Handing them to
        // a repository that has none is the same mistake typo3_test_run_guide
        // used to make, and the same answer applies.
        if (Scope::isOutsideCore([], $task)) {
            return ToolResult::create(
                Scope::OUTSIDE_CORE_NOTICE . ' The scripts these notes describe are the core checkout\'s own, so '
                . 'none is returned. What to run here is declared in this repository: its composer.json scripts, '
                . 'its package.json, its CI configuration.',
                ['query' => $task, 'matchCount' => 0, 'matches' => [], 'outsideCore' => true],
            );
        }

        $results = Knowledge::search($task, ['typo3-core-scripts']);

        if ($results !== []) {
            $text = self::renderSections($results);
            // Where nothing said which repository this is, the commands are
            // offered under their condition rather than stated as the answer.
            if (!Scope::isCoreWork([], $task)) {
                $text .= "\n\nThese commands run in a TYPO3 core checkout. In any other repository, what to run is "
                    . 'declared in its own composer.json, package.json and CI configuration.';
            }

            return ToolResult::create($text, [
                'query' => $task,
                'matchCount' => count($results),
                'matches' => self::matchRecords($results),
                'outsideCore' => false,
            ]);
        }

        // Nothing about scripts matched. Say so, and route to the documents that
        // do cover the topic instead of answering with the nearest script prose.
        $message = sprintf(
            'No section of the TYPO3 core script notes matched "%s". They cover: %s.',
            $task,
            self::topicList('typo3-core-scripts')
        );

        $elsewhere = Knowledge::search($task);
        $titles = array_values(array_unique(array_map(
            static fn(array $result): string => $result['title'],
            $elsewhere
        )));
        if ($titles !== []) {
            $message .= sprintf(
                "\n\nOther knowledge documents do match this query — call typo3_rule_lookup for: %s.",
                implode(', ', $titles)
            );
        }

        return ToolResult::create($message, [
            'query' => $task,
            'matchCount' => 0,
            'matches' => [],
            'elsewhere' => $titles,
            'outsideCore' => false,
        ]);
    }

    /**
     * What the prose cannot say of itself.
     *
     * The architecture hints carry since/until per statement and are filtered
     * or labelled by version. The markdown documents are the long form of the
     * same subjects and carry nothing, so a section describing a shape that
     * arrived in v13 reads on v12 exactly as it reads on main. Rather than
     * building a second binding mechanism for prose, every prose answer says
     * which of the two the caller is holding.
     */
    private const PROSE_NOT_VERSION_BOUND = 'These sections are prose and are not filtered by version. '
        . 'Where a subsystem changed inside the covered range, the statement that changed carries the range in the '
        . 'architecture hints — call typo3_architecture_lookup with targetVersion for the form that holds on yours.';

    /**
     * Renders matched knowledge sections as coherent excerpts: the section
     * keeps its own heading and original formatting, so code blocks and nested
     * lists survive.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, score: int, coverage: float, truncated: bool}> $results
     */
    private static function renderSections(array $results): string
    {
        return self::PROSE_NOT_VERSION_BOUND . "\n\n" . implode("\n\n", array_map(static function (array $result): string {
            $heading = $result['heading'] === '' ? $result['title'] : $result['heading'];
            $source = sprintf(
                'Source: %s (typo3://core/%s) — matches %d%% of the query terms',
                $result['title'],
                $result['id'],
                (int) round($result['coverage'] * 100),
            );

            $body = $result['body'];
            if ($result['truncated']) {
                $body .= "\n\n(section truncated — read typo3://core/" . $result['id'] . ' for the rest)';
            }

            return '## ' . $heading . "\n" . $source . "\n\n" . $body;
        }, $results));
    }

    /**
     * The same matched sections as data: the document they come from, how much
     * of the query they cover, and the resource holding the full text.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, score: int, coverage: float, truncated: bool}> $results
     * @return array<int, array<string, mixed>>
     */
    private static function matchRecords(array $results): array
    {
        return array_map(static fn(array $result): array => [
            'documentId' => $result['id'],
            'title' => $result['title'],
            'uri' => 'typo3://core/' . $result['id'],
            'heading' => $result['heading'] === '' ? $result['title'] : $result['heading'],
            'body' => $result['body'],
            'coverage' => round($result['coverage'], 3),
            'score' => $result['score'],
            'truncated' => $result['truncated'],
        ], $results);
    }

    private static function noKnowledgeMatch(string $query): ToolResult
    {
        $documents = implode("\n", array_map(
            static fn(array $document): string => '- ' . $document['title'] . ': ' . implode(', ', $document['topics']),
            Knowledge::topics()
        ));

        $text = sprintf(
            "No knowledge section matched \"%s\".\n\nThis knowledge base covers:\n%s\n\n"
            . 'For backend UI components use typo3_component_lookup, and call typo3_server_scope for what '
            . 'this server covers at all. '
            . 'If the topic should be covered here, leave a note with typo3_feedback_record.',
            $query,
            $documents
        );

        return ToolResult::create($text, [
            'query' => $query,
            'matchCount' => 0,
            'matches' => [],
            'alsoInHints' => [],
            'documents' => Knowledge::topics(),
        ]);
    }

    private static function topicList(string $documentId): string
    {
        foreach (Knowledge::topics() as $document) {
            if ($document['id'] === $documentId) {
                return implode(', ', $document['topics']);
            }
        }

        return '';
    }

    /** @param array<string, mixed> $args */
    private static function taskGuide(array $args): ToolResult
    {
        $task = (string) ($args['task'] ?? '');
        $area = isset($args['area']) ? trim((string) $args['area']) : '';
        $changeType = (string) ($args['changeType'] ?? 'unknown');

        $subject = trim($task . ' ' . $area);
        $paths = $area === '' ? [] : [$area];
        $domains = Domains::detect($paths, $task . ' ' . (self::CHANGE_TYPE_TERMS[$changeType] ?? ''));

        // Several of the conventions below — the changelog, the Gerrit
        // workflow, the runTests.sh suites — do not exist outside the core, so
        // handing them over as a checklist for a project extension is worse
        // than saying the question is outside what this server knows.
        $outsideCore = Scope::isOutsideCore($paths, $subject, $area);

        $intents = TaskIntents::scoped(
            TaskIntents::detect($subject . ' ' . $changeType),
            $outsideCore,
            Scope::isCoreWork($paths, $subject)
        );
        $confirmed = TaskIntents::confirmed($intents);
        $conditional = array_values(array_filter(
            $intents,
            static fn(array $intent): bool => !in_array($intent, $confirmed, true)
        ));

        $stated = isset($args['targetVersion']) ? (string) $args['targetVersion'] : null;
        $target = Versions::target($stated);
        $targets = Versions::targets($stated);
        $architecture = ArchitectureHints::find($paths, $task, 4, null, $targets);
        $testHints = array_slice(TestSuiteHints::find($subject, $domains, $target), 0, 4);
        if ($outsideCore) {
            $architecture['matchedHints'] = ArchitectureHints::withoutChecks($architecture['matchedHints']);
        }

        $lines = [];
        if ($outsideCore) {
            $lines[] = Scope::OUTSIDE_CORE_NOTICE . ' Take what follows as conventions that may transfer, not as '
                . 'a checklist for this task. '
                . 'typo3_server_scope states the boundary.';
            $lines[] = '';
        }

        $lines = array_merge($lines, [
            'Task: ' . $task,
            'Area: ' . ($area === '' ? 'unknown' : $area),
            'Change type: ' . $changeType,
            'Domains: ' . implode(', ', $domains),
        ]);
        if (count($targets) > 1) {
            $lines[] = self::versionScopeLine($targets);
        }
        if ($confirmed !== []) {
            $lines[] = 'Recognized as: ' . implode(', ', array_map(
                static fn(array $intent): string => (string) $intent['title'],
                $confirmed
            ));
        }
        foreach ($conditional as $intent) {
            $lines[] = 'Possibly also: ' . $intent['title'] . ', ' . $intent['condition']
                . '. Its checklist items are marked as conditional below and its checks are listed separately.';
        }

        $lines[] = '';
        $lines[] = 'Architecture hints:';
        if ($architecture['matchedHints'] !== []) {
            $examples = self::examplesByHint($target);
            foreach (ArchitectureHints::groupByCategory($architecture['matchedHints']) as $section) {
                $lines[] = '### ' . $section['category'];
                foreach ($section['hints'] as $hint) {
                    $lines[] = '## ' . $hint['title'];
                    $notice = self::bindingNotice($hint, $outsideCore);
                    if ($notice !== null) {
                        $lines[] = $notice;
                    }
                    foreach ($hint['hints'] as $entry) {
                        $lines[] = '- ' . self::statementLine($entry, $outsideCore);
                    }
                    if (isset($examples[$hint['id']])) {
                        $lines[] = $examples[$hint['id']];
                    }
                    if ($hint['checks'] !== []) {
                        $lines[] = 'Checks:';
                        foreach ($hint['checks'] as $check) {
                            $lines[] = '- ' . $check;
                        }
                    }
                }
            }
        } else {
            $lines[] = '- No architecture hint matched this task text. That means no convention was recognized, '
                . 'not that none applies: call typo3_architecture_lookup again with the concrete file paths once they are known.';
        }

        // Only the confirmed intents may state a rule as applying: a
        // conditionally matched one would fill the whole section with rules for
        // work the task may not contain at all.
        $rules = TaskIntents::rules($confirmed);
        if ($rules !== []) {
            $lines[] = '';
            $lines[] = 'Rules that apply to this task:';
            $lines[] = '';
            $lines[] = self::renderSections($rules);
        }

        // The checks of a matched architecture hint belong in the list as much
        // as the ones an intent carries. Leaving them out dropped the functional
        // suite from a FormEngine brief while the FormEngine hint that names it
        // was right there in the same answer.
        $checks = self::mergedChecks($confirmed, $architecture['matchedHints'], $target);
        $conditionalChecks = self::conditionalChecks($conditional, $checks, $target);

        // Every check this server knows is a runTests.sh invocation against a
        // script in the core repository. Reporting outsideCore and then listing
        // four of them was the whole complaint: the flag said the answer knew,
        // and the payload said it had not acted on it.
        if ($outsideCore) {
            $checks = [];
            $conditionalChecks = [];
            $testHints = [];
        }

        $lines[] = '';
        if ($outsideCore) {
            $lines[] = 'Checks: none of the core\'s own apply here, so none is listed. Verify with what this '
                . 'repository provides — the scripts in its composer.json, its package.json, and its CI '
                . 'configuration are where its own suites are declared.';
        } else {
            $lines[] = 'Relevant TYPO3 core checks:';
            foreach ($checks as $check) {
                $lines[] = '- `' . $check . '`';
            }
            if ($testHints !== []) {
                foreach ($testHints as $hint) {
                    $lines[] = '## ' . $hint['suite'];
                    $lines[] = '`' . $hint['command'] . '`';
                    if ($hint['targeted'] !== null) {
                        $lines[] = 'Targeted: `' . $hint['targeted'] . '`';
                    }
                    $lines[] = $hint['whenToUse'];
                }
            } elseif ($checks === []) {
                $lines[] = '- No topic-specific check matched. Run the narrowest relevant suite, then broaden before review.';
            }

            foreach ($conditionalChecks as $entry) {
                $lines[] = '';
                $lines[] = 'Checks for ' . $entry['title'] . ', ' . $entry['condition'] . ':';
                foreach ($entry['checks'] as $check) {
                    $lines[] = '- `' . $check . '`';
                }
            }
        }

        $checklist = [
            $outsideCore
                ? 'Confirm the target branch and the issue context of this repository.'
                : 'Confirm the target TYPO3 core branch and issue context.',
            'Inspect nearby code, tests, and established subsystem conventions.',
            'Keep the patch focused on the stated task.',
            'Add or update the narrowest useful test coverage.',
            'Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.',
        ];
        foreach (self::CHANGE_TYPE_CHECKLIST[$changeType] ?? [] as $entry) {
            $checklist[] = $entry;
        }
        foreach ($confirmed as $intent) {
            foreach ($intent['checklist'] as $entry) {
                $checklist[] = (string) $entry;
            }
        }
        foreach ($conditional as $intent) {
            foreach ($intent['checklist'] as $entry) {
                $checklist[] = ucfirst((string) $intent['condition']) . ': ' . lcfirst((string) $entry);
            }
        }
        $checklist[] = $outsideCore
            ? 'Write the commit message with typo3_commit_message_guide and workflow="project": '
                . 'summarize the changed behavior, the affected area and the commands you ran, and it '
                . 'hands back a draft that is wrapped and checked.'
            : 'Write the commit message with typo3_commit_message_guide: summarize the changed behavior, '
                . 'the affected area and the commands you ran, and it hands back a draft that carries '
                . 'the keyword, the trailers and the wrapping.';

        // Per line, not per section: a checklist mixes "reproduce the bug with
        // a failing test" — true anywhere — with a changelog file below
        // typo3/sysext/, which is a path the caller's repository does not have.
        if ($outsideCore) {
            $checklist = array_values(array_filter(
                $checklist,
                static fn(string $entry): bool => !Scope::isCoreOnly($entry)
            ));
        }

        $lines[] = '';
        $lines[] = 'Suggested checklist:';
        foreach ($checklist as $entry) {
            $lines[] = '- ' . $entry;
        }

        // The brief is assembled from bundled knowledge alone, so everything
        // that depends on the working tree is the agent's job. Saying which
        // parts those are — and how to get them — is more useful than letting
        // the checklist read as if the brief had already looked.
        $checkoutDiscovery = Scope::read()['checkoutDiscovery'];
        if ($outsideCore) {
            $checkoutDiscovery = array_values(array_filter(
                $checkoutDiscovery,
                static fn(array $entry): bool => !Scope::isCoreOnly($entry['establish'] . ' ' . $entry['how'])
            ));
        }
        $lines[] = '';
        $lines[] = 'Establish in your checkout — this server cannot see it:';
        foreach ($checkoutDiscovery as $entry) {
            $lines[] = '- ' . $entry['establish'] . "\n  " . $entry['how'];
        }

        $nextTools = self::nextTools(
            $intents,
            $domains,
            array_column($architecture['matchedHints'], 'id'),
            $target
        );
        if ($outsideCore) {
            $nextTools = array_values(array_filter(
                $nextTools,
                static fn(array $suggestion): bool => !Scope::isCoreOnly($suggestion['tool'] . ' ' . $suggestion['when'])
            ));
        }
        $lines[] = '';
        $lines[] = 'Next lookups for this task:';
        foreach ($nextTools as $suggestion) {
            $lines[] = '- ' . $suggestion['tool']
                . ($suggestion['when'] === '' ? '' : ' — ' . $suggestion['when']);
        }

        return ToolResult::create(implode("\n", $lines), [
            'task' => $task,
            'area' => $area === '' ? null : $area,
            'changeType' => $changeType,
            'targetVersion' => $target,
            'targetVersions' => $targets,
            'domains' => $domains,
            'outsideCore' => $outsideCore,
            'intents' => array_map(static fn(array $intent): array => [
                'id' => (string) $intent['id'],
                'title' => (string) $intent['title'],
                'confidence' => (string) $intent['confidence'],
                'condition' => (string) $intent['condition'],
            ], $intents),
            'architectureHints' => self::hintRecords($architecture['matchedHints']),
            'rules' => self::matchRecords($rules),
            'checks' => $checks,
            'conditionalChecks' => $conditionalChecks,
            'testSuites' => self::suiteRecords($testHints),
            'checklist' => $checklist,
            'checkoutDiscovery' => $checkoutDiscovery,
            'nextTools' => $nextTools,
        ]);
    }

    /**
     * The checks a brief states as applying: those of the confirmed intents and
     * those of every matched architecture hint, in that order and deduplicated.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, string>
     */
    private static function mergedChecks(array $intents, array $hints, ?int $target): array
    {
        $checks = [];
        foreach ($intents as $intent) {
            foreach ($intent['checks'] as $check) {
                $checks[(string) $check] = true;
            }
        }
        foreach ($hints as $hint) {
            foreach ($hint['checks'] as $check) {
                $checks[(string) $check] = true;
            }
        }

        // The hints arrive already filtered; the intents do not, and both name
        // suites that a given branch's runTests.sh may not have.
        return TestSuiteHints::checksFor(array_keys($checks), $target);
    }

    /**
     * The checks of the conditionally matched intents, minus the ones already
     * stated as applying.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, string> $stated
     * @return array<int, array{title: string, condition: string, checks: array<int, string>}>
     */
    private static function conditionalChecks(array $intents, array $stated, ?int $target): array
    {
        $entries = [];
        foreach ($intents as $intent) {
            $checks = array_values(array_diff(
                TestSuiteHints::checksFor(array_map('strval', $intent['checks']), $target),
                $stated,
            ));
            if ($checks === []) {
                continue;
            }
            $entries[] = [
                'title' => (string) $intent['title'],
                'condition' => (string) $intent['condition'],
                'checks' => $checks,
            ];
        }

        return $entries;
    }

    /**
     * Routes to the specialised tools, so an agent that starts here learns that
     * they exist instead of writing markup or label keys from memory.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, string> $domains
     * @param array<int, string> $hintIds ids of the architecture hints this brief matched
     * @return array<int, array{tool: string, when: string}>
     */
    private static function nextTools(array $intents, array $domains, array $hintIds, ?int $target): array
    {
        $candidates = [];
        foreach ($intents as $intent) {
            foreach ($intent['tools'] as $tool) {
                $candidates[] = (string) $tool;
            }
        }

        if (array_intersect([Domains::CSS, Domains::FLUID], $domains) !== []) {
            $candidates[] = 'typo3_component_lookup, before writing backend markup or CSS classes';
        }
        // A subject whose hint matched is a subject the caller is about to write
        // in, and both of these answer from the installation rather than from
        // memory. The pointer is in the hint text as well, which is exactly the
        // place nobody rereads while writing the fortieth label key.
        foreach (self::HINT_TOOLS as $hintId => $suggestion) {
            if (in_array($hintId, $hintIds, true)) {
                $candidates[] = $suggestion;
            }
        }
        $candidates[] = $target === null
            ? 'typo3_changelog_lookup, for what the version you are building on changed about this area'
            : sprintf(
                'typo3_changelog_lookup, for what %d changed about this area — the first stop when you have not '
                    . 'built on it recently, not only a lookup after the fact',
                $target
            );
        $candidates[] = 'typo3_architecture_lookup with the concrete file paths, once they are known';
        $candidates[] = 'typo3_test_run_guide, for the targeted runTests.sh invocation';
        // The one step this brief describes and never pointed at. A caller who
        // read the routing table at the start of a session is committing hours
        // later, from this list.
        $candidates[] = 'typo3_commit_message_guide, before committing';
        if (Feedback::isAvailable()) {
            $candidates[] = 'typo3_feedback_record, when one of these answers was wrong or incomplete';
        }

        // One entry per tool: an intent that already suggested a tool keeps its
        // own wording, the generic fallback for that tool is dropped.
        $suggestions = [];
        foreach ($candidates as $candidate) {
            $tool = strtok($candidate, ' ,');
            if ($tool === false || isset($suggestions[$tool])) {
                continue;
            }
            $suggestions[$tool] = [
                'tool' => $tool,
                // The candidates are written as one sentence, "tool, when", so
                // the separator has to come off with the tool name — otherwise
                // both halves carry it and the answer reads "tool , when".
                'when' => ltrim(substr($candidate, strlen($tool)), ' ,'),
            ];
        }

        return array_values($suggestions);
    }

    /**
     * Matched architecture hints as data, without the internal match patterns.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array<string, mixed>>
     */
    private static function hintRecords(array $hints): array
    {
        return array_map(static fn(array $hint): array => [
            'id' => (string) $hint['id'],
            'title' => (string) $hint['title'],
            'category' => (string) $hint['category'],
            'binding' => $hint['binding'] ?? null,
            'hints' => array_map(static fn(array $statement): array => [
                'text' => $statement['text'],
                'since' => $statement['since'],
                'until' => $statement['until'],
                'versions' => Versions::label($statement['since'], $statement['until']),
                'binding' => $statement['binding'] ?? null,
            ], $hint['hints']),
            'checks' => array_map('strval', $hint['checks']),
        ], array_values($hints));
    }

    /**
     * One statement as a line, with the versions it holds for where that is not
     * all of them.
     *
     * The range is rendered beside the sentence rather than inside it: the
     * sentence is the same sentence on every version it holds for, and a reader
     * filtering by version must not have to parse prose to do it. What it is
     * binding for is rendered the same way, and only where it is not this
     * caller's obligation — inside the core everything listed applies, so the
     * marker would be on every line and say nothing.
     *
     * @param array{text: string, since: ?int, until: ?int, binding: ?string} $statement
     */
    private static function statementLine(array $statement, bool $outsideCore = false): string
    {
        $labels = array_filter([
            Versions::label($statement['since'], $statement['until']),
            $outsideCore && ($statement['binding'] ?? null) === ArchitectureHints::BINDING_CORE
                ? 'binding for a core patch, a convention here'
                : '',
        ]);

        return $labels === [] ? $statement['text'] : $statement['text'] . ' [' . implode('; ', $labels) . ']';
    }

    /**
     * @param array<int, array{suite: string, command: string, description: string, whenToUse: string, domains: array<int, string>, targeted: ?string, since: ?int, until: ?int}> $hints
     * @return array<int, array<string, mixed>>
     */
    private static function suiteRecords(array $hints): array
    {
        return array_map(static fn(array $hint): array => [
            'suite' => $hint['suite'],
            'command' => $hint['command'],
            'targeted' => $hint['targeted'],
            'description' => $hint['description'],
            'whenToUse' => $hint['whenToUse'],
            'domains' => $hint['domains'],
            // Rendered the same way a statement's range is: beside the entry
            // rather than inside it, so an unfiltered listing still says which
            // branches actually have the suite.
            'versions' => Versions::label($hint['since'], $hint['until']),
        ], array_values($hints));
    }

    /** @param array<string, mixed> $args */
    private static function testRunGuide(array $args): ToolResult
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;

        /** @var array<int, string> $paths */
        $paths = array_map('strval', $args['paths'] ?? []);
        $paths = array_values(array_unique(array_merge($paths, Domains::pathsIn((string) $query))));
        $domains = Domains::fromPaths($paths);
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);

        // Every suite this guide knows is a Build/Scripts/runTests.sh
        // invocation, and that script is part of the core repository. Handing
        // it to a site package is worse than declining: the commands look
        // copy-pasteable and none of them exists there.
        if (Scope::isOutsideCore($paths, (string) $query)) {
            return ToolResult::create(
                Scope::OUTSIDE_CORE_NOTICE . ' Build/Scripts/runTests.sh is part of the core repository, so the '
                . 'suites this guide knows cannot be run from here and are left out rather than handed over. '
                . 'What such a repository needs instead — assembling a phpunit suite of its own, and the browser '
                . 'tests that go with it — is typo3_architecture_lookup with id=project-extension-tests and '
                . 'id=browser-tests. typo3_server_scope states the boundary.',
                [
                    'query' => $query,
                    'paths' => $paths,
                    'domains' => $domains,
                    'outsideCore' => true,
                    'suites' => [],
                    'invocation' => ['notes' => [], 'options' => [], 'examples' => []],
                ],
            );
        }

        $hints = TestSuiteHints::find($query, $domains, $target);

        $blocks = [];
        if ($domains !== []) {
            $blocks[] = sprintf(
                'Narrowed to the %s domain(s) the given paths touch. Suites outside them cannot fail on this change; '
                . 'call again without paths to see all of them.',
                implode(' and ', $domains)
            );
        }
        if ($hints === []) {
            $blocks[] = sprintf(
                'No runTests.sh suite matched "%s". Try "unit", "functional", "phpstan", "checkRst", "build", "composer", or "npm".',
                (string) $query
            );
        } else {
            foreach ($hints as $hint) {
                $block = ['## ' . $hint['suite'], 'Command from the TYPO3 core root:', '`' . $hint['command'] . '`'];
                if ($hint['targeted'] !== null) {
                    $block[] = 'Targeted run while iterating:';
                    $block[] = '`' . $hint['targeted'] . '`';
                }
                $block[] = '';
                $block[] = $hint['description'];
                $block[] = $hint['whenToUse'];
                $blocks[] = implode("\n", $block);
            }
        }

        $blocks[] = self::invocationBlock();

        return ToolResult::create(implode("\n\n", $blocks), [
            'query' => $query,
            'paths' => $paths,
            'domains' => $domains,
            'outsideCore' => false,
            'suites' => self::suiteRecords($hints),
            'invocation' => TestSuiteHints::invocation(),
        ]);
    }

    /**
     * The invocation rules that apply to every suite. Emitted with every answer:
     * without CI=true and the passthrough form, a suite command alone is rarely
     * what a patch actually needs.
     */
    private static function invocationBlock(): string
    {
        $invocation = TestSuiteHints::invocation();

        $lines = ['## Invoking runTests.sh'];
        foreach ($invocation['notes'] as $note) {
            $lines[] = '- ' . $note;
        }

        $lines[] = '';
        $lines[] = 'Options:';
        foreach ($invocation['options'] as $option) {
            $lines[] = '- `' . $option['option'] . '` — ' . $option['description'];
        }

        $lines[] = '';
        $lines[] = 'Examples:';
        foreach ($invocation['examples'] as $example) {
            $lines[] = '- ' . $example['purpose'] . ':';
            $lines[] = '  `' . $example['command'] . '`';
        }

        return implode("\n", $lines);
    }

    /**
     * Which TYPO3 versions the statements below were selected for, and why.
     *
     * The interesting case is the one this said nothing about for a long time:
     * a repository declaring `^13.4 || ^14.3` gets both majors, and a caller
     * that does not know this reads a statement labelled for one of them as the
     * current shape and the other as drift. It is the difference between the
     * two that the code is built around — the file kept for the older major,
     * the interface not replaced yet — so the sentence names it as a constraint
     * rather than leaving it to be discovered.
     *
     * @param array<int, int> $targets
     */
    private static function versionScopeLine(array $targets): string
    {
        if ($targets === []) {
            return 'No target TYPO3 version was stated and none was found to read, so every statement comes back '
                . 'with the versions it holds for. Pass targetVersion to have the ones that do not apply left out.';
        }
        if (count($targets) === 1) {
            return sprintf('Answered for TYPO3 v%d: statements that do not hold there are left out.', $targets[0]);
        }

        $constraint = Project::coreConstraint();

        return sprintf(
            'Answered for TYPO3 %s at once, because this repository declares typo3/cms-core as %s and one codebase '
            . 'serves all of them. A statement is kept when it holds on any of them, and the range beside it says '
            . 'which — where two statements about the same subject differ, that difference is the constraint this '
            . 'code lives under rather than something to clean up. Pass targetVersion to answer for one of them.',
            self::majorList($targets),
            $constraint === null ? 'a range' : '"' . $constraint . '"',
        );
    }

    /** @param array<int, int> $majors */
    private static function majorList(array $majors): string
    {
        $labels = array_map(static fn(int $major): string => 'v' . $major, $majors);
        $last = array_pop($labels);

        return $labels === [] ? $last : implode(', ', $labels) . ' and ' . $last;
    }

    /** @param array<string, mixed> $args */
    private static function architectureLookup(array $args): ToolResult
    {
        $paths = array_map('strval', $args['paths'] ?? []);
        $task = isset($args['task']) ? (string) $args['task'] : null;
        $limit = (int) ($args['limit'] ?? 6);
        $id = isset($args['id']) ? trim((string) $args['id']) : '';
        $stated = isset($args['targetVersion']) ? (string) $args['targetVersion'] : null;
        $target = Versions::target($stated);
        $targets = Versions::targets($stated);

        $result = ArchitectureHints::find($paths, $task ?? '', $limit, $id, $targets);

        // The hints transfer — a DataHandler or Fluid convention is the same
        // one outside the core — but the checks attached to them are all
        // runTests.sh invocations, and that script lives in the core
        // repository. So the hints stay and the commands go.
        $outsideCore = Scope::isOutsideCore($paths, $task ?? '');
        if ($outsideCore) {
            $result['matchedHints'] = ArchitectureHints::withoutChecks($result['matchedHints']);
        }

        $lines = [];
        if ($outsideCore) {
            $lines[] = Scope::OUTSIDE_CORE_NOTICE . ' The hints below are conventions that may transfer; the '
                . 'checks that normally come with them are left out, because Build/Scripts/runTests.sh is part '
                . 'of the core repository and does not exist here. typo3_server_scope states the boundary.';
            $lines[] = '';
        }
        if ($result['withheldCategories'] !== []) {
            $lines[] = sprintf(
                'This task names the frontend, so %s is withheld: it describes the TYPO3 backend interface — its '
                . 'Sass sources, its --typo3-* properties, its color schemes — and would be inverted advice for '
                . 'what a website renders. Frontend theming: https://docs.typo3.org. Name the backend in the task '
                . 'if you are styling a backend module.',
                implode(' and ', $result['withheldCategories']),
            );
            $lines[] = '';
        }
        if ($id !== '') {
            $lines[] = 'Hint requested by id: ' . $id;
        }
        if ($task !== null && $task !== '') {
            $lines[] = 'Task: ' . $task;
        }
        if ($paths !== []) {
            $lines[] = "Paths:\n" . implode("\n", array_map(static fn(string $p): string => '- ' . $p, $paths));
        }
        $lines[] = self::versionScopeLine($targets);
        if ($result['domains'] !== []) {
            $lines[] = 'Domains: ' . implode(', ', $result['domains'])
                . ' (hints outside these domains are not shown'
                . ($result['withheldCategories'] === []
                    ? ')'
                    : ', and ' . implode(' and ', $result['withheldCategories']) . ' was withheld inside them)');
        }
        $lines[] = '';
        $lines[] = 'Architecture hints:';

        if ($result['matchedHints'] !== []) {
            $sectionTexts = [];
            $examples = self::examplesByHint($target);
            foreach (ArchitectureHints::groupByCategory($result['matchedHints']) as $section) {
                $hintTexts = [];
                foreach ($section['hints'] as $hint) {
                    $block = ['## ' . $hint['title']];
                    $notice = self::bindingNotice($hint, $outsideCore);
                    if ($notice !== null) {
                        $block[] = $notice;
                    }
                    $block[] = 'Hints:';
                    foreach ($hint['hints'] as $entry) {
                        $block[] = '- ' . self::statementLine($entry, $outsideCore);
                    }
                    if (isset($examples[$hint['id']])) {
                        $block[] = $examples[$hint['id']];
                    }
                    if ($hint['checks'] !== []) {
                        $block[] = 'Relevant checks:';
                        foreach ($hint['checks'] as $check) {
                            $block[] = '- ' . $check;
                        }
                    }
                    $hintTexts[] = implode("\n", $block);
                }
                $sectionTexts[] = '### ' . $section['category'] . "\n\n" . implode("\n\n", $hintTexts);
            }
            $lines[] = implode("\n\n", $sectionTexts);
        } elseif ($result['withheldCategories'] !== []) {
            $lines[] = 'Nothing is left to show: the only domain this task touched is one this server answers for '
                . 'the backend alone.';
        } elseif ($id !== '') {
            $lines[] = sprintf('There is no hint with the id "%s".', $id);
        } else {
            $lines[] = 'No architecture hint matched. Name a path or a more specific topic, or ask for one of the ids below.';
        }

        // The index is the difference between "nothing matched your words" and
        // "nobody wrote this down". Without it both answers read the same, and
        // the caller tries another phrasing for a subject that does not exist —
        // or gives up on one that does.
        if ($result['availableHints'] !== []) {
            $lines[] = '';
            $lines[] = $id !== ''
                ? 'The ids there are:'
                : 'Hints that exist in these domains, requestable by id:';
            foreach ($result['availableHints'] as $entry) {
                $lines[] = '- ' . $entry['id'] . ' — ' . $entry['title'] . ' (' . $entry['category'] . ')';
            }
        }

        return ToolResult::create(implode("\n", $lines), [
            'task' => $task === '' ? null : $task,
            'paths' => array_values($paths),
            'targetVersion' => $target,
            'targetVersions' => $targets,
            'domains' => $result['domains'],
            'withheldCategories' => $result['withheldCategories'],
            'outsideCore' => $outsideCore,
            'hints' => self::hintRecords($result['matchedHints']),
            'availableHints' => $result['availableHints'],
        ]);
    }

    /** @param array<string, mixed> $args */
    private static function componentLookup(array $args): ToolResult
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);
        $installed = InstalledComponents::isAvailable($target);
        $split = Components::splitByTarget(Components::find($query, $target), $target);
        $components = $split['holds'];
        $withheld = $split['withheld'];
        $withheldNote = self::withheldComponents($withheld, $target);
        $sourceNote = self::componentSourceNote($installed);

        if ($components === []) {
            return ToolResult::create(
                sprintf(
                    "No TYPO3 component%s matched \"%s\". Try a component name (badge, card), a class (input-group), or a topic (search box). %s\n%s%s",
                    $withheld === [] ? '' : sprintf(' verified on TYPO3 v%d', (int) $target),
                    (string) $query,
                    $installed
                        ? 'The installed packages were checked, but the searchable component index remains curated; inspect the installed backend CSS for an uncatalogued class.'
                        : self::CATALOG_MISS_NOTE,
                    $withheldNote === '' ? '' : $withheldNote . "\n",
                    $sourceNote,
                ),
                [
                    'query' => $query,
                    'targetVersion' => $target,
                    'matchCount' => 0,
                    'components' => [],
                    'withheld' => self::withheldRecords($withheld),
                    'componentSource' => $installed ? 'installation' : 'catalog',
                    'catalog' => self::catalogRecord($installed),
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
                    ] + self::componentSourceRecord($c) + self::verifiedRecord($c), $components),
                    'withheld' => self::withheldRecords($withheld),
                    'componentSource' => $installed ? 'installation' : 'catalog',
                    'catalog' => self::catalogRecord($installed),
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
            ] + self::componentSourceRecord($c) + self::verifiedRecord($c), $described),
            'withheld' => self::withheldRecords($withheld),
            'checklist' => $checklist,
            'componentSource' => $installed ? 'installation' : 'catalog',
            'catalog' => self::catalogRecord($installed),
        ]);
    }

    private static function componentSourceNote(bool $installed): string
    {
        if (!$installed) {
            return self::catalogProvenance();
        }

        return sprintf(
            'Component contract: installed TYPO3 %s packages. Names, summaries, keywords, and fallback markup '
                . 'come from the curated catalog; classes and custom properties come from '
                . 'EXT:backend/Resources/Public/Css/backend.css, and an installed styleguide example replaces '
                . 'the fallback markup where available.',
            Instance::typo3Version(),
        );
    }

    /**
     * The evidence version travels inside the component, not only in the block
     * after it: clients often render one record without its surrounding answer.
     *
     * @param array<string, mixed> $component
     * @return array{
     *     classes: array<int, string>, sourceFiles: array<int, string>,
     *     markupSource: string, contractVersion: string, describesVersion: string
     * }
     */
    private static function componentSourceRecord(array $component): array
    {
        $snapshot = CatalogMeta::read()['source']['version'];
        $markupSource = $component['markupSource'] ?? 'catalog';
        $contractVersion = $component['contractVersion'] ?? $snapshot;

        return [
            'classes' => $component['classes'] ?? [],
            'sourceFiles' => $component['sourceFiles'] ?? [],
            'markupSource' => $markupSource,
            'contractVersion' => $contractVersion,
            'describesVersion' => $markupSource === 'installation' ? $contractVersion : $snapshot,
        ];
    }

    /**
     * The majors a catalog entry was verified on, as data beside the label.
     *
     * @param array<string, mixed> $component
     * @return array{since: ?int, until: ?int, verifiedOn: string}
     */
    private static function verifiedRecord(array $component): array
    {
        return [
            'since' => $component['since'],
            'until' => $component['until'],
            'verifiedOn' => Versions::label($component['since'], $component['until']),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $withheld
     * @return array<int, array<string, mixed>>
     */
    private static function withheldRecords(array $withheld): array
    {
        return array_map(static fn(array $c): array => [
            'name' => $c['name'],
            'title' => $c['title'],
            'sassPaths' => $c['sassPaths'],
            'demoPath' => $c['demoPath'],
        ] + self::verifiedRecord($c), $withheld);
    }

    /**
     * What the stated version cost the answer, and what to check instead.
     *
     * Dropping the entry silently would be the one thing worse than handing it
     * over: the caller then reads "this component does not exist" into an
     * answer that means "the catalog has it and was never verified where you
     * are". So it is named, with the branch and the sources to verify against.
     *
     * @param array<int, array<string, mixed>> $withheld
     */
    private static function withheldComponents(array $withheld, ?int $target): string
    {
        if ($withheld === [] || $target === null) {
            return '';
        }

        if (($withheld[0]['_installed'] ?? false) === true) {
            $lines = [sprintf(
                'Not found in the installed TYPO3 v%d backend component contract:',
                $target,
            )];
            foreach ($withheld as $component) {
                $lines[] = sprintf('- %s (%s)', $component['name'], $component['title']);
            }
            $lines[] = 'Their root class or custom element was absent from the installed backend CSS and JavaScript.';

            return implode("\n", $lines);
        }

        $branch = Versions::branch($target);
        $lines = [sprintf(
            'Withheld for TYPO3 v%d — in this catalog, and never verified there, so the classes and custom '
            . 'properties they describe may not exist on that version:',
            $target,
        )];
        foreach ($withheld as $component) {
            $lines[] = sprintf(
                '- %s (%s) — verified on %s; verify against %s',
                $component['name'],
                $component['title'],
                Versions::label($component['since'], $component['until']),
                $component['sassPaths'] === []
                    ? ($component['demoPath'] ?? 'the core checkout')
                    : implode(', ', $component['sassPaths']),
            );
        }
        $lines[] = sprintf(
            'Check those paths against %s before using any of them — a path that is not there is the answer too.',
            $branch === null ? 'a core checkout of that version' : 'the core repository\'s ' . $branch . ' branch',
        );

        return implode("\n", $lines);
    }

    /**
     * What a TYPO3 version changed, from the changelog that installation ships.
     *
     * The one question the knowledge base cannot answer from conventions: what
     * a given release broke, deprecated or added is a list, and the list is on
     * disk in every installation.
     *
     * @param array<string, mixed> $args
     */
    private static function changelogLookup(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $type = trim((string) ($args['type'] ?? ''));
        $version = trim((string) ($args['version'] ?? ''));
        $limit = (int) ($args['limit'] ?? 20);

        if (Changelog::directory() === null) {
            return self::consoleUnavailable(
                'no TYPO3 installation was found whose core package ships the changelog',
                ['query' => $query, 'matchCount' => 0, 'entries' => [], 'versions' => []],
            );
        }

        $terms = LabelSearch::terms($query);
        $matching = LabelSearch::carryingEvery(Changelog::entries($type, $version), $terms);
        usort($matching, static fn(array $a, array $b): int => version_compare($b['version'], $a['version'])
            ?: strcmp($a['key'], $b['key']));

        $shown = array_slice($matching, 0, $limit);
        $entries = array_map(static function (array $entry): array {
            $read = Changelog::read($entry);

            return [
                'type' => $entry['type'],
                'version' => $entry['version'],
                'issue' => $entry['issue'],
                'title' => $read['title'] === '' ? $entry['source'] : $read['title'],
                'tags' => $read['tags'],
                'file' => 'EXT:core/Documentation/Changelog/' . $entry['version'] . '/' . $entry['key'] . '.rst',
            ];
        }, $shown);

        $versions = Changelog::versions();
        if ($entries === []) {
            $lines = [sprintf(
                'No changelog entry in this installation %s.',
                $terms === [] ? 'matched those filters' : 'carries all of ' . self::quotedTerms($terms),
            )];
            $reached = array_values(array_filter(
                LabelSearch::perTermCounts(Changelog::entries($type, $version), $terms),
                static fn(array $term): bool => $term['matchCount'] > 0,
            ));
            if (count($terms) > 1 && $reached !== []) {
                $lines[] = 'On its own, ' . implode(', ', array_map(
                    static fn(array $term): string => sprintf('"%s" reaches %d entr(ies)', $term['term'], $term['matchCount']),
                    $reached,
                )) . '.';
            }
            $lines[] = sprintf(
                'The changelog here covers %s. A version this installation does not ship is not in it — read that '
                . 'one in the core repository or at https://docs.typo3.org.',
                $versions === [] ? 'nothing' : implode(', ', array_slice($versions, 0, 8)) . ' and older',
            );

            return ToolResult::create(implode("\n", $lines), [
                'query' => $query,
                'matchCount' => 0,
                'entries' => [],
                'versions' => $versions,
                'answeredBy' => 'packages',
            ]);
        }

        $lines = [sprintf(
            '%d changelog entr%s%s%s:',
            count($matching),
            count($matching) === 1 ? 'y' : 'ies',
            $query === '' ? '' : sprintf(' carrying %s', self::quotedTerms($terms)),
            count($matching) > count($entries) ? sprintf(' — showing the first %d', count($entries)) : '',
        )];
        foreach ($entries as $entry) {
            $lines[] = sprintf('- %s %s: %s (#%s)', $entry['version'], $entry['type'], $entry['title'], $entry['issue']);
            $lines[] = '  ' . $entry['file'] . ($entry['tags'] === [] ? '' : ' — ' . implode(', ', $entry['tags']));
        }
        $lines[] = '';
        $lines[] = 'Read the file for the description and the migration. A Deprecation or Breaking entry tagged '
            . 'FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can '
            . 'find the call sites for you.';

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'matchCount' => count($matching),
            'entries' => $entries,
            'versions' => $versions,
            'answeredBy' => 'packages',
        ]);
    }

    /**
     * What the repository around the installation consists of.
     *
     * The knowledge base describes TYPO3; this describes the project, because
     * a recommendation is only worth as much as its fit: a check that is not
     * declared here does not exist here, whatever the core does with the same
     * name.
     */
    private static function projectScope(): ToolResult
    {
        $project = Project::describe();
        if ($project === null) {
            return self::consoleUnavailable(
                'no TYPO3 installation was found to describe',
                ['root' => null, 'extensions' => [], 'sites' => [], 'commands' => [], 'patches' => []],
            );
        }

        $lines = [sprintf(
            '%s — %s, TYPO3 %s, PHP %s',
            $project['root'],
            $project['kind'],
            $project['typo3Version'] ?? 'version unknown',
            $project['phpConstraint'] ?? 'unconstrained',
        )];

        $lines[] = '';
        $lines[] = $project['extensions'] === []
            ? 'Extensions: none beyond TYPO3\'s own.'
            : 'Extensions that are not TYPO3\'s own:';
        foreach ($project['extensions'] as $extension) {
            $lines[] = sprintf('- %s (%s) — %s', $extension['key'], $extension['origin'], $extension['path']);
        }

        $lines[] = '';
        $lines[] = $project['sites'] === []
            ? 'Sites: none configured below config/sites/.'
            : 'Sites, with the sets each one depends on:';
        foreach ($project['sites'] as $site) {
            $lines[] = sprintf(
                '- %s%s%s%s',
                $site['identifier'],
                $site['base'] === '' ? '' : ' at ' . $site['base'],
                $site['rootPageId'] === null ? '' : ', root page ' . $site['rootPageId'],
                $site['sets'] === [] ? ', no sets' : ', sets: ' . implode(', ', $site['sets']),
            );
        }

        $lines[] = '';
        $lines[] = $project['commands'] === []
            ? 'This repository declares no commands of its own in composer.json or package.json. What to run is '
                . 'then whatever its CI configuration does.'
            : 'Commands this repository declares — these exist here, the core\'s runTests.sh suites do not:';
        foreach ($project['commands'] as $command) {
            $lines[] = sprintf('- %s (%s)', $command['command'], $command['source']);
        }

        if ($project['patches'] !== []) {
            $lines[] = '';
            $lines[] = 'Patched dependencies — these packages do not behave as their version says, and the next '
                . 'composer update either reapplies the patch or fails on it:';
            foreach ($project['patches'] as $patch) {
                $lines[] = sprintf(
                    '- %s: %s (%s)',
                    $patch['package'],
                    $patch['description'] === '' ? 'no description given' : $patch['description'],
                    $patch['file'],
                );
            }
        }

        return ToolResult::create(implode("\n", $lines), $project + ['answeredBy' => 'packages']);
    }

    /**
     * What one extension registers, from its own files.
     *
     * @param array<string, mixed> $args
     */
    private static function extensionScope(array $args): ToolResult
    {
        $key = trim((string) ($args['extension'] ?? ''));
        $extension = $key === '' ? null : Extension::describe($key);
        if ($extension === null) {
            return self::extensionMiss($key);
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
        // The boundary of this answer, stated rather than implied: what is
        // declared in a file is here, what an extension does at runtime is not.
        $lines[] = 'Read from the files, so this is what the extension declares — not what it does at runtime. '
            . 'Registrations made in ext_localconf.php with a PHP call, and anything a hook or an event listener '
            . 'changes, are not in this list; the files that could hold them are named above.';

        return ToolResult::create(implode("\n", $lines), $extension + ['answeredBy' => 'packages']);
    }

    /** The keys there are, so a miss is a question a caller can ask again. */
    private static function extensionMiss(string $key): ToolResult
    {
        $installed = array_keys(Instance::packages());
        if ($installed === []) {
            return self::consoleUnavailable(
                'no TYPO3 installation was found, so there are no extensions to describe',
                self::EXTENSION_MISS_FIELDS + ['key' => $key],
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
            self::EXTENSION_MISS_FIELDS + ['key' => $key, 'installed' => $installed],
        );
    }

    /**
     * The fields the extension schema requires, empty. A miss answers with the
     * same shape as a hit, so a client never has to branch on which it got.
     *
     * @var array<string, mixed>
     */
    private const EXTENSION_MISS_FIELDS = [
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
        'artifacts' => ['manual' => null, 'readme' => null, 'tests' => [], 'languageFiles' => []],
        'answeredBy' => 'nothing',
    ];

    /**
     * Whether an extension is part of the core, and since when.
     *
     * Answered from the catalog rather than from the installation being read,
     * because the case that matters is the extension that is not installed:
     * that is when the question is asked, and answering it from memory is how a
     * community package gets cited as evidence of what the core does.
     *
     * @param array<string, mixed> $args
     */
    private static function systemExtensionLookup(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);
        $matches = SystemExtensions::find($query, $target);

        $covered = implode(', ', array_map(
            static fn(array $version): string => $version['branch'],
            Versions::covered(),
        ));
        $shipped = $target === null
            ? sprintf('The core ships these on %s.', $covered)
            : sprintf('The core ships these on TYPO3 v%d.', $target);

        if ($matches === []) {
            return ToolResult::create(
                sprintf(
                    '"%s" is not a system extension on %s. That is an answer about the core, not about the '
                    . 'package: it may well exist on Packagist or in the TER, where it is a third-party extension '
                    . 'with its own release cycle. Call this without a query for everything the core does ship.',
                    $query,
                    $target === null ? $covered : 'TYPO3 v' . $target,
                ),
                [
                    'query' => $query,
                    'targetVersion' => $target,
                    'matchCount' => 0,
                    'extensions' => [],
                    'coveredVersions' => Versions::majors(),
                ],
            );
        }

        $lines = [$shipped, ''];
        foreach ($matches as $entry) {
            $range = Versions::label($entry['since'], $entry['until']);
            $lines[] = sprintf(
                '- %s (%s)%s',
                $entry['key'],
                $entry['package'],
                $range === '' ? '' : ' — ' . $range,
            );
            if ($entry['description'] !== '') {
                $lines[] = '  ' . $entry['description'];
            }
        }

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'targetVersion' => $target,
            'matchCount' => count($matches),
            'extensions' => array_map(static fn(array $entry): array => [
                'key' => $entry['key'],
                'package' => $entry['package'],
                'description' => $entry['description'],
                'since' => $entry['since'],
                'until' => $entry['until'],
                'shippedOn' => Versions::label($entry['since'], $entry['until']),
            ], $matches),
            'coveredVersions' => Versions::majors(),
        ]);
    }

    /**
     * What a whole hint is binding for, where that is not this caller.
     *
     * The backend's design system is the case this exists for: every rule in it
     * is a condition of a core patch and none of it is a condition of anything
     * in a project — which does not make it useless there, because a project
     * building a backend module wants exactly those rules. So the answer keeps
     * them and says which of the two it is handing over.
     *
     * @param array<string, mixed> $hint
     */
    private static function bindingNotice(array $hint, bool $outsideCore): ?string
    {
        if (!$outsideCore || ($hint['binding'] ?? null) !== ArchitectureHints::BINDING_CORE) {
            return null;
        }

        return 'Binding for a patch to the TYPO3 core. Here they are conventions you may adopt — worth having '
            . 'where this repository builds the same thing, and no condition of anything in it.';
    }

    /**
     * The core's own worked example per hint id, as one line for the answer.
     *
     * A hint is a summary of something that exists in full and passing; naming
     * it beside the summary is what makes "read it" available at the moment the
     * summary turns out to be thin, rather than in a document read once.
     *
     * @return array<string, string>
     */
    private static function examplesByHint(?int $target): array
    {
        $lines = [];
        foreach (References::on($target) as $entry) {
            if ($entry['hint'] !== null) {
                $lines[$entry['hint']] = 'Worked example: ' . $entry['path']
                    . ' — typo3_reference_list for what it demonstrates and where an installation has it.';
            }
        }

        return $lines;
    }

    /**
     * The worked examples the core ships, so "read X" can be the answer.
     *
     * @param array<string, mixed> $args
     */
    private static function referenceList(array $args): ToolResult
    {
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);
        $entries = References::on($target);

        $lines = [
            $target === null
                ? 'Worked examples in the TYPO3 core, with the versions each exists on.'
                : sprintf('Worked examples in the TYPO3 core, as TYPO3 v%d has them.', $target),
            'Paths are relative to a core checkout. Where none is at hand, they are also the paths in '
                . 'github.com/TYPO3/typo3 on the matching branch.',
            '',
        ];
        foreach ($entries as $entry) {
            $range = Versions::label($entry['since'], $entry['until']);
            $lines[] = '- ' . $entry['path'] . ($range === '' ? '' : ' — ' . $range);
            $lines[] = '  ' . $entry['reference'];
            if ($entry['caveat'] !== null) {
                $lines[] = '  ' . $entry['caveat'];
            }
            $lines[] = '  ' . ($entry['package'] === null
                // Build/ is the repository's own; a Composer installation has
                // none of it, and saying so beats a caller searching vendor/
                // for a directory that was never published.
                ? 'Only in the core repository — no Composer package ships it.'
                : 'In an installation: vendor/' . $entry['package'] . '/, below the same path with the '
                    . 'typo3/sysext/<key>/ prefix removed.');
            if ($entry['hint'] !== null) {
                $lines[] = '  Conventions: typo3_architecture_lookup id="' . $entry['hint'] . '"';
            }
        }

        return ToolResult::create(implode("\n", $lines), [
            'targetVersion' => $target,
            'matchCount' => count($entries),
            'references' => array_map(static fn(array $entry): array => [
                'id' => $entry['id'],
                'path' => $entry['path'],
                'package' => $entry['package'],
                'reference' => $entry['reference'],
                'caveat' => $entry['caveat'],
                'hint' => $entry['hint'],
                'since' => $entry['since'],
                'until' => $entry['until'],
                'existsOn' => Versions::label($entry['since'], $entry['until']),
            ], $entries),
            'coveredVersions' => Versions::majors(),
        ]);
    }

    /** @param array<string, mixed> $args */
    private static function catalogScope(array $args): ToolResult
    {
        $meta = CatalogMeta::read();
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);
        $installed = InstalledComponents::isAvailable($target);
        $split = Components::splitByTarget(Components::find(null, $target), $target);

        $lines = [
            $installed ? 'Installed component contract' : 'Catalog validity',
            $target === null
                ? 'No target TYPO3 version was stated and none was found to read, so the whole catalog answers and '
                    . 'every entry carries the versions it was verified on. Pass targetVersion to have the ones that '
                    . 'were not verified there withheld.'
                : ($installed
                    ? sprintf(
                        'For TYPO3 v%d, %d of the %d curated component entries were found in the installed backend '
                            . 'CSS or JavaScript. Their class and custom-property contracts were read from those packages.',
                        $target,
                        count($split['holds']),
                        count($split['holds']) + count($split['withheld']),
                    )
                    : sprintf(
                    'For TYPO3 v%d, %d of %d components were verified; entries that were not are withheld.',
                    $target,
                    count($split['holds']),
                    count($split['holds']) + count($split['withheld']),
                )),
            $installed
                ? 'The bundled catalog remains the curated search index and markup fallback; it does not override installed classes.'
                : 'Each component entry owns this validity range. It does not inherit the version of the source checkout below.',
            '',
            'Bundled fallback source checkout',
            '- Source: ' . $meta['source']['repository'],
            '- Checkout branch: ' . $meta['source']['branch'] . ' (TYPO3 ' . $meta['source']['version'] . ')',
            '- Commit: ' . $meta['source']['commit'],
            '- Verified: ' . $meta['verifiedAt'],
            '- Re-check with: `' . $meta['verifyCommand'] . '`',
            '',
            'Scope',
        ];
        foreach ($meta['scope'] as $catalog => $scope) {
            $lines[] = '- ' . $catalog . ': ' . $scope;
        }

        $lines[] = '';
        $lines[] = 'Counts';
        foreach ($meta['counts'] as $name => $count) {
            $lines[] = '- ' . $name . ': ' . $count;
        }

        $lines[] = '';
        $lines[] = $installed
            ? 'A lookup miss means the component is not in the curated search index. The installed backend CSS may '
                . 'still contain an uncatalogued class, so inspect it before concluding the class does not exist.'
            : 'A lookup that finds nothing means the entry is not in this snapshot. On a different core '
                . 'branch — a 13.4 backport, for example — verify against the checkout before concluding that a '
                . 'component or class does not exist.';

        $withheldNote = self::withheldComponents($split['withheld'], $target);
        if ($withheldNote !== '') {
            $lines[] = '';
            $lines[] = $withheldNote;
        }

        if (!$installed && CatalogMeta::skew() !== '') {
            $lines[] = '';
            $lines[] = CatalogMeta::skew();
        }

        return ToolResult::create(implode("\n", $lines), [
            'catalog' => self::catalogRecord($installed),
            'verifyCommand' => $meta['verifyCommand'],
            'scope' => $meta['scope'],
            'counts' => $meta['counts'],
            'targetVersion' => $target,
            'verifiedCount' => count($split['holds']),
            'componentSource' => $installed ? 'installation' : 'catalog',
            'withheld' => self::withheldRecords($split['withheld']),
        ]);
    }

    private static function catalogProvenance(): string
    {
        return CatalogMeta::line();
    }

    /**
     * The provenance every catalog answer carries, so a client can tell a miss
     * on an old snapshot from a miss on the branch it works on.
     *
     * @return array<string, string>
     */
    private static function catalogRecord(bool $componentsDerived = false): array
    {
        $meta = CatalogMeta::read();

        return [
            'repository' => $meta['source']['repository'],
            'branch' => $meta['source']['branch'],
            'version' => $meta['source']['version'],
            'commit' => $meta['source']['commit'],
            'verifiedAt' => $meta['verifiedAt'],
            // Both numbers were known and never contrasted. They travel
            // together now, in every answer that carries the pin at all.
            'installedVersion' => Instance::typo3Version(),
            'skew' => $componentsDerived || CatalogMeta::skew() === '' ? null : CatalogMeta::skew(),
        ];
    }

    /**
     * The answer for a question only the installation could have answered, when
     * it could not be asked.
     *
     * Kept in one place because the distinction it draws is the same every
     * time: an empty result and an unanswerable question look identical, and
     * only one of them means the thing does not exist.
     *
     * @param array<string, mixed> $data
     */
    private static function consoleUnavailable(string $error, array $data): ToolResult
    {
        $diagnosis = Typo3Cli::diagnose($error);
        if ($diagnosis === '' && Typo3Cli::caveat() !== '') {
            $diagnosis = 'What is known about this console: ' . Typo3Cli::caveat() . '.';
        }

        return ToolResult::create(
            sprintf(
                "The installation could not be asked, so this is unanswered rather than empty: %s.\n%s"
                . 'typo3_server_scope reports the installation and its console.',
                $error,
                $diagnosis === '' ? '' : $diagnosis . "\n",
            ),
            $data + [
                'answeredBy' => 'nothing',
                // The reason travels with the answer, not only in the text
                // beside it: a client that renders structuredContent alone
                // would otherwise see an empty result and nothing else, which
                // is exactly what a registry that really is empty looks like.
                'unavailable' => [
                    'reason' => $error,
                    'diagnosis' => $diagnosis,
                    'settings' => [
                        'root' => Instance::ROOT_VARIABLE,
                        'console' => Typo3Cli::CONSOLE_VARIABLE,
                    ],
                ],
            ],
        );
    }

    /**
     * Where the identifiers this tool answers with may be used.
     *
     * It travels with every answer rather than with the ones that look like
     * frontend work, because the tool is handed a query and not a task: nothing
     * in "product package box" says which half of TYPO3 it is for. An
     * identifier is resolved by IconFactory and rendered by <core:icon>, and a
     * frontend template reaches neither — so an answer without this sentence is
     * usable in a place where it is wrong.
     */
    private const ICON_SCOPE = 'These identifiers address the backend icon registry. They are resolved by '
        . 'IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, '
        . 'and needs its own inline SVG or asset file.';

    /**
     * Icon identifiers registered in the installation.
     *
     * The only instance question with no console command behind it, so the
     * registry is read from the three places TYPO3 assembles it from. An
     * identifier-shaped query is a validation and is answered as one: fuzzy
     * results for it are suggestions, and saying so is the whole point —
     * a confident wrong substitute is worse than a miss.
     *
     * @param array<string, mixed> $args
     */
    private static function iconLookup(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $limit = (int) ($args['limit'] ?? 40);
        $scope = self::ICON_SCOPE;

        if (!Instance::isAvailable()) {
            return self::consoleUnavailable(
                'no TYPO3 installation was found from the directory this server was started in',
                ['query' => $query, 'matchCount' => 0, 'suggestionCount' => 0, 'exactMatch' => false, 'icons' => []],
            );
        }

        $concepts = InstalledIcons::concepts();
        if ($query === '') {
            $lines = [$scope, ''];
            $lines[] = 'Icon categories in this installation: ' . implode(', ', InstalledIcons::categories()) . '.';
            $lines[] = '';
            $lines[] = 'Concept words that map to a shape: ' . implode(', ', array_keys($concepts)) . '.';

            return ToolResult::create(implode("\n", $lines), [
                'query' => $query,
                'matchCount' => 0,
                'suggestionCount' => 0,
                'exactMatch' => false,
                'icons' => [],
                'categories' => InstalledIcons::categories(),
                'concepts' => array_keys($concepts),
                'scope' => $scope,
                'answeredBy' => 'installation',
            ]);
        }

        $isIdentifier = InstalledIcons::looksLikeIdentifier($query);
        $exactMatch = $isIdentifier && InstalledIcons::has($query);
        $matches = self::rankIcons($query, $concepts);

        $total = count($matches);
        $suggestionCount = $isIdentifier
            ? count(array_filter(
                $matches,
                static fn(array $icon): bool => $icon['identifier'] !== mb_strtolower($query)
            ))
            : 0;
        $shown = array_slice($matches, 0, $limit);
        $root = Instance::root() ?? 'the installation';

        if ($shown === []) {
            return ToolResult::create(
                ($isIdentifier
                    ? sprintf('"%s" is not registered in %s.', $query, $root)
                    : sprintf(
                        'No icon in %s matches "%s". Identifiers spell the shape, not the intent — try a concept '
                        . 'word such as %s.',
                        $root,
                        $query,
                        implode(', ', array_slice(array_keys($concepts), 0, 8))
                    )) . "\n" . $scope,
                [
                    'query' => $query,
                    'matchCount' => 0,
                    'suggestionCount' => 0,
                    'exactMatch' => false,
                    'icons' => [],
                    'scope' => $scope,
                    'answeredBy' => 'installation',
                ],
            );
        }

        $header = $isIdentifier && !$exactMatch
            ? sprintf(
                '"%s" is not registered in %s. It is shaped like an identifier, so the %d below merely share a '
                . 'name part with it — suggestions, not the answer',
                $query,
                $root,
                $suggestionCount
            )
            : ($isIdentifier
                ? sprintf(
                    '"%s" is registered in %s%s',
                    $query,
                    $root,
                    $suggestionCount === 0
                        ? ''
                        : sprintf('; %d related identifier(s) follow as suggestions', $suggestionCount)
                )
                : sprintf('%d icon identifier(s) in %s match "%s"', $total, $root, $query));
        if ($total > count($shown)) {
            $header .= sprintf(' — showing the top %d', count($shown));
        }

        $lines = [$scope, '', $header . ':'];
        foreach ($shown as $icon) {
            $lines[] = '- ' . $icon['identifier'];
            if ($icon['aliasOf'] !== null) {
                $lines[] = '  alias of ' . $icon['aliasOf'];
            }
            if ($icon['source'] !== InstalledIcons::SOURCE_T3ICONS) {
                $lines[] = '  registered in ' . $icon['source'];
            }
            $lines[] = '  matched: ' . implode(', ', $icon['why']);
        }

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            // A complete identifier is a validation question. Its only match is
            // the identifier itself; same-category or same-name results are
            // suggestions and travel under their own count. In particular,
            // the usage prefixes actions-/content- must not turn one missing
            // identifier into hundreds of apparent matches.
            'matchCount' => $isIdentifier ? ($exactMatch ? 1 : 0) : $total,
            'suggestionCount' => $suggestionCount,
            'exactMatch' => $exactMatch,
            'icons' => $shown,
            'scope' => $scope,
            'answeredBy' => 'installation',
        ]);
    }

    /**
     * Ranks the registered identifiers against a query.
     *
     * A concept only contributes a term the identifier's own name did not
     * already carry, so a vague identifier cannot outrank a precise one by
     * matching the same word twice.
     *
     * @param array<string, array<int, string>> $concepts
     * @return array<int, array<string, mixed>>
     */
    private static function rankIcons(string $query, array $concepts): array
    {
        $terms = array_values(array_unique(array_filter(
            preg_split('/[\s_-]+/', mb_strtolower(trim($query))) ?: [],
            static fn(string $term): bool => $term !== ''
        )));
        if ($terms === []) {
            return [];
        }
        $normalized = implode('-', $terms);
        if (InstalledIcons::looksLikeIdentifier($query)) {
            // actions-, content-, status- and the other leading categories say
            // where/how an icon is used. They are not a distinguishing shape
            // and therefore contribute nothing to related suggestions.
            array_shift($terms);
        }

        $suggested = [];
        foreach ($terms as $term) {
            foreach ($concepts[$term] ?? [] as $identifier) {
                $suggested[$identifier][$term] = true;
            }
        }

        $scored = [];
        foreach (InstalledIcons::all() as $icon) {
            $segments = explode('-', $icon['identifier']);
            $matched = [];
            $score = 0;
            $why = [];

            foreach ($terms as $term) {
                if (in_array($term, $segments, true)) {
                    $matched[] = $term;
                    $score += 4;
                    $why[] = 'name part "' . $term . '"';
                } elseif (str_contains($icon['identifier'], $term)) {
                    $matched[] = $term;
                    $score += 2;
                    $why[] = 'substring "' . $term . '"';
                }
            }
            foreach ($suggested[$icon['identifier']] ?? [] as $concept => $unused) {
                if (!in_array($concept, $matched, true)) {
                    $matched[] = $concept;
                }
                $score += 3;
                $why[] = 'concept "' . $concept . '"';
            }
            if ($matched === []) {
                continue;
            }
            if ($icon['identifier'] === $normalized) {
                $score += 1000;
                $why[] = 'exact identifier';
            }

            $scored[] = $icon + ['matched' => count($matched), 'score' => $score, 'why' => $why];
        }

        usort($scored, static function (array $a, array $b): int {
            return $b['matched'] <=> $a['matched']
                ?: $b['score'] <=> $a['score']
                ?: strcmp($a['identifier'], $b['identifier']);
        });

        return $scored;
    }

    /** The globally registered Fluid namespaces of the installation. */
    private static function fluidNamespaceList(): ToolResult
    {
        $answer = Typo3Cli::json(['fluid:namespaces', '--json']);
        $answeredBy = 'installation';
        $declared = is_array($answer['data']) ? $answer['data'] : [];
        if (!$answer['ok'] || !is_array($answer['data'])) {
            // The declarations are files in the same packages, so a console
            // that cannot boot does not have to end the question. What the
            // files cannot say is what the container did with them.
            $declared = InstalledFluidNamespaces::all();
            if ($declared === []) {
                return self::consoleUnavailable($answer['error'], ['namespaces' => [], 'matchCount' => 0]);
            }
            $answeredBy = 'packages';
        }

        $namespaces = [];
        foreach ($declared as $prefix => $classNames) {
            $namespaces[] = [
                'prefix' => (string) $prefix,
                'phpNamespaces' => array_map('strval', (array) $classNames),
            ];
        }
        usort($namespaces, static fn(array $a, array $b): int => strcmp($a['prefix'], $b['prefix']));

        $lines = [sprintf('%d globally registered Fluid namespace(s):', count($namespaces))];
        foreach ($namespaces as $namespace) {
            $lines[] = '- ' . $namespace['prefix'] . ': ' . implode(', ', $namespace['phpNamespaces']);
        }
        $lines[] = '';
        $lines[] = 'These prefixes work in any template without being declared. Every other namespace is declared '
            . 'in the template itself — xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" on the root '
            . 'element, together with data-namespace-typo3-fluid="true" so the declaration is stripped from the output.';
        if ($answeredBy === 'packages') {
            $lines[] = '';
            $lines[] = sprintf(
                'Read from the Configuration/Fluid/Namespaces.php of the installed packages: the console could not '
                . 'be asked (%s). That is what the packages declare, not what the container assembled from them.',
                $answer['error'],
            );
        }

        return ToolResult::create(implode("\n", $lines), [
            'matchCount' => count($namespaces),
            'namespaces' => $namespaces,
            'answeredBy' => $answeredBy,
        ]);
    }

    /**
     * An effective TYPO3_CONF_VARS value: what it is at runtime after every
     * extension has had its say, which is rarely what the shipped default says.
     *
     * @param array<string, mixed> $args
     */
    private static function configurationLookup(array $args): ToolResult
    {
        $path = trim((string) ($args['path'] ?? ''), " \t/");

        $answer = Typo3Cli::json(['configuration:show', $path, '--type=active', '--json']);

        // A path that does not exist is a legitimate answer, not a breakage,
        // and the console says which of the two happened in its exit code.
        if (!$answer['ok'] && $answer['exitCode'] === 1 && str_contains($answer['error'], 'No configuration found')) {
            return ToolResult::create(
                sprintf('The installation has no configuration at "%s".', $path),
                ['path' => $path, 'found' => false, 'value' => null, 'answeredBy' => 'installation'],
            );
        }
        if (!$answer['ok']) {
            // found stays null rather than false: false is a statement about
            // the installation — "it has no value at that path" — and nothing
            // was consulted to make it.
            return self::consoleUnavailable($answer['error'], ['path' => $path, 'found' => null, 'value' => null]);
        }

        return ToolResult::create(
            sprintf(
                "Effective value of TYPO3_CONF_VARS/%s in this installation:\n\n```json\n%s\n```\n\n"
                . 'This is the assembled runtime value, not the default the core ships.',
                $path,
                json_encode($answer['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
            ),
            ['path' => $path, 'found' => true, 'value' => $answer['data'], 'answeredBy' => 'installation'],
        );
    }

    /**
     * The backend modules the installation has registered.
     *
     * The console has no JSON mode here, but it has a CSV one, which is a
     * format rather than a rendering — so nothing is recovered from a drawn
     * table.
     *
     * @param array<string, mixed> $args
     */
    private static function backendModuleLookup(array $args): ToolResult
    {
        $query = mb_strtolower(trim((string) ($args['query'] ?? '')));

        $result = Typo3Cli::run(['debug:backend:modules', '--csv-export']);
        if (!$result['ok']) {
            return self::consoleUnavailable(
                $result['error'] !== '' ? $result['error'] : trim($result['output']),
                ['query' => $query, 'matchCount' => 0, 'modules' => []],
            );
        }

        $modules = [];
        foreach (self::csvRows($result['output']) as $row) {
            // The three level columns are one path through the module tree, so
            // the deepest filled one is the module and the rest are above it.
            $levels = array_values(array_filter([
                $row['Main level'] ?? '',
                $row['Second level'] ?? '',
                $row['Third level'] ?? '',
            ], static fn(string $level): bool => trim($level) !== ''));
            if ($levels === []) {
                continue;
            }

            $module = [
                'identifier' => (string) array_pop($levels),
                'parents' => $levels,
                'extension' => (string) ($row['Pkg'] ?? ''),
                'labels' => (string) ($row['Labels'] ?? ''),
                'path' => (string) ($row['Path'] ?? ''),
                'position' => (string) ($row['Position'] ?? ''),
            ];

            $haystack = mb_strtolower(implode(' ', array_merge($module['parents'], [
                $module['identifier'],
                $module['extension'],
                $module['labels'],
                $module['path'],
            ])));
            if ($query !== '' && !str_contains($haystack, $query)) {
                continue;
            }
            $modules[] = $module;
        }

        if ($modules === []) {
            return ToolResult::create(
                sprintf('No backend module in this installation matches "%s".', $query),
                ['query' => $query, 'matchCount' => 0, 'modules' => [], 'answeredBy' => 'installation'],
            );
        }

        $lines = [sprintf('%d backend module(s)%s:', count($modules), $query === '' ? '' : ' matching "' . $query . '"')];
        foreach ($modules as $module) {
            $lines[] = '- ' . implode(' > ', array_merge($module['parents'], [$module['identifier']]));
            $lines[] = '  ' . $module['path'] . '  (' . $module['extension'] . ')';
            if ($module['labels'] !== '') {
                $lines[] = '  ' . $module['labels'];
            }
        }
        $lines[] = '';
        $lines[] = 'A module is declared in its extension\'s Configuration/Backend/Modules.php; the label in '
            . 'brackets is a translation domain reference.';

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'matchCount' => count($modules),
            'modules' => $modules,
            'answeredBy' => 'installation',
        ]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function csvRows(string $output): array
    {
        $lines = preg_split('/\R/', trim($output)) ?: [];
        $headers = null;
        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $fields = str_getcsv($line, ';', '"', '\\');
            if ($headers === null) {
                $headers = array_map('strval', $fields);
                continue;
            }
            if (count($fields) !== count($headers)) {
                continue;
            }
            $rows[] = array_map(static fn($v): string => (string) $v, array_combine($headers, $fields));
        }

        return $rows;
    }

    /**
     * Labels registered in the installation, answered by the installation.
     *
     * The console searches the packages it has active, which is what makes the
     * answer right: a project extension's labels are in it, and so are the
     * resource overrides the installation applies. Neither follows from a core
     * checkout, and neither could be shipped as a snapshot.
     *
     * @param array<string, mixed> $args
     */
    private static function labelLookup(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $extension = trim((string) ($args['extension'] ?? ''));
        $resource = trim((string) ($args['resource'] ?? ''));
        $limit = (int) ($args['limit'] ?? 25);
        $terms = LabelSearch::terms($query);

        if ($extension === '' && preg_match('#^EXT:([^/]+)/#', $resource, $matches) === 1) {
            $extension = $matches[1];
        }
        $arguments = ['language:domain:search', LabelSearch::consoleOption($terms), '--json', '--crop=0'];
        if ($extension !== '') {
            $arguments[] = '--extension=' . $extension;
        }

        $answer = Typo3Cli::json($arguments);

        // The console prints a warning instead of a payload when nothing
        // matched, and exits successfully while doing it. That is an
        // installation that answered "none", not one that could not be asked —
        // and the difference decides whether the caller refines the query or
        // goes looking for a console that is not broken.
        $answeredBy = 'installation';
        $candidates = [];
        if (!is_array($answer['data']) && $answer['exitCode'] !== 0) {
            // The labels are in the packages' files whether or not the console
            // boots, and it needs a migrated database to boot. A weaker answer
            // beats none, as long as it says which one it is.
            $candidates = InstalledLabels::all($extension);
            if ($candidates === []) {
                return self::consoleUnavailable(
                    $answer['error'],
                    [
                        'query' => $query,
                        'resource' => $resource === '' ? null : $resource,
                        'matchCount' => 0,
                        'labels' => [],
                        'terms' => [],
                    ],
                );
            }
            $answeredBy = 'packages';
        }

        /** @var array<string, mixed> $data */
        $data = is_array($answer['data']) ? $answer['data'] : [];
        foreach ($data['items'] ?? [] as $item) {
            foreach ($item['labels'] ?? [] as $label) {
                $candidates[] = [
                    'ref' => (string) $label['domain'] . ':' . (string) $label['reference'],
                    'domain' => (string) $label['domain'],
                    'key' => (string) $label['reference'],
                    'source' => (string) $label['label'],
                    'resource' => (string) ($item['resource'] ?? ''),
                ];
            }
        }

        if ($resource !== '') {
            $candidates = array_values(array_filter(
                $candidates,
                static fn(array $label): bool => $label['resource'] === $resource,
            ));
        }

        // The console returned everything carrying any of the words; the query
        // asked for the labels carrying all of them.
        $labels = LabelSearch::carryingEvery($candidates, $terms);
        $termCounts = LabelSearch::perTermCounts($candidates, $terms);

        $total = count($labels);
        $shown = array_slice($labels, 0, $limit);
        $instance = Instance::describe();

        $fromFiles = $answeredBy === 'packages' ? sprintf(
            "\n\nRead from the XLF files of the installed packages: the console could not be asked (%s). "
            . 'What that leaves out is the assembled runtime state — a label an installation replaces through '
            . 'LANG/resourceOverrides is shown here as its package ships it.',
            $answer['error'],
        ) : '';
        $reuseBoundary = $resource === ''
            ? "\n\nA match is reusable only when its resource is the one already used at the consuming code. "
                . 'A label from another module or package is not a shared vocabulary merely because its text matches; '
                . 'call again with resource once that usage context is known.'
            : "\n\nSearch restricted to the translation resource used at the consuming code: " . $resource . '.';

        if ($shown === []) {
            $lines = [sprintf(
                'No label in %s %s. This is an answer about your installation rather than about TYPO3 in general.',
                $resource !== '' ? $resource : ($instance['root'] ?? 'the installation'),
                count($terms) > 1 ? 'carries all of ' . self::quotedTerms($terms) : sprintf('matches "%s"', $query)
            )];

            $reached = array_values(array_filter($termCounts, static fn(array $t): bool => $t['matchCount'] > 0));
            if (count($terms) > 1 && $reached !== []) {
                $lines[] = '';
                $lines[] = 'On its own, ' . implode(', ', array_map(
                    static fn(array $t): string => sprintf('"%s" matches %d label(s)', $t['term'], $t['matchCount']),
                    $reached
                )) . ' — ask again with the one that narrows best.';
            }

            return ToolResult::create(implode("\n", $lines) . $reuseBoundary . $fromFiles, [
                'query' => $query,
                'resource' => $resource === '' ? null : $resource,
                'matchCount' => 0,
                'labels' => [],
                'terms' => $termCounts,
                'answeredBy' => $answeredBy,
            ]);
        }

        $lines = [sprintf('%d label(s) in %s match "%s"%s:', $total,
            $resource !== '' ? $resource : ($instance['root'] ?? '?'), $query,
            $total > count($shown) ? sprintf(' — showing the first %d', count($shown)) : '')];
        foreach ($shown as $label) {
            $lines[] = '- ' . $label['ref'];
            $lines[] = '  "' . $label['source'] . '"';
            $lines[] = '  ' . $label['resource'];
        }
        $lines[] = '';
        $lines[] = 'Reference a label by the domain form shown first (package.resource:key) — in TCA, in '
            . 'LanguageService::sL(), and in f:translate as separate domain and key attributes.';

        return ToolResult::create(implode("\n", $lines) . $reuseBoundary . $fromFiles, [
            'query' => $query,
            'resource' => $resource === '' ? null : $resource,
            'matchCount' => $total,
            'labels' => $shown,
            'terms' => $termCounts,
            'answeredBy' => $answeredBy,
        ]);
    }

    /**
     * @param array<int, string> $terms
     */
    private static function quotedTerms(array $terms): string
    {
        return implode(', ', array_map(static fn(string $term): string => '"' . $term . '"', $terms));
    }

    /**
     * The translation domain an XLF file resolves to, computed from its path.
     *
     * Nothing registers a domain: it follows from the path by the rules in
     * the core's own path-to-domain rules — the class holding them has been
     * both TranslationDomainMapper and TranslationDomainResolver. Computing it
     * rather than looking it up is what makes it answerable at all — for a file
     * in any extension, in any instance, and for one a patch is about to add,
     * which is exactly when it cannot be looked up anywhere.
     *
     * @param array<string, mixed> $args
     */
    private static function translationDomainLookup(array $args): ToolResult
    {
        $path = trim((string) ($args['path'] ?? ''));
        $domain = TranslationDomain::fromPath($path);

        if ($domain === null) {
            return ToolResult::create(
                sprintf(
                    "\"%s\" names no extension, so no translation domain follows from it.\n"
                    . 'Pass either an EXT: reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") '
                    . 'or a checkout path ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").',
                    $path
                ),
                ['path' => $path, 'domain' => null],
            );
        }

        // The domain form is younger than the versions this is asked from. On
        // an installation that has no resolver for it, the domain string is
        // syntactically fine and resolves to nothing at runtime: every label it
        // is written into silently renders empty. That is the one answer here
        // that has to be withheld rather than qualified.
        $major = Instance::typo3Major();
        if ($major !== null && $major < self::TRANSLATION_DOMAIN_SINCE) {
            $reference = str_starts_with($path, 'EXT:') ? $path : 'EXT:<key>/' . ltrim($path, '/');

            return ToolResult::create(
                implode("\n", [
                    sprintf(
                        'The installation here is TYPO3 %s, which has no translation domains: the API that resolves '
                        . 'them arrived after it. Reference the file itself instead:',
                        Instance::typo3Version(),
                    ),
                    '',
                    '  LLL:' . $reference . ':<trans-unit id>',
                    '',
                    sprintf(
                        'For the record, the domain this path would resolve to on a version that has them is "%s". '
                        . 'Writing it into a label on this installation renders nothing, and fails at runtime rather '
                        . 'than at build time.',
                        $domain,
                    ),
                ]),
                ['path' => $path, 'domain' => null, 'domainOnNewerVersions' => $domain],
            );
        }

        return ToolResult::create(
            implode("\n", [
                sprintf('%s resolves to the translation domain:', $path),
                '',
                '  ' . $domain,
                '',
                'Reference a label in it as "' . $domain . ':<trans-unit id>" — in TCA, in LanguageService::sL(), '
                    . 'and in f:translate as separate domain and key attributes.',
                'Which trans-units the file actually holds is a property of your checkout: read the file, and remember '
                    . 'that an installation can override it through LANG/resourceOverrides.',
            ]),
            ['path' => $path, 'domain' => $domain, 'domainOnNewerVersions' => null],
        );
    }

    /** @param array<string, mixed> $args */
    private static function commitMessageGuide(array $args): ToolResult
    {
        $existing = isset($args['message']) ? trim((string) $args['message']) : '';
        $workflow = CommitMessage::workflow($args['workflow'] ?? null);

        $parseChecks = [];
        if ($existing !== '') {
            $parsed = CommitMessage::parse($existing, $workflow);
            // Explicit arguments still win, so a message can be checked and
            // amended in one call: pass the message plus issue=12345.
            $input = array_merge($parsed['input'], array_intersect_key($args, array_flip([
                'changeType', 'summary', 'issue', 'relatedIssues', 'releases', 'isBreaking', 'isDeprecation',
            ])));
            $parseChecks = $parsed['checks'];
        } else {
            $input = $args;
        }
        $input['workflow'] = $workflow;

        if (!isset($input['summary']) || trim((string) $input['summary']) === '') {
            throw new \InvalidArgumentException(
                'Provide either a complete message, or changeType and summary.'
            );
        }

        /** @var array{changeType: string, summary: string} $input */
        $result = CommitMessage::create($input);

        $checks = $result['checks'];
        if ($parseChecks !== []) {
            // "Nothing to complain about" only holds when nothing complained.
            $checks = array_values(array_filter(
                $checks,
                static fn(array $check): bool => $check['level'] !== 'info'
            ));
        }

        $checks = array_merge($parseChecks, $checks);

        $heading = $existing === '' ? 'Commit message draft:' : 'Commit message, corrected:';
        $lines = [$heading, '```text', $result['message'], '```', '', 'Checks:'];
        foreach ($checks as $check) {
            $lines[] = '- ' . strtoupper($check['level']) . ': ' . $check['message'];
        }

        // Which rules were applied belongs in the answer, because the two sets
        // differ in what they demand rather than in how strict they are: a
        // caller who did not know about the other one reads a missing Forge
        // issue as a defect in their commit message.
        $lines[] = '';
        $lines[] = $workflow === CommitMessage::WORKFLOW_PROJECT
            ? 'Checked without the core workflow: keyword, 52/72 limits and wrapping apply, the Forge issue and '
                . 'the Releases: trailer do not. workflow="core" for a patch against the TYPO3 core.'
            : 'Checked against the core contribution rules, trailers included. workflow="project" applies the same '
                . 'subject and body rules without the Forge issue and the Releases: trailer.';

        return ToolResult::create(implode("\n", $lines), [
            'message' => $result['message'],
            'checks' => $checks,
            'workflow' => $workflow,
        ]);
    }
}
