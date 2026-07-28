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
     * Registered translation domains (one per default XLF file in scope), each
     * with its derived domain name and the file it resolves to.
     *
     * @return array<int, array{ext: string, file: string, ref: string, domain: string, count: int}>
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
            $domains[] = $domain + [
                'domain' => TranslationDomain::fromReference($domain['ref']) ?? $domain['ref'],
                'count' => $counts[$index] ?? 0,
            ];
        }

        $terms = self::terms(trim($query ?? ''));
        if ($terms !== []) {
            $domains = array_values(array_filter($domains, static function (array $domain) use ($terms): bool {
                $haystack = mb_strtolower($domain['ext'] . ' ' . $domain['file'] . ' ' . $domain['ref'] . ' ' . $domain['domain']);
                foreach ($terms as $term) {
                    if (str_contains($haystack, $term)) {
                        return true;
                    }
                }
                return false;
            }));
        }

        usort($domains, static fn(array $a, array $b): int => strcmp($a['domain'], $b['domain']));

        return $domains;
    }

    /**
     * Ranks labels against a free-text query, matching the trans-unit id and the
     * English source text.
     *
     * The primary reference is the translation domain form
     * ("backend.alt_doc:buttons.confirm.save_and_close"); the LLL file path is
     * carried along as the legacy form.
     *
     * @return array<int, array{id: string, source: string, ref: string, legacyRef: string, unusedSince: ?string, matchedIn: array<int, string>, coverage: float}>
     */
    public static function find(?string $query, bool $requireAllTerms = true): array
    {
        $data = self::data();
        $terms = self::terms(trim($query ?? ''));
        if ($terms === []) {
            return [];
        }

        $scored = [];
        foreach ($data['labels'] as $label) {
            [$score, $matched, $matchedIn] = self::scoreLabel($label['id'], $label['source'], $terms);
            if ($matched === 0 || ($requireAllTerms && $matched < count($terms))) {
                continue;
            }
            $domain = $data['domains'][$label['d']] ?? null;
            $legacyRef = $domain !== null ? $domain['ref'] . ':' . $label['id'] : $label['id'];
            $domainName = $domain !== null ? TranslationDomain::fromReference($domain['ref']) : null;

            $scored[] = [
                'label' => [
                    'id' => $label['id'],
                    'source' => $label['source'],
                    'ref' => $domainName !== null ? $domainName . ':' . $label['id'] : $legacyRef,
                    'legacyRef' => $legacyRef,
                    'unusedSince' => $label['unusedSince'] ?? null,
                    'matchedIn' => $matchedIn,
                    'coverage' => round($matched / count($terms), 3),
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
     * Returns [weightedScore, distinctTermsMatched, whereTheyMatched].
     *
     * Matches in the key id weigh more than matches in the source text; an exact
     * id segment scores highest.
     *
     * @param array<int, string> $terms
     * @return array{0: int, 1: int, 2: array<int, string>}
     */
    private static function scoreLabel(string $id, string $source, array $terms): array
    {
        $idLower = mb_strtolower($id);
        $idSegments = preg_split('/[._-]+/', $idLower) ?: [];
        $sourceLower = mb_strtolower($source);

        $score = 0;
        $matched = 0;
        $matchedIn = [];
        foreach ($terms as $term) {
            if (in_array($term, $idSegments, true)) {
                $score += 5;
                ++$matched;
                $matchedIn['key'] = true;
            } elseif (str_contains($idLower, $term)) {
                $score += 3;
                ++$matched;
                $matchedIn['key'] = true;
            } elseif (str_contains($sourceLower, $term)) {
                $score += 1;
                ++$matched;
                $matchedIn['text'] = true;
            }
        }

        return [$score, $matched, array_keys($matchedIn)];
    }
}
