<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Feedback;

use Composer\InstalledVersions;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Paths;

/**
 * Stores improvement feedback left by agents using this server, so gaps in the
 * knowledge base can be worked off later.
 *
 * What this writes into is this repository, never the TYPO3 installation the
 * server was reading — those are the two kinds of writing D-FBK-042 keeps
 * apart, and "read-only" throughout this repository is about the second. The
 * server's posture is untouched by anything below here.
 *
 * A development tool for building this server rather than part of using it.
 * isAvailable() is what enforces that: the tools exist only where this package
 * is the Composer root package, so whoever can reach them owns the checkout
 * they write into. Installed as a dependency the package lives in vendor/,
 * where anything written is lost on the next composer install, and the two
 * tools are not offered at all. TYPO3_DEV_COMPANION_EXCLUDE_TOOLS does not reach them
 * either — R-SCO-009.
 *
 * One feedback per file: concurrent agents never touch the same file, so no
 * read-modify-write races and no merge conflicts on a shared log.
 *
 * What a session pasted and should not have — a key, a password, a token read
 * out of the installation it was standing in — is taken out on the way in and
 * replaced by a marker, because the file this writes is committed and pushed.
 * Redaction says what counts as one and why each threshold is where it is.
 *
 * A feedback that was worked off moves to feedback/archive/ rather than being
 * deleted. What a session reported about this server — which skill it reached
 * for, what it had to establish elsewhere, what the answer cost it — is
 * evidence about this server that nothing else in the repository holds, and it
 * stays worth reading long after the gap it named was closed.
 */
final class Channel
{
    public const PACKAGE_NAME = 'typo3/dev-companion';

    public const CATEGORIES = ['missing-knowledge', 'wrong-answer', 'tool-gap', 'bug', 'idea'];

    private const MAX_FIELD_LENGTH = 4000;
    private const MAX_SLUG_LENGTH = 48;
    private const MAX_MODEL_LENGTH = 80;

    /** What a feedback says about the model where none was named. */
    public const UNATTRIBUTED = 'unknown';

    /**
     * How a field cut for length says so, the way Redaction marks a value it
     * took out of one.
     */
    private const CUT_MARKER = '[cut: %s]';

    /** Separates the commits in the log a feedback's own answer is read from. */
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
     * Records one feedback and returns the path it was written to, relative to the
     * project root.
     *
     * What a session pasted and should not have is taken out on the way in, and
     * `$redacted` is what was taken: the caller is told, because a report that
     * was altered says so or it is not a report. See Redaction for what counts
     * and why the thresholds are where they are.
     *
     * A field too long to be stored whole is altered as well, and `$cut` is
     * what went from it — reported for the same reason and beside it.
     *
     * @param array<string, mixed> $args
     * @param array<int, string>   $redacted what was removed, by field and shape
     * @param array<int, string>   $cut      what was cut for length, by field and how much
     */
    public static function record(array $args, array &$redacted = [], array &$cut = []): string
    {
        self::assertAvailable();

        $redacted = [];
        $cut = [];
        self::assertNoCallFrame('observation', $args['observation'] ?? null);
        self::assertNoCallFrame('query', $args['query'] ?? null);
        self::assertNoCallFrame('suggestion', $args['suggestion'] ?? null);
        $observation = self::text('observation', $args['observation'] ?? '', $cut);
        if ($observation === '') {
            throw new \InvalidArgumentException('An observation is required.');
        }

        $category = self::category($args['category'] ?? null);
        $tools = self::toolNames($args['tool'] ?? null);
        $observation = self::withoutSecrets('observation', $observation, $redacted);
        $query = self::withoutSecrets('query', self::text('query', $args['query'] ?? '', $cut), $redacted);
        // The third field for the same reason as the other two, though neither
        // the leak this was written for nor the todo that carried it named it:
        // it is prose a session writes and it is written into the same file, so
        // leaving it out would be a hole with nothing behind it.
        $suggestion = self::withoutSecrets('suggestion', self::text('suggestion', $args['suggestion'] ?? '', $cut), $redacted);
        $model = self::model($args['model'] ?? null);

        $directory = Paths::feedback();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Cannot create the feedback directory: %s', $directory));
        }

