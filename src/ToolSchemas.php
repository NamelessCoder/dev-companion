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
            'typo3_icon_lookup' => self::iconLookup(),
            'typo3_label_lookup' => self::labelLookup(),
            'typo3_catalog_scope' => self::catalogScope(),
            'typo3_commit_message_guide' => self::commitMessageGuide(),
            'typo3_make_me_better' => self::makeMeBetter(),
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
        ], ['purpose', 'covers', 'doesNotCover', 'routing']);
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
            'intents' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
            ], ['id', 'title']), 'The kinds of core work recognized in the task text.'),
            'architectureHints' => self::listOf(self::architectureHintRecord()),
            'rules' => self::listOf(self::knowledgeMatch(), 'Rule sections that apply to this task.'),
            'checks' => self::listOf(self::string(), 'Commands to run, ready to execute from the core root.'),
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
        ], ['suites', 'invocation']);
    }

    /** @return array<string, mixed> */
    private static function architectureLookup(): array
    {
        return self::object([
            'task' => self::nullableString(),
            'paths' => self::listOf(self::string()),
            'domains' => self::listOf(self::string(), 'Hints outside these domains are not returned.'),
            'hints' => self::listOf(self::architectureHintRecord()),
            'knowledgeSections' => self::listOf(self::knowledgeMatch(), 'Fallback prose, returned only when no structured hint matched.'),
        ], ['paths', 'domains', 'hints', 'knowledgeSections']);
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
                'sassPath' => self::nullableString('Sass source in the core checkout; null for a web component that carries its own styles.'),
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

    /** @return array<string, mixed> */
    private static function iconLookup(): array
    {
        return self::object([
            'query' => self::nullableString(),
            'matchCount' => self::integer(),
            'icons' => self::listOf(self::object([
                'identifier' => self::string('The registered T3Icons identifier.'),
                'category' => self::string(),
                'aliasOf' => self::nullableString('The identifier this one is an alias of.'),
                'matched' => self::integer('Query terms this identifier matched.'),
                'score' => self::integer(),
                'why' => self::listOf(self::string(), 'Why it matched: name part, substring, concept, exact identifier.'),
            ], ['identifier', 'category', 'aliasOf', 'score', 'why'])),
            'categories' => self::listOf(self::string()),
            'concepts' => self::listOf(self::string(), 'Concept keywords that map to icons.'),
            'catalog' => self::catalogProvenance(),
        ], ['matchCount', 'icons', 'catalog']);
    }

    /** @return array<string, mixed> */
    private static function labelLookup(): array
    {
        return self::object([
            'query' => self::nullableString(),
            'mode' => self::string('keys or domains.'),
            'matchCount' => self::integer(),
            'relaxed' => ['type' => 'boolean', 'description' => 'True when no label matched every query term and any-term matching was used.'],
            'labels' => self::listOf(self::object([
                'ref' => self::string('Translation domain reference (package.resource:key) — the canonical form.'),
                'legacyRef' => self::string('LLL: file path form.'),
                'key' => self::string('The trans-unit id.'),
                'source' => self::string('English source text.'),
                'unusedSince' => self::nullableString('x-unused-since marker; the label is retired but must not be deleted.'),
                'matchedIn' => self::listOf(self::string()),
            ], ['ref', 'legacyRef', 'key', 'source', 'unusedSince'])),
            'domains' => self::listOf(self::object([
                'domain' => self::string(),
                'ref' => self::string(),
                'ext' => self::string(),
                'file' => self::string(),
                'count' => self::integer(),
            ], ['domain', 'ref', 'count'])),
            'catalog' => self::catalogProvenance(),
        ], ['mode', 'matchCount', 'catalog']);
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
    private static function makeMeBetter(): array
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
                'tool' => self::string(),
                'title' => self::string(),
            ], ['file', 'date', 'category', 'status', 'tool', 'title'])),
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
