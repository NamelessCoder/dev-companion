<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Contribution\Gerrit;
use Typo3CmsMcp\Http\Recent;

/**
 * The review server is somebody else's host, so what is held here is everything
 * this side does with what comes back: the prefix that has to come off, the
 * question the query actually asks, and the three ways an answer can fail to be
 * one. The live call is exercised by the recording, which is evidence rather
 * than a check.
 */
final class GerritTest extends TestCase
{
    #[After]
    public function forgetWhatWasHeld(): void
    {
        Recent::forget();
    }

    /**
     * A response as the API sends it: the XSSI prefix, then the array. The two
     * revision fields are what `o=CURRENT_REVISION` adds, in the shape
     * review.typo3.org answered change 95040 with on 2026-08-03.
     */
    private const RESPONSE = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[TASK] Deprecate AssetCollector media handling",'
        . '"status":"MERGED","updated":"2026-08-02 20:40:50.000000000","_number":95040,"current_revision_number":3,'
        . '"current_revision":"e82b930e6e0587842427496c5ce01f625b27fb66"}]';

    #[Test]
    public function theQuestionIsWhichChangeNamesTheIssueInItsCommitMessage(): void
    {
        $asked = '';
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked = $url;

            return self::RESPONSE;
        });

        $answer = $gerrit->changesForIssue('#110348', 3);

        // `message:` and not a bare term: the issue number is in the commit
        // message, where `Resolves:` put it, and a free-text search would also
        // match a change that merely mentions it.
        self::assertSame('message:110348', $answer['query']);
        self::assertStringContainsString('q=message%3A110348', $asked);
        self::assertStringContainsString('n=3', $asked);
        self::assertSame('answered', $answer['status']);
    }

    #[Test]
    public function theResponseIsReadPastThePrefixThatKeepsABrowserFromRunningIt(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::RESPONSE);

        $change = $gerrit->changesForIssue('110348')['changes'][0];

        self::assertSame(95040, $change['number']);
        self::assertSame('MERGED', $change['status']);
        self::assertSame('main', $change['branch']);
        self::assertSame('[TASK] Deprecate AssetCollector media handling', $change['subject']);
        self::assertSame('https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040', $change['url']);
    }

    /**
     * A change is a series of patch sets, and a review is of one of them. The
     * commit is what a reviewer holds a local `HEAD` against; the number alone
     * cannot say whether the two are the same thing, and neither is served
     * unless the query asks for the current revision.
     */
    #[Test]
    public function theAnswerCarriesThePatchSetACheckoutIsHeldAgainst(): void
    {
        $asked = '';
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked = $url;

            return self::RESPONSE;
        });

        $change = $gerrit->change('95040')['changes'][0];

        self::assertStringContainsString('o=CURRENT_REVISION', $asked);
        self::assertSame(3, $change['patchSet']);
        self::assertSame('e82b930e6e0587842427496c5ce01f625b27fb66', $change['commit']);
    }

    /**
     * The option is the server's to honour. A response without the revision
     * fields is still an answer about the change, so the patch set is absent
     * rather than guessed — and zero is what the schema calls named none.
     */
    #[Test]
    public function aChangeWithoutARevisionSaysSoRatherThanInventingOne(): void
    {
        $gerrit = new Gerrit(static fn(): string => ")]}'\n" . '[{"_number":95040,"branch":"main","status":"NEW"}]');

        $change = $gerrit->change('95040')['changes'][0];

        self::assertSame(0, $change['patchSet']);
        self::assertSame('', $change['commit']);
    }

    /**
     * Nothing public is not nothing. A change pushed as private answers this
     * search with an empty list, so the caller is told what was searched rather
     * than that no patch exists — the distinction the tool's own text carries.
     */
    #[Test]
    public function aSearchThatMatchedNothingIsEmptyRatherThanUnavailable(): void
    {
        $gerrit = new Gerrit(static fn(): string => ")]}'\n[]");

        $answer = $gerrit->changesForIssue('105403');

        self::assertSame('empty', $answer['status']);
        self::assertSame([], $answer['changes']);
        self::assertNull($answer['cause']);
    }

    #[Test]
    public function aHostThatDoesNotAnswerIsSaidRatherThanReadAsNoPatch(): void
    {
        $gerrit = new Gerrit(static fn(): ?string => null);

        $answer = $gerrit->changesForIssue('110348');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-answering', $answer['cause']);
    }

    /**
     * A captive portal answers 200 with HTML. Skipping to the first `[` would
     * parse whatever followed it as a review, so anything that is not the API
     * is a failure with a name.
     */
    #[Test]
    public function somethingThatIsNotTheApiIsNotParsedAsOne(): void
    {
        $gerrit = new Gerrit(static fn(): string => '<!doctype html><title>Sign in</title>');

        $answer = $gerrit->changesForIssue('110348');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-parseable', $answer['cause']);
    }
}
