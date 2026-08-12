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
 * What is published is `documentation/` and nothing besides, so the front page
 * is that directory's own page — `D-DOC-026`. The repository's `readme.md` is
 * the landing page of the checkout and stays out of the site, because one file
 * serving both places is what put the promise paragraphs where no section of
 * the manual could hold them.
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

    /**
     * The renderer's own configuration, which sits with the corpus it
     * configures and is the one file in there that is not a page —
     * `D-DOC-027`.
     */
    private const CONFIG = 'guides.xml';

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

    /** The branch a link leaving the published tree points into. */
    private const BRANCH = 'main';

    /** Where the drawings sit, in the checkout and in the published site alike. */
    public const DRAWINGS = 'images';

    /** What the dark half of a drawing is called, beside the light one. */
    public const DARK = '-dark.svg';

    /**
     * The two steps of a render that are not this process, as the commands a
     * person could have typed.
     *
     * `-c` names the directory the renderer reads `guides.xml` from, and every
     * other path stays relative to the working directory — the `input` and
     * `output` that file declares, the renderer itself, and the finish step. So
     * both are run at the root of this checkout rather than wherever the caller
     * stands.
     */
    public const RENDER = ['build/guides/vendor/bin/guides', '--no-progress', '-c', self::SOURCE];
    private const FINISH = 'build/guides/vendor/typo3/soul-guides-theme/resources/dist/soul-finish.js';

    private static ?string $repository = null;

    /**
     * The second half of a render, over the pages the first half wrote.
     *
     * The theme ships it as one bundled file: it copies the stylesheet, the
     * script and the faces to the site root, draws every element on every page
     * ahead of the browser so a reader with no script still reads them, and
     * writes the index the search bar fetches. All three used to be this
     * repository's, and `D-DOC-024` is what handing them over rests on.
     *
     * @return list<string>
     */
    public static function finish(string $site): array
    {
        return ['node', self::FINISH, $site];
    }

    /**
     * The dark twin of every drawing the render published.
     *
     * A drawing ships as two files — the dark one a straight token swap of the
     * light one — and the renderer copies an image a page names and nothing
     * else. No page names the twin, so it has to be put beside the one that was
     * named.
     *
     * Nothing on the published page asks for it today: the script that swapped
     * the two was this repository's, and the theme renders a Markdown image as
     * a plain `<img>`, which is a document of its own and cannot be told which
     * mode the page is in. The twin is published anyway, because it is the dark
     * half of the drawing and the file that would otherwise have to be drawn
     * again — `D-DOC-024` is what is open about it.
     *
     * @return list<string> the names written
     */
    public static function publishDrawings(string $site): array
    {
        $target = (str_starts_with($site, '/') ? $site : Paths::root() . '/' . $site) . '/' . self::DRAWINGS;
        if (!is_dir($target)) {
            return [];
        }

        $written = [];
        foreach (Finder::create()->files()->in($target)->name('*.svg')->notName('*' . self::DARK)->sortByName() as $drawing) {
            $twin = Paths::root() . '/' . self::SOURCE . '/' . self::DRAWINGS . '/'
                . str_replace('.svg', self::DARK, $drawing->getFilename());
            if (!is_file($twin)) {
                continue;
            }
            copy($twin, $target . '/' . basename($twin));
            $written[] = basename($twin);
        }

        return $written;
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
        $sources = [];
        $files = Finder::create()->files()->in(Paths::root() . '/' . self::SOURCE)->notName(self::CONFIG)->sortByName();
        foreach ($files as $file) {
            $sources[] = self::SOURCE . '/' . str_replace('\\', '/', $file->getRelativePathname());
        }

        return $sources;
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
        return $path === self::SOURCE || str_starts_with($path, self::SOURCE . '/');
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
        if ($path === self::SOURCE) {
            return self::PUBLISHED_PAGE;
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
     * commit, as the command that installs it — only where it is missing.
     *
     * A gitignored build input rather than a dependency of this package, so a
     * fresh checkout has none of it and nothing else would say so: the renderer
     * and the theme it brings with it are absent as a missing file.
     *
     * @return array<string, list<string>> the command, by what it installs
     */
    public static function installs(): array
    {
        if (is_file(Paths::root() . '/' . self::RENDER[0])) {
            return [];
        }

        return ['the renderer and its theme' => ['composer', 'install', '--working-dir=build/guides', '--no-interaction']];
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
