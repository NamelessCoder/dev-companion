<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Searches the table of contents of the official, versioned TYPO3 manuals.
 *
 * docs.typo3.org publishes one complete table of contents at each manual root.
 * That is the public index used here; /search/ is deliberately not called
 * because robots.txt excludes it. The selected result pages are then read for a
 * short excerpt, so the answer remains a route into the canonical source rather
 * than a second copy of it.
 */
final class Documentation
{
    private const HOST = 'https://docs.typo3.org';

    /** @var array<string, string> */
    private const DOCUMENTS = [
        'typo3/reference-coreapi' => 'TYPO3 Explained',
        'typo3/reference-typoscript' => 'TypoScript Explained',
    ];

    /** @param \Closure(string): ?string|null $fetch */
    public function __construct(private readonly ?\Closure $fetch = null)
    {
    }

    /**
     * @param list<string> $queries
     * @return array{
     *   status: 'answered'|'empty'|'unavailable',
     *   targetVersion: string,
     *   source: string,
     *   queries: list<string>,
     *   results: list<array{
     *     title: string,
     *     url: string,
     *     document: string,
     *     documentTitle: string,
     *     documentVersion: string,
     *     section: string,
     *     excerpt: string
     *   }>,
     *   unavailable: array{reason: string}|null
     * }
     */
    public function lookup(array $queries, string $targetVersion, int $limit = 6): array
    {
        $queries = array_values(array_filter(array_map(trim(...), $queries), static fn(string $query): bool => $query !== ''));
        $candidates = [];
        $reachable = 0;

        foreach (self::DOCUMENTS as $document => $documentTitle) {
            $base = sprintf('%s/m/%s/%s/en-us/', self::HOST, $document, rawurlencode($targetVersion));
            $html = $this->get($base);
            if ($html === null) {
                continue;
            }
            ++$reachable;

            foreach ($this->links($html, $base) as $link) {
                $score = $this->score($queries, $link['title'] . ' ' . $link['path']);
                if ($score === 0) {
                    continue;
                }
                $key = $document . '|' . $link['url'];
                if (!isset($candidates[$key]) || $score > $candidates[$key]['score']) {
                    $candidates[$key] = [
                        'score' => $score,
                        'title' => $link['title'],
                        'url' => $link['url'],
                        'document' => $document,
                        'documentTitle' => $documentTitle,
                    ];
                }
            }
        }

        if ($reachable === 0) {
            return $this->answer('unavailable', $queries, $targetVersion, [], [
                'reason' => 'The versioned TYPO3 documentation indexes could not be reached.',
            ]);
        }

        uasort($candidates, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
        $results = [];
        foreach (array_slice($candidates, 0, $limit) as $candidate) {
            $page = $this->get($candidate['url']);
            $results[] = [
                'title' => $candidate['title'],
                'url' => $candidate['url'],
                'document' => $candidate['document'],
                'documentTitle' => $candidate['documentTitle'],
                'documentVersion' => $targetVersion,
                'section' => $candidate['title'],
                'excerpt' => $page === null ? '' : $this->excerpt($page),
            ];
        }

        return $this->answer($results === [] ? 'empty' : 'answered', $queries, $targetVersion, $results, null);
    }

    /**
     * @return list<array{title: string, path: string, url: string}>
     */
    private function links(string $html, string $base): array
    {
        $document = new \DOMDocument();
        if (!@$document->loadHTML($html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
            return [];
        }

        $links = [];
        $seen = [];
        foreach ((new \DOMXPath($document))->query('//a[@href]') ?: [] as $anchor) {
            if (!$anchor instanceof \DOMElement) {
                continue;
            }
            $href = trim($anchor->getAttribute('href'));
            $title = trim((string) preg_replace('/\s+/u', ' ', $anchor->textContent));
            if ($title === '' || !$this->isDocumentPage($href)) {
                continue;
            }

            $path = explode('#', $href, 2)[0];
            $url = str_starts_with($path, 'https://') ? $path : $base . ltrim($path, '/');
            if (isset($seen[$url])) {
                continue;
            }
            $seen[$url] = true;
            $links[] = ['title' => $title, 'path' => $path, 'url' => $url];
        }

        return $links;
    }

    private function isDocumentPage(string $href): bool
    {
        return $href !== ''
            && !str_starts_with($href, '#')
            && !str_starts_with($href, '../')
            && !str_starts_with($href, '_')
            && !str_starts_with($href, 'https://')
            && str_ends_with(explode('#', $href, 2)[0], '.html');
    }

    /** @param list<string> $queries */
    private function score(array $queries, string $candidate): int
    {
        $candidate = strtolower($candidate);
        $score = 0;
        foreach ($queries as $query) {
            $query = strtolower($query);
            $queryScore = str_contains($candidate, $query) ? 20 : 0;
            $words = array_values(array_unique(preg_split('/[^\pL\pN]+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: []));
            foreach ($words as $word) {
                if (strlen($word) >= 3 && str_contains($candidate, $word)) {
                    $queryScore += 3;
                }
            }
            $score += $queryScore;
        }

        return $score;
    }

    private function excerpt(string $html): string
    {
        $document = new \DOMDocument();
        if (!@$document->loadHTML($html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
            return '';
        }

        $xpath = new \DOMXPath($document);
        $parts = [];
        foreach ($xpath->query('//article[@role="main"]//p') ?: [] as $node) {
            $text = trim((string) preg_replace('/\s+/u', ' ', $node->textContent));
            if ($text === '') {
                continue;
            }
            $parts[] = $text;
            if (strlen(implode(' ', $parts)) >= 500) {
                break;
            }
        }

        return substr(implode(' ', $parts), 0, 700);
    }

    private function get(string $url): ?string
    {
        if ($this->fetch !== null) {
            return ($this->fetch)($url);
        }

        $handle = curl_init($url);
        if ($handle === false) {
            return null;
        }
        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_USERAGENT => 'typo3-cms-mcp/' . ServerFactory::SERVER_VERSION,
        ]);
        $body = curl_exec($handle);
        $status = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);

        return is_string($body) && $status >= 200 && $status < 300 ? $body : null;
    }

    /**
     * @param 'answered'|'empty'|'unavailable' $status
     * @param list<string> $queries
     * @param list<array{title: string, url: string, document: string, documentTitle: string, documentVersion: string, section: string, excerpt: string}> $results
     * @param array{reason: string}|null $unavailable
     * @return array{
     *   status: 'answered'|'empty'|'unavailable',
     *   targetVersion: string,
     *   source: string,
     *   queries: list<string>,
     *   results: list<array{title: string, url: string, document: string, documentTitle: string, documentVersion: string, section: string, excerpt: string}>,
     *   unavailable: array{reason: string}|null
     * }
     */
    private function answer(
        string $status,
        array $queries,
        string $targetVersion,
        array $results,
        ?array $unavailable,
    ): array {
        return [
            'status' => $status,
            'targetVersion' => $targetVersion,
            'source' => self::HOST,
            'queries' => $queries,
            'results' => $results,
            'unavailable' => $unavailable,
        ];
    }
}
