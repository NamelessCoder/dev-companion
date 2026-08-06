<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\Gerrit;
use TYPO3\DevCompanion\Http\Recent;

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

    /**
     * What `message:<number>` really answers, measured against
     * review.typo3.org on 2026-08-05: the change the issue is resolved by, and
     * the change whose own number happens to be that of the issue.
     *
     * The second one is the false positive `feedback/2026-08-05-033826`
     * reported five of. Its message names issue 106318 and carries the queried
     * number in the `Reviewed-on:` trailer alone, which is the trailer a merged
     * change gains and which ends in the change's own number.
     */
    private const BOTH = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Do not split paragraphs at inner linebreaks",'
        . '"status":"NEW","_number":95108,"current_revision_number":1,'
        . '"current_revision":"6929747e86b1d45993fb4ca950fc8e47ba5c1ca4","revisions":{'
        . '"6929747e86b1d45993fb4ca950fc8e47ba5c1ca4":{"commit":{"message":"[BUGFIX] Do not split paragraphs at inner linebreaks\n\nRteHtmlParser divided the content at every line break.\n\nResolves: #88556\nReleases: main, 14.3, 13.4\nChange-Id: I17ba56a7a78a2282495fb422513d4859e2818d05\n"}}}},'
        . '{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Parallel execution of non-parallel scheduler task",'
        . '"status":"MERGED","_number":88556,"current_revision_number":9,'
        . '"current_revision":"a1b2c3d4e5f60718293a4b5c6d7e8f9012345678","revisions":{'
        . '"a1b2c3d4e5f60718293a4b5c6d7e8f9012345678":{"commit":{"message":"[BUGFIX] Parallel execution of non-parallel scheduler task\n\nClose a time window in Scheduler::executeTask().\n\nResolves: #106318\nReleases: main, 13.4\nChange-Id: I1264b5c248dd9aa5402383a498d82650932f29e4\nReviewed-on: https://review.typo3.org/c/Packages/TYPO3.CMS/+/88556\n"}}}}]';

    /**
     * The one the query was for, and not the one that shares its number.
     *
     * Both skills that call this treat a hit as grounds to stop working, so a
     * change that does not name the issue is the answer this tool must not
     * give: a session reading one MERGED core change with a plausible subject
     * has no signal at all that it is spurious.
     */
    #[Test]
    public function aChangeMatchedByItsOwnNumberRatherThanByItsMessageIsNotAnswered(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::BOTH);

        $answer = $gerrit->changesForIssue('88556');

        self::assertSame([95108], array_column($answer['changes'], 'number'));
        self::assertSame(1, $answer['dropped']);
        self::assertSame('answered', $answer['status']);
    }

    /**
     * The trailer that carries the number without meaning it is the one a
     * merged change gains, and it ends in the change's own number — so reading
     * the message as text would clear exactly the change the filter is for.
     */
    #[Test]
    public function theNumberInAReviewUrlIsNotTheIssueBeingNamed(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::BOTH);

        // 106318 is what the merged change actually resolves, and it is named
        // in a trailer rather than in a URL.
        $answer = $gerrit->changesForIssue('106318');

        self::assertSame([88556], array_column($answer['changes'], 'number'));
        self::assertSame(1, $answer['dropped']);
    }

    /**
     * Everything the server matched being a false positive is the truthful
     * empty answer, which is what the caller acts on: nothing public names this
     * issue.
     */
    #[Test]
    public function anAnswerOfNothingButFalsePositivesIsEmpty(): void
    {
        $gerrit = new Gerrit(static fn(): string => self::BOTH);

        $answer = $gerrit->changesForIssue('95108');

        self::assertSame('empty', $answer['status']);
        self::assertSame([], $answer['changes']);
        self::assertSame(2, $answer['dropped']);
        self::assertNull($answer['cause']);
    }

    /**
     * The commit message is what the filter reads, so it is asked for — and
     * only where there is something to hold against it. A caller naming a
     * change has named it.
     */
    #[Test]
    public function theCommitMessageIsAskedForWhereTheAnswerIsHeldAgainstIt(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::BOTH;
        });

        $gerrit->changesForIssue('88556');
        $gerrit->change('95108');

        self::assertStringContainsString('o=CURRENT_COMMIT', $asked[0]);
        self::assertStringNotContainsString('o=CURRENT_COMMIT', $asked[1]);
        // What was read to decide is not what was asked for. The answer is
        // which changes exist, and the commit is what a checkout is held
        // against.
        self::assertArrayNotHasKey('message', $gerrit->changesForIssue('88556')['changes'][0]);
        self::assertArrayNotHasKey('message', $gerrit->change('95108')['changes'][0]);
    }

    /**
     * A server that answered without the option is not a reason to drop
     * everything or to hand back the false positive: the one rule that needs no
     * message is that a change is not the answer to the issue whose number it
     * carries as its own.
     */
    #[Test]
    public function aChangeWhoseMessageDidNotComeBackIsJudgedByItsNumberAlone(): void
    {
        $gerrit = new Gerrit(static fn(): string => ")]}'\n"
            . '[{"_number":88556,"branch":"main","status":"MERGED","subject":"[BUGFIX] Parallel execution"},'
            . '{"_number":95108,"branch":"main","status":"NEW","subject":"[BUGFIX] Do not split paragraphs"}]');

        $answer = $gerrit->changesForIssue('88556');

        self::assertSame([95108], array_column($answer['changes'], 'number'));
        self::assertSame(1, $answer['dropped']);
    }

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
