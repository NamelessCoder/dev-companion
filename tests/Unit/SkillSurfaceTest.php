<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Sdk\Skills;
use TYPO3\DevCompanion\Upkeep\SkillSurface;

/** The generated task-skill catalog against the files the installer publishes. */
final class SkillSurfaceTest extends TestCase
{
    #[Test]
    public function documentationTracksOnlyTheCatalogIntroduction(): void
    {
        $files = [];
        foreach (Finder::create()->files()->in(SkillSurface::directory())->sortByName() as $file) {
            $files[] = $file->getRelativePathname();
        }

        self::assertSame(['readme.rst'], $files);
    }

    /** Every installed workflow has one readable page and every page belongs to one. */
    #[Test]
    public function everyPublishedSkillIsReadable(): void
    {
        $files = SkillSurface::files();
        self::assertArrayHasKey(SkillSurface::index(), $files);
        self::assertStringContainsString('.. grid:: wide', $files[SkillSurface::index()]);
        self::assertCount(
            count(Skills::skills()) + 1,
            array_filter(array_keys($files), static fn(string $file): bool => str_ends_with($file, '.rst')),
            'the catalog and one page per skill are the only generated pages',
        );
        foreach (Skills::skills() as $skill) {
            self::assertStringContainsString(
                '.. card:: :doc:`' . $skill['title'],
                $files[SkillSurface::index()],
                $skill['id'] . ' has no catalog card',
            );
            $matches = array_filter(
                array_keys($files),
                static fn(string $file): bool => str_ends_with($file, '/' . $skill['id'] . '/readme.rst'),
            );
            self::assertCount(1, $matches, $skill['id'] . ' has no individual documentation page');
            self::assertStringContainsString(
                '.. literalinclude:: SKILL.md',
                $files[array_values($matches)[0]],
                $skill['id'] . ' does not embed its Markdown file',
            );

            $markdown = dirname(SkillSurface::index()) . '/' . $skill['id'] . '/SKILL.md';
            self::assertSame(Skills::read($skill['id']), $files[$markdown]);

            foreach (Skills::references($skill['id']) as $reference) {
                self::assertSame(
                    Skills::reference($skill['id'], $reference),
                    $files[dirname(SkillSurface::index()) . '/' . $skill['id'] . '/' . $reference],
                    $skill['id'] . ' does not copy ' . $reference . ' unchanged',
                );
                self::assertStringContainsString(
                    '.. literalinclude:: ' . $reference,
                    $files[array_values($matches)[0]],
                    $skill['id'] . ' does not embed ' . $reference . ' on its page',
                );
            }
        }
    }
}
