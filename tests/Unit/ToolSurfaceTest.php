<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Tool\Registry;
use Typo3CmsMcp\Upkeep\ToolAnswers;
use Typo3CmsMcp\Upkeep\ToolCalls;
use Typo3CmsMcp\Upkeep\ToolSurface;

/**
 * The tool reference against the registry it is rendered from.
 *
 * A generated page nothing reads back is a hand-written one that was generated
 * once. `ToolNamingTest` holds the readme to the tool names, which catches a
 * tool nobody described; this catches the half no prose ever carried — the
 * description that was rewritten in the class, and the schema field that was
 * added to it.
 */
final class ToolSurfaceTest extends TestCase
{
    #[Test]
    public function theReferenceIsWhatTheRegistryDeclares(): void
    {
        self::assertSame(
            ToolSurface::page(),
            (string) file_get_contents(ToolSurface::file()),
            'documentation/clients/tools.md is not what the registry declares — run bin/cli tools:index',
        );
    }

    /**
     * What the comparison above cannot say on its own: that the page is the
     * whole surface. A renderer that dropped every tool would agree with a file
     * that had none.
     */
    #[Test]
    public function everyOfferedToolIsOnThePage(): void
    {
        $page = (string) file_get_contents(ToolSurface::file());

        $missing = [];
        foreach (Registry::definitions() as $definition) {
            if (!str_contains($page, '## `' . $definition['name'] . '`')) {
                $missing[] = $definition['name'];
            }
        }

        self::assertSame([], $missing, 'offered and rendered nowhere');
        self::assertSame(
            count(Registry::definitions()),
            substr_count($page, "\n## `typo3_"),
            'the page carries a section for a tool the registry does not offer',
        );
    }

    /**
     * Every tool on the page either links to what it answered or says why there
     * is none, and it says it in its own section.
     *
     * Neither happened for the two feedback tools. The head of the page promised
     * a link for every tool below, the renderer emitted nothing where a page was
     * missing, and the promise was therefore false in the one direction a reader
     * cannot check: an absent link and an absent recording look the same.
     *
     * The comparison above cannot catch that on its own — it holds the file to
     * the renderer, and a renderer that says nothing agrees with a file that
     * says nothing.
     */
    #[Test]
    public function everyToolEitherLinksToItsAnswerOrSaysWhyItHasNone(): void
    {
        $sections = self::sections((string) file_get_contents(ToolSurface::file()));

        foreach (array_column(Registry::definitions(), 'name') as $name) {
            self::assertArrayHasKey($name, $sections, $name . ' has no section on the page');
            $section = (string) preg_replace('/\s+/', ' ', $sections[$name]);

            if (is_file(ToolAnswers::file($name))) {
                self::assertStringContainsString(
                    '(tool-answers/' . $name . '.md)',
                    $section,
                    $name . ' was recorded and its section does not link to the recording',
                );
                continue;
            }

            self::assertArrayHasKey(
                $name,
                ToolCalls::undriven(),
                $name . ' has no recorded answer and no written reason for having none',
            );
            self::assertStringContainsString(
                (string) preg_replace('/\s+/', ' ', ToolCalls::undriven()[$name]),
                $section,
                $name . ' has no recorded answer and its section does not say why',
            );
        }
    }

    /**
     * The page split at its tool headings, keyed by the tool each part is about.
     *
     * @return array<string, string>
     */
    private static function sections(string $page): array
    {
        preg_match_all('/^## `(typo3_[a-z_]+)`$(.*?)(?=^## `|\z)/ms', $page, $matches, PREG_SET_ORDER);

        $sections = [];
        foreach ($matches as $match) {
            $sections[$match[1]] = $match[2];
        }

        return $sections;
    }
}
