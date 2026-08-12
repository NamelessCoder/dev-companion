<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Upkeep\Links;
use TYPO3\DevCompanion\Upkeep\Site;

/**
 * What the published copy of `documentation/` owes a reader who has no checkout.
 *
 * The corpus is the real one, because the defect this guards is a property of
 * these pages: they were written against a tree the site does not carry, and a
 * fixture of two invented files would say nothing about the hundred-odd links
 * that actually leave it.
 */
final class SiteTest extends TestCase
{
    /** `R-COD-003`: the copy is written where nothing keeps it. */
    private string $target = '';

    protected function setUp(): void
    {
        $this->target = sys_get_temp_dir() . '/dev-companion-site-' . getmypid();
    }

    protected function tearDown(): void
    {
        Directory::remove($this->target);
    }

    /**
     * The whole point of the copy. A link into `decisions/`, `requirements/`,
     * `todo/`, `src/` or `AGENTS.md` is a path the site does not serve, and it
     * is not a dead link on the site because it is not a relative link there at
     * all.
     */
    #[Test]
    public function noPublishedPageKeepsALinkToAFileTheSiteDoesNotCarry(): void
    {
        Site::build($this->target);

        $leaving = [];
        foreach ($this->published() as $file => $rst) {
            Links::rewritten($rst, static function (string $target) use ($file, &$leaving): string {
                if (self::escapes(dirname($file), (string) strtok($target, '#'))) {
                    $leaving[] = $file . ' still points at ' . $target;
                }

                return $target;
            });
        }

        self::assertSame([], $leaving);
    }

