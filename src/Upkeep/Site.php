<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Process\SystemRunner;

/**
 * The pages published, written out as the source a site generator publishes.
 *
 * Two things are published: the repository's own `readme.md`, which is what
 * somebody deciding whether this server is for them reads, and `documentation/`,
 * which is how the work here is carried out. The readme is the front page,
 * because a visitor arriving at the site is a user before they are a
 * contributor — `D-DOC-018`.
 *
 * `documentation/` was written for a reader who has the whole checkout: 87 of
 * its 246 relative links point at a decision, a requirement, a todo or a class
 * that no visitor of the site has. Published as they stand, a third of the
 * cross-references go nowhere. So every link leaving the published pages is
 * rewritten here to the file on GitHub, and the sources keep the paths a reader
 * of the checkout follows — which is also what `links:check` goes on reading.
 *
 * The copy is what carries the changes a generator needs, rather than the
 * sources. `readme.md` is what this repository calls a directory's own page and
 * `index.md` is what a generator publishes as the directory itself, and a
 * rename in place would leave every convention here saying "readme" about a
 * file called something else.
 *
 * A link inside a fenced block is rewritten like any other, because `Links` is
 * where this repository says what a link is and it reads them the same way. The
 * recorded tool answers hold none today.
 */
final class Site
{
    /** The directory published, relative to the root. */
    public const SOURCE = 'documentation';

    /** The page the site opens on, relative to the root. */
    public const FRONT = 'readme.md';

    /**
     * Where the site is built, and what `guides.xml` names as its `input` and
     * its `output`. Gitignored, because a build product that is committed is
     * one somebody edits.
     */
    public const ROOT = '.site';
    public const TARGET = self::ROOT . '/source';
    public const HTML = self::ROOT . '/html';

    /** What a directory's own page is called here, and what it is published as. */
    private const OWN_PAGE = 'readme.md';
    private const PUBLISHED_PAGE = 'index.md';

    /**
     * What the map of `documentation/` is published as, since the front page is
     * the repository's own readme. Its own title, so the sidebar names it the
     * way the page does.
     */
    private const MAP_PAGE = 'how-the-work-is-done.md';

    /** The branch a link leaving the published tree points into. */
    private const BRANCH = 'main';

    /** Where every page links the stylesheet and the script, from the site root. */
    public const ASSETS = 'assets';

    /** What the layout fetches to search over, from the site root. */
    public const SEARCH = 'search.json';

    /**
     * The two steps of a render that are not this process, as the commands a
     * person could have typed.
     *
     * The renderer reads `guides.xml` from the working directory, and both npm
     * invocations name their prefix from it, so every one of them is run at the
     * root of this checkout rather than wherever the caller stands.
     */
    public const BUILD_ASSETS = ['npm', '--prefix', 'build/guides', 'run', 'build'];
    public const RENDER = ['build/guides/vendor/bin/guides', '--no-progress'];

    /** Where `npm run build` writes them, and what the layout reads their names from. */
    private const BUILT = 'build/guides/theme/assets/dist';
    private const MANIFEST = 'manifest.txt';

    private static ?string $repository = null;

    /**
     * Where the built assets are read from, for a caller that has none.
     *
     * `npm run build` writes them and the suite does not run it, so a test
     * holding what is copied hands in a directory of its own rather than
     * skipping itself on a machine where nobody has built anything.
     */
    private static ?string $built = null;

    public static function useBuilt(?string $directory): void
    {
        self::$built = $directory;
    }

    /**
     * Writes the copy, and takes back out of it whatever this no longer writes.
     *
     * Removing the strangers rather than the directory: a build that starts by
     * deleting a path it was handed is one bad argument away from deleting
     * something else.
     *
     * @return array{written: list<string>, removed: list<string>}
     */
    public static function build(string $target): array
    {
        $target = str_starts_with($target, '/') ? $target : Paths::root() . '/' . $target;

        $written = [];
        foreach (self::sources() as $source) {
            $published = self::published($source);
            $contents = (string) file_get_contents(Paths::root() . '/' . $source);
            self::write(
                $target . '/' . $published,
                str_ends_with($source, '.md') ? self::page($source, $contents) : $contents,
            );
            $written[$published] = true;
        }

        return ['written' => array_keys($written), 'removed' => self::sweep($target, $written)];
    }

    /**
     * Every file the site is made of, named as this repository names it.
     *
     * @return list<string>
     */
    private static function sources(): array
    {
        $sources = [self::FRONT];
        foreach (Finder::create()->files()->in(Paths::root() . '/' . self::SOURCE)->sortByName() as $file) {
            $sources[] = self::SOURCE . '/' . str_replace('\\', '/', $file->getRelativePathname());
        }

        return $sources;
    }

