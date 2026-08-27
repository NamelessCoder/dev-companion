<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Installation\Instance;

/**
 * The code a report's own text names, placed in the packages the installation
 * ships.
 *
 * A stale issue's status is untouched by definition, so nothing on the tracker
 * says that a 2015 report is about code that is gone. What says it is the code
 * the report names, and a page of 25 stale Bugs read on 2026-08-27 names it in
 * five shapes: a namespace with the leading backslash, without it and doubled
 * inside a PHP string, a `Class::method`, a core file path out of a pasted
 * stack trace, and a bare class name — which for five of those rows is the only
 * handle there is (`D-ANS-122`).
 *
 * What comes back is where a name stands and never whether the defect
 * reproduces. A name this cannot place is unplaced rather than gone: a wrong
 * "gone" discards a valid candidate unread, which costs more than the hand
 * reading it replaces. Which is also why a bare name is taken only where the
 * report marks it as code or an installed package ships one — a capitalised
 * word is a class as often as it is the label of a button.
 */
final class CitedCode
{
    /** A class named with its namespace, which places it without guessing. */
    public const QUALIFIED = 'qualified';

    /** A class named without one — the weak handle, and often the only one. */
    public const UNQUALIFIED = 'unqualified';

    /** A file of the core tree, as a stack trace pastes it. */
    public const FILE = 'file';

    /** An installed package ships it, and the method named on it where one was. */
    public const SHIPPED = 'shipped';

    /** No installed package ships it, which core having removed it and an extension nobody installed both look like. */
    public const NOT_SHIPPED = 'notShipped';

    /** Nothing here could place it: no installed package owns the namespace, or there is no installation at all. */
    public const UNPLACED = 'unplaced';

    /**
     * The most names one report is read for.
     *
     * A pasted stack trace names one file per frame, and what a triage reads is
     * the first few. The strong handles are kept first, so the cut falls on the
     * bare names.
     */
    private const MOST = 20;

    /** Where a package keeps the classes its namespace maps onto. */
    private const CLASSES = 'Classes';

    /**
     * A class named with its namespace, and the member named on it.
     *
     * Three segments at least: `Vendor\Extension\Class` is the shortest name
     * that carries a package, and two segments carry none.
     */
    private const QUALIFIED_NAMES = '~(?<![\w\\\\])\\\\?(?P<class>[A-Z][A-Za-z0-9_]*(?:\\\\[A-Z][A-Za-z0-9_]*){2,})(?:(?:::|->)(?P<member>[A-Za-z_][A-Za-z0-9_]*))?~';

    /**
     * A member named on a class the report did not qualify.
     *
     * One hump is enough here, where a bare name needs two: `Result::forProperty`
     * is a call and `Result` on its own is an English word.
     */
    private const CALLS = '~(?<![\w\\\\])(?P<class>[A-Z][A-Za-z0-9_]*)(?:::|->)(?P<member>[A-Za-z_][A-Za-z0-9_]*)~';

    /**
     * A bare class name: a capital, a lowercase run, and a further capital.
     *
     * Two humps rather than one, because a sentence starts with a capitalised
     * word and this runs over prose. What it costs is a one-word class name —
     * `Example`, which the same reading found belonging to an example extension
     * and matching two core test fixtures. What this shape alone does not
     * settle is whether the word is code at all, which is `cued()`'s half.
     */
    private const BARE_NAMES = '~(?<![\w\\\\$])(?P<class>[A-Z][a-z0-9]+(?:[A-Z][A-Za-z0-9]*)+)(?![\w\\\\])~';

    /** A core file, wherever a stack trace's absolute prefix starts. */
    private const FILES = '~typo3/sysext/(?P<extension>[a-z0-9_]+)/(?P<path>[A-Za-z0-9_./-]+\.[A-Za-z]{1,5})~';

