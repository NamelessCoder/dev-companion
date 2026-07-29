<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Loads architecture hints from one JSON file per section under
 * knowledge/architecture-hints/ and matches them against paths/topics.
 */
final class ArchitectureHints
{
    /**
     * The two categories that describe the TYPO3 backend's own interface.
     *
     * They are named that way because they read as general otherwise: the Sass
     * structure, the `--typo3-*` custom properties and the Lit components are
     * the backend's, and a frontend theme that follows them is following the
     * wrong conventions. Every other category — PHP, Fluid, TypoScript — holds
     * for whatever is written in it.
     */
    public const CATEGORY_CSS = 'Backend CSS';
    public const CATEGORY_TYPESCRIPT = 'Backend TypeScript';

    /** @var array<string, string> */
    private const SECTION_LABELS = [
        'php' => 'PHP',
        'typoscript' => 'TypoScript',
        'fluid' => 'Fluid',
        'typescript' => self::CATEGORY_TYPESCRIPT,
        'javascript' => 'JavaScript',
        'css' => self::CATEGORY_CSS,
        'general' => 'General',
    ];

    /**
     * What a statement or a whole hint is binding for.
     *
     * Absent is the ordinary case and means "wherever TYPO3 is written": an API
     * that throws is an API that throws, in the core and in a sitepackage
     * alike. CORE marks what is only binding for a patch to the core — the
     * backend's own design system, the changelog artifact, the test paths of
     * the mono repository. Outside the core those are not dropped, because a
     * project styling a backend module needs exactly them; they are marked, so
     * a caller can tell an obligation from a convention worth adopting.
     */
    public const BINDING_CORE = 'core';

    /**
     * What a hint is searched by, and what each field is worth.
     *
     * `appliesTo` was long the only one, and the cost of that is what put this
     * here: a hint was reachable through the handful of keywords somebody
     * thought of while writing it, and its own 200 words were invisible. The
     * statements a hint is made of are the part that names the symptom — «the
     * failure is a service-not-found at request time» — and a caller arriving
     * with the symptom is the caller the hint was written for.
     *
     * The curated vocabulary still outweighs the prose, so where somebody
     * anticipated a phrasing, that hint is what comes back.
     *
     * @var array<string, int>
     */
    private const FIELD_WEIGHTS = ['title' => 4, 'appliesTo' => 4, 'text' => 1];

    /**
     * Share of the query a hint's own words have to cover to answer on their
     * own, when no `appliesTo` pattern was matched. Below it the hint mentions
     * the subject rather than being about it.
     */
    private const MIN_COVERAGE = 0.5;

    /**
     * Hint length that still counts for what its terms say.
     *
     * The mean hint body, so the ordinary hint is undamped and the outliers are
     * what this corrects — and they are why it is needed: hint bodies are
     * uncapped and differ by more than twenty times, from thirty words to over
     * a thousand. Without it the longest hint in the corpus answered "file
     * upload storage configuration" at full confidence, because a text that
     * long contains every one of those words somewhere.
     */
    private const UNDILUTED_WORDS = 200;

    /** @return array<int, array{id: string, title: string, appliesTo: array<int, string>, hints: array<int, array{text: string, since: ?int, until: ?int, binding: ?string}>, checks: array<int, string>, category: string, binding: ?string}> */
    public static function load(?int $target = null): array
    {
        $dir = Paths::knowledge() . '/architecture-hints';
        $files = glob($dir . '/*.json') ?: [];
        sort($files);

        $hints = [];
        foreach ($files as $path) {
            $decoded = json_decode((string) file_get_contents($path), true);
            if (!is_array($decoded)) {
                throw new \RuntimeException(sprintf('Invalid architecture-hints/%s', basename($path)));
            }

            $category = self::sectionLabel(substr(basename($path), 0, -strlen('.json')));
            foreach ($decoded as $entry) {
                $hints[] = [
                    'id' => (string) $entry['id'],
                    'title' => (string) $entry['title'],
                    'appliesTo' => array_map('strval', $entry['appliesTo'] ?? []),
                    'hints' => array_map(self::statement(...), $entry['hints'] ?? []),
                    'checks' => array_map('strval', $entry['checks'] ?? []),
                    'category' => $category,
                    'binding' => isset($entry['binding']) ? (string) $entry['binding'] : null,
                ];
            }
        }

        return self::forVersion($hints, $target);
    }

    /**
     * One statement, with the versions it holds for.
     *
     * A bare string is the ordinary case and means "everywhere this knowledge
     * base reaches". Only a statement that stops being true somewhere has to
     * say where, and it says it as data rather than in the sentence — so the
     * sentence stays the same sentence on every version it holds for, and the
     * range can be filtered, rendered and checked.
     *
     * `binding` is the same idea for who a statement obliges rather than which
     * version it holds on, and it sits here for the same reason: one sentence
     * in an otherwise transferable hint is what a changelog obligation is, and
     * splitting the hint to say so would duplicate the six statements around it.
     *
     * @return array{text: string, since: ?int, until: ?int, binding: ?string}
     */
    private static function statement(mixed $entry): array
    {
        if (!is_array($entry)) {
            return ['text' => (string) $entry, 'since' => null, 'until' => null, 'binding' => null];
        }

        return [
            'text' => (string) ($entry['text'] ?? ''),
            'since' => isset($entry['since']) ? (int) $entry['since'] : null,
            'until' => isset($entry['until']) ? (int) $entry['until'] : null,
            'binding' => isset($entry['binding']) ? (string) $entry['binding'] : null,
        ];
    }

