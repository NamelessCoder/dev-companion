<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * What every tool actually answered, once, written down: the half of a tool's
 * page below `## Answered`, where `ToolSurface` writes the half above it.
 *
 * A recording and not a check, because it needs an installation and no test run
 * discovers one — `D-DOC-006`. The tools `ToolCalls::derived()` names read
 * nothing an installation contains, so `derivedSections()` writes their half
 * against `CoreFixture` and `tools:check` holds it. Of two working directories,
 * because neither fills the surface alone, and the second answer goes on the
 * pages of the tools that declare `answeredBy` — `D-DOC-012`.
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

        // The heading and the underline that makes it one, which is what tells
        // it from the word standing in a sentence.
        return preg_match('/^Answered\n-+\n.*/ms', (string) file_get_contents($file), $matched) === 1
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

        $derived = self::derivedSections($tools);
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

        $pages = ToolSurface::standingPages();
        foreach (Registry::definitions() as $definition) {
            $name = $definition['name'];
            $answered = $derived[$name] ?? ($tools !== [] && !in_array($name, $tools, true)
                ? self::recordedIn(ToolSurface::file($name))
                : (isset($recordings[0]['answers'][$name]) ? self::recording($today, $name, $recordings) : ''));
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
            // A derived answer is not evidence about this root, and recording
            // it would put a second one under the same heading that says so.
            if (in_array($name, ToolCalls::derived(), true)) {
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
    private static function pointAt(?string $root): void
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
            . ' The tools that declare ``answeredBy`` carry an answer from each, under a heading naming which; '
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

        return self::answered($name, sprintf(
            'Recorded on %s by ``bin/cli tools:record``. %s Nothing checks what is below this heading; everything '
            . 'above it is derived from the class that answers the call, and ``bin/cli tools:check`` holds it.',
            $today,
            self::of($of),
        ), $of);
    }

    /**
     * The answered half of one tool: what it is of, then every call it drives.
     *
     * Both halves of the surface come through here — a recording of one or two
     * roots, and a derived section of exactly one — because what a reader
     * compares is the same thing either way: the call, then what came back.
     * Only the opening sentence says which kind of evidence it is looking at.
     *
     * @param list<array{against: string, shortly: string, answers: array<string, array<string, array{0: string, 1: string}>>}> $of
     */
    private static function answered(string $name, string $opening, array $of): string
    {
        $lines = [...Rst::heading('Answered', 1), ...self::wrapped($opening), ''];

        foreach (ToolCalls::all() as $label => [$tool, $arguments]) {
            if ($tool !== $name) {
                continue;
            }
            array_push($lines, ...Rst::heading($label, 2));
            $lines[] = 'Called with:';
            $lines[] = '';
            array_push($lines, ...self::fenced('json', self::json($arguments === [] ? new \stdClass() : $arguments)));

            foreach ($of as $recording) {
                // The heading only where there is something to tell apart.
                if (count($of) > 1) {
                    array_push($lines, ...Rst::heading('From ' . $recording['shortly'], 3));
                }
                [$text, $data] = $recording['answers'][$name][$label];
                $lines[] = 'Text:';
                $lines[] = '';
                array_push($lines, ...self::fenced('', $text));
                $lines[] = 'Data:';
                $lines[] = '';
                array_push($lines, ...self::fenced('json', $data));
            }
        }

        return implode("\n", $lines);
    }

    /**
     * The answered half of every derived tool, against the root that declares
     * nothing but its identity.
     *
     * It points at that root and puts back whatever was pointed at before,
     * because both callers reach this in the middle of something else:
     * `tools:index` and `tools:check` are standing wherever they were started,
     * and `tools:record` is between its two recordings.
     *
     * @param list<string> $only the tools a caller narrowed the run to, empty for all of them
     * @return array<string, string>
     */
    public static function derivedSections(array $only = []): array
    {
        $names = ToolCalls::derived();
        if ($only !== []) {
            $names = array_values(array_intersect($names, $only));
        }
        if ($names === []) {
            return [];
        }

        $was = Instance::startedFrom();
        self::pointAt(CoreFixture::write());

        $opening = sprintf(
            'Derived by ``bin/cli tools:index``, and ``bin/cli tools:check`` holds it — the same as everything above '
            . 'this heading. This tool reads nothing an installation contains: what reaches its answer is the '
            . 'bundled knowledge and which TYPO3 major the caller is on, so what comes back is written down rather '
            . 'than recorded from one machine\'s checkout. Answered against %s, declaring TYPO3 %s.',
            self::describeRoot(CoreFixture::root()),
            CoreFixture::typo3Version(),
        );

        $sections = [];
        foreach ($names as $name) {
            $sections[$name] = self::answered($name, $opening, [[
                'against' => $opening,
                'shortly' => '',
                'answers' => [$name => self::answersOf($name)],
            ]]);
        }

        self::pointAt($was);

        return $sections;
    }

    /**
     * Every call one tool drives, answered from wherever this is pointed.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    private static function answersOf(string $name): array
    {
        $answers = [];
        foreach (ToolCalls::all() as $label => [$tool, $arguments]) {
            if ($tool === $name) {
                $answers[$label] = self::answer($name, $arguments);
            }
        }

        return $answers;
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
     * A heading is read in the contents list beside every other heading of the
     * page, so it carries the one thing a reader is telling apart: which of the
     * two roots. Where it sits, which version it declares and whether its
     * console answered stand in the sentence above, once — written into every
     * heading they were longer than several of the answers under them.
     */
    private static function shortly(): string
    {
        $root = Instance::describe()['root'] ?? null;

        return $root === null ? 'no installation' : self::root((string) $root)[0];
    }

    /** The same root, as the sentence that says what it is. */
    private static function describeRoot(string $root): string
    {
        return self::root($root)[1];
    }

    /**
     * A recorded root, named by what it is rather than by where it sits: what a
     * heading calls it, and what a sentence says it is.
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
     *
     * @return array{0: string, 1: string}
     */
    private static function root(string $root): array
    {
        $root = (string) (realpath($root) ?: $root);

        $fixture = (string) realpath(Fixture::root());
        if ($fixture !== '' && $root === $fixture) {
            return ['the fixture installation', 'the installation this repository writes below .fixtures/'];
        }

        $checkout = (string) realpath(CoreFixture::root());
        if ($checkout !== '' && $root === $checkout) {
            return ['the fixture core checkout', 'the core checkout this repository writes below .fixtures/'];
        }

        $checkouts = (string) realpath(Checkouts::directory());
        if ($checkouts !== '' && str_starts_with($root, $checkouts)) {
            $line = trim(substr($root, strlen($checkouts)), '/');

            return ['the ' . $line . ' core checkout', 'the ' . $line . ' core checkout below .checkouts/'];
        }

        $environments = (string) realpath(Environments::directory());
        if ($environments !== '' && str_starts_with($root, $environments)) {
            $name = strtoupper(basename($root));

            return ['the ' . $name . ' environment', 'the ' . $name . ' this repository makes below .environments/'];
        }

        return ['an outside installation', 'an installation outside this repository'];
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
        // A recorded answer carries whatever the tool said, fences and
        // directives included, and none of it can end the block: the content of
        // a directive is what is indented under it, so there is no closing
        // marker for the content to imitate. Counting backtick runs to outrun
        // the longest was what the markdown this replaced needed — `D-DOC-029`.
        return Rst::code($language === '' ? 'text' : $language, $content);
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