    /**
     * The built stylesheet and script, copied beside the pages that link them.
     *
     * The manifest stays behind: it is how the layout learns the two names at
     * render time, and no page asks for it. Nothing is swept here, because the
     * names carry a hash and yesterday's file is what a reader mid-deploy is
     * still asking for.
     *
     * @return list<string> the names written, empty where nothing is built
     */
    public static function publishAssets(string $site): array
    {
        $built = self::$built ?? Paths::root() . '/' . self::BUILT;
        if (!is_dir($built)) {
            return [];
        }

        $target = (str_starts_with($site, '/') ? $site : Paths::root() . '/' . $site) . '/' . self::ASSETS;
        if (!is_dir($target)) {
            mkdir($target, 0777, true);
        }

        $written = [];
        foreach (Finder::create()->files()->in($built)->notName(self::MANIFEST)->sortByName() as $file) {
            copy($file->getPathname(), $target . '/' . $file->getFilename());
            $written[] = $file->getFilename();
        }

        return $written;
    }

    /**
     * The index the site is searched over, written beside the pages it filters.
     *
     * @return array{file: string, pages: int, bytes: int}
     */
    public static function publishSearch(string $site): array
    {
        $index = self::search();
        $file = (str_starts_with($site, '/') ? $site : Paths::root() . '/' . $site) . '/' . self::SEARCH;
        file_put_contents($file, json_encode($index, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        return ['file' => $site . '/' . self::SEARCH, 'pages' => count($index), 'bytes' => (int) filesize($file)];
    }

    /**
     * What the published site is searched over: one entry per page, each with
     * the URL it is served at, its title, its headings and its prose.
     *
     * The renderer offers no search of any kind, and 47 pages of which several
     * run past 60 KB are browsed rather than read. This is the index a page
     * filters in the reader's browser — small enough to fetch once, because
     * what is left out is the half that would dominate it.
     *
     * Fenced blocks are left out. They are 582 of them and most are a recorded
     * tool answer in JSON, so indexing them buries every prose match under the
     * evidence and triples what a reader downloads to find it. What a caller
     * searches an answer by is its tool name, and that is the page title.
     *
     * @return list<array{url: string, title: string, headings: list<string>, text: string}>
     */
    public static function search(): array
    {
        $index = [];
        foreach (self::sources() as $source) {
            if (!str_ends_with($source, '.md')) {
                continue;
            }
            $markdown = (string) file_get_contents(Paths::root() . '/' . $source);
            $published = self::published($source);

            $headings = [];
            if (preg_match_all('/^#{1,6}\s+(.+?)\s*$/m', self::prose($markdown), $found) !== false) {
                $headings = array_map(self::plain(...), $found[1]);
            }

            $index[] = [
                'url' => substr($published, 0, -3) . '.html',
                'title' => array_shift($headings) ?? $published,
                'headings' => $headings,
                'text' => self::plain((string) preg_replace('/^#{1,6}\s+.+?$/m', '', self::prose($markdown))),
            ];
        }

        return $index;
    }

    /** A page with everything a reader does not read as a sentence taken out. */
    private static function prose(string $markdown): string
    {
        return (string) preg_replace('/^ {0,3}```.*?^ {0,3}```/ms', '', $markdown);
    }

    /** Markdown reduced to the words in it, on one line. */
    private static function plain(string $markdown): string
    {
        $text = (string) preg_replace('/!?\[([^\]]*)\]\([^)]*\)/', '$1', $markdown);
        // The underscore stays. It is emphasis in markdown and it is half of
        // every tool name here, and `typo3_icon_lookup` is what somebody
        // searching for that page types.
        $text = (string) preg_replace('/[`*>#|]+/', ' ', $text);

        return trim((string) preg_replace('/\s+/', ' ', $text));
    }

    /**
     * One page as it is published: every link that stays inside the tree kept
     * as it was written, and every link that leaves it turned into the file on
     * GitHub.
     */
    public static function page(string $file, string $markdown): string
    {
        $directory = dirname($file);
        $here = dirname(self::published($file));
        $here = $here === '.' ? '' : $here;

        return Links::rewritten($markdown, static function (string $target) use ($directory, $here): string {
            $path = strtok($target, '#');
            if ($path === false) {
                return $target;
            }
            $fragment = substr($target, strlen($path));

            $resolved = self::resolve($directory, $path);
            if (self::isPublished($resolved)) {
                // The heading is dropped rather than carried. The generator
                // resolves one inside the page it is on and none in another
                // page, and what it does with `answer-sources.md#packages` is
                // discard the whole reference — text, link and all. Landing the
                // reader on the page is the half of it that survives.
                return self::relative($here, self::published($resolved));
            }

            // A directory and a file are two paths on GitHub, and half the
            // entries this documentation points at are directories.
            return sprintf(
                '%s/%s/%s/%s%s',
                self::repository(),
                is_dir(Paths::root() . '/' . $resolved) ? 'tree' : 'blob',
                self::BRANCH,
                $resolved,
                $fragment,
            );
        });
    }

    /** Whether a path in this repository is one the site serves. */
    public static function isPublished(string $path): bool
    {
        return $path === self::FRONT || $path === self::SOURCE || str_starts_with($path, self::SOURCE . '/');
    }

    /**
     * What a path in this repository is called on the site.
     *
     * `documentation/` is served at the root, so a link that was written
     * against the checkout points at a page a segment higher than it reads.
     * Every target is resolved against the repository and named again from
     * there, rather than the last segment being swapped in place.
     */
    public static function published(string $path): string
    {
        if ($path === self::FRONT) {
            return self::PUBLISHED_PAGE;
        }
        if ($path === self::SOURCE || $path === self::SOURCE . '/' . self::OWN_PAGE) {
            return self::MAP_PAGE;
        }

        return (string) preg_replace(
            '#(^|/)' . preg_quote(self::OWN_PAGE, '#') . '$#',
            '$1' . self::PUBLISHED_PAGE,
            substr($path, strlen(self::SOURCE) + 1),
        );
    }

    /** Where one published page points at another, from the directory it is served in. */
    private static function relative(string $from, string $to): string
    {
        $up = $from === '' ? [] : explode('/', $from);
        $down = explode('/', $to);
        while ($up !== [] && count($down) > 1 && $up[0] === $down[0]) {
            array_shift($up);
            array_shift($down);
        }

        return str_repeat('../', count($up)) . implode('/', $down);
    }

    /**
     * What a render needs below `build/guides/` and this repository does not
     * commit, as the command that installs each — only where it is missing.
     *
     * Both are gitignored build inputs rather than dependencies of this
     * package, so a fresh checkout has neither and nothing else would say so:
     * the renderer is absent as a missing file and the theme's packages as an
     * npm error nobody reads as an install step.
     *
     * @return array<string, list<string>> the command, by what it installs
     */
    public static function installs(): array
    {
        $installs = [];
        if (!is_file(Paths::root() . '/' . self::RENDER[0])) {
            $installs['the renderer'] = ['composer', 'install', '--working-dir=build/guides', '--no-interaction'];
        }
        if (!is_dir(Paths::root() . '/build/guides/node_modules')) {
            $installs["the theme's packages"] = ['npm', '--prefix', 'build/guides', 'ci'];
        }

        return $installs;
    }

    /**
     * What a test hands in, so nothing it drives has to exist on the machine.
     *
     * `R-COD-003`: the suite runs neither the renderer nor a package manager,
     * and a case that holds the order the steps go in mocks them rather than
     * waiting minutes for a real install.
     */
    private static ?CommandRunner $runner = null;

    public static function useRunner(?CommandRunner $runner): void
    {
        self::$runner = $runner;
    }

    /**
     * One step of a render, with both its streams as one string.
     *
     * No timeout: the first render on a machine is a `composer install` and an
     * `npm ci`, and a number that would not cut one of those short on a cold
     * cache is not one anybody can name.
     *
     * @param list<string> $command
     *
     * @return array{0: int, 1: string}
     */
    public static function run(array $command): array
    {
        $result = (self::$runner ?? new SystemRunner())->run($command, Paths::root());

        return [$result['exitCode'], $result['output'] . $result['error']];
    }

    /**
     * Where the sources live, said once, in the manifest that already declares
     * the package to everybody else. A repository that moves moves in one line
     * and every link this writes follows it.
     */
    public static function repository(): string
    {
        if (self::$repository !== null) {
            return self::$repository;
        }

        $manifest = json_decode(
            (string) file_get_contents(Paths::root() . '/composer.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $source = is_array($manifest) && is_array($manifest['support'] ?? null) ? $manifest['support']['source'] ?? null : null;
        if (!is_string($source) || $source === '') {
            throw new \RuntimeException('composer.json declares no support.source, so nothing says where the published links point.');
        }

        return self::$repository = rtrim($source, '/');
    }

    /**
     * What stands in the target that this build did not write, taken back out.
     * A page that was renamed or deleted is otherwise served for as long as the
     * directory is kept.
     *
     * @param array<string, true> $written
     *
     * @return list<string>
     */
    private static function sweep(string $target, array $written): array
    {
        $removed = [];
        foreach (Finder::create()->files()->in($target)->ignoreDotFiles(false)->sortByName() as $file) {
            $found = str_replace('\\', '/', $file->getRelativePathname());
            if (!isset($written[$found])) {
                unlink($file->getPathname());
                $removed[] = $found;
            }
        }

        // Deepest first, so a directory emptied by the pass above is gone
        // before the one holding it is looked at.
        $directories = Finder::create()->directories()->in($target)->ignoreDotFiles(false)->reverseSorting();
        foreach ($directories as $directory) {
            if (!Finder::create()->in($directory->getPathname())->ignoreDotFiles(false)->hasResults()) {
                rmdir($directory->getPathname());
            }
        }

        return $removed;
    }

    /** Where a link written in one directory of this repository points, from the root. */
    private static function resolve(string $directory, string $path): string
    {
        $segments = [];
        foreach (explode('/', $directory . '/' . $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                array_pop($segments);
                continue;
            }
            $segments[] = $segment;
        }

        return implode('/', $segments);
    }

    private static function write(string $path, string $contents): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, $contents);
    }
}