    /**
     * Whether a link written in one directory of the copy lands outside it,
     * spelled out rather than asked of `Site`: what is held here is where the
     * links point, and a resolver borrowed from the code under test would
     * assert that the code agrees with itself.
     */
    private static function escapes(string $directory, string $path): bool
    {
        if (str_starts_with($path, '/')) {
            return true;
        }

        $depth = 0;
        foreach (explode('/', ($directory === '.' ? '' : $directory . '/') . $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            $depth += $segment === '..' ? -1 : 1;
            if ($depth < 0) {
                return true;
            }
        }

        return false;
    }

    /** And every relative link that stayed is a file the copy has. */
    #[Test]
    public function everyLinkThePublishedCopyKeepsResolvesInsideIt(): void
    {
        Site::build($this->target);

        $dead = [];
        foreach (array_keys($this->published()) as $file) {
            foreach (Links::deadIn($this->target . '/' . $file) as $link) {
                $dead[] = $file . ' links to ' . $link['link'];
            }
        }

        self::assertSame([], $dead);
    }

    /** A link that left is the file on GitHub, on the branch the site is built from. */
    #[Test]
    public function aLinkOutOfTheTreeBecomesTheFileInTheRepository(): void
    {
        $published = Site::page(
            'documentation/server/versions.rst',
            'The rule is in `AGENTS.md <../../AGENTS.md>`_, and the runs are in `the runs <../../scenarios/runs/>`_.',
        );

        self::assertStringContainsString(Site::repository() . '/blob/main/AGENTS.md', $published);
        // A directory and a file are two different paths there.
        self::assertStringContainsString(Site::repository() . '/tree/main/scenarios/runs', $published);
    }

    /** What a heading on such a file is called survives the rewrite. */
    #[Test]
    public function aHeadingNamedOnALinkThatLeftIsKept(): void
    {
        self::assertStringEndsWith(
            '/blob/main/AGENTS.md#prose>`_',
            Site::page('documentation/contributing/glossary.rst', 'It is in `AGENTS.md <../../AGENTS.md#prose>`_'),
        );
    }

    /**
     * A reference into another page of the corpus resolves, which is what the
     * move to reStructuredText bought — `D-DOC-029`.
     *
     * Markdown could not do this. A link naming a heading in another page was
     * discarded whole by the generator, text and all, so `Site` dropped the
     * fragment and landed the reader at the top of the page instead. What
     * replaces that is a label the renderer resolves and fails loudly on, and
     * what fails loudly in a checkout with no renderer is this.
     */
    #[Test]
    public function everyReferenceIntoAnotherPageIsAnsweredByALabel(): void
    {
        $dead = [];
        foreach (Links::deadLabels() as $link) {
            $dead[] = $link['file'] . ':' . $link['line'] . ' → ' . $link['link'];
        }

        self::assertSame([], $dead);
    }

    /**
     * And a reference is left exactly as written, because the renderer is what
     * resolves it. `Site` rewrites the links that leave the tree and nothing
     * else, which is the whole of what it does to a page now.
     */
    #[Test]
    public function aReferenceInsideTheCorpusIsNotRewritten(): void
    {
        $written = 'Answers from :ref:`packages <answer-sources-packages>` and :doc:`versions <../versions>`.';

        self::assertSame(
            $written,
            Site::page('documentation/server/tools/typo3_icon_lookup.rst', $written),
        );
    }

    /**
     * A directory's own page is `readme.rst` here and `index.rst` there,
     * because a generator publishes the second as the directory itself. The
     * links naming it already say `index`, since that is the name the renderer
     * resolves a `:doc:` against.
     */
    #[Test]
    public function aDirectorysOwnPageIsPublishedAsItsIndex(): void
    {
        Site::build($this->target);

        self::assertFileExists($this->target . '/server/tools/index.rst');
        self::assertFileDoesNotExist($this->target . '/readme.rst');
        self::assertFileDoesNotExist($this->target . '/server/tools/readme.rst');
        self::assertStringContainsString(
            ':doc:`Records <records/index>`',
            (string) file_get_contents($this->target . '/index.rst'),
        );
    }

    /**
     * `D-DOC-026`: the site is `documentation/` and nothing besides, so it opens
     * on that directory's own page and the checkout's readme is a file the site
     * does not carry.
     */
    #[Test]
    public function theSiteOpensOnTheDocumentationsOwnPage(): void
    {
        Site::build($this->target);

        self::assertStringStartsWith(
            "TYPO3 Dev Companion\n===================",
            (string) file_get_contents($this->target . '/index.rst'),
        );
        self::assertFileDoesNotExist($this->target . '/how-the-work-is-done.rst');
        self::assertStringContainsString(
            Site::repository() . '/blob/main/readme.md',
            Site::page('documentation/usage/installing.rst', 'The `readme <../../readme.md>`_ has the short version.'),
        );
    }

    /**
     * `D-DOC-027`: the renderer's own configuration sits with the pages and is
     * not one of them. Published, it would land in the input directory it
     * declares.
     */
    #[Test]
    public function theRenderersConfigurationIsNotPublished(): void
    {
        Site::build($this->target);

        self::assertFileExists(Paths::root() . '/' . Site::SOURCE . '/guides.xml');
        self::assertFileDoesNotExist($this->target . '/guides.xml');
    }

    /** An external link is nobody's to rewrite. */
    #[Test]
    public function anExternalLinkIsLeftAsItWasWritten(): void
    {
        $written = 'See `the manual <https://docs.typo3.org/>`_ and :ref:`below <glossary-what-it-is>`.';

        self::assertSame($written, Site::page('documentation/contributing/glossary.rst', $written));
    }

    /** The images the pages carry are copied, since a page without them says less. */
    #[Test]
    public function whatIsNotAPageIsCarriedOverUnchanged(): void
    {
        Site::build($this->target);

        $source = Paths::root() . '/' . Site::SOURCE . '/images/system-overview.svg';
        self::assertFileExists($this->target . '/images/system-overview.svg');
        self::assertFileEquals($source, $this->target . '/images/system-overview.svg');
    }

    /** A page that was renamed or deleted stops being served on the next build. */
    #[Test]
    public function aFileTheDocumentationNoLongerHasIsTakenOutOfTheCopy(): void
    {
        Site::build($this->target);
        mkdir($this->target . '/gone');
        file_put_contents($this->target . '/gone/page.rst', "Gone\n====\n");

        $built = Site::build($this->target);

        self::assertSame(['gone/page.rst'], $built['removed']);
        self::assertDirectoryDoesNotExist($this->target . '/gone');
    }

    /**
     * `D-DOC-024`: every directory the site serves has a page of its own.
     *
     * The rail and the trail are built from the directories, and a page whose
     * directory has no `readme.rst` is attached to nothing: the renderer says so
     * as a warning nobody reads and the page is then in no menu at all. Six of
     * them were unreachable that way.
     */
    #[Test]
    public function everyDirectoryOfTheDocumentationHasItsOwnPage(): void
    {
        $source = Paths::root() . '/' . Site::SOURCE;
        $without = [];
        foreach (Finder::create()->directories()->in($source)->sortByName() as $directory) {
            if (!Finder::create()->files()->in($directory->getPathname())->name('*.rst')->hasResults()) {
                continue;
            }
            if (!is_file($directory->getPathname() . '/readme.rst')) {
                $without[] = $directory->getRelativePathname();
            }
        }

        self::assertSame([], $without);
    }

    /**
     * `D-DOC-023`: no drawing sets type below the floor.
     *
     * 13px at drawn size is the system's, and a drawing is shown at two
     * thirds of that size in the column it sits in. Two of them carried 12px
     * labels, which nothing but a measurement would have caught.
     */
    #[Test]
    public function noDrawingSetsTypeBelowTheFloor(): void
    {
        $drawings = Paths::root() . '/' . Site::SOURCE . '/' . Site::DRAWINGS;
        $small = [];
        foreach (Finder::create()->files()->in($drawings)->name('*.svg')->sortByName() as $drawing) {
            preg_match_all('/font-size="([\d.]+)"/', (string) file_get_contents($drawing->getPathname()), $sizes);
            foreach ($sizes[1] as $size) {
                if ((float) $size < 13) {
                    $small[] = $drawing->getFilename() . ' sets ' . $size . 'px';
                }
            }
        }

        self::assertSame([], array_values(array_unique($small)));
    }

    /**
     * Every page of the copy, by the name it carries there.
     *
     * @return array<string, string>
     */
    private function published(): array
    {
        $pages = [];
        foreach (Finder::create()->files()->in($this->target)->name('*.rst')->sortByName() as $file) {
            $pages[str_replace('\\', '/', $file->getRelativePathname())] = (string) file_get_contents($file->getPathname());
        }

        self::assertNotSame([], $pages);

        return $pages;
    }
}
