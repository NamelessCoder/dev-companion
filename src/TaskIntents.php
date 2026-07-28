<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Recognises what kind of core work a task description asks for.
 *
 * A task brief is the first tool an agent calls, so it has to know that
 * "deprecate a method" is one of the most rule-heavy change types in the core
 * and not just a cleanup. Each intent carries the checklist items, checks, and
 * follow-up tools that apply, plus the query that pulls the matching rule
 * sections out of the knowledge documents.
 */
final class TaskIntents
{
    /** Knowledge documents an intent may pull rule sections from. */
    private const RULE_DOCUMENTS = [
        'typo3-core-rules',
        'typo3-commit-messages',
        'typo3-gerrit-workflow',
        'typo3-core-architecture',
    ];

    /**
     * @return array<int, array{id: string, title: string, match: array<int, string>, rulesQuery: string, checklist: array<int, string>, checks: array<int, string>, tools: array<int, string>}>
     */
    public static function load(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('task-intents.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid task-intents.json');
        }

        return array_map(static fn(array $entry): array => [
            'id' => (string) $entry['id'],
            'title' => (string) $entry['title'],
            'match' => array_map('strval', $entry['match'] ?? []),
            'rulesQuery' => (string) ($entry['rulesQuery'] ?? ''),
            'checklist' => array_map('strval', $entry['checklist'] ?? []),
            'checks' => array_map('strval', $entry['checks'] ?? []),
            'tools' => array_map('strval', $entry['tools'] ?? []),
        ], $decoded);
    }

    /**
     * Intents mentioned in the task text, in catalog order.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function detect(string $text): array
    {
        $haystack = mb_strtolower($text);

        return array_values(array_filter(self::load(), static function (array $intent) use ($haystack): bool {
            foreach ($intent['match'] as $needle) {
                if (Text::containsWord($haystack, $needle)) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * The rule sections behind the detected intents, deduplicated.
     *
     * @param array<int, array<string, mixed>> $intents
     * @return array<int, array{id: string, title: string, heading: string, body: string, score: int, coverage: float, truncated: bool}>
     */
    public static function rules(array $intents, int $limitPerIntent = 2): array
    {
        $sections = [];
        $seen = [];
        foreach ($intents as $intent) {
            if ($intent['rulesQuery'] === '') {
                continue;
            }
            foreach (Knowledge::search($intent['rulesQuery'], self::RULE_DOCUMENTS, $limitPerIntent) as $section) {
                $key = $section['id'] . '#' . $section['heading'];
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $sections[] = $section;
            }
        }

        return $sections;
    }
}
