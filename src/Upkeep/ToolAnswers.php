<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tool\Registry;

/**
 * What every tool actually answered, once, written down.
 *
 * `documentation/clients/tools.md` says which fields an answer has; this says
 * what one looks like filled — a match with its score, an `unsupported` with
 * its cause. That half is not derivable from the registry and cannot be a test
 * either: it needs an installation, and no test run discovers one.
 *
 * So it is a recording rather than a check. It is evidence from one machine on
 * one day, it says so at its head, and nothing fails on it being older than the
 * code — a command only a machine with checkouts can run must not be able to
 * turn CI red.
 */
final class ToolAnswers
{
    /**
     * What an absolute path is written as, so the page is not one machine's
     * layout. Deepest first, because an installation below this checkout has to
     * be recognised before the checkout around it is.
     *
     * @var array<int, array{0: callable(): ?string, 1: string}>
     */
    private const REDACTED = [
        [[Instance::class, 'root'], '<installation>'],
        [[Paths::class, 'root'], '<repository>'],
    ];

    /** The width the rest of the documentation is written at. */
    private const WIDTH = 79;

    /** How many lines of an answer's text are shown before it is cut. */
    private const LINES = 14;

    /** How many entries of a list are shown before the rest is counted. */
    private const ENTRIES = 2;

    /** How long a string is shown before it is cut. */
    private const CHARACTERS = 320;

    public static function file(): string
    {
        return Paths::root() . '/documentation/clients/tool-answers.md';
    }

    /**
     * The page as one string: the head, what answered, then every call.
     *
     * The caller has already pointed `Instance` at whatever is being recorded
     * against, because that is what decides half of these answers.
     */
    public static function rendered(string $today): string
    {
        $blocks = [self::head($today)];
        foreach (ToolCalls::all() as $label => [$name, $arguments]) {
            $blocks[] = self::call($label, $name, $arguments);
        }

        return implode("\n", $blocks);
    }

    /**
     * The two roots, written as what they are.
     *
     * Several of these answers name where they looked, and a recording that
     * kept those paths would commit one machine's directory layout into a page
     * every reader of this package gets. What the answer is showing is that it
     * says where it looked, which survives the substitution.
     */
    private static function withoutAbsolutePaths(string $text): string
    {
        foreach (self::REDACTED as [$root, $name]) {
            $path = $root();
            if (is_string($path) && $path !== '') {
                $text = str_replace($path, $name, $text);
            }
        }

        // Everything above the checkout is the machine rather than the
        // recording, and it reaches the page through the directories discovery
        // walked on its way up.
        $home = (string) getenv('HOME');

        return $home === '' ? $text : str_replace($home, '<home>', $text);
    }

    /** What this recording is of, which is the whole of what makes it readable later. */
    private static function head(string $today): string
    {
        $installation = Instance::describe();
        $root = $installation['root'] ?? null;

        return implode("\n", [
            '# What the tools answered',
            '',
            self::wrap(sprintf(
                'Recorded on %s by `bin/cli tools:record`, over the calls '
                . '`Upkeep\\ToolCalls` holds — the same ones `ToolContractTest` validates. It is one run on one '
                . 'machine and it may be older than the code: nothing checks it, and '
                . '`documentation/clients/tools.md` is where the current shape of an answer is.',
                $today,
            )),
            '',
            self::wrap(sprintf(
                'Answered against %s. Half of these answers belong to it rather than to this server — which labels '
                . 'and icons exist, what the project consists of — and the other half would read the same '
                . 'anywhere.',
                $root === null
                    ? 'no installation: the recording ran where nothing was found above the working directory'
                    : sprintf(
                        '%s, TYPO3 %s, %s, whose console %s',
                        $installation['kind'] ?? 'an installation',
                        Instance::typo3Version() ?? 'of an unread version',
                        self::describeRoot((string) $root),
                        Typo3Cli::isAvailable()
                            ? 'answers'
                            : 'could not be reached: ' . self::withoutAbsolutePaths(rtrim(Typo3Cli::reason(), '.')),
                    ),
            )),
            '',
            self::wrap(sprintf(
                'Every answer is cut to fit: the text at %d lines, a list at %d entries with the rest counted, a '
                . 'string at %d characters. The data stays valid JSON, so what a cut leaves behind is a string '
                . 'saying how much is missing rather than a broken document. Absolute paths are written as '
                . '`<repository>`, `<installation>` and `<home>`, because where a machine keeps its checkouts is '
                . 'not what these answers are showing.',
                self::LINES,
                self::ENTRIES,
                self::CHARACTERS,
            )),
            '',
        ]);
    }

    /**
     * A recorded root, named by what it is rather than by where it sits.
     *
     * The core checkouts are this repository's own and `bin/cli
     * checkouts:update` recreates them, so naming the branch says how to record
     * the same thing again. Anything else is somebody's machine, and the path
     * to it is not evidence anybody else can use.
     */
    private static function describeRoot(string $root): string
    {
        $checkouts = Checkouts::directory();

        return str_starts_with($root, $checkouts)
            ? 'the ' . trim(substr($root, strlen($checkouts)), '/') . ' core checkout below .checkouts/'
            : 'an installation outside this repository';
    }

    /** @param array<string, mixed> $arguments */
    private static function call(string $label, string $name, array $arguments): string
    {
        $result = Registry::call($name, $arguments);

        return implode("\n", [
            '## `' . $name . '` — ' . $label,
            '',
            '```json',
            self::json($arguments === [] ? new \stdClass() : $arguments),
            '```',
            '',
            'Text:',
            '',
            '```',
            self::lines(self::withoutAbsolutePaths($result->text)),
            '```',
            '',
            'Data:',
            '',
            '```json',
            self::json(self::cut($result->data)),
            '```',
            '',
        ]);
    }

    /** The first lines of an answer, with what was cut counted rather than dropped silently. */
    private static function lines(string $text): string
    {
        $lines = preg_split('/\R/', rtrim($text)) ?: [];
        if (count($lines) <= self::LINES) {
            return implode("\n", $lines);
        }

        return implode("\n", array_slice($lines, 0, self::LINES))
            . sprintf("\n… %d more lines", count($lines) - self::LINES);
    }

    /**
     * The data with its long parts counted instead of shown.
     *
     * A list keeps its first entries and gains one saying how many followed, a
     * string keeps its opening — both of which are still JSON, so a reader can
     * paste the block into anything that parses one and see the shape.
     */
    private static function cut(mixed $value): mixed
    {
        if (is_string($value)) {
            // Before the cut rather than after it: a path chopped in the middle
            // is no longer the string a substitution would recognise.
            $value = self::withoutAbsolutePaths($value);

            return mb_strlen($value) <= self::CHARACTERS
                ? $value
                : mb_substr($value, 0, self::CHARACTERS) . sprintf('… (%d characters)', mb_strlen($value));
        }
        if (!is_array($value)) {
            return $value;
        }
        if (array_is_list($value)) {
            $kept = array_map(self::cut(...), array_slice($value, 0, self::ENTRIES));

            return count($value) <= self::ENTRIES
                ? $kept
                : [...$kept, sprintf('… %d more', count($value) - self::ENTRIES)];
        }

        return array_map(self::cut(...), $value);
    }

    private static function json(mixed $value): string
    {
        return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function wrap(string $text): string
    {
        return wordwrap($text, self::WIDTH);
    }
}