    /**
     * The same hints with every statement that does not hold on the target
     * version removed, and a hint that has nothing left removed with them.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array<string, mixed>>
     */
    public static function forVersion(array $hints, ?int $target): array
    {
        if ($target === null) {
            return $hints;
        }

        $kept = [];
        foreach ($hints as $hint) {
            $statements = array_values(array_filter(
                $hint['hints'],
                static fn(array $statement): bool => Versions::holds($statement['since'], $statement['until'], $target),
            ));
            if ($statements === []) {
                continue;
            }
            $hint['hints'] = $statements;
            $kept[] = $hint;
        }

        return $kept;
    }

    /**
     * One hint by its id, for the tools that carry a fixed piece of guidance
     * with their own answer instead of waiting for a matching query.
     *
     * @return array{id: string, title: string, appliesTo: array<int, string>, hints: array<int, array{text: string, since: ?int, until: ?int}>, checks: array<int, string>, category: string}|null
     */
    public static function byId(string $id, ?int $target = null): ?array
    {
        foreach (self::load($target) as $hint) {
            if ($hint['id'] === $id) {
                return $hint;
            }
        }

        return null;
    }

    /**
     * Matches hints against paths and task text, restricted to the domains the
     * input actually touches.
     *
     * The prose sections from the architecture documents are a fallback, not an
     * addition: they are only returned when no structured hint matched, because
     * otherwise they restate the hints and bury them.
     *
     * A hint asked for by id is returned as it is. Matching is a guess about
     * what the caller meant; an id is not, and the index returned on a miss
     * exists so that guessing at phrasings can be replaced by naming one.
     *
     * @param array<int, string> $paths
     * @return array{
     *     matchedHints: array<int, array<string, mixed>>,
     *     knowledgeSections: array<int, array{id: string, title: string, heading: string, body: string, score: int, coverage: float, truncated: bool}>,
     *     domains: array<int, string>,
     *     withheldCategories: array<int, string>,
     *     availableHints: array<int, array{id: string, title: string, category: string}>
     * }
     */
    public static function find(array $paths, string $task, int $limit, ?string $id = null, ?int $target = null): array
    {
        $id = trim((string) $id);
        if ($id !== '') {
            $hint = self::byId($id, $target);

            return [
                'matchedHints' => $hint === null ? [] : [$hint],
                'knowledgeSections' => [],
                // Nothing was inferred from paths or a task, so nothing is
                // claimed about them.
                'domains' => [],
                'withheldCategories' => [],
                'availableHints' => $hint === null ? self::index(null) : [],
            ];
        }

        $task = trim($task);
        $haystack = mb_strtolower(implode("\n", array_merge($paths, [$task])));

        $domains = Domains::detect($paths, $task);
        $categories = Domains::hintCategories($domains);

        // Where the task is about the website, the two backend UI categories
        // are withheld rather than applied. A missing answer sends the caller
        // to the frontend documentation; an inverted one sends them to rewrite
        // a working theme against the backend's tokens.
        $withheld = [];
        if (Domains::namesTheFrontend($paths, $task)) {
            $withheld = array_values(array_intersect(
                $categories,
                [self::CATEGORY_CSS, self::CATEGORY_TYPESCRIPT],
            ));
            $categories = array_values(array_diff($categories, $withheld));
        }

        $candidates = array_values(array_filter(
            self::load($target),
            static fn(array $hint): bool => in_array($hint['category'], $categories, true),
        ));

        // The weights are taken over the candidates rather than over every
        // hint there is, so a term says what it separates within the domains
        // actually being searched.
        $searchable = array_map(self::searchable(...), $candidates);
        $weights = TermSearch::weights(TermSearch::terms($task), $searchable);
        $askedFor = array_sum($weights);

        $scored = [];
        foreach ($candidates as $index => $hint) {
            $keywords = self::scoreKeywords($hint, $haystack);
            [$score, $covered] = TermSearch::score(
                $searchable[$index],
                $weights,
                self::FIELD_WEIGHTS,
                self::UNDILUTED_WORDS,
            );
            $coverage = $askedFor > 0.0 ? $covered / $askedFor : 0.0;

            // Two ways in, and they answer different questions. A matched
            // pattern means somebody anticipated this phrasing — or the caller
            // named a path, which no term match can read. Coverage means the
            // hint is about what was asked whether or not anybody anticipated
            // it.
            if ($keywords === 0 && $coverage < self::MIN_COVERAGE) {
                continue;
            }

            // How it got here travels with it. Nothing in an answer reads it —
            // `bin/hints` does, and a matcher that cannot say why it returned
            // something is one nobody can tune without guessing.
            $hint['matchedOn'] = ['keywords' => $keywords, 'score' => $score];
            $scored[] = ['hint' => $hint, 'keywords' => $keywords, 'score' => $score];
        }

        // Curation first: where a pattern was written for this phrasing, that
        // hint outranks one that merely reads well against it, and the longer
        // pattern is the more specific claim. Everything the matcher answered
        // before this field layout existed therefore still answers the same.
        usort($scored, static function (array $a, array $b): int {
            return $b['keywords'] <=> $a['keywords']
                ?: $b['score'] <=> $a['score']
                ?: strcmp($a['hint']['title'], $b['hint']['title']);
        });

        $matchedHints = array_map(
            static fn(array $entry): array => $entry['hint'],
            array_slice($scored, 0, $limit)
        );

        $knowledgeSections = [];
        if ($matchedHints === [] && $task !== '') {
            $documents = ['typo3-core-architecture'];
            // The CSS architecture document describes the same backend, so it
            // is withheld on the same condition as the hints taken from it.
            if (in_array(Domains::CSS, $domains, true) && !in_array(self::CATEGORY_CSS, $withheld, true)) {
                $documents[] = 'typo3-css-architecture';
            }
            $knowledgeSections = Knowledge::search($task, $documents, $limit);
        }

        return [
            'matchedHints' => $matchedHints,
            'knowledgeSections' => $knowledgeSections,
            'domains' => $domains,
            'withheldCategories' => $withheld,
            // What there would have been to find. A caller that phrased the
            // query differently from the hint has no way to tell that from a
            // subject nobody wrote down, and tries another phrasing either way.
            'availableHints' => $matchedHints === [] ? self::index($categories) : [],
        ];
    }

