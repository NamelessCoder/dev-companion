<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Symfony\Component\Finder\Finder;
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
 * one day, every page says so at its head, and nothing fails on it being older
 * than the code — a command only a machine with checkouts can run must not be
 * able to turn CI red.
 *
 * One file per tool, and the answers whole. A reader arrives with one tool in
 * hand, and on one page of all of them everything else was in the way. Whole,
 * because what a recording is for is seeing a filled answer, and a block with
 * `… 14 more` where the entries were is a count of one instead.
 */
final class ToolAnswers
{
    /**
     * What an absolute path is written as, so the pages are not one machine's
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

    public static function directory(): string
    {
        return Paths::root() . '/documentation/clients/tool-answers';
    }

    public static function file(string $tool): string
    {
        return self::directory() . '/' . $tool . '.md';
    }

    /** The map: what the recording is of, and which tool is on which page. */
    public static function index(): string
    {
        return self::directory() . '/readme.md';
    }

    /**
     * The whole recording, keyed by the file each page is written to.
     *
     * The caller has already pointed `Instance` at whatever is being recorded
     * against, because that is what decides half of these answers.
     *
     * @return array<string, string>
     */
    public static function rendered(string $today): array
    {
        $calls = [];
        foreach (ToolCalls::all() as $label => [$name, $arguments]) {
            $calls[$name][$label] = self::call($label, $name, $arguments);
        }

        $pages = [self::index() => self::indexPage($today, array_map(array_keys(...), $calls))];
        foreach ($calls as $name => $answers) {
            $pages[self::file($name)] = self::page($today, $name, $answers);
        }

        return $pages;
    }

    /** What a recording left behind, so a page for a tool that is gone can go with it. */
    public static function written(): Finder
    {
        return Finder::create()->files()->in(self::directory())->name('*.md')->sortByName();
    }

    /**
     * @param array<string, list<string>> $labels
     */
    private static function indexPage(string $today, array $labels): string
    {
        $lines = [
            '# What the tools answered',
            '',
            self::wrap(sprintf(
                'Recorded on %s by `bin/cli tools:record`, over the calls `Upkeep\\ToolCalls` holds — the same ones '
                . '`ToolContractTest` validates. One page per tool, each answer whole. It is one run on one machine '
                . 'and it may be older than the code: nothing checks it, and [tools.md](../tools.md) is where the '
                . 'current shape of an answer is.',
                $today,
            )),
            '',
            self::wrap(self::against() . ' Half of these answers belong to it rather than to this server — which '
                . 'labels and icons exist, what the project consists of — and the other half would read the same '
                . 'anywhere.'),
            '',
            self::wrap(
                'Absolute paths are written as `<repository>`, `<installation>` and `<home>`, because where a '
                . 'machine keeps its checkouts is not what these answers are showing. Nothing else is rewritten: '
                . 'each block is what a client received.',
            ),
            '',
        ];

        // One line per tool, unwrapped: a break inside a tool name or a call
        // label costs a reader more than a long line does, and this is the list
        // somebody scans for the one page they came for.
        foreach ($labels as $name => $calls) {
            $lines[] = sprintf('- [`%s`](%s.md) — %s', $name, $name, implode(', ', $calls));
        }

        array_push($lines, ...self::absent());

        return implode("\n", $lines) . "\n";
    }

    /**
     * The offered tools this recording has no page for, and why.
     *
     * It comes after the list rather than before it, because that is where the
     * reader who scanned the list for their tool and did not find it has
     * arrived. A map that says which pages are here and nothing about the ones
     * that are not leaves a deliberate absence looking like an omission — the
     * same reading `tools.md` invited until it stated it per tool, and the words
     * are `ToolCalls::undriven()`'s so that the two say the same thing.
     *
     * @return list<string>
     */
    private static function absent(): array
    {
        $offered = array_column(Registry::definitions(), 'name');
        $undriven = array_intersect_key(ToolCalls::undriven(), array_flip($offered));
        if ($undriven === []) {
            return [];
        }

        $lines = [
            '',
            self::wrap('Not here, and stated again in [tools.md](../tools.md) at each of them:'),
            '',
        ];
        foreach ($undriven as $name => $why) {
            $lines[] = self::wrap(sprintf('- `%s` — %s', $name, $why), '  ');
        }

        return $lines;
    }

