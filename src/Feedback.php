<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Composer\InstalledVersions;

/**
 * Stores improvement notes left by agents using this server, so gaps in the
 * knowledge base can be worked off later.
 *
 * Writing is deliberately limited to a standalone checkout. Installed as a
 * dependency the package lives in vendor/, where anything written is lost on
 * the next composer install — so the feedback tools are not offered at all in
 * that mode and the server stays strictly read-only.
 *
 * One note per file: concurrent agents never touch the same file, so no
 * read-modify-write races and no merge conflicts on a shared log.
 */
final class Feedback
{
    public const PACKAGE_NAME = 'typo3/cms-mcp';

    public const CATEGORIES = ['missing-knowledge', 'wrong-answer', 'tool-gap', 'bug', 'idea'];

    private const MAX_FIELD_LENGTH = 4000;
    private const MAX_SLUG_LENGTH = 48;

    /**
     * True only when this package is the Composer root package, i.e. a
     * standalone checkout rather than a dependency in someone's vendor/.
     */
    public static function isAvailable(): bool
    {
        if (!class_exists(InstalledVersions::class)) {
            return false;
        }

        return (InstalledVersions::getRootPackage()['name'] ?? null) === self::PACKAGE_NAME;
    }

    /**
     * Records one note and returns the path it was written to, relative to the
     * project root.
     *
     * @param array<string, mixed> $args
     */
    public static function record(array $args): string
    {
        self::assertAvailable();

        $observation = self::text($args['observation'] ?? '');
        if ($observation === '') {
            throw new \InvalidArgumentException('An observation is required.');
        }

        $category = self::category($args['category'] ?? null);
        $tool = self::toolName($args['tool'] ?? null);
        $query = self::text($args['query'] ?? '');
        $suggestion = self::text($args['suggestion'] ?? '');

        $directory = Paths::feedback();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Cannot create the feedback directory: %s', $directory));
        }

        $file = self::uniquePath($directory, $observation);
        $document = self::render($observation, $category, $tool, $query, $suggestion);

        if (file_put_contents($file, $document) === false) {
            throw new \RuntimeException(sprintf('Cannot write the feedback note: %s', $file));
        }

        return 'feedback/' . basename($file);
    }

    /**
     * Reads recorded notes, newest first.
     *
     * @return array<int, array{file: string, date: string, category: string, status: string, tool: string, title: string}>
     */
    public static function notes(?string $status = 'open', ?string $category = null, int $limit = 20): array
    {
        self::assertAvailable();

        $files = glob(Paths::feedback() . '/*.md') ?: [];
        rsort($files); // filenames start with the timestamp, so this is newest first

        $notes = [];
        foreach ($files as $file) {
            $note = self::parse($file);
            if ($note === null) {
                continue;
            }
            if ($status !== null && $status !== 'all' && $note['status'] !== $status) {
                continue;
            }
            if ($category !== null && $note['category'] !== $category) {
                continue;
            }

            $notes[] = $note;
            if (count($notes) >= $limit) {
                break;
            }
        }

        return $notes;
    }

    private static function assertAvailable(): void
    {
        if (!self::isAvailable()) {
            throw new \RuntimeException(
                'Feedback is only available when the server runs from a standalone checkout, '
                . 'not when it is installed as a Composer dependency.',
            );
        }
    }

    /**
     * @return array{file: string, date: string, category: string, status: string, tool: string, title: string}|null
     */
    private static function parse(string $file): ?array
    {
        $contents = file_get_contents($file);
        if ($contents === false) {
            return null;
        }

        $meta = [];
        if (preg_match('/^---\R(.*?)\R---\R/s', $contents, $matches) === 1) {
            foreach (preg_split('/\R/', $matches[1]) ?: [] as $line) {
                if (preg_match('/^([a-z]+):\s*(.*)$/', trim($line), $pair) === 1) {
                    $meta[$pair[1]] = trim($pair[2]);
                }
            }
        }

        // The first heading is the note's title.
        $title = '';
        if (preg_match('/^# (.+)$/m', $contents, $heading) === 1) {
            $title = trim($heading[1]);
        }

        return [
            'file' => 'feedback/' . basename($file),
            'date' => $meta['date'] ?? '',
            'category' => $meta['category'] ?? 'idea',
            'status' => $meta['status'] ?? 'open',
            'tool' => $meta['tool'] ?? '',
            'title' => $title,
        ];
    }

    private static function render(
        string $observation,
        string $category,
        string $tool,
        string $query,
        string $suggestion,
    ): string {
        $frontMatter = [
            'date: ' . date('c'),
            'category: ' . $category,
            'status: open',
        ];
        if ($tool !== '') {
            $frontMatter[] = 'tool: ' . $tool;
        }

        $document = "---\n" . implode("\n", $frontMatter) . "\n---\n\n";
        $document .= '# ' . self::title($observation) . "\n\n";
        $document .= "## Observation\n\n" . $observation . "\n";

        if ($query !== '') {
            $document .= "\n## Query\n\n" . $query . "\n";
        }
        if ($suggestion !== '') {
            $document .= "\n## Suggestion\n\n" . $suggestion . "\n";
        }

        return $document;
    }

    /**
     * Builds the filename from a timestamp plus a slug of the observation. The
     * agent never supplies the name, so it cannot escape the directory.
     */
    private static function uniquePath(string $directory, string $observation): string
    {
        $slug = self::slug($observation);
        $base = date('Y-m-d-His') . ($slug === '' ? '' : '-' . $slug);

        $file = $directory . '/' . $base . '.md';
        $counter = 2;
        while (file_exists($file)) {
            $file = $directory . '/' . $base . '-' . $counter . '.md';
            ++$counter;
        }

        return $file;
    }

    private static function slug(string $text): string
    {
        $slug = strtolower($text);
        $slug = (string) preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        if (strlen($slug) <= self::MAX_SLUG_LENGTH) {
            return $slug;
        }

        // Cut on a word boundary so the filename stays readable.
        $slug = substr($slug, 0, self::MAX_SLUG_LENGTH);
        $lastDash = strrpos($slug, '-');

        return $lastDash === false ? $slug : substr($slug, 0, $lastDash);
    }

    private static function title(string $observation): string
    {
        $firstLine = trim((string) strtok($observation, "\n"));

        return strlen($firstLine) > 100 ? substr($firstLine, 0, 97) . '...' : $firstLine;
    }

    private static function text(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        $text = trim($value);

        return strlen($text) > self::MAX_FIELD_LENGTH ? substr($text, 0, self::MAX_FIELD_LENGTH) : $text;
    }

    private static function category(mixed $value): string
    {
        return is_string($value) && in_array($value, self::CATEGORIES, true) ? $value : 'idea';
    }

    private static function toolName(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        return (string) preg_replace('/[^a-z0-9_]/', '', strtolower(trim($value)));
    }
}