    /**
     * Every hint there is, by id and title, optionally narrowed to categories.
     *
     * @param array<int, string>|null $categories
     * @return array<int, array{id: string, title: string, category: string}>
     */
    public static function index(?array $categories): array
    {
        $index = [];
        foreach (self::load() as $hint) {
            if ($categories !== null && !in_array($hint['category'], $categories, true)) {
                continue;
            }
            $index[] = ['id' => $hint['id'], 'title' => $hint['title'], 'category' => $hint['category']];
        }

        return $index;
    }

    /**
     * The same hints without their checks.
     *
     * A hint is a convention and holds wherever TYPO3 is written; the checks
     * attached to it are runTests.sh invocations against a script that is part
     * of the core repository. Outside the core the advice therefore stays and
     * the commands go.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array<string, mixed>>
     */
    public static function withoutChecks(array $hints): array
    {
        return array_map(static function (array $hint): array {
            $hint['checks'] = [];

            return $hint;
        }, $hints);
    }

    /**
     * Groups matched hints into sections, preserving each hint's relative order
     * within a section and ordering sections by the known section order.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array{category: string, hints: array<int, array<string, mixed>>}>
     */
    public static function groupByCategory(array $hints): array
    {
        $grouped = [];
        foreach ($hints as $hint) {
            $grouped[$hint['category']][] = $hint;
        }

        $order = array_values(self::SECTION_LABELS);
        $rank = static function (string $category) use ($order): int {
            $index = array_search($category, $order, true);
            return $index === false ? count($order) : $index;
        };

        $sections = [];
        foreach ($grouped as $category => $sectionHints) {
            $sections[] = ['category' => (string) $category, 'hints' => $sectionHints];
        }

        usort($sections, static function (array $a, array $b) use ($rank): int {
            return $rank($a['category']) <=> $rank($b['category'])
                ?: strcmp($a['category'], $b['category']);
        });

        return $sections;
    }

    /**
     * The fields of a hint the matcher reads, keyed the way FIELD_WEIGHTS
     * names them.
     *
     * @param array<string, mixed> $hint
     * @return array<string, string>
     */
    private static function searchable(array $hint): array
    {
        return [
            'title' => (string) $hint['title'],
            'appliesTo' => implode("\n", $hint['appliesTo']),
            'text' => implode("\n", array_column($hint['hints'], 'text')),
        ];
    }

    /**
     * How much of the curated vocabulary the query spells out.
     *
     * This runs the other way round from the term scoring beside it: the
     * pattern is searched in the query, not the query in the hint, because a
     * pattern is a phrase or a path fragment — `Configuration/Services.yaml`,
     * `/Service/` — and what makes it a match is the caller naming it. A longer
     * pattern is the more specific claim and counts for more.
     *
     * @param array{appliesTo: array<int, string>} $hint
     */
    private static function scoreKeywords(array $hint, string $haystack): int
    {
        $score = 0;
        foreach ($hint['appliesTo'] as $pattern) {
            $normalized = mb_strtolower($pattern);
            if (str_contains($haystack, $normalized)) {
                $score += strlen($normalized);
            }
        }

        return $score;
    }

    private static function sectionLabel(string $fileBaseName): string
    {
        return self::SECTION_LABELS[$fileBaseName] ?? ucfirst($fileBaseName);
    }
}
