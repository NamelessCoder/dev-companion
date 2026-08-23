<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\Gerrit;
use TYPO3\DevCompanion\Http\Recent;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\GerritLookup;

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
     * One change of a backport pair, as `change:95169` answered it on
     * 2026-08-14: the id both changes carry is in the number-form response, and
     * nothing in it names the other change.
     */
    private const NUMBERED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Add link parsing in RTE figcaption",'
        . '"status":"NEW","updated":"2026-08-07 17:50:27.000000000","_number":95169,'
        . '"change_id":"I4b0290760f14296feec6ab30ad49595899ca08f4","current_revision_number":2,'
        . '"current_revision":"65aa3ece11944bf20f6baeb52c13b39a2009150f"}]';

    /**
     * The same pair as `change:I4b02…` answered it the same day — both changes,
     * the backport first, because Gerrit orders by last activity and the 13.4
     * change moved an hour after the one on `main`.
     */
    private const SHARED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"13.4","subject":"[BUGFIX] Add link parsing in RTE figcaption",'
        . '"status":"NEW","updated":"2026-08-07 18:54:00.000000000","_number":93202,'
        . '"change_id":"I4b0290760f14296feec6ab30ad49595899ca08f4","current_revision_number":2,'
        . '"current_revision":"a50286ef4ccb170282ed88eee5768d63f81597c5"},'
        . '{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Add link parsing in RTE figcaption",'
        . '"status":"NEW","updated":"2026-08-07 17:50:27.000000000","_number":95169,'
        . '"change_id":"I4b0290760f14296feec6ab30ad49595899ca08f4","current_revision_number":2,'
        . '"current_revision":"65aa3ece11944bf20f6baeb52c13b39a2009150f"}]';

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
     * has no signal at all that it is spurious — `D-ANS-055`.
     */
    #[Requirement('R-ANS-023')]
    #[Decision('D-ANS-055')]
    #[Test]
    public function aChangeMatchedByItsNumberAndNotItsMessageIsNotAnswered(): void
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
     * the message as text would clear exactly the change the filter is for —
     * `D-ANS-055`.
     */
    #[Requirement('R-ANS-023')]
    #[Decision('D-ANS-055')]
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
     * issue — `D-ANS-055`.
     */
    #[Requirement('R-ANS-023')]
    #[Decision('D-ANS-055')]
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
     * A page of issues is one question, and each change is sorted onto the
     * issue its commit message names.
     *
     * The alternative is a call per row, which is what keeps a backlog answer
     * from carrying the one signal a triage stops on (`D-ANS-069`). The filter
     * carries over unchanged: a change is the answer to the issue its message
     * names and to no other number in the batch.
     */
    #[Decision('D-ANS-069')]
    #[Test]
    public function aPageOfIssuesIsOneQueryAndEachHitLandsOnTheIssueItNames(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::BOTH;
        });

        $found = $gerrit->changesForIssues([88556, 106318]);

        self::assertCount(1, $asked);
        self::assertStringContainsString('q=message%3A88556%20OR%20message%3A106318', $asked[0]);
        self::assertStringContainsString('o=CURRENT_COMMIT', $asked[0]);
        self::assertSame([95108], array_column($found[88556], 'number'));
        // And the merged change is the answer to the issue it resolves rather
        // than to the one whose number it carries as its own.
        self::assertSame([88556], array_column($found[106318], 'number'));
        self::assertArrayNotHasKey('message', $found[88556][0]);
    }

    /**
     * What bounds a query is the URL rather than a documented limit, so a page
     * wider than one batch is more than one call — `D-ANS-069`.
     */
    #[Test]
    public function aPageWiderThanOneQueryIsAskedInBatches(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return ")]}'\n[]";
        });

        self::assertSame([], $gerrit->changesForIssues(range(80001, 80015)));
        self::assertCount(2, $asked);
    }

    /**
     * The commit message is what the filter reads, so it is asked for — and
     * only where there is something to hold against it. A caller naming a
     * change has named it — `D-ANS-055`.
     */
    #[Decision('D-ANS-055')]
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
     * carries as its own — `D-ANS-055`.
     */
    #[Requirement('R-ANS-023')]
    #[Decision('D-ANS-055')]
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
    #[Requirement('R-ANS-021')]
    #[Test]
    public function theAnswerCarriesThePatchSetACheckoutIsHeldAgainst(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::RESPONSE;
        });

        $change = $gerrit->change('95040')['changes'][0];

        self::assertStringContainsString('o=CURRENT_REVISION', $asked[0]);
        self::assertSame(3, $change['patchSet']);
        self::assertSame('e82b930e6e0587842427496c5ce01f625b27fb66', $change['commit']);
    }

    /**
     * The other half of that, and `D-ANS-068`. The remote is in the ref because
     * a core clone fetches from the GitHub mirror, where it does not exist.
     *
     * The second case is the padding. Gerrit shards by the change number modulo
     * 100 written as two digits, so 95108 is filed under `08` and a ref built
     * by hand from the last digit alone resolves to nothing.
     */
    #[Decision('D-ANS-068')]
    #[Test]
    public function theAnswerCarriesTheRefThatFetchesThePatchSetItNames(): void
    {
        $change = (new Gerrit(static fn(): string => self::RESPONSE))->change('95040')['changes'][0];

        self::assertSame([
            'ref' => 'refs/changes/40/95040/3',
            'remote' => 'https://review.typo3.org/Packages/TYPO3.CMS',
        ], $change['fetch']);

        $sharded = (new Gerrit(static fn(): string => self::BOTH))->changesForIssue('88556')['changes'][0];

        self::assertSame('refs/changes/08/95108/1', $sharded['fetch']['ref']);
    }

    /**
     * A backport is a change of its own on the release branch, linked to the
     * original by the Change-Id and by nothing else — `D-ANS-080`. Which handle
     * the caller holds decided until now whether it is in the answer at all:
     * `feedback/2026-08-12-092654` reviewed 95169 and learned of its 13.4
     * sibling from the tracker.
     */
    #[Decision('D-ANS-080')]
    #[Test]
    public function aChangeNamedByItsNumberAnswersWithItsSiblings(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return count($asked) === 1 ? self::NUMBERED : self::SHARED;
        });

        $answer = $gerrit->change('95169', 10);

        $queries = array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '?q=')));
        self::assertStringContainsString('q=change%3A95169', $queries[0]);
        // The id the first response carried, asked for as a query of its own.
        self::assertStringContainsString('q=change%3AI4b0290760f14296feec6ab30ad49595899ca08f4', $queries[1]);
        self::assertCount(2, $queries);
        self::assertSame([95169, 93202], array_column($answer['changes'], 'number'));
        self::assertSame(['main', '13.4'], array_column($answer['changes'], 'branch'));
        self::assertSame(
            'I4b0290760f14296feec6ab30ad49595899ca08f4',
            $answer['changes'][0]['changeId'],
        );
        // The query that answers this set by hand is the second one.
        self::assertSame('change:I4b0290760f14296feec6ab30ad49595899ca08f4', $answer['query']);
    }

    /**
     * The other handle asks the whole question in one query, so nothing is
     * asked twice — including where that query answers one change, which is a
     * patch nobody has backported yet — `D-ANS-080`.
     */
    #[Decision('D-ANS-080')]
    #[Test]
    public function aChangeIdIsNotAskedAgainWhereItIsWhatTheCallerPassed(): void
    {
        $asked = 0;
        $shared = new Gerrit(function (string $url) use (&$asked): string {
            $asked += str_contains($url, '?q=') ? 1 : 0;

            return self::SHARED;
        });
        $alone = new Gerrit(function (string $url) use (&$asked): string {
            $asked += str_contains($url, '?q=') ? 1 : 0;

            return self::NUMBERED;
        });

        $pair = $shared->change('I4b0290760f14296feec6ab30ad49595899ca08f4', 10);
        // Lower case, because a commit message is copied by hand and Gerrit
        // matches the id either way.
        $one = $alone->change('i4b0290760f14296feec6ab30ad49595899ca08f4', 10);

        self::assertSame(2, $asked);
        self::assertSame([93202, 95169], array_column($pair['changes'], 'number'));
        self::assertSame([95169], array_column($one['changes'], 'number'));
    }

    /**
     * `n` is applied after the change that was named, not to the query that
     * finds its siblings. Gerrit orders by last activity, so
     * `change:I4b02…&n=1` answers the backport — a caller asking for one change
     * by number would otherwise be handed the other one — `D-ANS-080`.
     */
    #[Decision('D-ANS-080')]
    #[Test]
    public function theChangeThatWasNamedIsInItsOwnAnswerWhateverTheLimit(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => str_contains($url, 'q=change%3A95169')
            ? self::NUMBERED
            : self::SHARED);

        $answer = $gerrit->change('95169', 1);

        self::assertSame([95169], array_column($answer['changes'], 'number'));
    }

    /**
     * A second query that did not answer is not an absence of siblings, so the
     * change the caller named stands rather than being replaced by nothing —
     * `D-ANS-080`.
     */
    #[Decision('D-ANS-080')]
    #[Test]
    public function aSiblingQueryThatDidNotAnswerLeavesTheNamedChangeStanding(): void
    {
        $answers = [self::NUMBERED, null];
        $gerrit = new Gerrit(static function () use (&$answers): ?string {
            return array_shift($answers);
        });

        $answer = $gerrit->change('95169', 10);

        self::assertSame('answered', $answer['status']);
        self::assertSame([95169], array_column($answer['changes'], 'number'));
    }

    /**
     * The option is the server's to honour. A response without the revision
     * fields is still an answer about the change, so the patch set is absent
     * rather than guessed — and zero is what the schema calls named none.
     *
     * There is no ref either, because a ref names a patch set: null rather than
     * a string carrying a zero, which would fetch nothing and read like a
     * command to run — `D-ANS-068`.
     */
    #[Requirement('R-ANS-021')]
    #[Decision('D-ANS-068')]
    #[Test]
    public function aChangeWithoutARevisionSaysSoRatherThanInventingOne(): void
    {
        $gerrit = new Gerrit(static fn(): string => ")]}'\n" . '[{"_number":95040,"branch":"main","status":"NEW"}]');

        $change = $gerrit->change('95040')['changes'][0];

        self::assertSame(0, $change['patchSet']);
        self::assertSame('', $change['commit']);
        self::assertNull($change['fetch']);
    }

    /**
     * Change 91563, the head of the stack `feedback/2026-08-21-074010` read as
     * one change.
     */
    private const STACKED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[WIP][FEATURE] Introduce Action API",'
        . '"status":"NEW","updated":"2026-08-19 09:12:41.000000000","_number":91563,'
        . '"change_id":"I8ca1a6d0d3d5e0c02c1d3a2b7e6f5a4c3b2a1908","current_revision_number":46,'
        . '"current_revision":"ad7dc9be0e8a2c4f6b8d0a2c4e6f8a0b2c4d6e80"}]';

    /**
     * What `/changes/91563/revisions/current/related` answered on 2026-08-21,
     * four of its fifteen entries: the top of the stack, the one merged part,
     * the change that was asked about, and what it is built on.
     *
     * 92323 is the entry the two revision numbers are two facts on — the chain
     * holds patch set 8 while the change stands at 10.
     */
    private const RELATED = ")]}'\n"
        . '{"changes":['
        . '{"project":"Packages/TYPO3.CMS","change_id":"I455266664387f5bb28c7518fb962a13d90b75ff0",'
        . '"commit":{"commit":"e02be2c6672f09e8ca570924e13aff37efc89ff2",'
        . '"parents":[{"commit":"9ada9785fe1c8ff22b3f9ccf4d431c70e735bdf4"}],'
        . '"subject":"[WIP][FEATURE] Provide Record Actions"},'
        . '"_change_number":92197,"_revision_number":9,"_current_revision_number":9,"status":"NEW"},'
        . '{"project":"Packages/TYPO3.CMS","change_id":"I3b8c1e6a5d4f2c0b9a8e7d6c5b4a39281706f5e4",'
        . '"commit":{"commit":"b4c6d8e0f2a4c6e80a2c4e6f8a0b2c4d6e8f0a2c",'
        . '"parents":[{"commit":"ad7dc9be0e8a2c4f6b8d0a2c4e6f8a0b2c4d6e80"}],'
        . '"subject":"[TASK] Avoid `json_encode()` workarounds in Settings API"},'
        . '"_change_number":92323,"_revision_number":8,"_current_revision_number":10,"status":"MERGED"},'
        . '{"project":"Packages/TYPO3.CMS","change_id":"I8ca1a6d0d3d5e0c02c1d3a2b7e6f5a4c3b2a1908",'
        . '"commit":{"commit":"ad7dc9be0e8a2c4f6b8d0a2c4e6f8a0b2c4d6e80",'
        . '"parents":[{"commit":"c4e6f8a0b2c4d6e8f0a2c4e6f8a0b2c4d6e8f0a2"}],'
        . '"subject":"[WIP][FEATURE] Introduce Action API"},'
        . '"_change_number":91563,"_revision_number":46,"_current_revision_number":46,"status":"NEW"},'
        . '{"project":"Packages/TYPO3.CMS","change_id":"Ie6f8a0b2c4d6e8f0a2c4e6f8a0b2c4d6e8f0a2c4",'
        . '"commit":{"commit":"c4e6f8a0b2c4d6e8f0a2c4e6f8a0b2c4d6e8f0a2","parents":[],'
        . '"subject":"[TASK] Introduce JSON SchemaBuilder"},'
        . '"_change_number":93064,"_revision_number":16,"_current_revision_number":16,"status":"NEW"}]}';

    /** The stacked change, with its chain where that is what was asked for. */
    private static function stacked(string $url): string
    {
        return str_contains($url, '/related') ? self::RELATED : self::STACKED;
    }

    /**
     * A change read alone says a feature exists; the stack under it says what
     * the feature consists of and how far it has got — `D-ANS-094`.
     *
     * `feedback/2026-08-21-074010` read 91563 and was handed the head of a
     * fifteen-change stack with nothing saying there was more to read. The
     * order is git parentage, child first, so the place of the change in the
     * list is what says how much is stacked on it and how much it is built on.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function aChangeCarriesTheStackOfChangesItIsOnePartOf(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::stacked($url);
        });

        $change = $gerrit->change('91563')['changes'][0];

        self::assertContains(
            'https://review.typo3.org/changes/91563/revisions/current/related',
            $asked,
        );
        self::assertSame([92197, 92323, 91563, 93064], array_column($change['chain'], 'number'));
        // The entry's own state, which is what says that one part of the
        // feature landed and one is still open.
        self::assertSame(['NEW', 'MERGED', 'NEW', 'NEW'], array_column($change['chain'], 'status'));
        self::assertSame([false, false, true, false], array_column($change['chain'], 'thisChange'));
        self::assertSame('[TASK] Introduce JSON SchemaBuilder', $change['chain'][3]['subject']);
    }

    /**
     * The two revision numbers per entry are two facts, and the merged entry is
     * where they come apart: the stack holds patch set 8 of change 92323 while
     * that change stands at 10.
     *
     * Acting on the patch set the chain names is the mistake carrying only that
     * number would make possible, which is `D-ANS-094`'s second **Wrong if**.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function aChainEntryNamesThePatchSetInTheStackAndTheOneItStandsAt(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => self::stacked($url));

        $chain = $gerrit->change('91563')['changes'][0]['chain'];

        self::assertSame(['chainedAt' => 8, 'patchSet' => 10], [
            'chainedAt' => $chain[1]['chainedAt'],
            'patchSet' => $chain[1]['patchSet'],
        ]);
        // The ordinary entry, where the chain holds what the change stands at.
        self::assertSame([46, 46], [$chain[2]['chainedAt'], $chain[2]['patchSet']]);
    }

    /**
     * By the number and not by the Change-Id. `/changes/<Change-Id>/…/related`
     * answered 404 `Multiple changes found` on 2026-08-21 for the backport pair
     * `D-ANS-080` puts in this answer, so the handle the caller passed is not
     * the handle this call can be made with — `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function theChainIsAskedByTheChangeNumberOfEveryChangeInTheAnswer(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return str_contains($url, 'q=change%3A95169') ? self::NUMBERED : self::SHARED;
        });

        $gerrit->change('95169', 10);

        self::assertSame([
            'https://review.typo3.org/changes/95169/revisions/current/related',
            'https://review.typo3.org/changes/93202/revisions/current/related',
        ], array_values(array_filter($asked, static fn(string $url): bool => str_contains($url, '/related'))));
    }

    /**
     * A change nothing is stacked on and that is stacked on nothing. The review
     * server answers `{"changes":[]}` for it — 20 bytes measured on 2026-08-21
     * — and that is the ordinary case rather than a failure, so it is an empty
     * chain and not a null one — `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function aChangeStandingAloneHasAnEmptyChainRatherThanNone(): void
    {
        $gerrit = new Gerrit(static fn(string $url): string => str_contains($url, '/related')
            ? ")]}'\n" . '{"changes":[]}'
            : self::RESPONSE);

        $change = $gerrit->change('95040')['changes'][0];

        self::assertSame([], $change['chain']);
    }

    /**
     * A chain that could not be read is not a change standing alone. Nothing in
     * the change payload says whether there is one, so an empty list here would
     * be this side inventing the answer the call failed to bring back —
     * `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function aChainCallThatDidNotAnswerIsNotAChangeStandingAlone(): void
    {
        $gerrit = new Gerrit(static fn(string $url): ?string => str_contains($url, '/related')
            ? null
            : self::RESPONSE);

        $answer = $gerrit->change('95040');

        self::assertSame('answered', $answer['status']);
        self::assertNull($answer['changes'][0]['chain']);
    }

    /**
     * The stack is what the caller reads, so the text half says where the
     * change sits in it and which entries have moved on — `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function theTextHalfSaysWhereInTheStackTheChangeSits(): void
    {
        $chain = (new Gerrit(static fn(string $url): string => self::stacked($url)))
            ->change('91563')['changes'][0];

        $said = implode("\n", GerritLookup::chain($chain, true));

        self::assertStringContainsString('4 changes, 2 stacked on this one and 1 under it', $said);
        self::assertStringContainsString('91563 · NEW · [WIP][FEATURE] Introduce Action API · this change', $said);
        self::assertStringContainsString('chained at patch set 8, now at 10', $said);
        // Nothing to print for a change standing alone: that is the ordinary
        // case rather than a finding.
        self::assertSame([], GerritLookup::chain(['chain' => []], true));
        // And an issue search asked for none of it, so silence there is not a
        // claim that it could not be read.
        self::assertSame([], GerritLookup::chain(['chain' => null], false));
        self::assertStringContainsString('could not be read', implode("\n", GerritLookup::chain(['chain' => null], true)));
    }

    /**
     * The two relations are told apart where both are in one answer. A stack is
     * different changes built on one another; a shared Change-Id is one patch
     * on several branches, and reading the first as the second would say the
     * label was the whole of the work — `D-ANS-094`.
     */
    #[Decision('D-ANS-094')]
    #[Test]
    public function theTwoRelationsAChangeStandsInAreToldApart(): void
    {
        $said = implode("\n", GerritLookup::relations(false));

        self::assertStringContainsString('built on one another', $said);
        self::assertStringContainsString('Change-Id', $said);
        // The entry's status is the entry's, which is the first way a chain is
        // misread.
        self::assertStringContainsString('MERGED entry says that change landed', $said);
        // Said only where an entry in this answer is behind, because it is a
        // warning about those entries rather than a property of chains.
        self::assertStringNotContainsString('moved on since', $said);
        self::assertStringContainsString('moved on since', implode("\n", GerritLookup::relations(true)));
    }

    /**
     * The review half of change 93319, as review.typo3.org answered it on
     * 2026-08-14 with the labels, the accounts and the messages asked for.
     *
     * Trimmed to what is read: the avatars, the reviewers and the revisions are
     * three quarters of the 57.9 KB and nothing here looks at them. Verified
     * runs to +2 on this server, which is why a +1 leaves it unsatisfied.
     */
    private const REVIEWED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[TASK] Add E2E tests for page creation",'
        . '"status":"NEW","_number":93319,"change_id":"I4924af7ec012e7b12771d64c75687f194952e703",'
        . '"current_revision_number":21,"current_revision":"39f5c1b0a2d4e6f8a0b2c4d6e8f0a2b4c6d8e0f2",'
        . '"total_comment_count":0,"unresolved_comment_count":0,'
        . '"submit_records":[{"rule_name":"gerrit~DefaultSubmitRule","status":"NOT_READY","labels":['
        . '{"label":"Verified","status":"NEED"},{"label":"Code-Review","status":"NEED"}]}],'
        . '"labels":{"Verified":{"recommended":{"_account_id":7064,"name":"Oliver Klee"},"all":['
        . '{"value":1,"date":"2026-08-13 18:16:10.000000000","_account_id":7064,"name":"Oliver Klee"},'
        . '{"value":1,"date":"2026-08-13 17:39:41.000000000","_account_id":52386,"name":"core-ci","tags":["SERVICE_USER"]}],'
        . '"values":{"-1":"Fails"," 0":"No score","+1":"Verified","+2":"Verified by team member"},"value":1,"default_value":0},'
        . '"Code-Review":{"all":[{"value":0,"_account_id":7059,"name":"Benni Mack"}],'
        . '"values":{"-2":"This shall not be merged"," 0":"No score","+2":"Looks good to me, approved"},"default_value":0}}';

    /** What `o=MESSAGES` adds to it, three of the 46 that change carries. */
    private const LOG = ',"messages":['
        . '{"id":"a1","author":{"_account_id":52386,"name":"core-ci","tags":["SERVICE_USER"]},'
        . '"date":"2026-07-15 12:33:09.000000000","message":"Patch Set 20: Verified+1\n\nCore CI is happy: https://git.typo3.org/typo3/CI/cms/-/pipelines/104789","_revision_number":20},'
        . '{"id":"a2","author":{"_account_id":7059,"name":"Benni Mack"},"date":"2026-07-17 07:17:46.000000000",'
        . '"message":"Patch Set 20: Code-Review+1","_revision_number":20},'
        . '{"id":"a3","author":{"_account_id":7059,"name":"Benni Mack"},"date":"2026-08-13 17:27:20.000000000",'
        . '"tag":"autogenerated:gerrit:newPatchSet",'
        . '"message":"Patch Set 21: Patch Set 20 was rebased\n\nOutdated Votes:\n* Code-Review+1 (copy condition: \"changekind:NO_CHANGE OR is:MIN\")\n","_revision_number":21}]';

    /** The change as that query answers it, with the log where it asked for one. */
    private static function reviewed(string $url): string
    {
        return self::REVIEWED . (str_contains($url, 'o=MESSAGES') ? self::LOG : '') . '}]';
    }

    /**
     * The votes are what the surface turns on, so they come with a change
     * nobody asked anything extra for — `D-ANS-079`.
     *
     * A reviewer picking this change up needs two things the fields before this
     * one cannot say: that the Verified+1 is not enough to submit on this
     * server, and that Code-Review stands at nothing although somebody is on
     * it. Both are the label state rather than a count of patch sets.
     */
    #[Test]
    public function aChangeCarriesTheVoteEachLabelStandsAt(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::reviewed($url);
        });

        $change = $gerrit->change('93319')['changes'][0];

        self::assertStringContainsString('o=DETAILED_LABELS', $asked[0]);
        self::assertStringContainsString('o=DETAILED_ACCOUNTS', $asked[0]);
        self::assertSame('I4924af7ec012e7b12771d64c75687f194952e703', $change['changeId']);
        self::assertSame([
            [
                'label' => 'Verified',
                'satisfied' => false,
                'votes' => [
                    ['voter' => 'Oliver Klee', 'value' => 1, 'on' => '2026-08-13 18:16:10.000000000'],
                    ['voter' => 'core-ci', 'value' => 1, 'on' => '2026-08-13 17:39:41.000000000'],
                ],
            ],
            [
                'label' => 'Code-Review',
                'satisfied' => false,
                // A reviewer who was added and has not voted, which is not the
                // same answer as nobody being on it.
                'votes' => [['voter' => 'Benni Mack', 'value' => 0, 'on' => '']],
            ],
        ], $change['labels']);
    }

    /**
     * The comments are a second endpoint, and a change that says it carries
     * none is answered without asking it.
     *
     * 93319 is that change: `total_comment_count` is 0 and its whole review
     * history is in the messages. The count is in the payload of the call that
     * was made anyway, so the second one is spent only where there is something
     * to fetch.
     */
    #[Test]
    public function aChangeWithNoCommentCostsNoCallToFindThatOut(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::reviewed($url);
        });

        $change = $gerrit->change('93319')['changes'][0];

        self::assertSame([], array_filter($asked, static fn(string $url): bool => str_contains($url, '/comments')));
        self::assertSame(0, $change['commentCount']);
        self::assertSame([], $change['comments']);
    }

    /**
     * What `/changes/95179/comments` answered on 2026-08-14, trimmed to the
     * fields that are read. One of the three is unresolved and has a reply
     * under it, which is the case the surface is about.
     */
    private const COMMENTS = ")]}'\n"
        . '{"/PATCHSET_LEVEL":['
        . '{"author":{"_account_id":37281,"name":"Mathias Brodala"},"unresolved":true,"patch_set":1,'
        . '"id":"9dc25800_aed74e93","updated":"2026-08-10 06:45:34.000000000",'
        . '"message":"Can we add an Important RST for this?"},'
        . '{"author":{"_account_id":7342,"name":"Georg Ringer"},"unresolved":false,"patch_set":1,'
        . '"id":"85e9c99f_d1a448dd","updated":"2026-08-10 08:02:08.000000000",'
        . '"message":"this should be have an important.rst + no 13.4 backport pls"},'
        . '{"author":{"_account_id":21249,"name":"Benjamin Kott"},"unresolved":true,"patch_set":1,'
        . '"id":"399ce3e3_1d8a86be","in_reply_to":"9dc25800_aed74e93","updated":"2026-08-10 07:02:56.000000000",'
        . '"message":"sure! will check it later"}]}';

    /** The same change, with comments on it and no messages asked for. */
    private const COMMENTED = ")]}'\n"
        . '[{"project":"Packages/TYPO3.CMS","branch":"main","subject":"[BUGFIX] Do not split paragraphs",'
        . '"status":"NEW","_number":95179,"change_id":"I17ba56a7a78a2282495fb422513d4859e2818d05",'
        . '"current_revision_number":1,"current_revision":"6929747e86b1d45993fb4ca950fc8e47ba5c1ca4",'
        . '"total_comment_count":3,"unresolved_comment_count":2,"labels":{},"submit_records":[]}]';

    /**
     * A comment somebody replied to without resolving is two facts, and this
     * server hands over both rather than deciding between them.
     *
     * `unresolved` alone would report a thread that was answered; the reply
     * alone would drop one that was answered and left open on purpose. Change
     * 95179 carries exactly that comment, which is why neither field is read
     * here into an answer of "nobody answered" — `D-ANS-079`.
     */
    #[Test]
    public function aCommentCarriesItsThreadRatherThanAVerdictOnIt(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return str_contains($url, '/comments') ? self::COMMENTS : self::COMMENTED;
        });

        $change = $gerrit->change('95179')['changes'][0];

        self::assertContains('https://review.typo3.org/changes/95179/comments', $asked);
        self::assertSame(3, $change['commentCount']);
        // Oldest first, across the files rather than within one: a thread is
        // read in the order it was written.
        self::assertSame(
            ['Mathias Brodala', 'Benjamin Kott', 'Georg Ringer'],
            array_column($change['comments'], 'author'),
        );

        $answered = $change['comments'][1];
        self::assertTrue($answered['unresolved']);
        self::assertSame('9dc25800_aed74e93', $answered['inReplyTo']);
        self::assertSame('/PATCHSET_LEVEL', $answered['file']);
        self::assertNull($answered['line']);
        self::assertSame(1, $answered['patchSet']);
    }

    /**
     * None of it reaches the issue search. That question is whether a patch
     * exists, it is answered with up to 25 changes, and the comments are a call
     * each — so a hit says the review was not read rather than that there is
     * none of it.
     */
    #[Test]
    public function anIssueSearchAsksForNoneOfTheReview(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::BOTH;
        });

        $change = $gerrit->changesForIssue('88556')['changes'][0];

        self::assertCount(1, $asked);
        self::assertStringNotContainsString('o=DETAILED_LABELS', $asked[0]);
        self::assertNull($change['labels']);
        self::assertNull($change['comments']);
        self::assertNull($change['messages']);
    }

    /**
     * The log is 57.9 KB against 14.3 KB on one change, so it is asked for —
     * and the half a service user wrote is what "people" drops.
     *
     * The rule is the account rather than the message tag. Gerrit tags the
     * messages it generates on an upload, but those are the uploader's own and
     * carry the copy condition a rebase dropped a vote by, which is the one
     * thing the log is worth fetching for.
     */
    #[Test]
    public function theReviewLogIsAskedForAndTheServiceUsersHalfIsSeparated(): void
    {
        $asked = [];
        $gerrit = new Gerrit(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return self::reviewed($url);
        });

        self::assertNull($gerrit->change('93319')['changes'][0]['messages']);
        self::assertStringNotContainsString('o=MESSAGES', implode(' ', $asked));

        // The call this one starts at, since the query before it went looking
        // for siblings (`D-ANS-080`) and every query of a call carries the
        // option alike.
        $first = count($asked);
        $whole = $gerrit->change('93319', 1, 'all')['changes'][0];

        self::assertStringContainsString('o=MESSAGES', $asked[$first]);
        self::assertCount(3, $whole['messages']);
        self::assertSame(1, $whole['botMessageCount']);
        self::assertStringContainsString('copy condition', $whole['messages'][2]['message']);
        self::assertSame(21, $whole['messages'][2]['patchSet']);

        $written = $gerrit->change('93319', 1, 'people')['changes'][0];

        self::assertSame(['Benni Mack', 'Benni Mack'], array_column($written['messages'], 'author'));
        // Answered whichever way it was asked, so a zero here is the tag gone
        // rather than a change no bot has been near.
        self::assertSame(1, $written['botMessageCount']);
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

    /**
     * `R-ANS-027`. An empty answer for a named change is two things at once.
     *
     * `feedback/2026-08-07-132416` looked up the Change-Id its own commit
     * carried, got `{"status":"empty","changes":[]}`, and made "this was never
     * pushed" the first finding of a review — recommending its author
     * coordinate with a contributor who was themselves. The change was private,
     * and this server reads Gerrit without credentials, so nothing in that
     * answer could have said otherwise.
     */
    #[Requirement('R-ANS-027')]
    #[Test]
    public function anEmptyAnswerForANamedChangeSaysWhatItCannotSeparate(): void
    {
        $said = GerritLookup::indistinguishable('empty', 'I7701923d80dbd29377213fa71c74ecad88cf7d31');

        self::assertNotNull($said);
        self::assertStringContainsString('without credentials', $said);
        self::assertStringContainsString('private', $said);

        // A search over commit messages is a claim about a query, not about a
        // record the caller is holding, and it has its own sentence.
        self::assertNull(GerritLookup::indistinguishable('empty', ''));
        // Nothing to hedge where something came back.
        self::assertNull(GerritLookup::indistinguishable('answered', '95162'));
        self::assertNull(GerritLookup::indistinguishable('unavailable', '95162'));
    }

    /**
     * Where the tracker settled it, the answer stops hedging.
     *
     * Gerrit Code Review posts a note on the issue for every patch set it
     * receives, so a review URL there and nothing from the review server is not
     * two possibilities — the change exists and this reader may not see it.
     * That is the report's own last idea, and only the issue side of it is
     * built: searching the tracker for a change number costs 2.5 seconds and
     * answers two issues, one unrelated, and searching for a Change-Id answers
     * nothing at all.
     */
    #[Requirement('R-ANS-027')]
    #[Test]
    public function aReviewNoteOnTheIssueTurnsTheHedgeIntoAnAnswer(): void
    {
        $said = GerritLookup::indistinguishable('empty', '', [
            'author' => 'Gerrit Code Review',
            'url' => 'https://review.typo3.org/c/Packages/TYPO3.CMS/+/95162',
        ]);

        self::assertNotNull($said);
        self::assertStringContainsString('does exist', $said);
        self::assertStringContainsString('95162', $said);
        // And it is no longer the sentence that says nothing here separates the
        // two, because something did.
        self::assertStringNotContainsString('nothing here separates them', $said);

        // Still nothing to say where the review server answered.
        self::assertNull(GerritLookup::indistinguishable('answered', '', [
            'author' => 'Gerrit Code Review',
            'url' => 'https://review.typo3.org/c/Packages/TYPO3.CMS/+/95162',
        ]));
    }

    /**
     * `D-SKL-038`. A caller holding one change is about to review it or to
     * fetch it, and both of those are a workflow this server publishes.
     *
     * `feedback/2026-08-12-092545` is the session that shows what the answer
     * was short of: no skill opened, `typo3_project_describe`'s schema was
     * loaded and the tool never called, and this answer — the one call it did
     * make — handed it the ref it then fetched the patch set with by hand.
     */
    #[Test]
    public function aNamedChangeIsHandedTheWorkflowsThatOwnIt(): void
    {
        $said = GerritLookup::workflow('answered', '95169');

        self::assertNotNull($said);
        self::assertStringContainsString('typo3-core-patch-review', $said);
        self::assertStringContainsString('typo3-core-patch-checkout', $said);
        // The call the order opens on, which is the act the session skipped.
        self::assertStringContainsString('typo3_project_describe', $said);
        // And not the tool two sessions finished a task without calling:
        // naming one nobody invokes is what `D-ANS-061` ruled out.
        self::assertStringNotContainsString('typo3_server_scope', $said);

        // The issue form takes none of it. "Has somebody already fixed this"
        // precedes triage, patch development and review alike, so there is no
        // one workflow to name.
        self::assertNull(GerritLookup::workflow('answered', ''));
        // Nothing to hand over where nothing came back.
        self::assertNull(GerritLookup::workflow('empty', '95169'));
        self::assertNull(GerritLookup::workflow('unavailable', '95169'));
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
