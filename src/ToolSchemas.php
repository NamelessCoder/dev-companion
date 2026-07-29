<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * The JSON schemas of the structured tool results.
 *
 * They are a contract: a client may validate `structuredContent` against the
 * declared `outputSchema`, so a field named here has to exist in the answer,
 * with the type stated. Schemas stay open (no additionalProperties: false) so a
 * new field is an addition rather than a break, and only fields that are always
 * present are required.
 *
 * They live next to the tool definitions in Tools, not inside them, because the
 * record shapes — a knowledge match, an architecture hint, a catalog entry —
 * are shared by several tools.
 */
final class ToolSchemas
{
    /** @return array<string, mixed>|null */
    public static function forTool(string $name): ?array
    {
        return match ($name) {
            'typo3_server_scope' => self::serverScope(),
            'typo3_rule_lookup' => self::knowledgeLookup(),
            'typo3_script_lookup' => self::scriptLookup(),
            'typo3_task_guide' => self::taskGuide(),
            'typo3_test_run_guide' => self::testRunGuide(),
            'typo3_architecture_lookup' => self::architectureLookup(),
            'typo3_component_lookup' => self::componentLookup(),
            'typo3_translation_domain_lookup' => self::translationDomainLookup(),
            'typo3_label_lookup' => self::labelLookup(),
            'typo3_fluid_namespace_list' => self::fluidNamespaceList(),
            'typo3_configuration_lookup' => self::configurationLookup(),
            'typo3_backend_module_lookup' => self::backendModuleLookup(),
            'typo3_icon_lookup' => self::iconLookup(),
            'typo3_changelog_lookup' => self::changelogLookup(),
            'typo3_project_scope' => self::projectScope(),
            'typo3_extension_scope' => self::extensionScope(),
            'typo3_catalog_scope' => self::catalogScope(),
            'typo3_commit_message_guide' => self::commitMessageGuide(),
            'typo3_feedback_record' => self::feedbackRecord(),
            'typo3_feedback_list' => self::feedbackList(),
            default => null,
        };
    }