        // The one field that is a name rather than a report, so it is redacted
        // like the rest and never cut: a title with a marker in the middle of
        // it is what a listing shows.
        $subject = self::withoutSecrets('subject', trim((string) ($args['subject'] ?? '')), $redacted);

        $file = self::uniquePath($directory, $subject === '' ? $observation : $subject);
        $title = self::title($subject === '' ? $observation : $subject);
        $document = self::render($observation, $category, $tools, $query, $suggestion, $model, self::origin(), $title);

        if (file_put_contents($file, $document) === false) {
            throw new \RuntimeException(sprintf('Cannot write the feedback feedback: %s', $file));
        }

        $path = 'feedback/' . basename($file);
        // The card the report brings with it, written here rather than by the
        // commit that brings the feedback in: a session that records one and
        // goes on working leaves the board right, and nothing has to be run
        // afterwards for it to be — `D-FBK-045`.
        Card::write($path, $title);

        return $path;
    }

    /**
     * Moves a feedback that was worked off into the archive, and returns the path
     * it now has, relative to the project root.
     *
     * Where a feedback stands is what says whether it was answered, so this is the
     * whole of closing one: the open directory holds the questions, the archive
     * holds the ones that have an answer. The feedback itself is not rewritten
     * beyond saying so — it is a session's report about this server, and the
     * report is what makes the answer readable later.
     */
    public static function archive(string $feedback): string
    {
        self::assertAvailable();

        $name = basename(trim($feedback));
        $source = Paths::feedback() . '/' . $name;
        if ($name === '' || !str_ends_with($name, '.md') || !is_file($source)) {
            throw new \InvalidArgumentException(sprintf('There is no open feedback named %s.', $name));
        }

        $directory = Paths::feedbackArchive();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Cannot create the archive directory: %s', $directory));
        }

        $target = $directory . '/' . $name;
        if (file_exists($target)) {
            throw new \RuntimeException(sprintf('%s is already archived.', $name));
        }

        $contents = (string) preg_replace(
            '/^status: .*$/m',
            "status: closed\nclosed: " . date('Y-m-d'),
            (string) file_get_contents($source),
            1,
        );
        if (file_put_contents($target, $contents) === false) {
            throw new \RuntimeException(sprintf('Cannot write the archived feedback: %s', $target));
        }
        if (!unlink($source)) {
            throw new \RuntimeException(sprintf('Cannot remove the feedback it was archived from: %s', $source));
        }

        return 'feedback/archive/' . $name;
    }

    /**
     * Reads recorded feedback, newest first.
     *
     * Both halves are the same files read the same way, so a query that asks
     * for a category or a tool is answered over all of them. That is what the
     * archive buys: a closed feedback used to be a filename in a commit, with the
     * front matter that says what it was about long gone.
     *
     * @return array<int, array{file: string, date: string, category: string, status: string, model: string, directory: string, tool: string, tools: array<int, string>, title: string, closedBy: ?array{commit: string, date: string, subject: string}}>
     */
    public static function all(
        ?string $status = 'open',
        ?string $category = null,
        int $limit = 20,
        ?string $tool = null,
    ): array {
        self::assertAvailable();

        $directories = array_values(array_filter([
            $status === 'closed' ? null : Paths::feedback(),
            $status === 'open' ? null : Paths::feedbackArchive(),
        ], static fn(?string $directory): bool => $directory !== null && is_dir($directory)));

        $files = $directories === [] ? [] : Finder::create()->files()->in($directories)->depth(0)->name('*.md')
            // The filename starts with the timestamp the feedback was recorded at, so
            // this is newest first across both halves — which directory a feedback is
            // in says whether it was answered, not when it arrived.
            ->sort(static fn(\SplFileInfo $left, \SplFileInfo $right): int => strcmp($right->getFilename(), $left->getFilename()));

        $wanted = $tool === null ? null : (self::comparable(self::toolNames($tool)[0] ?? '') ?: null);
        $answers = self::answers();

        $found = [];
        foreach ($files as $file) {
            $feedback = self::parse($file->getPathname(), $answers);
            if ($feedback === null) {
                continue;
            }
            if ($category !== null && $feedback['category'] !== $category) {
                continue;
            }
            if ($wanted !== null && !in_array($wanted, array_map(self::comparable(...), $feedback['tools']), true)) {
                continue;
            }

            $found[] = $feedback;
            if (count($found) >= $limit) {
                break;
            }
        }

        return $found;
    }

    /**
     * What became of each archived feedback, read from the commit that archived it.
     *
     * One commit implements the improvement and moves the feedback, so that
     * commit's subject is the sentence that answers it — and the answer is the
     * half the agent that reported the gap cannot see for itself. Reading it
     * back from the history rather than writing it into the feedback is what keeps
     * it from being a second copy of what git already has, and a feedback archived
     * but not yet committed simply has no answer yet.
     *
     * The feedback worked off before this archive existed carry their own commit
     * in the front matter, which wins: they were all moved here in one commit,
     * and that move says nothing about any of them.
     *
     * @return array<string, array{commit: string, date: string, subject: string}>
     */
    private static function answers(): array
    {
        $log = self::git([
            'log',
            '--diff-filter=A',
            '--name-only',
            '--date=short',
            // %x00 and %x1f are git's own escapes: the argument carries them as
            // text, and git writes the separators the output is split on.
            '--format=%x00%h%x1f%ad%x1f%s',
            '--',
            'feedback/archive',
        ], self::root());
        if ($log === null) {
            return [];
        }

        $answers = [];
        foreach (explode(self::COMMIT_MARKER, $log) as $block) {
            $lines = preg_split('/\R/', trim($block)) ?: [];
            $header = array_shift($lines);
            if ($header === null || !str_contains($header, "\x1f")) {
                continue;
            }
            [$commit, $date, $subject] = array_pad(explode("\x1f", $header, 3), 3, '');

            foreach ($lines as $path) {
                $path = trim($path);
                if (str_starts_with($path, 'feedback/archive/') && str_ends_with($path, '.md')) {
                    $answers[$path] ??= ['commit' => $commit, 'date' => $date, 'subject' => $subject];
                }
            }
        }

        return $answers;
    }

    /**
     * Runs one git command in the checkout the store belongs to, and returns
     * its output — or null where git could not answer.
     *
     * The working directory is the store's own root rather than
     * `Paths::root()`, which is the same directory in an installation and is
     * not one in a test writing into a store of its own. There the temporary
     * root carries no `.git`, this answers null before starting anything, and
     * a unit test that only wanted to list feedback does not run `git log`
     * — `R-COD-003`.
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

    /**
     * One field with what looked like a credential taken out of it, and a line
     * on the list for each thing taken.
     *
     * The field is named in what comes back because the answer has to be able
     * to say where the value was: a session that pasted a key into its
     * observation and its query has two things to look at, and one sentence
     * naming neither sends it to read the whole file.
     *
     * @param array<int, string> $redacted
     */
    private static function withoutSecrets(string $field, string $text, array &$redacted): string
    {
        $redaction = Redaction::of($text);
        foreach ($redaction->removed as $what) {
            $redacted[] = $field . ': ' . $what;
        }

        return $redaction->text;
    }

    /**
     * The checkout the feedback store belongs to, which a path is made
     * relative to.
     *
     * Read off the store rather than from `Paths::root()`, so that a test
     * writing into a store of its own is handed back the same relative path
     * `record()` gave it — `R-COD-003`.
     */
    public static function root(): string
    {
        return dirname(Paths::feedback());
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
     * @param array<string, array{commit: string, date: string, subject: string}> $answers
     * @return array{file: string, date: string, category: string, status: string, model: string, directory: string, tool: string, tools: array<int, string>, title: string, closedBy: ?array{commit: string, date: string, subject: string}}|null
     */
    private static function parse(string $file, array $answers): ?array
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

        // The first heading is the feedback's title.
        $title = '';
        if (preg_match('/^# (.+)$/m', $contents, $heading) === 1) {
            $title = trim($heading[1]);
        }

        $tools = self::toolNames($meta['tool'] ?? '');
        $relative = substr($file, strlen(self::root()) + 1);
        $archived = str_starts_with($relative, 'feedback/archive/');

        return [
            'file' => $relative,
            'date' => $meta['date'] ?? '',
            'category' => $meta['category'] ?? 'idea',
            // The directory says it, not the front matter: a feedback that was
            // answered is one that was moved, and a status somebody edited in
            // place is the one thing that could disagree with where it is.
            'status' => $archived ? 'closed' : 'open',
            // A feedback written before the field existed carries no model, which
            // is the same thing the field says when it was not answered.
            'model' => $meta['model'] ?? self::UNATTRIBUTED,
            // Where the session stood. It is written into every feedback and was
            // readable only by opening one, so nothing could see that a gap was
            // reported by thirty sessions out of one checkout rather than by one
            // — which is a different judgement (`D-FBK-025`).
            'directory' => $meta['directory'] ?? '',
            // Both: the string is what a feedback has always carried, the list is
            // what a caller can filter or group by without parsing it back.
            'tool' => implode(', ', $tools),
            'tools' => $tools,
            'title' => $title,
            // An open feedback has nothing closing it yet, and the field is present
            // either way so a caller never has to ask which half it is holding.
            'closedBy' => $archived ? self::answer($meta, $answers[$relative] ?? null) : null,
        ];
    }

    /**
     * What the feedback itself says became of it, and otherwise what the history
     * says. Only the feedback restored from before the archive existed carry it
     * themselves — see answers().
     *
     * @param array<string, string> $meta
     * @param array{commit: string, date: string, subject: string}|null $fromHistory
     * @return array{commit: string, date: string, subject: string}|null
     */
    private static function answer(array $meta, ?array $fromHistory): ?array
    {
        $subject = trim($meta['subject'] ?? '', '"');
        if ($subject === '') {
            return $fromHistory;
        }

        return [
            'commit' => $meta['commit'] ?? '',
            'date' => $meta['closed'] ?? '',
            'subject' => $subject,
        ];
    }

    /**
     * Where the feedback was written from: the working directory of the session
     * that left it.
     *
     * A feedback is read long after the session that produced it has ended, and
     * "the icon lookup returned nothing" means something different depending on
     * which project it was asked in. The directory is the one thing that says
     * which — it is how the feedback gets checked against the installation it came
     * from instead of against whatever is at hand.
     *
     * Only ever the directory an entrypoint handed to Instance, so a feedback left
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
        string $model,
        string $origin,
        string $title,
    ): string {
        $frontMatter = [
            'date: ' . date('c'),
            'category: ' . $category,
            'status: open',
            // Always written, "unknown" included: a feedback that carries no model
            // at all is indistinguishable from one recorded before the field
            // existed, and the whole point of the field is that an unattributed
            // feedback says so.
            'model: ' . $model,
        ];
        if ($tools !== []) {
            $frontMatter[] = 'tool: ' . implode(', ', $tools);
        }
        if ($origin !== '') {
            $frontMatter[] = 'directory: ' . $origin;
        }

        $document = "---\n" . implode("\n", $frontMatter) . "\n---\n\n";
        $document .= '# ' . $title . "\n\n";
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
     *
     * The slug begins at the first word that tells this feedback apart from one
     * already named after the same opening. A session files one feedback per
     * subject and files them in one breath, so eight of them open on the
     * sentence that says which session this is — "Debrief of the … session,
     * missed item: …" — and those are exactly the 48 characters a name has room
     * for. Eight files then differ by their timestamp alone, which is the one
     * thing about a feedback nobody is looking for. What goes in the name has to be
     * what only this feedback says.
     */
    private static function uniquePath(string $directory, string $observation): string
    {
        $words = self::words($observation);
        $slug = self::slug($words);

        // Only the feedback whose name this one would take have to be read, and
        // their own first line is where the shared opening can be measured.
        $shared = 0;
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*-' . $slug . '.md') as $taken) {
            $shared = max($shared, self::opening($words, self::words(self::heading($taken->getPathname()))));
        }
        if ($shared > 0 && $shared < count($words)) {
            $slug = self::slug(array_slice($words, $shared));
        }

        $base = date('Y-m-d-His') . ($slug === '' ? '' : '-' . $slug);

        $file = $directory . '/' . $base . '.md';
        $counter = 2;
        while (file_exists($file)) {
            $file = $directory . '/' . $base . '-' . $counter . '.md';
            ++$counter;
        }

        return $file;
    }

    /**
     * How many words two observations open with in common, which is where the
     * one being written starts saying something of its own.
     *
     * @param array<int, string> $left
     * @param array<int, string> $right
     */
    private static function opening(array $left, array $right): int
    {
        $shared = 0;
        while (isset($left[$shared], $right[$shared]) && $left[$shared] === $right[$shared]) {
            ++$shared;
        }

        return $shared;
    }

    /** A feedback's own first line, as it was written into the file. */
    private static function heading(string $file): string
    {
        $contents = (string) file_get_contents($file);

        return preg_match('/^# (.+)$/m', $contents, $heading) === 1 ? trim($heading[1]) : '';
    }

    /** @return array<int, string> */
    private static function words(string $text): array
    {
        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), '-');

        return $slug === '' ? [] : explode('-', $slug);
    }

    /** @param array<int, string> $words */
    private static function slug(array $words): string
    {
        $slug = implode('-', $words);
        if (strlen($slug) <= self::MAX_SLUG_LENGTH) {
            return $slug;
        }

        // Cut on a word boundary so the filename stays readable.
        $slug = substr($slug, 0, self::MAX_SLUG_LENGTH);
        $lastDash = strrpos($slug, '-');

        return $lastDash === false ? $slug : substr($slug, 0, $lastDash);
    }

    /**
     * The first line, cut to a length by characters rather than by bytes.
     *
     * `substr()` counts bytes, and a feedback is prose an agent wrote — one
     * arriving with a dash or a quotation mark past the ninety-seventh byte was
     * cut through the middle of it, and what the heading then carried was half
     * a character. It is not only ugly: the file stops being valid UTF-8, so
     * `grep` treats it as binary and silently matches nothing in it, which is
     * how this was found.
     */
    private static function title(string $observation): string
    {
        $firstLine = trim((string) strtok($observation, "\n"));

        return mb_strlen($firstLine) > 100 ? mb_substr($firstLine, 0, 97) . '...' : $firstLine;
    }

    /**
     * One field as it is stored: cut where a stored field ends, and saying so
     * where it was cut.
     *
     * Marked for the reason a redaction is marked — a report that was altered
     * says so, or a reader cannot tell it from one that ended there. The cut
     * needs it more. A redaction leaves the name of what it took standing
     * beside its marker, while this falls on a character count, mid-word, and
     * takes with it every sign that the sentence was going anywhere.
     * `feedback/2026-08-03-144316` lost the sentence naming what it was
     * reporting, and neither the file nor the answer said a word had gone.
     *
     * The marker stands past the cap rather than inside it, which is where
     * title() puts its own. The cap is what a session's prose is held to, and
     * taking the marker's characters out of that prose would cut the field
     * twice over in order to say once that it was cut.
     *
     * @param array<int, string> $cut
     */
    private static function text(string $field, mixed $value, array &$cut): string
    {
        if (!is_string($value)) {
            return '';
        }

        $text = trim($value);
        $length = mb_strlen($text);
        if ($length <= self::MAX_FIELD_LENGTH) {
            return $text;
        }

        $what = sprintf(
            '%d characters past the %d-character limit',
            $length - self::MAX_FIELD_LENGTH,
            self::MAX_FIELD_LENGTH,
        );
        $cut[] = $field . ': ' . $what;

        return mb_substr($text, 0, self::MAX_FIELD_LENGTH) . ' ' . sprintf(self::CUT_MARKER, $what);
    }

    /**
     * A field that arrived carrying the call it was sent in is refused.
     *
     * On 2026-08-04 one session filed fourteen feedback whose observation ended
     * in `</observation>`, the whole `suggestion` parameter and `</invoke>`: the
     * parameters had been closed with a tag named after themselves, so
     * everything after the first bad close was folded into the first field and
     * the arguments behind it never arrived. `suggestion` is optional, so
     * nothing rejected the call, and fourteen reports were stored with their
     * proposal buried in the middle of a paragraph — `D-FBK-044`.
     *
     * The two shapes are structural rather than topical: a field that ends in
     * the closing tag of the call, and a parameter opening a line of its own. A
     * session reporting *about* this failure quotes those markers inline and
     * mid-sentence, which is why neither check is a search for the string.
     */
    private static function assertNoCallFrame(string $field, mixed $value): void
    {
        if (!is_string($value)) {
            return;
        }

        $text = trim($value);
        if (!str_ends_with($text, '</invoke>') && preg_match('/\n<parameter name="[^"]+">/', $text) !== 1) {
            return;
        }

        throw new \InvalidArgumentException(sprintf(
            'The %s carries the frame of the call it arrived in. A parameter closed with a tag named after itself '
            . 'swallows everything after it, arguments included, so what reached this server is one field holding '
            . 'the rest of the call. Close every parameter with the closing tag its opening declared and send the '
            . 'call again.',
            $field,
        ));
    }

    private static function category(mixed $value): string
    {
        return is_string($value) && in_array($value, self::CATEGORIES, true) ? $value : 'idea';
    }

    /**
     * The model that left the feedback, as it named itself.
     *
     * Half the feedback this server receives are about what a session did rather
     * than about what an answer said — a skill whose steps were read and not
     * run, a tool nothing reached for. That is behaviour, and behaviour belongs
     * to one model: without the name, two models' habits arrive as one
     * undifferentiated report and neither can be worked off.
     *
     * The schema asks for it and the write never fails on it. A feedback is worth
     * more than its attribution, and a missing name is recorded as
     * "unknown" — which is also what a model that does not know its own
     * identifier is asked to send, because an invented one is worse than none.
     */
    private static function model(mixed $value): string
    {
        $model = is_string($value) ? trim((string) preg_replace('/\s+/', ' ', $value)) : '';
        if ($model === '') {
            return self::UNATTRIBUTED;
        }

        return mb_strlen($model) > self::MAX_MODEL_LENGTH ? mb_substr($model, 0, self::MAX_MODEL_LENGTH) : $model;
    }

    /**
     * The tools a feedback is about.
     *
     * An observation is regularly about several tools at once — the four that
     * go quiet together when the console cannot be reached, say. Stripping
     * everything but [a-z0-9_] from one string ran their names together into
     * one unsearchable word, which is what a growing pile is least able to afford:
     * the obvious thing to want from the list is every feedback about one tool.
     *
     * A string is split on what separates names in one — a comma or a space —
     * rather than having the separator deleted, and that is the form the schema
     * declares. A list is still taken as a list for a caller standing in this
     * package, but no longer for one on the wire: `D-ANS-017` traded the
     * declared union away, and an array reaching `record()` from outside is
     * refused by the validator before this is called.
     *
     * What is left of a name after the separators is kept as it was written,
     * hyphen included: a skill is `typo3-extension-conformance` in the listing
     * a session reads it from and in `skills/`, and stripping the hyphen stored
     * an identifier the project carries nowhere — invisible to the grep that is
     * the obvious way to ask what was reported about it (`R-FBK-013`).
     *
     * The case is kept for the same reason and was not, until 2026-08-04:
     * everything this server registers is lower case, so the fold showed on the
     * one kind of name that is not ours — a client's own tool, named because a
     * session reached for it instead. `comparable()` is where two spellings
     * meet, so folding there costs nothing and folding here cost the name
     * (`D-FBK-039`).
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
            $name = (string) preg_replace('/[^A-Za-z0-9_-]/', '', trim($candidate));
            if ($name !== '') {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * One name in the form two spellings of it can be compared in.
     *
     * A tool is `typo3_documentation_lookup` and a skill is
     * `typo3-extension-conformance`, and a session names either with whichever
     * separator it has in front of it. What is stored is what was written, so
     * the filter is where the spellings meet — the rule `D-ANS-006` already
     * applies to an identifier a caller looks up, applied to the one thing this
     * store is filtered by.
     */
    private static function comparable(string $name): string
    {
        return (string) preg_replace('/[^a-z0-9]/', '', strtolower($name));
    }
}
