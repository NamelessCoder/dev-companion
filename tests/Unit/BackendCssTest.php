<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\BackendCss;

final class BackendCssTest extends TestCase
{
    /**
     * The case this was written for. `table-fit` is filed as a modifier of the
     * table and is the element above it, and reading the class off its own name
     * is what shipped it onto the wrong node.
     */
    #[Decision('D-CAT-008')]
    #[Test]
    public function aWrapperIsPlacedAboveTheClassItWraps(): void
    {
        $css = new BackendCss('.table-fit{overflow-x:auto}.table-fit>.table{margin:0}');

        self::assertSame('around', $css->position('table-fit', 'table'));
    }

    #[Decision('D-CAT-008')]
    #[Test]
    public function aModifierWrittenOnTheSameElementIsPlacedOnIt(): void
    {
        $css = new BackendCss('.panel.panel-heading{padding:0}');

        self::assertSame('on', $css->position('panel-heading', 'panel'));
    }

    #[Decision('D-CAT-008')]
    #[Test]
    public function aPartWrittenBeneathTheRootIsPlacedBelowIt(): void
    {
        $css = new BackendCss('.card .card-header{padding:1rem}');

        self::assertSame('below', $css->position('card-header', 'card'));
    }

    /**
     * The honest answer for a modifier that carries no position to get wrong.
     * Saying `on` here would be a guess: nothing in the stylesheet writes the
     * two names together, and the derivation states what it read.
     */
    #[Decision('D-CAT-008')]
    #[Test]
    public function aClassNoSelectorPlacesHasNoPosition(): void
    {
        $css = new BackendCss('.badge-info{--typo3-badge-bg:blue}.badge{padding:0}');

        self::assertNull($css->position('badge-info', 'badge'));
    }

    /**
     * A class whose name begins with another class's name is a different class,
     * and the boundary is what the derivation reads. `.table-bordered` is not
     * `.table`, and matching it as one placed every bordered table inside a
     * wrapper that does not exist.
     */
    #[Decision('D-CAT-008')]
    #[Test]
    public function aLongerNameIsNotTheClassItStartsWith(): void
    {
        $css = new BackendCss('.table-fit>.table-bordered{border:0}');

        self::assertNull($css->position('table-fit', 'table'));
    }

    #[Decision('D-CAT-008')]
    #[Test]
    public function whatIsStyledWithinIsSeparateFromWhereTheClassSits(): void
    {
        $css = new BackendCss('.table-fit>.table{margin:0}.table-fit>typo3-backend-progress-bar{height:3px}');

        self::assertSame(
            ['.table', 'typo3-backend-progress-bar'],
            $css->stylesWithin('table-fit', ['.table', '.badge', 'typo3-backend-progress-bar']),
        );
    }

    /** Only what the caller already knows, so an implementation detail beneath a wrapper stays out. */
    #[Decision('D-CAT-008')]
    #[Test]
    public function whatIsStyledWithinHoldsOnlyNamesTheCallerNamed(): void
    {
        $css = new BackendCss('.table-fit>.some-internal-shim{display:block}');

        self::assertSame([], $css->stylesWithin('table-fit', ['.table']));
    }

    /**
     * A media query holds selectors and an at-rule head is not one. Reading the
     * head as a selector put `@media (min-width:768px)` in the list, where it
     * matched nothing and cost every lookup a pass over it.
     */
    #[Decision('D-CAT-008')]
    #[Test]
    public function selectorsInsideAMediaQueryAreRead(): void
    {
        $css = new BackendCss('@media (min-width:768px){.table-fit>.table{margin:0}}');

        self::assertSame(['.table-fit>.table'], $css->selectors());
    }

    #[Decision('D-CAT-008')]
    #[Test]
    public function aCommentIsNotASelector(): void
    {
        $css = new BackendCss("/* .commented-out {} */\n.table{width:100%}");

        self::assertSame(['.table'], $css->selectors());
    }

    #[Decision('D-CAT-008')]
    #[Test]
    #[DataProvider('combinators')]
    public function everyCombinatorPlacesTheClassTheSameWay(string $selector): void
    {
        $css = new BackendCss($selector . '{margin:0}');

        self::assertSame('around', $css->position('table-fit', 'table'));
    }

    /** @return array<string, list<string>> */
    public static function combinators(): array
    {
        return [
            'child' => ['.table-fit>.table'],
            'descendant' => ['.table-fit .table'],
            'child, spaced' => ['.table-fit > .table'],
            'deeper' => ['.table-fit>div>.table'],
        ];
    }

    #[Decision('D-CAT-008')]
    #[Test]
    public function aClassTheStylesheetNeverWritesIsNotCarried(): void
    {
        $css = new BackendCss('.table{width:100%}');

        self::assertFalse($css->carries('table-fit'));
        self::assertTrue($css->carries('table'));
    }
}