    /** @return array<string, mixed> */
    private static function serverScope(): array
    {
        return self::object([
            'purpose' => self::string('What this server is for.'),
            'instructions' => self::string('The boundary statement clients receive at initialize time.'),
            'covers' => self::listOf(self::object([
                'topic' => self::string(),
                'depth' => self::string('How deeply the topic is covered.'),
                'tools' => self::listOf(self::string()),
                'source' => self::string('Knowledge file or typo3:// resource behind the topic.'),
                'provenance' => ['type' => 'string', 'enum' => ['core-only', 'transferable', 'installation'], 'description' => 'What the answers are worth outside the core. core-only: the contribution process and the scripts of that repository. transferable: a convention that holds wherever TYPO3 is written. installation: answered by the installation being read rather than from a snapshot.'],
            ], ['topic', 'depth', 'tools', 'source', 'provenance'])),
            'doesNotCover' => self::listOf(self::object([
                'topic' => self::string(),
                'why' => self::string(),
                'instead' => self::string('What to do instead of asking this server.'),
            ], ['topic', 'why', 'instead'])),
            'checkoutDiscovery' => self::listOf(self::object([
                'establish' => self::string(),
                'how' => self::string(),
            ], ['establish', 'how'])),
            'routing' => self::listOf(self::object([
                'when' => self::string(),
                'call' => self::string(),
            ], ['when', 'call'])),
            'versions' => self::listOf(self::object([
                'major' => self::integer(),
                'branch' => self::string('The branch that line is verified against.'),
                'status' => self::string('lts, stable, or development.'),
            ], ['major', 'branch', 'status']), 'The TYPO3 versions the knowledge is bound to. A statement outside a range is left out when a target version is known.'),
            'profile' => self::object([
                'active' => ['type' => 'string', 'enum' => ['all', 'project'], 'description' => 'Which half of the server this client is offered. all: every tool. project: the same server without the core contribution surface, because a project or extension repository cannot follow it.'],
                'via' => ['type' => 'string', 'enum' => ['environment', 'installation'], 'description' => 'Whether the profile was named by the environment variable or followed from the kind of installation that was found.'],
                'omits' => self::listOf(self::string(), 'The tools this profile leaves out of the tool list. Empty in the all profile.'),
                'variable' => self::string('Environment variable that names the profile outright.'),
                'misconfiguration' => self::nullableString('Set when the variable named a profile that does not exist. The derived one is used instead.'),
            ], ['active', 'via', 'omits', 'variable']),
            'installation' => self::object([
                'found' => ['type' => 'boolean', 'description' => 'Whether there is an installation to read at all.'],
                'root' => self::nullableString('Absolute path of the installation.'),
                'kind' => self::nullableString('core-checkout or composer-project.'),
                'via' => self::nullableString('How it was determined: discovery (walked up from the start directory) or environment (named by TYPO3_MCP_ROOT).'),
                'startedFrom' => self::nullableString('Where the search started, or the configured value.'),
                'searched' => self::listOf(self::string(), 'The directories the search walked. A failure here means a layout that cannot be read or a server started in the wrong place — this says which.'),
                'packageCount' => self::integer('TYPO3 packages found in it.'),
                'misconfiguration' => self::nullableString('Set when a configured value could not be followed. Nothing falls back to a discovered installation.'),
                'console' => self::object([
                    'reachable' => ['type' => 'boolean', 'description' => 'False means every installation-backed tool answers with answeredBy: nothing.'],
                    'via' => self::nullableString('ddev, php, or override.'),
                    'php' => self::nullableString('The PHP version it runs on, where that is known.'),
                    'command' => self::nullableString('The invocation, as it is run.'),
                    'reason' => self::nullableString('Why it cannot be run. Null when it can.'),
                    'caveat' => self::nullableString('What limits the console that was found — a project whose containers are stopped answers what its files say and fails on everything that boots TYPO3 against its database. Null when nothing limits it.'),
                ], ['reachable']),
                'settings' => self::object([
                    'root' => self::string('Environment variable that names the installation root.'),
                    'console' => self::string('Environment variable that names the console command.'),
                ], ['root', 'console']),
            ], ['found', 'searched', 'packageCount', 'console']),
        ], ['purpose', 'covers', 'doesNotCover', 'routing', 'versions', 'profile', 'installation']);
    }

    /**
     * Why an installation-backed answer is unanswered rather than empty.
     *
     * Present exactly when answeredBy is "nothing". The text said this all
     * along; a client that renders structuredContent and drops the text block
     * saw an empty result and nothing else.
     *
     * @return array<string, mixed>
     */
    private static function unavailable(): array
    {
        return self::object([
            'reason' => self::string('What stopped the installation from being asked.'),
            'diagnosis' => self::string('What the reason means where the message alone does not say it — a console that starts and then fails on a missing table has a database without a schema, not a broken installation. Empty where nothing beyond the reason is known.'),
            'settings' => self::object([
                'root' => self::string('Environment variable that names the installation root.'),
                'console' => self::string('Environment variable that names the console command.'),
            ], ['root', 'console']),
        ], ['reason']);
    }

    /**
     * The knowledge lookup shape plus the boundary, because every command in
     * the script notes is a command in the core repository.
     *
     * @return array<string, mixed>
     */
    private static function scriptLookup(): array
    {
        $schema = self::knowledgeLookup();
        $schema['properties']['outsideCore'] = [
            'type' => 'boolean',
            'description' => 'True when the query reads as a project or third-party extension. No script is then '
                . 'returned: these are the core checkout\'s own, and that repository declares its commands itself.',
        ];
        $schema['required'][] = 'outsideCore';

        return $schema;
    }

