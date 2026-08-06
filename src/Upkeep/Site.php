<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * `documentation/` written out as the source a site generator publishes.
 *
 * Only that directory is published, and it was written for a reader who has the
 * whole checkout: 87 of its 246 relative links point at a decision, a
 * requirement, a todo or a class that no visitor of the site has. Published as
 * they stand, a third of the cross-references go nowhere. So every link leaving
 * the tree is rewritten here to the file on GitHub, and the sources keep the
 * paths a reader of the checkout follows — which is also what `links:check`
 * goes on reading.
 *
 * The copy is what carries the two changes a generator needs, rather than the
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

    /**
     * Where the copy is written, and what `guides.xml` names as its `input`.
     * Below a gitignored directory, because a build product that is committed
     * is one somebody edits.
     */
    public const TARGET = '.site/source';

    /** What a directory's own page is called here, and what it is published as. */
    private const OWN_PAGE = 'readme.md';
    private const PUBLISHED_PAGE = 'index.md';

    /** The branch a link leaving the published tree points into. */
    private const BRANCH = 'main';

    private static ?string $repository = null;

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
        $files = Finder::create()->files()->in(Paths::root() . '/' . self::SOURCE)->sortByName();
        foreach ($files as $file) {
            $source = str_replace('\\', '/', $file->getRelativePathname());
            $published = self::published($source);
            $contents = (string) file_get_contents($file->getPathname());
            self::write(
                $target . '/' . $published,
                $file->getExtension() === 'md' ? self::page($source, $contents) : $contents,
            );
            $written[$published] = true;
        }

        return ['written' => array_keys($written), 'removed' => self::sweep($target, $written)];
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
        $files = Finder::create()->files()->in(Paths::root() . '/' . self::SOURCE)->name('*.md')->sortByName();
        foreach ($files as $file) {
            $markdown = (string) file_get_contents($file->getPathname());
            $published = self::published(str_replace('\\', '/', $file->getRelativePathname()));

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
        $directory = dirname($file) === '.' ? self::SOURCE : self::SOURCE . '/' . dirname($file);

        return Links::rewritten($markdown, static function (string $target) use ($directory): string {
            $path = strtok($target, '#');
            if ($path === false) {
                return $target;
            }
            $fragment = substr($target, strlen($path));

            $resolved = self::resolve($directory, $path);
            if ($resolved === self::SOURCE || str_starts_with($resolved, self::SOURCE . '/')) {
                // The heading is dropped rather than carried. The generator
                // resolves one inside the page it is on and none in another
                // page, and what it does with `answer-sources.md#packages` is
                // discard the whole reference — text, link and all. Landing the
                // reader on the page is the half of it that survives.
                return self::published($path);
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

    /**
     * What a path inside the published tree is called there. Only the last
     * segment moves, so a link resolved against the directory it was written in
     * still resolves against the same one.
     */
    public static function published(string $path): string
    {
        return (string) preg_replace(
            '#(^|/)' . preg_quote(self::OWN_PAGE, '#') . '$#',
            '$1' . self::PUBLISHED_PAGE,
            $path,
        );
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
