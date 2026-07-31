<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Searches and reads the official, versioned TYPO3 manuals.
 *
 * docs.typo3.org publishes one complete table of contents at each manual root.
 * That is the public index used here; /search/ is deliberately not called
 * because robots.txt excludes it. The selected result pages are then read for a
 * short excerpt. A canonical result URL can then be handed back to read that
 * page as text, without re-deriving its API from an installed source tree.
 */
final class Documentation
{
    private const HOST = 'https://docs.typo3.org';

    /** @var array<string, string> */
    private const DOCUMENTS = [
        'typo3/reference-coreapi' => 'TYPO3 Explained',
        'typo3/reference-typoscript' => 'TypoScript Explained',
        // The TCA reference is its own manual, and TYPO3 Explained does not
        // repeat it: a question about `inline`, `foreign_field` or a column
        // type was searched in two manuals that describe everything around TCA
        // and never TCA itself, and came back with whatever else carried the
        // word.
        'typo3/reference-tca' => 'TCA Reference',
    ];

    /**
     * What a page is searched by. The title is what it is called; the path is
     * the section it sits in, which is the other half of what a table of
     * contents knows — "Assets" says little, `ApiOverview/Assets/Index.html`
     * says where it belongs. The manual is the third thing a caller names
     * without meaning to name a page: a question about TCA belongs in the TCA
     * reference before it belongs in any page of another manual that carries
     * the word.
     *
     * @var array<string, int>
     */
    private const FIELD_WEIGHTS = ['title' => 4, 'path' => 2, 'manual' => 2];

    /**
     * A title and a path are a handful of words each, so nothing here is long
     * enough to be diluted — except a deep path, which is exactly the candidate
     * that collects accidental terms.
     */
    private const UNDILUTED_WORDS = 12;

    /** @param \Closure(string): ?string|null $fetch */
    public function __construct(private readonly ?\Closure $fetch = null) {}

