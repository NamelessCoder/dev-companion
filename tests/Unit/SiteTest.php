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
            'knowledge/versions.md',
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
            Site::page('glossary.md', 'It is in [AGENTS.md](../AGENTS.md#prose)'),
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
            Site::page('tools/typo3_icon_lookup.md', 'Answers from [`packages`](answer-sources.md#packages)'),
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

        self::assertFileExists($this->target . '/index.md');
        self::assertFileExists($this->target . '/tools/index.md');
        self::assertFileDoesNotExist($this->target . '/readme.md');
        self::assertFileDoesNotExist($this->target . '/tools/readme.md');
        self::assertStringContainsString(
            '](feedback/index.md)',
            (string) file_get_contents($this->target . '/index.md'),
        );
    }

    /** An external link is nobody's to rewrite. */
    #[Test]
    public function anExternalLinkIsLeftAsItWasWritten(): void
    {
        $written = 'See [the manual](https://docs.typo3.org/) and [below](#what-it-is).';

        self::assertSame($written, Site::page('glossary.md', $written));
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
        self::assertContains('index.html', $urls, 'the map page is the site root');
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