    /** @return array<string, mixed> */
    private static function knowledgeLookup(): array
    {
        return self::object([
            'query' => self::string(),
            'matchCount' => self::integer(),
            'matches' => self::listOf(self::knowledgeMatch()),
            'documents' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
                'topics' => self::listOf(self::string()),
            ], ['id', 'title', 'topics']), 'Documents in the knowledge base with the topics they cover. Returned when nothing matched.'),
            'elsewhere' => self::listOf(self::string(), 'Documents outside the searched ones that do match the query.'),
            'alsoInHints' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
            ], ['id', 'title']), 'Architecture hints matching the same query. They are a second corpus, searched by typo3_architecture_lookup, which takes one of these ids.'),
        ], ['query', 'matchCount', 'matches']);
    }

    /** @return array<string, mixed> */
    private static function taskGuide(): array
    {
        return self::object([
            'task' => self::string(),
            'area' => self::nullableString('Affected subsystem or path, if one was given.'),
            'changeType' => self::string(),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major the answer was composed for — stated by the caller, or read from the installation. Null means nothing was filtered by version.'],
            'domains' => self::listOf(self::string()),
            'outsideCore' => ['type' => 'boolean', 'description' => 'True when the task reads as work on a project or third-party extension. The answer then holds core conventions that may transfer, not a checklist for the task.'],
            'intents' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
                'confidence' => ['type' => 'string', 'enum' => ['strong', 'weak'], 'description' => 'weak: a word named the subject without naming the work, or the intent is a core-only one and nothing in the task says this is core work. Either way it applies only under its condition.'],
                'condition' => self::string('When a weakly matched intent applies. Empty for a strong match.'),
            ], ['id', 'title', 'confidence', 'condition']), 'The kinds of core work recognized in the task text.'),
            'architectureHints' => self::listOf(self::architectureHintRecord()),
            'rules' => self::listOf(self::knowledgeMatch(), 'Rule sections that apply to this task.'),
            'checks' => self::listOf(self::string(), 'Commands to run, ready to execute from the core root.'),
            'conditionalChecks' => self::listOf(self::object([
                'title' => self::string(),
                'condition' => self::string(),
                'checks' => self::listOf(self::string()),
            ], ['title', 'condition', 'checks']), 'Checks that only apply if the task really is the kind of work a weakly matched intent suggests.'),
            'testSuites' => self::listOf(self::testSuiteRecord()),
            'checklist' => self::listOf(self::string()),
            'checkoutDiscovery' => self::listOf(self::object([
                'establish' => self::string(),
                'how' => self::string(),
            ], ['establish', 'how']), 'What this server cannot see and the agent has to establish itself.'),
            'nextTools' => self::listOf(self::object([
                'tool' => self::string(),
                'when' => self::string(),
            ], ['tool', 'when'])),
        ], ['task', 'changeType', 'domains', 'architectureHints', 'checks', 'checklist', 'nextTools']);
    }

    /** @return array<string, mixed> */
    private static function testRunGuide(): array
    {
        return self::object([
            'query' => self::nullableString(),
            'paths' => self::listOf(self::string(), 'The paths the answer was narrowed by, given ones and ones named in the query.'),
            'domains' => self::listOf(self::string(), 'Domains those paths touch. Empty means nothing was narrowed.'),
            'outsideCore' => ['type' => 'boolean', 'description' => 'True when the paths read as a project or third-party extension. No suite is then returned: runTests.sh is part of the core repository and cannot be run there.'],
            'suites' => self::listOf(self::testSuiteRecord()),
            'invocation' => self::object([
                'notes' => self::listOf(self::string()),
                'options' => self::listOf(self::object([
                    'option' => self::string(),
                    'description' => self::string(),
                ], ['option', 'description'])),
                'examples' => self::listOf(self::object([
                    'purpose' => self::string(),
                    'command' => self::string(),
                ], ['purpose', 'command'])),
            ], ['notes', 'options', 'examples']),
        ], ['outsideCore', 'suites', 'invocation']);
    }

    /** @return array<string, mixed> */
    private static function architectureLookup(): array
    {
        return self::object([
            'task' => self::nullableString(),
            'paths' => self::listOf(self::string()),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major the answer was composed for — stated by the caller, or read from the installation. Null means nothing was filtered and every statement carries its own range.'],
            'domains' => self::listOf(self::string(), 'Hints outside these domains are not returned.'),
            'withheldCategories' => self::listOf(self::string(), 'Categories that matched the domains but were left out because the task names the frontend. "Backend CSS" and "Backend TypeScript" describe the TYPO3 backend interface and are wrong advice for what a website renders; see docs.typo3.org for frontend theming.'),
            'outsideCore' => ['type' => 'boolean', 'description' => 'True when the paths or the task read as a project or third-party extension. The hints still hold; their checks are then empty, because runTests.sh is part of the core repository.'],
            'hints' => self::listOf(self::architectureHintRecord()),
            'availableHints' => self::listOf(self::object([
                'id' => self::string('Ask for this hint outright by passing it as id.'),
                'title' => self::string(),
                'category' => self::string(),
            ], ['id', 'title', 'category']), 'The hints that exist in the searched domains, returned when none matched. Empty on a hit.'),
            'knowledgeSections' => self::listOf(self::knowledgeMatch(), 'Fallback prose, returned only when no structured hint matched.'),
        ], ['paths', 'domains', 'withheldCategories', 'outsideCore', 'hints', 'availableHints', 'knowledgeSections']);
    }

    /** @return array<string, mixed> */
    private static function componentLookup(): array
    {
        return self::object([
            'query' => self::nullableString(),
            'matchCount' => self::integer(),
            'components' => self::listOf(self::object([
                'name' => self::string(),
                'title' => self::string(),
                'summary' => self::string(),
                'rootClass' => self::string(),
                'variants' => self::listOf(self::string()),
                'modifiers' => self::listOf(self::string()),
                'subComponents' => self::listOf(self::string()),
                'customProperties' => self::listOf(self::string()),
                'markup' => self::string('Canonical markup of the component.'),
                'examples' => self::listOf(self::string()),
                'sassPath' => self::nullableString('Primary Sass source in the core checkout; null for a web component that carries its own styles.'),
                'sassPaths' => self::listOf(self::string(), 'Every Sass source the component spans. A component can be split across several files.'),
                'demoPath' => self::nullableString('Styleguide demo in the core checkout, if there is one.'),
                'matchedIn' => self::listOf(self::string(), 'Where the query matched: name, keywords, sub-component classes, description.'),
                'describesVersion' => self::string('The TYPO3 version this entry was taken from. Markup, sub-component classes and the custom-property contract are what that version has; compare with catalog.installedVersion before pasting.'),
            ], ['name', 'title', 'rootClass', 'sassPath', 'demoPath', 'describesVersion'])),
            'checklist' => self::object([
                'title' => self::string(),
                'intro' => self::string(),
                'items' => self::listOf(self::string()),
            ], ['title', 'items']),
            'catalog' => self::catalogProvenance(),
        ], ['matchCount', 'components', 'catalog']);
    }

    /**
     * Every answer that comes from the installation says so, because an empty
     * result and an unanswerable question are not the same thing.
     *
     * @return array<string, mixed>
     */
    private static function answeredBy(): array
    {
        return [
            'type' => 'string',
            'enum' => ['installation', 'packages', 'nothing'],
            'description' => 'installation: its assembled runtime state answered. packages: read from the files '
                . 'the installed packages ship, because the console could not be asked — overrides applied at '
                . 'runtime are not reflected. nothing: the installation could not be asked at all, so an empty '
                . 'result is unanswered rather than a miss.',
        ];
    }

    /** @return array<string, mixed> */
    private static function iconLookup(): array
    {
        return self::object([
            'query' => self::string(),
            'matchCount' => self::integer(),
            'exactMatch' => ['type' => 'boolean', 'description' => 'Whether the query was a registered identifier. False for a query shaped like one that is not registered — the listed icons are then suggestions, not the answer.'],
            'answeredBy' => self::answeredBy(),
            'unavailable' => self::unavailable(),
            'icons' => self::listOf(self::object([
                'identifier' => self::string(),
                'category' => self::string(),
                'aliasOf' => self::nullableString('The identifier this one is an alias of.'),
                'source' => self::string('Where it is registered: t3icons, flags, or the EXT:<key>/Configuration/Icons.php that declares it.'),
                'matched' => self::integer('Query terms it matched.'),
                'score' => self::integer(),
                'why' => self::listOf(self::string()),
            ], ['identifier', 'category', 'aliasOf', 'source'])),
            'categories' => self::listOf(self::string(), 'Returned when no query was given.'),
            'concepts' => self::listOf(self::string(), 'Concept words that map to a shape. Returned when no query was given.'),
        ], ['query', 'matchCount', 'exactMatch', 'answeredBy', 'icons']);
    }

    /** @return array<string, mixed> */
    private static function fluidNamespaceList(): array
    {
        return self::object([
            'matchCount' => self::integer(),
            'answeredBy' => self::answeredBy(),
            'unavailable' => self::unavailable(),
            'namespaces' => self::listOf(self::object([
                'prefix' => self::string('The prefix usable in a template without declaring it, for example "core".'),
                'phpNamespaces' => self::listOf(self::string(), 'The PHP namespaces it resolves ViewHelpers from.'),
            ], ['prefix', 'phpNamespaces'])),
        ], ['matchCount', 'answeredBy', 'namespaces']);
    }

    /** @return array<string, mixed> */
    private static function configurationLookup(): array
    {
        return self::object([
            'path' => self::string('The TYPO3_CONF_VARS path that was read.'),
            'found' => ['type' => ['boolean', 'null'], 'description' => 'Whether the installation has a value at that path. Null when nothing was consulted — see unavailable; false is a statement about the installation and is never made without one.'],
            'value' => ['description' => 'The effective runtime value, of whatever shape the configuration has.'],
            'answeredBy' => self::answeredBy(),
            'unavailable' => self::unavailable(),
        ], ['path', 'found', 'answeredBy']);
    }

    /** @return array<string, mixed> */
    private static function backendModuleLookup(): array
    {
        return self::object([
            'query' => self::string(),
            'matchCount' => self::integer(),
            'answeredBy' => self::answeredBy(),
            'unavailable' => self::unavailable(),
            'modules' => self::listOf(self::object([
                'identifier' => self::string(),
                'parents' => self::listOf(self::string(), 'The modules it sits under, outermost first.'),
                'extension' => self::string('The package that declares it.'),
                'labels' => self::string('Its label, with the translation domain reference behind it.'),
                'path' => self::string('The backend route it answers on.'),
                'position' => self::string('Its declared before/after position, if any.'),
            ], ['identifier', 'parents', 'extension', 'path'])),
        ], ['query', 'matchCount', 'answeredBy', 'modules']);
    }

    /** @return array<string, mixed> */
    private static function labelLookup(): array
    {
        return self::object([
            'query' => self::string(),
            'matchCount' => self::integer(),
            'answeredBy' => self::answeredBy(),
            'unavailable' => self::unavailable(),
            'terms' => self::listOf(self::object([
                'term' => self::string('One word of the query; a label has to carry every one of them.'),
                'matchCount' => self::integer('How many labels this word alone reaches — where to narrow when the query as a whole reaches none.'),
            ], ['term', 'matchCount'])),
            'labels' => self::listOf(self::object([
                'ref' => self::string('Translation domain reference (package.resource:key) — the canonical form.'),
                'domain' => self::string(),
                'key' => self::string('The trans-unit id.'),
                'source' => self::string('The label text in the searched locale.'),
                'resource' => self::string('The XLF file it lives in.'),
            ], ['ref', 'domain', 'key', 'source'])),
        ], ['query', 'matchCount', 'answeredBy', 'terms', 'labels']);
    }

    /** @return array<string, mixed> */
    private static function translationDomainLookup(): array
    {
        return self::object([
            'path' => self::string('The XLF path the domain was computed from.'),
            'domain' => self::nullableString('The translation domain it resolves to. Null when the path names no extension, and also when the installation being read is too old to resolve domains at all — there the full LLL:EXT: reference is the answer.'),
            'domainOnNewerVersions' => self::nullableString('Set only in that second case: what the domain would be on a version that has them. It is not usable on this installation.'),
        ], ['path', 'domain']);
    }

    /** @return array<string, mixed> */
    private static function changelogLookup(): array
    {
        return self::object([
            'query' => self::string(),
            'matchCount' => self::integer('Entries carrying every word of the query, before the limit.'),
            'entries' => self::listOf(self::object([
                'type' => ['type' => 'string', 'enum' => ['Breaking', 'Deprecation', 'Feature', 'Important']],
                'version' => self::string('The version directory it was released in.'),
                'issue' => self::string('Forge issue number.'),
                'title' => self::string(),
                'tags' => self::listOf(self::string(), 'Index tags. FullyScanned or PartiallyScanned means the extension scanner has a matcher for it.'),
                'file' => self::string('EXT: reference of the entry, to read the description and the migration.'),
            ], ['type', 'version', 'issue', 'title', 'tags', 'file'])),
            'versions' => self::listOf(self::string(), 'The versions this installation ships changelog entries for, newest first. Anything outside them is not in this answer.'),
            'answeredBy' => self::answeredBy(),
            'unavailable' => self::unavailable(),
        ], ['query', 'matchCount', 'entries', 'versions', 'answeredBy']);
    }

    /** @return array<string, mixed> */
    private static function projectScope(): array
    {
        return self::object([
            'root' => self::nullableString('Absolute path of the project. Null when there is no installation to describe.'),
            'kind' => self::string('core-checkout or composer-project.'),
            'typo3Version' => self::nullableString('The TYPO3 version installed here, read from the core package.'),
            'phpConstraint' => self::nullableString('What composer.json requires of PHP.'),
            'coreConstraint' => self::nullableString('What it requires of typo3/cms-core.'),
            'extensions' => self::listOf(self::object([
                'key' => self::string(),
                'path' => self::string('Relative to the project root.'),
                'origin' => ['type' => 'string', 'enum' => ['project', 'third-party'], 'description' => 'project: inside the repository, so what it is working on. third-party: installed as a dependency.'],
            ], ['key', 'path', 'origin']), 'Extensions that are not TYPO3 system extensions.'),
            'sites' => self::listOf(self::object([
                'identifier' => self::string(),
                'base' => self::string(),
                'rootPageId' => ['type' => ['integer', 'null']],
                'sets' => self::listOf(self::string(), 'The site sets this site depends on, by their composer-style name.'),
                'languages' => self::listOf(self::string()),
            ], ['identifier', 'base', 'rootPageId', 'sets', 'languages'])),
            'commands' => self::listOf(self::object([
                'command' => self::string('Ready to run in this repository.'),
                'source' => self::string('composer.json or package.json.'),
            ], ['command', 'source']), 'What this repository declares. A check that is not here does not exist here.'),
            'patches' => self::listOf(self::object([
                'package' => self::string('The dependency being patched.'),
                'description' => self::string('What the patch is for, where composer.json says.'),
                'file' => self::string('The patch file, relative to the project root.'),
            ], ['package', 'description', 'file']), 'Patches from extra.patches. A patched package does not behave as its version says.'),
            'answeredBy' => self::answeredBy(),
            'unavailable' => self::unavailable(),
        ], ['root', 'extensions', 'sites', 'commands', 'patches', 'answeredBy']);
    }

    /** @return array<string, mixed> */
    private static function extensionScope(): array
    {
        return self::object([
            'key' => self::string('The extension key that was asked for.'),
            'path' => self::nullableString('Absolute path of the extension. Null when the installation does not have it.'),
            'origin' => ['type' => ['string', 'null'], 'enum' => ['system', 'project', 'third-party', null], 'description' => 'system: TYPO3\'s own. project: inside the repository. third-party: installed as a dependency.'],
            'composerName' => self::nullableString('The Composer package name it declares.'),
            'description' => self::nullableString('What its composer.json says it is.'),
            'requires' => self::listOf(self::object([
                'package' => self::string(),
                'constraint' => self::string(),
            ], ['package', 'constraint']), 'What it requires, which is where a version conflict during an upgrade comes from.'),
            'tcaTables' => self::listOf(self::string(), 'Tables its Configuration/TCA/ defines, by file name.'),
            'tcaOverrides' => self::listOf(self::string(), 'Tables it extends below Configuration/TCA/Overrides/.'),
            'contentElements' => self::listOf(self::string(), 'CType identifiers it adds to tt_content, read from the addTcaSelectItem() calls in those override files. An identifier assembled at runtime or taken from a constant is not among them.'),
            'backendModules' => self::listOf(self::string(), 'Module identifiers from Configuration/Backend/Modules.php.'),
            'backendRoutes' => self::listOf(self::string(), 'Route names from Configuration/Backend/Routes.php and AjaxRoutes.php.'),
            'icons' => self::listOf(self::string(), 'Identifiers from Configuration/Icons.php. typo3_icon_lookup searches every package at once.'),
            'siteSets' => self::listOf(self::object([
                'name' => self::string('The composer-style set name a site depends on.'),
                'path' => self::string('Relative to the extension.'),
            ], ['name', 'path'])),
            'middlewares' => self::listOf(self::string(), 'Middleware identifiers from Configuration/RequestMiddlewares.php, across the request scopes.'),
            'serviceTags' => self::listOf(self::string(), 'Tags its Services.yaml carries, such as data.processor, event.listener or console.command.'),
            'fluidRoots' => self::listOf(self::string(), 'Which of Resources/Private/Templates, Partials and Layouts exist.'),
            'fluidNamespaces' => self::listOf(self::string(), 'Prefixes it registers globally in Configuration/Fluid/Namespaces.php.'),
            'typoScript' => self::listOf(self::string(), 'Files below Configuration/TypoScript/.'),
            'classes' => self::listOf(self::object([
                'kind' => self::string('The Classes/ subdirectory, for example EventListener or DataProcessing.'),
                'files' => self::integer('PHP files below it.'),
            ], ['kind', 'files'])),
            'files' => self::listOf(self::string(), 'Registration files it ships, from ext_localconf.php to Initialisation/data.t3d.'),
            'installed' => self::listOf(self::string(), 'On a miss: the extension keys this installation does have.'),
            'answeredBy' => self::answeredBy(),
            'unavailable' => self::unavailable(),
        ], ['key', 'path', 'origin', 'tcaTables', 'tcaOverrides', 'contentElements', 'backendModules', 'icons', 'siteSets', 'serviceTags', 'files', 'answeredBy']);
    }

    /** @return array<string, mixed> */
    private static function catalogScope(): array
    {
        return self::object([
            'catalog' => self::catalogProvenance(),
            'verifyCommand' => self::string(),
            'scope' => self::object([], [], 'One entry per catalog describing what it contains.'),
            'counts' => self::object([], [], 'One entry per catalog with its number of entries.'),
        ], ['catalog', 'verifyCommand', 'scope', 'counts']);
    }

    /** @return array<string, mixed> */
    private static function commitMessageGuide(): array
    {
        return self::object([
            'message' => self::string('The commit message, ready to use.'),
            'checks' => self::listOf(self::object([
                'level' => ['type' => 'string', 'enum' => ['error', 'warning', 'info']],
                'code' => self::string('Stable identifier of the check, for example summary-too-long.'),
                'message' => self::string(),
            ], ['level', 'code', 'message'])),
            'workflow' => [
                'type' => 'string',
                'enum' => ['core', 'project'],
                'description' => 'Which rules the draft was written and checked against. "core" adds the Forge '
                    . 'issue and the Releases: trailer and demands them; "project" applies the subject and body '
                    . 'rules alone.',
            ],
        ], ['message', 'checks', 'workflow']);
    }

    /** @return array<string, mixed> */
    private static function feedbackRecord(): array
    {
        return self::object([
            'file' => self::string('Path of the recorded note, relative to the project root.'),
        ], ['file']);
    }

    /** @return array<string, mixed> */
    private static function feedbackList(): array
    {
        return self::object([
            'count' => self::integer(),
            'notes' => self::listOf(self::object([
                'file' => self::string(),
                'date' => self::string(),
                'category' => self::string(),
                'status' => self::string(),
                'tool' => self::string('The tools the note is about, comma-separated. Empty when it names none.'),
                'tools' => self::listOf(self::string(), 'The same names as a list, to filter or group by without parsing.'),
                'title' => self::string(),
            ], ['file', 'date', 'category', 'status', 'tool', 'tools', 'title'])),
        ], ['count', 'notes']);
    }

    /** @return array<string, mixed> */
    private static function knowledgeMatch(): array
    {
        return self::object([
            'documentId' => self::string(),
            'title' => self::string('Title of the knowledge document.'),
            'uri' => self::string('typo3://core resource holding the full document.'),
            'heading' => self::string('Heading of the matched section.'),
            'body' => self::string('The section as written, formatting included.'),
            'coverage' => ['type' => 'number', 'description' => 'Share of the query terms the section covers, 0 to 1.'],
            'score' => self::integer('Weighted match score; headings weigh more than body text.'),
            'truncated' => ['type' => 'boolean', 'description' => 'Whether the body was cut; read the resource for the rest.'],
        ], ['documentId', 'title', 'uri', 'heading', 'body', 'coverage', 'score', 'truncated']);
    }

    /** @return array<string, mixed> */
    private static function architectureHintRecord(): array
    {
        return self::object([
            'id' => self::string(),
            'title' => self::string(),
            'category' => self::string('PHP, TypeScript, JavaScript, CSS, or General.'),
            'hints' => self::listOf(self::object([
                'text' => self::string('The statement itself. It reads the same on every version it holds for; the range is beside it, never inside it.'),
                'since' => ['type' => ['integer', 'null'], 'description' => 'First TYPO3 major this holds on. Null means as far back as this knowledge base reaches.'],
                'until' => ['type' => ['integer', 'null'], 'description' => 'Last TYPO3 major this holds on. Null means it still holds.'],
                'versions' => self::string('The same range as a sentence, empty when the statement is bound to nothing.'),
            ], ['text', 'since', 'until', 'versions'])),
            'checks' => self::listOf(self::string(), 'Commands relevant to this hint.'),
        ], ['id', 'title', 'category', 'hints', 'checks']);
    }

    /** @return array<string, mixed> */
    private static function testSuiteRecord(): array
    {
        return self::object([
            'suite' => self::string(),
            'command' => self::string('Full command, run from the core root.'),
            'targeted' => self::nullableString('Narrowed form for iterating on a single file or test.'),
            'description' => self::string(),
            'whenToUse' => self::string(),
            'domains' => self::listOf(self::string()),
        ], ['suite', 'command', 'targeted']);
    }

    /** @return array<string, mixed> */
    private static function catalogProvenance(): array
    {
        return self::object([
            'repository' => self::string(),
            'branch' => self::string(),
            'version' => self::string('TYPO3 version of the snapshot.'),
            'commit' => self::string('Core revision the catalogs were taken from.'),
            'verifiedAt' => self::string(),
            'installedVersion' => self::nullableString('TYPO3 version of the installation this server was started in, where there is one. Null means there was nothing to compare the snapshot with.'),
            'skew' => self::nullableString('Set when that installation and the snapshot are different TYPO3 majors, and what to do about it. Null when they agree or nothing is known.'),
        ], ['branch', 'version', 'commit', 'verifiedAt'], 'The core revision behind catalog answers, and how it relates to the installation being read. A miss means "not in this snapshot".');
    }

    /**
     * @param array<string, mixed> $properties
     * @param array<int, string> $required
     * @return array<string, mixed>
     */
    private static function object(array $properties, array $required = [], string $description = ''): array
    {
        $schema = ['type' => 'object'];
        if ($properties !== []) {
            $schema['properties'] = $properties;
        }
        if ($required !== []) {
            $schema['required'] = $required;
        }
        if ($description !== '') {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /**
     * @param array<string, mixed> $items
     * @return array<string, mixed>
     */
    private static function listOf(array $items, string $description = ''): array
    {
        $schema = ['type' => 'array', 'items' => $items];
        if ($description !== '') {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /** @return array<string, mixed> */
    private static function string(string $description = ''): array
    {
        return $description === '' ? ['type' => 'string'] : ['type' => 'string', 'description' => $description];
    }

    /** @return array<string, mixed> */
    private static function nullableString(string $description = ''): array
    {
        $schema = ['type' => ['string', 'null']];
        if ($description !== '') {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /** @return array<string, mixed> */
    private static function integer(string $description = ''): array
    {
        return $description === '' ? ['type' => 'integer'] : ['type' => 'integer', 'description' => $description];
    }
}