    /**
     * The product names that are spelled the way a class is.
     *
     * Everything else the bare-name pattern picks out of prose is answered as
     * not shipped, which is true of it. These would be too, and they are the
     * ones a TYPO3 bug report writes often enough to be noise.
     */
    private const NOT_A_CLASS = [
        'MySQL',
        'MariaDB',
        'PostgreSQL',
        'JavaScript',
        'TypeScript',
        'TypoScript',
        'GitHub',
        'GitLab',
        'PhpStorm',
    ];

    /**
     * Every class file the installed packages ship, by the name of its file.
     *
     * @var array<string, list<array{extension: string, path: string}>>
     */
    private static array $index = [];

    /** The installation that index was built from, so it is never carried into another. */
    private static ?string $indexed = null;

    /**
     * The code these texts name, each with where it stands.
     *
     * @return list<array{name: string, kind: string, method: string, state: string, in: list<array{extension: string, path: string}>}>
     */
    public static function in(string ...$texts): array
    {
        $packages = Instance::packages();
        $placed = [];
        foreach (self::named(implode("\n", $texts)) as $cited) {
            $cue = $cited['cue'];
            unset($cited['cue']);
            // Nothing to place a name against is not a name that is gone.
            $stands = $packages === []
                ? ['state' => self::UNPLACED, 'in' => []]
                : self::stands($cited, $packages);
            // A word the report neither marks as a class nor an installed
            // package ships is as often prose: the button list of one RTE
            // configuration read on 2026-08-27 is eleven of them, and reporting
            // those as not shipped is a verdict about English.
            if (!$cue && $stands['state'] !== self::SHIPPED) {
                continue;
            }
            $placed[] = $cited + $stands;
        }

        return array_slice($placed, 0, self::MOST);
    }

    /**
     * The names the text carries, strongest handle first and each of them once.
     *
     * `cue` is whether the text itself says the name is code. A namespace, a
     * call and a file path say so by their shape; a bare name says so by the
     * markup or the word around it, and where it says nothing the name stands
     * only if an installed package ships one.
     *
     * @return list<array{name: string, kind: string, method: string, cue: bool}>
     */
    private static function named(string $text): array
    {
        $cued = self::cued($text);
        $text = self::readable($text);

        $named = [];
        preg_match_all(self::FILES, $text, $files, PREG_SET_ORDER);
        foreach ($files as $file) {
            self::add($named, 'typo3/sysext/' . $file['extension'] . '/' . $file['path'], self::FILE, '', true);
        }
        preg_match_all(self::QUALIFIED_NAMES, $text, $qualified, PREG_SET_ORDER);
        foreach ($qualified as $class) {
            self::add($named, $class['class'], self::QUALIFIED, self::method($class['member'] ?? ''), true);
        }
        preg_match_all(self::CALLS, $text, $calls, PREG_SET_ORDER);
        foreach ($calls as $call) {
            self::add($named, $call['class'], self::UNQUALIFIED, self::method($call['member']), true);
        }

        // The bare names last and against what is already there, because the
        // same class is regularly written out once and referred to by its short
        // name afterwards.
        $carried = [];
        foreach ($named as $entry) {
            $segments = explode('\\', basename($entry['name'], '.php'));
            $carried[strtolower((string) end($segments))] = true;
        }
        $bare = [];
        preg_match_all(self::BARE_NAMES, $text, $names, PREG_SET_ORDER);
        foreach ($names as $name) {
            if (!isset($carried[strtolower($name['class'])]) && !in_array($name['class'], self::NOT_A_CLASS, true)) {
                self::add($bare, $name['class'], self::UNQUALIFIED, '', isset($cued[strtolower($name['class'])]));
            }
        }

        return [...array_values($named), ...array_values($bare)];
    }