    /**
     * @param list<string> $queries
     * @return array{
     *   mode: 'search'|'page',
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
     *     excerpt: string,
     *     content: string
     *   }>,
     *   unavailable: array{reason: string}|null
     * }
     */
    public function lookup(array $queries, string $targetVersion, int $limit = 6): array
    {
        $queries = array_values(array_filter(array_map(trim(...), $queries), static fn(string $query): bool => $query !== ''));
        $pages = [];
        $reachable = 0;

        foreach (self::DOCUMENTS as $document => $documentTitle) {
            $base = sprintf('%s/m/%s/%s/en-us/', self::HOST, $document, rawurlencode($targetVersion));
            $html = $this->get($base);
            if ($html === null) {
                continue;
            }
            ++$reachable;

            foreach ($this->links($html, $base) as $link) {
                $pages[$document . '|' . $link['url']] = [
                    'score' => 0,
                    'title' => $link['title'],
                    'url' => $link['url'],
                    'document' => $document,
                    'documentTitle' => $documentTitle,
                    'searchable' => [
                        'title' => self::split($link['title']),
                        'path' => self::split($link['path']),
                        'manual' => $documentTitle,
                    ],
                ];
            }
        }

        if ($reachable === 0) {
            return $this->answer('search', 'unavailable', $queries, $targetVersion, [], [
                'reason' => 'The versioned TYPO3 documentation indexes could not be reached.',
            ]);
        }

        // Every manual is weighed against every other manual's pages, because
        // what makes a term worth something is how few of all the pages there
        // are carry it.
        $searchable = array_column($pages, 'searchable');
        foreach ($queries as $query) {
            $weights = TermSearch::weights(TermSearch::terms(self::split($query)), $searchable);
            $scores = [];
            foreach ($pages as $key => $page) {
                [$scores[$key]] = TermSearch::score(
                    $page['searchable'],
                    $weights,
                    self::FIELD_WEIGHTS,
                    self::UNDILUTED_WORDS,
                );
            }

            // Each query is its own question and its scores are its own scale —
            // one made of common words scores everything higher than one made of
            // rare ones. So a page is worth how well it answers a question
            // rather than what that question's words happen to be worth, and it
            // keeps the best question it answers rather than the sum of the ones
            // it brushes past. Two questions in one call otherwise return one
            // question's pages twice over.
            $best = max([0, ...$scores]);
            foreach ($scores as $key => $score) {
                $pages[$key]['score'] = max($pages[$key]['score'], $best === 0 ? 0 : (int) round($score / $best * 1000));
            }
        }

        $candidates = array_filter($pages, static fn(array $page): bool => $page['score'] > 0);
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
                'content' => '',
            ];
        }

        return $this->answer('search', $results === [] ? 'empty' : 'answered', $queries, $targetVersion, $results, null);
    }

    /**
     * Read one canonical URL returned by lookup(), on the same version.
     *
     * @return array{
     *   mode: 'search'|'page',
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
     *     excerpt: string,
     *     content: string
     *   }>,
     *   unavailable: array{reason: string}|null
     * }
     */
    public function page(string $url, string $targetVersion): array
    {
        $owner = null;
        foreach (self::DOCUMENTS as $document => $documentTitle) {
            $base = sprintf('%s/m/%s/%s/en-us/', self::HOST, $document, rawurlencode($targetVersion));
            if (str_starts_with($url, $base) && str_ends_with(explode('#', $url, 2)[0], '.html')) {
                $owner = ['document' => $document, 'title' => $documentTitle];
                break;
            }
        }
        if ($owner === null) {
            throw new \InvalidArgumentException(
                'page must be a canonical result URL for targetVersion from typo3_documentation_lookup',
            );
        }

        $html = $this->get($url);
        if ($html === null) {
            return $this->answer('page', 'unavailable', [], $targetVersion, [], [
                'reason' => 'The selected TYPO3 documentation page could not be reached.',
            ]);
        }

        $content = $this->content($html);
        $title = $this->title($html);
        if ($content === '') {
            return $this->answer('page', 'empty', [], $targetVersion, [], null);
        }

        return $this->answer('page', 'answered', [], $targetVersion, [[
            'title' => $title,
            'url' => $url,
            'document' => $owner['document'],
            'documentTitle' => $owner['title'],
            'documentVersion' => $targetVersion,
            'section' => $title,
            'excerpt' => substr($content, 0, 700),
            'content' => $content,
        ]], null);
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

    /**
     * The same text with the compound names in it taken apart.
     *
     * Both sides need it, and for the same reason. What is searched is a table
     * of contents — page titles and paths — and no page is titled after the
     * class it documents, while a caller arrives with the words that are in the
     * code: `AssetCollector`, `FunctionalTestCase`, `executeFrontendSubRequest`.
     * Split, those reach the pages that are actually called "Assets" and
     * "Functional tests", and no list of the identifiers there are has to be
     * kept. The candidate side is split for the mirror image of it: a term is
     * matched at a word boundary, and `AfterPageColumnsSelectedForLocalizationEvent`
     * has one word in it until it is taken apart.
     */
    private static function split(string $text): string
    {
        return (string) preg_replace(
            '/(?<=\p{Ll})(?=\p{Lu})|(?<=\p{Lu})(?=\p{Lu}\p{Ll})/u',
            ' ',
            $text,
        );
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

    private function title(string $html): string
    {
        $document = new \DOMDocument();
        if (!@$document->loadHTML($html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
            return '';
        }

        $xpath = new \DOMXPath($document);
        $heading = $xpath->query('//article[@role="main"]//h1[1]')->item(0)
            ?? $xpath->query('//h1[1]')->item(0)
            ?? $xpath->query('//title[1]')->item(0);

        return $heading === null ? '' : self::plain($heading->textContent);
    }

    /**
     * The page body as compact Markdown-like text. Code examples and headings
     * keep their boundaries; navigation outside the main article is omitted.
     */
    private function content(string $html): string
    {
        $document = new \DOMDocument();
        if (!@$document->loadHTML($html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
            return '';
        }

        $xpath = new \DOMXPath($document);
        $article = $xpath->query('//article[@role="main"]')->item(0);
        if (!$article instanceof \DOMElement) {
            return '';
        }

        $blocks = [];
        foreach ($xpath->query('.//h1|.//h2|.//h3|.//h4|.//h5|.//h6|.//p|.//pre|.//li|.//dt', $article) ?: [] as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }
            $nestedList = $xpath->query('ancestor::li', $node);
            if (in_array($node->tagName, ['p', 'li'], true) && $nestedList !== false && $nestedList->length > 0) {
                continue;
            }

            if ($node->tagName === 'pre') {
                $text = trim($node->textContent);
                $block = $text === '' ? '' : "```\n" . $text . "\n```";
            } else {
                $text = self::plain($node->textContent);
                if ($node->tagName === 'dt' && strlen($text) > 300) {
                    $names = $xpath->query(
                        './/*[contains(concat(" ", normalize-space(@class), " "), " sig-name ")]',
                        $node,
                    );
                    $name = $names === false ? null : $names->item(0);
                    $text = $name === null ? '' : self::plain($name->textContent);
                }
                $block = match ($node->tagName) {
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6' => str_repeat('#', (int) substr($node->tagName, 1)) . ' ' . $text,
                    'li' => '- ' . $text,
                    'dt' => $text === '' ? '' : '**' . $text . '**',
                    default => $text,
                };
            }
            if ($block !== '' && end($blocks) !== $block) {
                $blocks[] = $block;
            }
        }

        return implode("\n\n", $blocks);
    }

    private static function plain(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $text));
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
     * @param 'search'|'page' $mode
     * @param list<array{title: string, url: string, document: string, documentTitle: string, documentVersion: string, section: string, excerpt: string, content: string}> $results
     * @param array{reason: string}|null $unavailable
     * @return array{
     *   mode: 'search'|'page',
     *   status: 'answered'|'empty'|'unavailable',
     *   targetVersion: string,
     *   source: string,
     *   queries: list<string>,
     *   results: list<array{title: string, url: string, document: string, documentTitle: string, documentVersion: string, section: string, excerpt: string, content: string}>,
     *   unavailable: array{reason: string}|null
     * }
     */
    private function answer(
        string $mode,
        string $status,
        array $queries,
        string $targetVersion,
        array $results,
        ?array $unavailable,
    ): array {
        return [
            'mode' => $mode,
            'status' => $status,
            'targetVersion' => $targetVersion,
            'source' => self::HOST,
            'queries' => $queries,
            'results' => $results,
            'unavailable' => $unavailable,
        ];
    }
}
