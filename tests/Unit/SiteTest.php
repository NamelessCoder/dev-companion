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
 * fixture of two invented files would say nothing about the 87 links that
 * actually leave it.
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
        Site::useBuilt(null);
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
        foreach ($this->published() as $file => $markdown) {
            Links::rewritten($markdown, static function (string $target) use ($file, &$leaving): string {
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
            'documentation/knowledge/versions.md',
            'The rule is in [AGENTS.md](../../AGENTS.md), and the runs are in [scenarios/runs/](../../scenarios/runs/).',
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
            '/blob/main/AGENTS.md#prose)',
            Site::page('documentation/glossary.md', 'It is in [AGENTS.md](../AGENTS.md#prose)'),
        );
    }

    /**
     * And the heading named on a link that stayed is dropped, because the
     * generator resolves one inside the page it is on and none in another page.
     * What it did with the 33 links into `answer-sources.md` was discard each
     * one whole — the text stayed and the link was gone, on pages nothing here
     * re-reads. Landing the reader on the page is what is kept instead.
     */
    #[Test]
    public function noPublishedLinkNamesAHeadingInAnotherPage(): void
    {
        Site::build($this->target);

        $carrying = [];
        foreach ($this->published() as $file => $markdown) {
            Links::rewritten($markdown, static function (string $target) use ($file, &$carrying): string {
                if (!str_starts_with($target, 'http') && str_contains($target, '#')) {
                    $carrying[] = $file . ' names a heading in ' . $target;
                }

                return $target;
            });
        }

        self::assertSame([], $carrying);
        self::assertStringEndsWith(
            '](answer-sources.md)',
            Site::page('documentation/tools/typo3_icon_lookup.md', 'Answers from [`packages`](answer-sources.md#packages)'),
        );
    }

    /**
     * A directory's own page is `readme.md` here and `index.md` there, in the
     * file name and in every link naming it, because a generator publishes the
     * second as the directory itself.
     */
    #[Test]
    public function aDirectorysOwnPageIsPublishedAsItsIndex(): void
    {
        Site::build($this->target);

        self::assertFileExists($this->target . '/tools/index.md');
        self::assertFileDoesNotExist($this->target . '/readme.md');
        self::assertFileDoesNotExist($this->target . '/tools/readme.md');
        self::assertStringContainsString(
            '](feedback/index.md)',
            (string) file_get_contents($this->target . '/how-the-work-is-done.md'),
        );
    }

    /**
     * `D-DOC-018`: what the site opens on is the readme a visitor of the
     * repository lands on too, and the map of `documentation/` is a page below
     * it rather than the front one.
     */
    #[Test]
    public function theSiteOpensOnTheReadmeAndNotOnTheMap(): void
    {
        Site::build($this->target);

        $front = (string) file_get_contents($this->target . '/index.md');
        self::assertStringStartsWith('# TYPO3 Dev Companion', $front);
        self::assertFileExists($this->target . '/how-the-work-is-done.md');
    }

    /**
     * The front page sits a directory above the tree the rest of the site is,
     * so a link it wrote against the checkout names one segment the site does
     * not serve.
     */
    #[Test]
    public function aLinkOnTheFrontPageDropsTheDirectoryTheSiteServesAtItsRoot(): void
    {
        $published = Site::page(
            Site::FRONT,
            'Installing is [here](documentation/clients/installing.md), and the map is'
            . ' [there](documentation/readme.md).',
        );

        self::assertStringContainsString('](clients/installing.md)', $published);
        self::assertStringContainsString('](how-the-work-is-done.md)', $published);
    }

    /** And a page below it reaches the front page by climbing out of its own directory. */
    #[Test]
    public function aPageBelowTheFrontOneReachesItAsTheIndex(): void
    {
        self::assertStringContainsString(
            '](../index.md)',
            Site::page('documentation/feedback/readme.md', 'What it is: [readme](../../readme.md).'),
        );
    }

    /**
     * `D-DOC-019`: the two built files are copied beside the pages, and the
     * manifest that named them to the layout stays behind.
     */
    #[Test]
    public function theBuiltAssetsAreCopiedBesideThePagesAndTheManifestIsNot(): void
    {
        $built = $this->target . '-built';
        mkdir($built, 0777, true);
        file_put_contents($built . '/site.1234abcd.css', 'body{}');
        file_put_contents($built . '/site.5678ef90.js', '(function(){})();');
        file_put_contents($built . '/manifest.txt', "site.1234abcd.css\nsite.5678ef90.js\n");
        Site::useBuilt($built);

        try {
            $published = Site::publishAssets($this->target);

            self::assertSame(['site.1234abcd.css', 'site.5678ef90.js'], $published);
            self::assertFileExists($this->target . '/' . Site::ASSETS . '/site.1234abcd.css');
            self::assertFileExists($this->target . '/' . Site::ASSETS . '/site.5678ef90.js');
            // It says what the two are called and no page asks for it.
            self::assertFileDoesNotExist($this->target . '/' . Site::ASSETS . '/manifest.txt');
        } finally {
            Directory::remove($built);
        }
    }

    /** Nothing built is a site served unstyled, so it is a failure and not an empty copy. */
    #[Test]
    public function nothingBuiltIsReportedRatherThanCopied(): void
    {
        Site::useBuilt($this->target . '-absent');

        self::assertSame([], Site::publishAssets($this->target));
    }

    /** An external link is nobody's to rewrite. */
    #[Test]
    public function anExternalLinkIsLeftAsItWasWritten(): void
    {
        $written = 'See [the manual](https://docs.typo3.org/) and [below](#what-it-is).';

        self::assertSame($written, Site::page('documentation/glossary.md', $written));
    }

    /** The images the pages carry are copied, since a page without them says less. */
    #[Test]
    public function whatIsNotMarkdownIsCarriedOverUnchanged(): void
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
        file_put_contents($this->target . '/gone/page.md', "# Gone\n");

        $built = Site::build($this->target);

        self::assertSame(['gone/page.md'], $built['removed']);
        self::assertDirectoryDoesNotExist($this->target . '/gone');
    }

    /**
     * The index is over the pages as they are served, so a hit is a URL rather
     * than a path somebody then has to translate.
     */
    #[Test]
    public function theSearchIndexNamesEveryPageByTheUrlItIsServedAt(): void
    {
        Site::build($this->target);
        $index = Site::search();

        $urls = array_column($index, 'url');
        self::assertContains('index.html', $urls, 'the readme is the site root');
        self::assertContains('how-the-work-is-done.html', $urls);
        self::assertContains('tools/index.html', $urls);
        self::assertContains('tools/typo3_icon_lookup.html', $urls);
        foreach ($urls as $url) {
            self::assertFileExists(
                $this->target . '/' . substr($url, 0, -5) . '.md',
                $url . ' is indexed and not published',
            );
        }
    }

    /**
     * What is left out is the recorded evidence. 582 fenced blocks, most of
     * them a tool answer in JSON, would bury every prose match under them and
     * be most of what a reader downloads to find one.
     */
    #[Test]
    public function theSearchIndexHoldsTheProseAndNotTheRecordedAnswers(): void
    {
        $index = Site::search();

        $recorded = array_values(array_filter(
            $index,
            static fn(array $page): bool => $page['url'] === 'tools/typo3_icon_lookup.html',
        ));

        self::assertNotSame([], $recorded);
        self::assertSame('typo3_icon_lookup', $recorded[0]['title']);
        self::assertNotSame('', $recorded[0]['text']);
        foreach ($index as $page) {
            self::assertStringNotContainsString('```', $page['text'], $page['url'] . ' carries a fenced block');
            self::assertStringNotContainsString('](', $page['text'], $page['url'] . ' carries a link target');
        }
    }

    /**
     * `D-DOC-023`: a drawing ships light and dark, and the twin is published.
     *
     * The renderer copies an image a page names and nothing else, and no page
     * names the twin — the script asks for it once the reader is in dark. A
     * drawing without one is a light canvas on a dark page, which is the
     * defect the two files exist to prevent.
     */
    #[Test]
    public function everyDrawingShipsItsDarkTwinAndThePublishedOneCarriesIt(): void
    {
        $drawings = Paths::root() . '/' . Site::SOURCE . '/' . Site::DRAWINGS;
        $light = [];
        foreach (Finder::create()->files()->in($drawings)->name('*.svg')->notName('*' . Site::DARK)->sortByName() as $drawing) {
            $name = $drawing->getFilename();
            self::assertFileExists(
                $drawings . '/' . str_replace('.svg', Site::DARK, $name),
                $name . ' has no dark twin',
            );
            $light[] = $name;
        }
        self::assertNotSame([], $light);

        // What a render would have put there: one named drawing, and the twin
        // nobody named beside it.
        mkdir($this->target . '/' . Site::DRAWINGS, 0777, true);
        copy($drawings . '/' . $light[0], $this->target . '/' . Site::DRAWINGS . '/' . $light[0]);

        self::assertSame([str_replace('.svg', Site::DARK, $light[0])], Site::publishDrawings($this->target));
    }

    /**
     * `D-DOC-023`: the theme states no colour of its own.
     *
     * The design system's first rule is that a token value is never
     * redeclared locally, and the way that breaks is one hex written into a
     * rule at half past five. The package `site.css` imports is where a value
     * is allowed to stand; everything the site adds names one.
     *
     * `color-mix()` is not one of them: the system mixes tokens itself, and
     * what the header needs — the canvas, translucent — has no token of its
     * own.
     *
     * The scan reads the rules and not the prose above them. The header
     * explains this rule by naming the two values it was broken with, and a
     * comment declares nothing — which is how `51e70499` turned an
     * explanation of the rule into a breach of it.
     */
    #[Test]
    public function theThemeWritesNoColourOfItsOwn(): void
    {
        $theme = (string) file_get_contents(Paths::root() . '/build/guides/theme/assets/site.css');
        $found = preg_match_all(
            '/#[0-9a-fA-F]{3,8}\b|\b(?:rgba?|hsla?|oklch)\(/',
            (string) preg_replace('#/\*.*?\*/#s', '', $theme),
            $matches,
        );

        self::assertSame(0, $found, 'site.css states ' . implode(', ', $matches[0]) . ' rather than a token');
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
        foreach (Finder::create()->files()->in($this->target)->name('*.md')->sortByName() as $file) {
            $pages[str_replace('\\', '/', $file->getRelativePathname())] = (string) file_get_contents($file->getPathname());
        }

        self::assertNotSame([], $pages);

        return $pages;
    }
}