    /**
     * One tool: what the recording is of, then every call of it.
     *
     * The head is on each page rather than left to the index, because a
     * recording that does not say which day and which installation it is of is
     * an assertion about nothing — and this is the page somebody arrives on.
     *
     * @param array<string, string> $answers
     */
    private static function page(string $today, string $name, array $answers): string
    {
        return implode("\n", [
            '# What `' . $name . '` answered',
            '',
            self::wrap(sprintf(
                'Recorded on %s by `bin/cli tools:record`. %s Nothing checks this page; [tools.md](../tools.md) is '
                . 'where the current shape of an answer is, and [readme.md](readme.md) is what the recording as a '
                . 'whole is of.',
                $today,
                self::against(),
            )),
            '',
            ...array_values($answers),
        ]);
    }

    /** The installation these answers came out of, in one sentence. */
    private static function against(): string
    {
        $installation = Instance::describe();
        $root = $installation['root'] ?? null;

        return sprintf(
            'Answered against %s.',
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
        );
    }

    /**
     * A recorded root, named by what it is rather than by where it sits.
     *
     * The core checkouts are this repository's own and `bin/cli
     * checkouts:update` recreates them, so naming the branch says how to record
     * the same thing again. Anything else is somebody's machine, and the path
     * to it is not evidence anybody else can use.
     *
     * Both sides are resolved first: a worktree reaches the checkouts through a
     * symlink, so the recording made in one would otherwise call this
     * repository's own checkout somebody's machine.
     */
    private static function describeRoot(string $root): string
    {
        $checkouts = (string) realpath(Checkouts::directory());
        $root = (string) (realpath($root) ?: $root);

        return $checkouts !== '' && str_starts_with($root, $checkouts)
            ? 'the ' . trim(substr($root, strlen($checkouts)), '/') . ' core checkout below .checkouts/'
            : 'an installation outside this repository';
    }

    /** @param array<string, mixed> $arguments */
    private static function call(string $label, string $name, array $arguments): string
    {
        $result = Registry::call($name, $arguments);

        return implode("\n", [
            '## ' . $label,
            '',
            'Called with:',
            '',
            ...self::fenced('json', self::json($arguments === [] ? new \stdClass() : $arguments)),
            '',
            'Text:',
            '',
            ...self::fenced('', self::withoutAbsolutePaths(rtrim($result->text))),
            '',
            'Data:',
            '',
            ...self::fenced('json', self::withoutAbsolutePaths(self::json($result->data))),
            '',
        ]);
    }

    /**
     * A block whose fence is longer than anything inside it.
     *
     * Several of these answers are markdown themselves — the script notes hand
     * back commands in fenced blocks, and a knowledge section comes back as it
     * was written. Three backticks around that ends the block at the answer's
     * own fence and renders the rest of the page as prose.
     *
     * @return list<string>
     */
    private static function fenced(string $language, string $content): array
    {
        preg_match_all('/^ {0,3}(`{3,})/m', $content, $inside);
        $runs = array_map(strlen(...), $inside[1]);
        $fence = str_repeat('`', $runs === [] ? 3 : max($runs) + 1);

        return [$fence . $language, $content, $fence];
    }

    /**
     * The two roots, written as what they are.
     *
     * Several of these answers name where they looked, and a recording that
     * kept those paths would commit one machine's directory layout into pages
     * every reader of this package gets. What the answer is showing is that it
     * says where it looked, which survives the substitution.
     *
     * It runs over the rendered JSON rather than over the values behind it:
     * slashes are left unescaped, so a path is the same characters in a data
     * block as in a text one, and one substitution reaches both.
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

    private static function json(mixed $value): string
    {
        return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function wrap(string $text, string $continuation = ''): string
    {
        return wordwrap($text, self::WIDTH - strlen($continuation), "\n" . $continuation);
    }
}