    /**
     * The bare names the report itself says are code, by the name they are
     * written under.
     *
     * Two ways it says it, and both are in the page read on 2026-08-27: the
     * tracker's own code markup, and the word that names what the thing is —
     * "the DatabaseConnection class" in #72962, "Extbase RequestBuilder class"
     * in #82033.
     *
     * @return array<string, true>
     */
    private static function cued(string $text): array
    {
        $cued = [];
        preg_match_all('~@([^@\n]{1,200})@~', $text, $code);
        foreach ($code[1] as $fragment) {
            preg_match_all('~[A-Z][A-Za-z0-9_]*~', $fragment, $names);
            foreach ($names[0] as $name) {
                $cued[strtolower($name)] = true;
            }
        }

        preg_match_all(
            '~(?:\bnew\s+|\bclass\s+)([A-Z][A-Za-z0-9_]*)|([A-Z][A-Za-z0-9_]*)\s+class\b~',
            $text,
            $said,
            PREG_SET_ORDER,
        );
        foreach ($said as $one) {
            $name = ($one[1] ?? '') !== '' ? $one[1] : ($one[2] ?? '');
            if ($name !== '') {
                $cued[strtolower($name)] = true;
            }
        }

        return $cued;
    }

    /**
     * The text with the tracker's own markup off the names.
     *
     * Redmine glues it to the token — `@PropertyMappingConfiguration@`,
     * `_TcaColumnsOverrides_` — and an underscore is a word character, so a
     * boundary never falls where the name starts.
     */
    private static function readable(string $text): string
    {
        $text = (string) preg_replace('~@([^@\n]{1,200})@~', ' $1 ', $text);
        $text = (string) preg_replace('~(?<!\w)_([^_\n]{1,200})_(?!\w)~', ' $1 ', $text);

        // A namespace written inside a PHP string carries its backslashes
        // doubled, which is the same name and no other.
        return str_replace('\\\\', '\\', $text);
    }

    /**
     * The method a member names, and nothing where the member is not one.
     *
     * `Foo::class` names the class and `Foo::CONSTANT` a constant. Looking for
     * either in the class file would report a name that is there as gone.
     */
    private static function method(string $member): string
    {
        return $member === 'class' || preg_match('~^[A-Z][A-Z0-9_]*$~', $member) === 1 ? '' : $member;
    }

    /**
     * @param array<string, array{name: string, kind: string, method: string, cue: bool}> $named
     */
    private static function add(array &$named, string $name, string $kind, string $method, bool $cue): void
    {
        $name = ltrim($name, '\\');
        $named[$kind . '|' . $name . '|' . $method] ??= [
            'name' => $name,
            'kind' => $kind,
            'method' => $method,
            'cue' => $cue,
        ];
    }

    /**
     * Where one name stands in the installed packages.
     *
     * @param array{name: string, kind: string, method: string} $cited
     * @param array<string, string>                             $packages
     * @return array{state: string, in: list<array{extension: string, path: string}>}
     */
    private static function stands(array $cited, array $packages): array
    {
        $in = match ($cited['kind']) {
            self::FILE => self::shippedFile($cited['name'], $packages),
            self::QUALIFIED => self::shippedClass($cited['name'], $packages),
            default => self::index($packages)[strtolower($cited['name'])] ?? [],
        };
        if ($in === null) {
            return ['state' => self::UNPLACED, 'in' => []];
        }
        if ($in === []) {
            return ['state' => self::NOT_SHIPPED, 'in' => []];
        }
        // The class stands and the method named on it does not, which is a
        // verdict of its own: the state is about the name that was cited and
        // `in` says where the class it sat on is.
        if ($cited['method'] !== '' && !self::declares($in, $cited['method'])) {
            return ['state' => self::NOT_SHIPPED, 'in' => $in];
        }

        return ['state' => self::SHIPPED, 'in' => $in];
    }

