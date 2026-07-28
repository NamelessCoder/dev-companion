<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Catalog;

use Typo3CmsMcp\Paths;

/**
 * Loads and searches the curated TYPO3 core label (XLF) catalog from
 * catalog/labels.json. Scope is the core sysexts a contributor typically
 * touches. Each label carries its trans-unit id, English source text, the
 * fully-qualified LLL reference, and an optional x-unused-since marker.
 *
 * Lets contributors reuse an existing label and reference it correctly instead
 * of inventing a new key or guessing the EXT: path.
 */
final class Labels
{
    /**
     * @return array{
     *     domains: array<int, array{ext: string, file: string, ref: string}>,
     *     labels: array<int, array{d: int, id: string, source: string, unusedSince?: string}>
     * }
     */
    private static function data(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('labels.json')), true);
        if (!is_array($decoded) || !isset($decoded['domains'], $decoded['labels'])) {
            throw new \RuntimeException('Invalid catalog/labels.json');
        }

        /** @var array{domains: array<int, array{ext: string, file: string, ref: string}>, labels: array<int, array{d: int, id: string, source: string, unusedSince?: string}>} $decoded */
        return $decoded;
    }

    /**
     * Registered translation domains (one per default XLF file in scope).
     *
     * @return array<int, array{ext: string, file: string, ref: string, count: int}>
     */
    public static function domains(?string $query): array
    {
        $data = self::data();

        $counts = [];
        foreach ($data['labels'] as $label) {
            $counts[$label['d']] = ($counts[$label['d']] ?? 0) + 1;
        }

        $domains = [];
        foreach ($data['domains'] as $index => $domain) {
            $domains[] = $domain + ['count' => $counts[$index] ?? 0];
        }

        $terms = self::terms(trim($query ?? ''));
        if ($terms !== []) {
            $domains = array_values(array_filter($domains, static function (array $domain) use ($terms): bool {
                $haystack = mb_strtolower($domain['ext'] . ' ' . $domain['file'] . ' ' . $domain['ref']);
                foreach ($terms as $term) {
                    if (str_contains($haystack, $term)) {
                        return true;
                    }
                }
                return false;
            }));
        }

        usort($domains, static fn(array $a, array $b): int => strcmp($a['ref'], $b['ref']));

        return $domains;
    }

    /**
     * Ranks labels against a free-text query, matching the trans-unit id and the
     * English source text. Returns fully-qualified LLL references.
     *
     * @return array<int, array{id: string, source: string, ref: string, unusedSince: ?string}>
     */
    public static function find(?string $query): array
    {
        $data = self::data();
        $terms = self::terms(trim($query ?? ''));
        if ($terms === []) {
            return [];
        }

        $scored = [];
        foreach ($data['labels'] as $label) {
            $score = self::scoreLabel($label['id'], $label['source'], $terms);
            if ($score === 0) {
                continue;
            }
            $domain = $data['domains'][$label['d']] ?? null;
            $ref = $domain !== null ? $domain['ref'] . ':' . $label['id'] : $label['id'];
            $scored[] = [
                'label' => [
                    'id' => $label['id'],
                    'source' => $label['source'],
                    'ref' => $ref,
                    'unusedSince' => $label['unusedSince'] ?? null,
                ],
                'score' => $score,
            ];
        }

        usort($scored, static function (array $a, array $b): int {
            return $b['score'] <=> $a['score']
                ?: strcmp($a['label']['ref'], $b['label']['ref']);
        });

        return array_map(static fn(array $entry): array => $entry['label'], $scored);
    }

    /** @return array<int, string> */
    private static function terms(string $query): array
    {
        $terms = [];
        foreach (preg_split('/[\s._-]+/', mb_strtolower($query)) ?: [] as $term) {
            $term = preg_replace('/[^a-z0-9]/', '', $term) ?? '';
            if ($term !== '' && strlen($term) >= 2) {
                $terms[] = $term;
            }
        }

        return array_values(array_unique($terms));
    }

    /**
     * Matches in the key id weigh more than matches in the source text; an exact
     * id segment scores highest.
     *
     * @param array<int, string> $terms
     */
    private static function scoreLabel(string $id, string $source, array $terms): int
    {
        $idLower = mb_strtolower($id);
        $idSegments = preg_split('/[._-]+/', $idLower) ?: [];
        $sourceLower = mb_strtolower($source);

        $score = 0;
        foreach ($terms as $term) {
            if (in_array($term, $idSegments, true)) {
                $score += 5;
            } elseif (str_contains($idLower, $term)) {
                $score += 3;
            } elseif (str_contains($sourceLower, $term)) {
                $score += 1;
            }
        }

        return $score;
    }
}
