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

    /** Separates the commits in the log the closed notes are read from. */
    private const COMMIT_MARKER = "\x00";

    /**
     * True only when this package is the Composer root package, i.e. a
     * standalone checkout rather than a dependency in someone's vendor/.
     */
    public static function isAvailable(): bool
    {
        if (!class_exists(InstalledVersions::class)) {
            return false;
        }

        return InstalledVersions::getRootPackage()['name'] === self::PACKAGE_NAME;
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
        $tools = self::toolNames($args['tool'] ?? null);
        $query = self::text($args['query'] ?? '');
        $suggestion = self::text($args['suggestion'] ?? '');

        $directory = Paths::feedback();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Cannot create the feedback directory: %s', $directory));
        }

        $file = self::uniquePath($directory, $observation);
        $document = self::render($observation, $category, $tools, $query, $suggestion, self::origin());

        if (file_put_contents($file, $document) === false) {
            throw new \RuntimeException(sprintf('Cannot write the feedback note: %s', $file));
        }

        return 'feedback/' . basename($file);
    }

    /**
     * Reads recorded notes, newest first.
     *
     * @return array<int, array{file: string, date: string, category: string, status: string, tool: string, tools: array<int, string>, title: string, closedBy: ?array{commit: string, date: string, subject: string}}>
     */
    public static function notes(
        ?string $status = 'open',
        ?string $category = null,
        int $limit = 20,
        ?string $tool = null,
    ): array {
        self::assertAvailable();

        $files = glob(Paths::feedback() . '/*.md') ?: [];
        rsort($files); // filenames start with the timestamp, so this is newest first

        $wanted = $tool === null ? null : (self::toolNames($tool)[0] ?? null);

        $notes = [];
        if ($status !== 'closed') {
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
                if ($wanted !== null && !in_array($wanted, $note['tools'], true)) {
                    continue;
                }

                $notes[] = $note;
                if (count($notes) >= $limit) {
                    break;
                }
            }
        }

        // A closed note has no front matter left to filter on, so a query that
        // asks for a category or a tool is answered from the open half alone
        // rather than with entries that match neither.
        if (($status === 'closed' || $status === 'all') && $category === null && $wanted === null) {
            foreach (self::closed($limit) as $note) {
                if (count($notes) >= $limit) {
                    break;
                }
                $notes[] = $note;
            }
        }

        return $notes;
    }

    /**
     * The notes that were worked off, read from the commits that deleted them.
     *
     * A note is closed by deleting the file, and the commit that does it is the
     * record of what came of it. That record is complete and it is also
     * invisible to the agent that wrote the note: from where it stands the file
     * it recorded simply stopped existing, which reads as "lost", and the same
     * gap gets reported again. So the history is read back — the commit, its
     * date, and its subject, which is the sentence that says what happened.
     *
     * Nothing is written and nothing is checked out. Where git is not there or
     * the checkout has no history, there is no closed half and the list says
     * so by being empty.
     *
     * @return array<int, array{file: string, date: string, category: string, status: string, tool: string, tools: array<int, string>, title: string, closedBy: array{commit: string, date: string, subject: string}}>
     */
    private static function closed(int $limit): array
    {
        $root = dirname(Paths::feedback());
        $log = self::git([
            'log',
            '--diff-filter=D',
            '--name-only',
            '--date=short',
            // %x00 and %x1f are git's own escapes: the argument carries them as
            // text, and git writes the separators the output is split on.
            '--format=%x00%h%x1f%ad%x1f%s',
            '--max-count=' . max($limit * 4, 40),
            '--',
            'feedback',
        ], $root);
        if ($log === null) {
            return [];
        }

        $notes = [];
        foreach (explode(self::COMMIT_MARKER, $log) as $block) {
            $lines = preg_split('/\R/', trim($block)) ?: [];
            $header = array_shift($lines);
            if ($header === null || !str_contains($header, "\x1f")) {
                continue;
            }
            [$commit, $date, $subject] = array_pad(explode("\x1f", $header, 3), 3, '');

            foreach ($lines as $path) {
                $path = trim($path);
                if (!str_starts_with($path, 'feedback/') || !str_ends_with($path, '.md')) {
                    continue;
                }
                $notes[] = [
                    'file' => $path,
                    // The filename carries the timestamp the note was recorded
                    // at, which is the half a closed note still has.
                    'date' => substr(basename($path), 0, 10),
                    // The file is gone, and with it the front matter. What a
                    // closed note still has is its name, its date and the
                    // commit that closed it.
                    'category' => '',
                    'status' => 'closed',
                    'tool' => '',
                    'tools' => [],
                    'title' => self::titleFromFileName($path),
                    'closedBy' => ['commit' => $commit, 'date' => $date, 'subject' => $subject],
                ];
                if (count($notes) >= $limit) {
                    return $notes;
                }
            }
        }

        return $notes;
    }

    /**
     * Runs one git command in this checkout and returns its output, or null
     * where git could not answer.
     *
     * @param array<int, string> $command
     */
    private static function git(array $command, string $workingDirectory): ?string
    {
        if (!is_dir($workingDirectory . '/.git')) {
            return null;
        }

        // stdin is closed for the child rather than inherited, for the reason
        // Typo3Cli::execute() states: this runs while the server is answering a
        // client, and the client's next request is sitting in that stdin.
        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = @proc_open(array_merge(['git'], $command), $descriptors, $pipes, $workingDirectory, null);
        if (!is_resource($process)) {
            return null;
        }

        fclose($pipes[0]);
        $output = (string) stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process) === 0 ? $output : null;
    }

    /** The slug a note filename carries, read back as words. */
    private static function titleFromFileName(string $path): string
    {
        $slug = (string) preg_replace('/^\d{4}-\d{2}-\d{2}-\d{6}-?/', '', basename($path, '.md'));

        return $slug === '' ? basename($path) : str_replace('-', ' ', $slug);
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
     * @return array{file: string, date: string, category: string, status: string, tool: string, tools: array<int, string>, title: string, closedBy: null}|null
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

        $tools = self::toolNames($meta['tool'] ?? '');

        return [
            'file' => 'feedback/' . basename($file),
            'date' => $meta['date'] ?? '',
            'category' => $meta['category'] ?? 'idea',
            'status' => $meta['status'] ?? 'open',
            // Both: the string is what a note has always carried, the list is
            // what a caller can filter or group by without parsing it back.
            'tool' => implode(', ', $tools),
            'tools' => $tools,
            'title' => $title,
            // An open note has nothing closing it yet, and the field is present
            // either way so a caller never has to ask which half it is holding.
            'closedBy' => null,
        ];
    }

    /**
     * Where the note was written from: the working directory of the session
     * that left it.
     *
     * A note is read long after the session that produced it has ended, and
     * "the icon lookup returned nothing" means something different depending on
     * which project it was asked in. The directory is the one thing that says
     * which — it is how the note gets checked against the installation it came
     * from instead of against whatever is at hand.
     *
     * Only ever the directory an entrypoint handed to Instance, so a note left
     * through an endpoint that has no caller directory simply carries none
     * rather than that endpoint's own.
     */
    private static function origin(): string
    {
        $startedFrom = Instance::startedFrom();

        return $startedFrom === null ? '' : $startedFrom;
    }

    /**
     * @param array<int, string> $tools
     */
    private static function render(
        string $observation,
        string $category,
        array $tools,
        string $query,
        string $suggestion,
        string $origin,
    ): string {
        $frontMatter = [
            'date: ' . date('c'),
            'category: ' . $category,
            'status: open',
        ];
        if ($tools !== []) {
            $frontMatter[] = 'tool: ' . implode(', ', $tools);
        }
        if ($origin !== '') {
            $frontMatter[] = 'directory: ' . $origin;
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

    /**
     * The tools a note is about.
     *
     * An observation is regularly about several tools at once — the four that
     * go quiet together when the console cannot be reached, say. Stripping
     * everything but [a-z0-9_] from one string ran their names together into
     * one unsearchable word, which is what a backlog is least able to afford:
     * the obvious thing to want from the list is every note about one tool.
     *
     * A list is accepted as a list, and a string is split on what separates
     * names in one — a comma or a space — rather than having the separator
     * deleted.
     *
     * @return array<int, string>
     */
    private static function toolNames(mixed $value): array
    {
        $candidates = is_array($value)
            ? $value
            : (preg_split('/[\s,;]+/', is_string($value) ? $value : '') ?: []);

        $names = [];
        foreach ($candidates as $candidate) {
            if (!is_string($candidate)) {
                continue;
            }
            $name = (string) preg_replace('/[^a-z0-9_]/', '', strtolower(trim($candidate)));
            if ($name !== '') {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }
}
