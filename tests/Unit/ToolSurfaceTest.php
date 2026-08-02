<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Tool\Registry;
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
}
