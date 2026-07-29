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
    ];

    /**
     * @return array<int, array{id: string, title: string, coreOnly: bool, match: array<int, string>, matchWeak: array<int, string>, condition: string, rulesQuery: string, checklist: array<int, string>, checks: array<int, string>, tools: array<int, string>}>
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
            // Whether the intent is the core's own contribution process rather
            // than a kind of work. Patch submission is one: outside the core
            // there is no Gerrit to submit to, so the intent is not a weaker
            // match there — it is not one at all.
            'coreOnly' => (bool) ($entry['coreOnly'] ?? false),
            'match' => array_map('strval', $entry['match'] ?? []),
            'matchWeak' => array_map('strval', $entry['matchWeak'] ?? []),
            'condition' => (string) ($entry['condition'] ?? ''),
            'rulesQuery' => (string) ($entry['rulesQuery'] ?? ''),
            'checklist' => array_map('strval', $entry['checklist'] ?? []),
            'checks' => array_map('strval', $entry['checks'] ?? []),
            'tools' => array_map('strval', $entry['tools'] ?? []),
        ], $decoded);
    }

    /**
     * Intents mentioned in the task text, in catalog order, each carrying how
     * sure the match is.
     *
     * A word can name a subject without naming the work: "field label" in a
     * FormEngine task is not an XLF change, but the word alone looks exactly
     * like one. Such a needle lives in matchWeak, and the intent it triggers is
     * returned as conditional rather than as recognized — its checklist and
     * checks apply only if the change really does what the word suggests.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function detect(string $text): array
    {
        $haystack = mb_strtolower($text);
        $detected = [];

        foreach (self::load() as $intent) {
            $confidence = null;
            foreach ($intent['match'] as $needle) {
                if (Text::containsWord($haystack, $needle)) {
                    $confidence = 'strong';
                    break;
                }
            }
            if ($confidence === null) {
                foreach ($intent['matchWeak'] as $needle) {
                    if (Text::containsWord($haystack, $needle)) {
                        $confidence = 'weak';
                        break;
                    }
                }
            }
            if ($confidence !== null) {
                $intent['confidence'] = $confidence;
                $detected[] = $intent;
            }
        }

        return $detected;
    }

    /**
     * The intents a brief may state as fact — the strongly matched ones.
     *
     * A weak match is never promoted, not even when it is the only one. The
     * brief does not know whether the task really is that kind of work, and
     * saying so is more useful than guessing: the checklist and checks of a
     * weak intent are still returned, marked with the condition they hold
     * under.
     *
     * @param array<int, array<string, mixed>> $intents
     * @return array<int, array<string, mixed>>
     */
    public static function confirmed(array $intents): array
    {
        return array_values(array_filter(
            $intents,
            static fn(array $intent): bool => $intent['confidence'] === 'strong'
        ));
    }

    /**
     * The detected intents, with the core-only ones held to the evidence there
     * is for them.
     *
     * The words that select a core-only intent are ordinary ones — "review",
     * "push", "submit" — and they occur in every description of maintenance
     * work. Reading one of them as a Gerrit patch submission put the whole core
     * contribution workflow into an answer about a third-party extension, which
     * is not a partly wrong answer but a wholly wrong one.
     *
     * Outside the core the intent is dropped: there is no Gerrit to submit to,
     * so it is not a weaker match there but none at all. Where nothing says
     * either way it is demoted to a conditional match, because most tasks name
     * neither side and a brief that guesses is what this is fixing.
     *
     * @param array<int, array<string, mixed>> $intents
     * @return array<int, array<string, mixed>>
     */
    public static function scoped(array $intents, bool $outsideCore, bool $coreWork): array
    {
        $scoped = [];
        foreach ($intents as $intent) {
            if ($intent['coreOnly'] !== true || $coreWork) {
                $scoped[] = $intent;
                continue;
            }
            if ($outsideCore) {
                continue;
            }
            $intent['confidence'] = 'weak';
            $scoped[] = $intent;
        }

        return $scoped;
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
