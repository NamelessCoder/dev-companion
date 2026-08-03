<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Typo3CmsMcp\Installation\Icons;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Installation\Typo3Runtime;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tool\Registry;

/**
 * What every tool actually answered, once, written down.
 *
 * The half of a tool's page above `## Answered` says which fields an answer
 * has; this is what goes below it — what one looks like filled, a match with
 * its score, an `unsupported` with its cause. That half is not derivable from
 * the registry and cannot be a test either: it needs an installation, and no
 * test run discovers one.
 *
 * So it is a recording rather than a check. It is evidence from one machine on
 * one day, every section says so where it opens, and nothing fails on it being
 * older than the code — a command only a machine with checkouts can run must
 * not be able to turn CI red.
 *
 * It sits on the tool's own page rather than in a directory of its own, so the
 * reader who arrived with one tool in hand meets the shape and a filled answer
 * without following a link. `ToolSurface` renders the half above and carries
 * this one over untouched; this writes the half below and carries that one over
 * the same way.
 *
 * The answers are whole. What a recording is for is seeing a filled answer, and
 * a block with `… 14 more` where the entries were is a count of one instead.
 *
 * Of two working directories, because no single one fills the surface. A core
 * checkout answers the core half and has no console, so the tools that reach an
 * installation come back `unsupported` or read the packages; the installation
 * `Fixture` writes answers those from its booted TYPO3 and has no
 * `runTests.sh`, `Build/Sources` or `EXT:styleguide` for the other half to
 * read. Recording
 * against one of them alone is a trade rather than an improvement, and
 * `D-DOC-006` has what each side costs. So the second recording is added where
 * it is the answer and nowhere else: a tool that declares `answeredBy` is
 * declaring that its answer has two provenances, and those are exactly the
 * pages that carry both.
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

    /** The recorded half of a page as it stands, and nothing where there is none. */
    public static function recordedIn(string $file): string
    {
        if (!is_file($file)) {
            return '';
        }

        return preg_match('/^## Answered\n.*/ms', (string) file_get_contents($file), $matched) === 1
            ? rtrim($matched[0]) . "\n"
            : '';
    }

    /**
     * Every page of the surface, the recorded halves rewritten.
     *
     * Every call is answered from `$primary`, and the calls of the
     * installation-backed tools are answered a second time from
     * `$installation`. Both are pointed at here rather than by the caller: the
     * substitutions that keep a machine's layout out of the pages read the
     * installation root as it stands, so an answer has to be rendered while the
     * root it came from is the one that is pointed at.
     *
     * `$tools` narrows the run to the tools named. What is not named keeps the
     * section its page already carries, which is the only way to answer for one
     * tool without dropping every answer this root cannot produce.
     *
     * @param list<string> $tools
     * @return array<string, string>
     */
    public static function rendered(
        string $today,
        string $primary,
        ?string $installation = null,
        array $tools = [],
    ): array {
        $backed = self::installationBacked();
        if ($tools !== []) {
            $backed = array_values(array_intersect($backed, $tools));
        }

        $recordings = [self::recordAgainst($primary, $tools)];
        // No second recording where the named tools have no installation-backed
        // one among them. An empty list means every tool to `recordAgainst`, so
        // narrowing to one that answers the same from any root would otherwise
        // record the whole surface a second time and head every page it touched
        // with a second provenance it does not have.
        if ($installation !== null && $backed !== []) {
            $recordings[] = self::recordAgainst($installation, $backed);
        }
        self::pointAt($primary);

        $pages = [ToolSurface::index() => ToolSurface::indexPage()];
        foreach (Registry::definitions() as $definition) {
            $name = $definition['name'];
            $answered = $tools !== [] && !in_array($name, $tools, true)
                ? self::recordedIn(ToolSurface::file($name))
                : (isset($recordings[0]['answers'][$name]) ? self::recording($today, $name, $recordings) : '');
            $pages[ToolSurface::file($name)] = ToolSurface::page($definition, $answered);
        }

        return $pages;
    }

    /**
     * Every call answered from one root, with the sentence saying which.
     *
     * `$only` narrows it to the tools whose answers that root is being recorded
     * for; empty means all of them.
     *
     * @param list<string> $only
     * @return array{against: string, shortly: string, answers: array<string, array<string, array{0: string, 1: string}>>}
     */
    private static function recordAgainst(string $root, array $only = []): array
    {
        self::pointAt($root);

        $answers = [];
        foreach (ToolCalls::all() as $label => [$name, $arguments]) {
            if ($only !== [] && !in_array($name, $only, true)) {
                continue;
            }
            $answers[$name][$label] = self::answer($name, $arguments);
        }

        return ['against' => self::against(), 'shortly' => self::shortly(), 'answers' => $answers];
    }

    /**
     * Moves every reading of an installation to this one.
     *
     * Discovery is the only one of these a caller normally sets, because in a
     * session there is one installation and it is found once. A recording moves
     * between two in one process, and each of the three below memoizes what it
     * read from the last one: the console invocation, the booted runtime's
     * answer, the icon registry. Forgetting the discovery alone leaves the
     * second recording showing the first installation's registries under the
     * second one's head.
     */
    private static function pointAt(string $root): void
    {
        Instance::discoverFrom($root);
        Typo3Cli::forget();
        Typo3Runtime::forget();
        Icons::forget();
    }

    /**
     * The tools whose answer depends on the installation, as they say so
     * themselves: `answeredBy` is the field a tool declares when its answer has
     * two provenances — the booted installation, or the packages read as files
     * because the console could not be asked. That is precisely the property
     * that makes a second recording worth its lines, so it is read off the
     * registry rather than written down again here. A list would be the thing
     * that still names a tool after the field left it.
     *
     * @return list<string>
     */
    private static function installationBacked(): array
    {
        $names = [];
        foreach (Registry::definitions() as $definition) {
            if (isset($definition['outputSchema']['properties']['answeredBy'])) {
                $names[] = $definition['name'];
            }
        }

        return $names;
    }

    /**
     * What the recording is of, in as many sentences as it has roots.
     *
     * One root reads as it always did. Two say so first, because a reader who
     * meets a second answer further down has to have been told there is one —
     * and because "recorded against a checkout" was the sentence that made the
     * installation half of this surface look like it had no filled answer.
     *
     * @param list<array{against: string, shortly: string, answers: array<string, array<string, array{0: string, 1: string}>>}> $recordings
     */
    private static function of(array $recordings): string
    {
        if (count($recordings) === 1) {
            return $recordings[0]['against'];
        }

        return 'Of two working directories, because what this server answers depends on which one a client is '
            . 'standing in, and neither fills the whole surface. '
            . implode(' ', array_column($recordings, 'against'))
            . ' The tools that declare `answeredBy` carry an answer from each, under a heading naming which; '
            . 'every other answer is from the first alone, because nothing in it would differ.';
    }

    /**
     * The recorded half of one tool's page: what it is of, then every call.
     *
     * It says which day and which installation it came from because a recording
     * that does not is an assertion about nothing, and because the half above it
     * is derived and may not make that claim for it — `D-DOC-006`.
     *
     * A call is the unit whether it was answered once or twice, so the second
     * answer goes under the same heading as the first. The arguments are the
     * same both times and are written once: what a reader came to compare is the
     * two answers, and a call whose two halves sit 600 lines apart is not a
     * comparison.
     *
     * @param list<array{against: string, shortly: string, answers: array<string, array<string, array{0: string, 1: string}>>}> $recordings
     */
    private static function recording(string $today, string $name, array $recordings): string
    {
        $of = array_values(array_filter($recordings, static fn(array $r): bool => isset($r['answers'][$name])));

        $lines = [
            '## Answered',
            '',
            ...self::wrapped(sprintf(
                'Recorded on %s by `bin/cli tools:record`. %s Nothing checks what is below this heading; everything '
                . 'above it is derived from the class that answers the call, and `bin/cli tools:check` holds it.',
                $today,
                self::of($of),
            )),
            '',
        ];

        foreach (ToolCalls::all() as $label => [$tool, $arguments]) {
            if ($tool !== $name) {
                continue;
            }
            $lines[] = '### ' . $label;
            $lines[] = '';
            $lines[] = 'Called with:';
            $lines[] = '';
            $lines = [...$lines, ...self::fenced('json', self::json($arguments === [] ? new \stdClass() : $arguments))];
            $lines[] = '';

            foreach ($of as $recording) {
                // The heading only where there is something to tell apart.
                if (count($of) > 1) {
                    $lines[] = '#### From ' . $recording['shortly'];
                    $lines[] = '';
                }
                [$text, $data] = $recording['answers'][$name][$label];
                $lines[] = 'Text:';
                $lines[] = '';
                $lines = [...$lines, ...self::fenced('', $text)];
                $lines[] = '';
                $lines[] = 'Data:';
                $lines[] = '';
                $lines = [...$lines, ...self::fenced('json', $data)];
                $lines[] = '';
            }
        }

        return implode("\n", $lines);
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
     * The same, short enough to head one answer among several.
     *
     * The full sentence carries the kind, the version and the console's reason,
     * which is what a page's head is for. Repeated over every call it would be
     * longer than several of the answers under it, so what is left here is the
     * one thing a reader is telling apart: which of the two roots, and whether
     * its console answered.
     */
    private static function shortly(): string
    {
        $root = Instance::describe()['root'] ?? null;

        return $root === null
            ? 'no installation'
            : self::describeRoot((string) $root)
                . ', whose console ' . (Typo3Cli::isAvailable() ? 'answers' : 'could not be reached');
    }

    /**
     * A recorded root, named by what it is rather than by where it sits.
     *
     * The three this repository can produce are named as such — `bin/cli
     * tools:record` writes the fixture itself, `bin/cli checkouts:update` makes
     * a core checkout and `bin/cli environment:create` an environment — so
     * naming which says how to record the same thing again. Anything else is
     * somebody's machine, and the path to it is not evidence anybody else can
     * use.
     *
     * The fixture is named first and named as one. What it answers is true of
     * it and of nothing else, and a reader who cannot see that from the heading
     * has a page of two answers it looks like one machine gave.
     *
     * Both sides are resolved first: a worktree reaches the checkouts through a
     * symlink, so the recording made in one would otherwise call this
     * repository's own checkout somebody's machine.
     */
    private static function describeRoot(string $root): string
    {
        $root = (string) (realpath($root) ?: $root);

        $fixture = (string) realpath(Fixture::root());
        if ($fixture !== '' && $root === $fixture) {
            return 'the installation this repository writes below .fixtures/';
        }

        $checkouts = (string) realpath(Checkouts::directory());
        if ($checkouts !== '' && str_starts_with($root, $checkouts)) {
            return 'the ' . trim(substr($root, strlen($checkouts)), '/') . ' core checkout below .checkouts/';
        }

        $environments = (string) realpath(Environments::directory());
        if ($environments !== '' && str_starts_with($root, $environments)) {
            return 'the ' . strtoupper(basename($root)) . ' this repository makes below .environments/';
        }

        return 'an installation outside this repository';
    }

    /**
     * One call, as its two halves, with this root's paths already written out.
     *
     * The substitution happens here rather than at rendering time because it
     * reads the installation root as it stands, and by the time a page is
     * composed the roots have both been pointed at.
     *
     * @param array<string, mixed> $arguments
     * @return array{0: string, 1: string}
     */
    private static function answer(string $name, array $arguments): array
    {
        $result = Registry::call($name, $arguments);

        return [
            self::withoutAbsolutePaths(rtrim($result->text)),
            self::withoutAbsolutePaths(self::json($result->data)),
        ];
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

    /**
     * The same, as the lines it becomes, so a caller assembling a page by lines
     * does not put a wrapped paragraph back together to split it again.
     *
     * @return list<string>
     */
    private static function wrapped(string $text, string ...$rest): array
    {
        return explode("\n", self::wrap($rest === [] ? $text : sprintf($text, ...$rest)));
    }
}