    /**
     * Where a qualified class sits, empty where its own package does not ship
     * it, and null where no installed package owns the namespace at all.
     *
     * @param array<string, string> $packages
     * @return list<array{extension: string, path: string}>|null
     */
    private static function shippedClass(string $class, array $packages): ?array
    {
        $segments = explode('\\', $class);
        // TYPO3's own packages carry the vendor and the product in front of the
        // extension: `TYPO3\CMS\Core` is `core`, where `BK2K\BootstrapPackage`
        // is `bootstrap_package`.
        $at = $segments[0] === 'TYPO3' && ($segments[1] ?? '') === 'CMS' ? 2 : 1;
        $key = self::extensionKey($segments[$at] ?? '');
        $below = array_slice($segments, $at + 1);
        if ($below === [] || !isset($packages[$key])) {
            return null;
        }

        $file = $packages[$key] . '/' . self::CLASSES . '/' . implode('/', $below) . '.php';
        if (is_file($file)) {
            return [['extension' => $key, 'path' => self::relative($file)]];
        }

        // Where a package puts a namespace is the package's own to say — a test
        // class sits beside `Classes/` rather than below it — so a path that
        // missed is asked of that package's files before the name is reported
        // as absent from it.
        return array_values(array_filter(
            self::index($packages)[strtolower((string) end($segments))] ?? [],
            static fn(array $where): bool => $where['extension'] === $key,
        ));
    }

    /**
     * Where a core file sits, empty where its package does not ship it, and
     * null where the installation has no such package.
     *
     * @param array<string, string> $packages
     * @return list<array{extension: string, path: string}>|null
     */
    private static function shippedFile(string $cited, array $packages): ?array
    {
        if (preg_match('~^typo3/sysext/([a-z0-9_]+)/(.+)$~', $cited, $matched) !== 1) {
            return null;
        }
        $package = $packages[$matched[1]] ?? null;
        if ($package === null) {
            return null;
        }

        return is_file($package . '/' . $matched[2])
            ? [['extension' => $matched[1], 'path' => self::relative($package . '/' . $matched[2])]]
            : [];
    }

    /**
     * Whether one of these files declares that method.
     *
     * @param list<array{extension: string, path: string}> $in
     */
    private static function declares(array $in, string $method): bool
    {
        $root = Instance::root();
        foreach ($in as $where) {
            $file = $root === null ? $where['path'] : $root . '/' . $where['path'];
            if (!is_file($file)) {
                continue;
            }
            $pattern = '~\bfunction\s+' . preg_quote($method, '~') . '\s*\(~i';
            if (preg_match($pattern, (string) file_get_contents($file)) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Every class file below the packages, by the name of its file.
     *
     * This is what places a bare name at all, and it is built once per
     * installation and only where a bare name was cited. A file name matching
     * two packages names both: picking one of them is where a right-looking
     * verdict lands on the wrong class.
     *
     * @param array<string, string> $packages
     * @return array<string, list<array{extension: string, path: string}>>
     */
    private static function index(array $packages): array
    {
        $root = (string) Instance::root();
        if (self::$indexed === $root) {
            return self::$index;
        }

        $index = [];
        foreach ($packages as $key => $path) {
            if (!is_dir($path . '/' . self::CLASSES)) {
                continue;
            }
            foreach (Finder::create()->files()->in($path . '/' . self::CLASSES)->name('*.php')->sortByName() as $file) {
                $index[strtolower($file->getBasename('.php'))][] = [
                    'extension' => $key,
                    'path' => self::relative($file->getPathname()),
                ];
            }
        }
        self::$indexed = $root;

        return self::$index = $index;
    }

    /** The extension key a namespace segment is: `IndexedSearch` is `indexed_search`. */
    private static function extensionKey(string $segment): string
    {
        return strtolower((string) preg_replace('~(?<!^)[A-Z]~', '_$0', $segment));
    }

    /** Where the file is, from the installation root a caller is standing in. */
    private static function relative(string $file): string
    {
        $root = str_replace('\\', '/', (string) Instance::root());
        $path = str_replace('\\', '/', $file);

        return $root !== '' && str_starts_with($path, $root . '/') ? substr($path, strlen($root) + 1) : $path;
    }
}
