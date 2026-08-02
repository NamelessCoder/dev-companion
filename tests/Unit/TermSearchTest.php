<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Search\TermSearch;

/**
 * Which words of a query are searched for at all.
 *
 * The floor used to be three characters, and a ViewHelper named after an
 * English keyword fell under it: `Global/If.html` of the ViewHelper reference
 * is titled "if", and `f:if` left nothing to search for once the tokenizer had
 * run (`D-ANS-023`). What decided the floor is not what a short word is worth
 * but how it is matched, and below four characters it is matched as a whole
 * word.
 */
final class TermSearchTest extends TestCase
{
    #[Test]
    public function aTwoLetterWordIsATerm(): void
    {
        self::assertSame(['if'], TermSearch::terms('f:if'));
        self::assertSame(['if', 'else', 'condit'], TermSearch::terms('f:if f:then f:else condition'));
    }

    /**
     * One letter is a whole word wherever it is spelled out, so it separates
     * nothing — the `f` of `f:if` is the `f` of every other tag in the book.
     */
    #[Test]
    public function oneLetterIsNot(): void
    {
        self::assertSame([], TermSearch::terms('f x 9'));
    }

    /**
     * The floor was doing the stopword list's work for words this short, and
     * moving it means the list has to name them itself: "set it up from
     * scratch" otherwise reaches Setting up backend user groups.
     */
    #[Test]
    public function aTwoLetterWordThatSaysNothingAboutTheSubjectIsStillDropped(): void
    {
        self::assertSame(['set', 'test'], TermSearch::terms('set up the tests so we can go'));
    }

    /**
     * Below four characters a term is matched whole, which is the reason the
     * floor could move at all: none of the prefix noise `PREFIX_FROM_LENGTH`
     * guards against reaches a word this short.
     */
    #[Test]
    public function aShortTermIsCarriedAsAWholeWordAndNotAsAPrefix(): void
    {
        self::assertTrue(TermSearch::carries('if', 'if'));
        self::assertTrue(TermSearch::carries('be.security.if Authenticated', 'if'));
        self::assertFalse(TermSearch::carries('a different ifPresent check', 'if'));
    }
}
