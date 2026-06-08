<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Reads and searches the bundled markdown knowledge documents.
 */
final class Knowledge
{
    /** @return array<int, array{id: string, title: string, path: string}> */
    public static function documents(): array
    {
        $dir = Paths::knowledge();
        $documents = [];

        foreach (glob($dir . '/*.md') ?: [] as $path) {
            $content = (string) file_get_contents($path);
            $fileName = basename($path);
            $documents[] = [
                'id' => substr($fileName, 0, -strlen('.md')),
                'title' => self::readTitle($content) ?? $fileName,
                'path' => $path,
            ];
        }

        usort($documents, static fn(array $a, array $b): int => strcmp($a['id'], $b['id']));

        return $documents;
    }

    public static function read(string $id): string
    {
        foreach (self::documents() as $document) {
            if ($document['id'] === $id) {
                return (string) file_get_contents($document['path']);
            }
        }

        throw new \RuntimeException(sprintf('Unknown knowledge document: %s', $id));
    }

    /**
     * Term-scored paragraph search across all markdown documents.
     *
     * @return array<int, array{id: string, title: string, excerpts: array<int, string>}>
     */
    public static function search(string $query): array
    {
        $terms = self::terms($query);
        if ($terms === []) {
            return [];
        }

        $results = [];
        foreach (self::documents() as $document) {
            $content = (string) file_get_contents($document['path']);
            $scored = [];

            foreach (preg_split('/\n{2,}/', $content) ?: [] as $paragraph) {
                $paragraph = trim($paragraph);
                if ($paragraph === '') {
                    continue;
                }
                $score = self::scoreParagraph($paragraph, $terms);
                if ($score > 0) {
                    $scored[] = ['paragraph' => $paragraph, 'score' => $score];
                }
            }

            if ($scored === []) {
                continue;
            }

            usort($scored, static fn(array $a, array $b): int => $b['score'] <=> $a['score']);

            $results[] = [
                'id' => $document['id'],
                'title' => $document['title'],
                'excerpts' => array_map(
                    static fn(array $entry): string => $entry['paragraph'],
                    array_slice($scored, 0, 5)
                ),
            ];
        }

        return $results;
    }

    /** @return array<int, string> */
    private static function terms(string $query): array
    {
        $normalized = trim(mb_strtolower($query));
        if ($normalized === '') {
            return [];
        }

        return array_values(array_filter(preg_split('/\s+/', $normalized) ?: []));
    }

    /** @param array<int, string> $terms */
    private static function scoreParagraph(string $paragraph, array $terms): int
    {
        $haystack = mb_strtolower($paragraph);
        $score = 0;
        foreach ($terms as $term) {
            if (str_contains($haystack, $term)) {
                $score++;
            }
        }

        return $score;
    }

    private static function readTitle(string $content): ?string
    {
        foreach (preg_split('/\n/', $content) ?: [] as $line) {
            $line = trim($line);
            if (str_starts_with($line, '# ')) {
                return trim(preg_replace('/^#\s+/', '', $line) ?? $line);
            }
        }

        return null;
    }
}
