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
        self::assertSame(['if', 'then', 'else', 'condit'], TermSearch::terms('f:if f:then f:else condition'));
    }

    /**
     * A word behind a namespace prefix is the name of a thing rather than the
     * English word it is spelled like, so the stopword list does not reach it —
     * `f:or` and `f:then` otherwise have no term at all (`D-ANS-047`).
     */
    #[Test]
    public function aWordBehindANamespacePrefixIsNotAStopword(): void
    {
        self::assertSame(['or'], TermSearch::terms('f:or'));
        self::assertSame(['then'], TermSearch::terms('f:then'));
        self::assertSame(['ext', 'core'], TermSearch::terms('EXT:core'));
    }

    /**
     * The colon has to touch both sides. A sentence puts a space after it, and
     * that is what keeps the same word a stopword in prose — seven of the 41
     * scenario prompts say "or" or "then" in a sentence.
     */
    #[Test]
    public function theSameWordAfterTheColonOfASentenceIsNot(): void
    {
        self::assertSame(['note', 'label', 'wrong'], TermSearch::terms('Note: the label is wrong'));
        self::assertSame(['fix', 'that', 'take', 'throug', 'review'], TermSearch::terms('fix that, then take it through review'));
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
