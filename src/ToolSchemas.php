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
            'typo3_rule_lookup', 'typo3_script_lookup' => self::knowledgeLookup(),
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
            ], ['topic', 'depth', 'tools', 'source'])),
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
                ], ['reachable']),
                'settings' => self::object([
                    'root' => self::string('Environment variable that names the installation root.'),
                    'console' => self::string('Environment variable that names the console command.'),
                ], ['root', 'console']),
            ], ['found', 'searched', 'packageCount', 'console']),
        ], ['purpose', 'covers', 'doesNotCover', 'routing', 'installation']);
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
            'settings' => self::object([
                'root' => self::string('Environment variable that names the installation root.'),
                'console' => self::string('Environment variable that names the console command.'),
            ], ['root', 'console']),
        ], ['reason']);
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
        ], ['query', 'matchCount', 'matches']);
    }

    /** @return array<string, mixed> */
    private static function taskGuide(): array
    {
        return self::object([
            'task' => self::string(),
            'area' => self::nullableString('Affected subsystem or path, if one was given.'),
            'changeType' => self::string(),
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
            'domains' => self::listOf(self::string(), 'Hints outside these domains are not returned.'),
            'outsideCore' => ['type' => 'boolean', 'description' => 'True when the paths or the task read as a project or third-party extension. The hints still hold; their checks are then empty, because runTests.sh is part of the core repository.'],
            'hints' => self::listOf(self::architectureHintRecord()),
            'knowledgeSections' => self::listOf(self::knowledgeMatch(), 'Fallback prose, returned only when no structured hint matched.'),
        ], ['paths', 'domains', 'outsideCore', 'hints', 'knowledgeSections']);
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
            ], ['name', 'title', 'rootClass', 'sassPath', 'demoPath'])),
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
        return ['type' => 'string', 'enum' => ['installation', 'nothing'], 'description' => 'nothing: the installation could not be asked, so an empty result is unanswered rather than a miss.'];
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
            'domain' => self::nullableString('The translation domain it resolves to; null when the path names no extension.'),
        ], ['path', 'domain']);
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
        ], ['message', 'checks']);
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
            'hints' => self::listOf(self::string()),
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
        ], ['branch', 'version', 'commit', 'verifiedAt'], 'The core revision behind catalog answers. A miss means "not in this snapshot".');
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
