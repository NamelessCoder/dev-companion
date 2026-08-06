<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\QueuedTodo;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Command\TodoSync;
use TYPO3\DevCompanion\Upkeep\OpenFeedback;
use TYPO3\DevCompanion\Upkeep\Todo;

/**
 * @phpstan-import-type Section from Todo
 */
final class TodoTest extends TestCase
{
    use QueuedTodo;

    /**
     * A todo is read by a session that has read nothing else, and the files
     * look identical from the outside. Where one sits is what keeps "not
     * queued, and deliberately so" from reading as the next piece of work, and
     * the head it opens with is the rest of what a reader is owed.
     */
    #[Test]
    public function everyTodoSaysWhatItIsBeforeItSaysAnythingElse(): void
    {
        $todos = Todo::all();

        self::assertNotSame([], $todos);
        foreach ($todos as $todo) {
            self::assertNotSame('', $todo['title'], $todo['path'] . ' opens with no heading');
            self::assertSame([], $todo['strays'], $todo['path'] . ' opens with lines that are no field');
            self::assertContains($todo['kind'], ['queue', 'recurring', 'progress', 'waiting', 'reference'], $todo['path']);
        }
    }

    /**
     * `bin/cli todo:next` performs the readings a session owes rather than naming
     * them, so exactly one todo has to run each. None and the command silently
     * stops doing half its job; two and it does it twice.
     */
    #[Test]
    public function theStandingReadingsAreRunOnce(): void
    {
        $run = array_merge(...array_column(Todo::recurring(), 'run'));

        foreach (Todo::READINGS as $reading) {
            self::assertSame([$reading], array_values(array_filter($run, static fn(string $r): bool => $r === $reading)));
        }
    }

    /**
     * The board is where a feedback waits, so one that is on none is one no
     * session will be handed. That used to be a sighting's job and is now a
     * card per feedback, which means the failure has moved: nothing prints the
     * pile any more, so a feedback with no card is invisible rather than merely
     * far down a list.
     *
     * It holds what `bin/cli todo:sync` leaves behind rather than the command
     * itself. A feedback recorded since the last run of it is exactly the case
     * this would catch, and the answer is to run it.
     *
     * One assertion over the set rather than one per feedback, because an empty
     * `feedback/` is the state the board is in whenever everything is worked
     * off — `D-FBK-013` — and a loop asserts nothing there, which PHPUnit
     * reports as risky and CI reads as a failure.
     */
    #[Test]
    public function everyOpenFeedbackIsOnTheBoard(): void
    {
        $served = Todo::serves();

        self::assertSame(
            [],
            array_values(array_diff(array_column(OpenFeedback::all(), 'file'), $served)),
            'a feedback is open and no todo answers for it — `bin/cli todo:sync`',
        );
    }

    /**
     * The same relation from above, which is the half `bin/cli todo:sync` cannot
     * see: it skips a feedback a todo already serves, so it never writes a
     * second card, and it never looks at the card that was already there when a
     * later judgement folded that feedback onto another todo. What is left is a
     * card asking for a judgement somebody has made, ten lines away in a listing
     * and sharing no word with it — which is one claimed session, spent
     * arriving where the repository already was (`D-FBK-040`).
     */
    #[Test]
    public function noJudgementLeavesBehindTheCardItReplaced(): void
    {
        self::assertSame(
            [],
            array_column(Todo::folded(), 'card'),
            'a card still asks for a judgement another todo serves — delete the card the judgement replaced',
        );
    }

    /**
     * What tells the two apart is the step, and the step is a constant: every
     * card `bin/cli todo:sync` writes carries the same sentence, because judging
     * one feedback is the same work whichever it is, and a judgement is what
     * replaces it. So a body still equal to `TodoSync::STEP` is a card nobody
     * has judged, and beside a todo that serves the same feedback it is the one
     * to delete.
     *
     * Both other readings are here because both are legitimate and neither may
     * be reported: a card standing alone is the ordinary state of the board, and
     * a feedback a judgement split across two todos is two pieces of work rather
     * than a pair — what is wrong is an unjudged card beside a judged one, not
     * two cards.
     */
    #[Test]
    public function theCardAJudgementReplacedIsFoundByTheStepItStillCarries(): void
    {
        $feedback = 'feedback/' . self::MARKER . '.md';
        $card = $this->queueATodo('low', '2026-08-02-090000', $feedback, TodoSync::STEP);

        self::assertSame([], Todo::folded(), 'a card nobody has judged yet is one somebody has');

        $judged = $this->queueATodo('normal', '2026-08-03-090000', $feedback);

        self::assertSame(
            [['card' => $card['path'], 'feedback' => $feedback, 'judged' => [$judged['path']]]],
            Todo::folded(),
        );

        $split = $this->queueATodo('normal', '2026-08-03-090001', $feedback);

        self::assertSame(
            [['card' => $card['path'], 'feedback' => $feedback, 'judged' => [$judged['path'], $split['path']]]],
            Todo::folded(),
            'a feedback split across two judged todos is reported as a card nobody judged',
        );
    }

    /**
     * A cadence measured in days is what keeps five sessions in an afternoon
     * from asking the same question five times, and it can only do that if the
     * date it counts from is one PHP can read. A todo in the queue carries no
     * cadence at all: what comes round is never deleted, and the queue is what
     * a commit empties.
     */
    #[Test]
    public function whatRecursOnAClockCarriesADateItCanBeCountedFrom(): void
    {
        foreach (Todo::items() as $item) {
            self::assertSame('', $item['every'], $item['path'] . ' is queued and recurs');
        }

        foreach (Todo::recurring() as $todo) {
            if ($todo['every'] === 'session') {
                self::assertSame('', $todo['checked'], $todo['path'] . ' recurs every session and is dated');
                self::assertTrue(Todo::due($todo['every'], $todo['checked']));
                continue;
            }

            self::assertMatchesRegularExpression('/^\d+ days?$/', $todo['every'], $todo['path']);
            self::assertIsInt(strtotime($todo['checked']), $todo['path'] . ' was last checked ' . $todo['checked']);
        }

        self::assertFalse(Todo::due('7 days', '2026-07-01', '2026-07-05'));
        self::assertTrue(Todo::due('7 days', '2026-07-01', '2026-07-08'));
        self::assertTrue(Todo::due('7 days', '', '2026-07-05'), 'a todo nobody has dated is one that gets looked at');
    }

    /**
     * A todo that serves nothing is an idea, and one without a next concrete
     * step is worse than no todo at all: a session that reads it cannot start.
     * What it names has to be readable too — a feedback is deleted by the commit
     * that closes it, and a todo still naming one is either finished or has a
     * part left that nobody has trimmed it down to.
     */
    #[Test]
    public function everyTodoAnswersForSomethingThatCanStillBeRead(): void
    {
        $todos = array_merge(Todo::recurring(), Todo::items(), Todo::progress(), Todo::waiting());

        // Not the queue, which empties and is meant to: what is never empty is
        // what recurs, because a recurring todo is never deleted.
        self::assertNotSame([], $todos, 'no todo of any kind is readable, and one of them comes round every session');
        foreach ($todos as $todo) {
            self::assertNotSame([], $todo['serves'], $todo['path'] . ' serves nothing');
            self::assertNotSame('', $todo['body'], $todo['path'] . ' has no next concrete step');
            foreach ($todo['serves'] as $what) {
                self::assertNull(
                    Todo::unreadable($what),
                    $todo['path'] . ' serves ' . $what . ', ' . Todo::unreadable($what),
                );
            }
        }
    }

    /**
     * A todo that waits is out of the queue and says what it waits on, which is
     * the whole of what the state adds: `bin/cli todo:next` offers it to nobody, so
     * the question it is blocked on is asked by no session again. What it took
     * on still counts as taken on — a waiting todo that stopped answering for
     * its requirement would put that requirement back among the unresolved for the
     * next session to queue a second time.
     */
    #[Test]
    public function whatWaitsCarriesTheQuestionItWaitsOn(): void
    {
        $served = Todo::serves();

        foreach (Todo::waiting() as $todo) {
            self::assertNotSame('', $todo['waitingOn'], $todo['path'] . ' waits and does not say on what');
            self::assertSame('', $todo['every'], $todo['path'] . ' waits and recurs');
            foreach ($todo['serves'] as $what) {
                self::assertContains($what, $served, $todo['path'] . ' waits and answers for nothing');
            }
        }
    }

    /**
     * The queue is an order, not an assignment: `bin/cli todo:next` reads the
     * same first item for everybody who asks, which is right while one session
     * works at a time and wrong the moment two do. What is in hand is out of
     * the queue, so the second session is handed the item behind it rather than
     * the same one — and it still answers for what it took on, because a
     * requirement that fell back among the unresolved while somebody was working on
     * it is one a second session queues all over again.
     */
    #[Test]
    public function whatIsInHandIsOfferedToNobodyElse(): void
    {
        $this->queueATodo();
        $queued = Todo::items();
        $this->claimInProgress('todo/' . self::MARKER);

        $inHand = $this->ownTodos(Todo::progress());

        self::assertCount(1, $inHand);
        self::assertSame('progress', $inHand[0]['kind']);
        self::assertSame('todo/' . self::MARKER, $inHand[0]['branch']);
        self::assertSame('2026-08-01', $inHand[0]['claimed']);
        self::assertSame($queued, Todo::items(), 'a todo in hand is still in the queue somebody else reads');
        self::assertContains('todo/', Todo::serves(), 'a todo in hand answers for nothing it took on');
    }

    /**
     * The branch a claim carries is the one it was given, not the one its name
     * derives to.
     *
     * They are the same claim after claim until a todo is worked twice: one
     * released and taken on again derives the branch the first session left
     * standing, and `git worktree add -b` there fails on a name that is already
     * a piece of half-finished work. So the second claim is given a free name
     * and the file records it — which is what `claimed()` matches on, so a
     * session in that worktree is handed its own todo and not nothing.
     */
    #[Test]
    public function aClaimCarriesTheBranchItWasGivenRatherThanTheOneItDerivesTo(): void
    {
        $queued = $this->queueATodo();

        Todo::claim($queued, '2026-08-01', Todo::branch($queued) . '-2');

        $inHand = $this->ownTodos(Todo::progress())[0];
        self::assertSame('todo/' . self::MARKER . '-2', $inHand['branch']);
        self::assertNotSame(
            Todo::branch($queued),
            $inHand['branch'],
            'the claim records the derived branch, so a second one would be cut on the first one\'s work',
        );
    }

    /**
     * Taking one on and putting it back are one move in two directions, and the
     * second is what keeps the first usable: a session ends where it ends, and
     * a state that can only be entered fills up with claims nobody is working
     * and nobody else is offered.
     *
     * What the round trip has to leave intact is the todo — a claim rewrites
     * the head and nothing else, because the step is the part somebody else has
     * to be able to start from. What it must not leave is the claim itself: a
     * branch nobody is on reads exactly like a branch somebody is on.
     *
     * The name survives both moves, which is what the stamp bought over the
     * number: a released todo comes back where it was, at the priority somebody
     * gave it, rather than at the end of a queue it never asked to leave.
     * Putting it further down is a judgement now, and judgements are written.
     */
    #[Test]
    public function aClaimIsOneMoveThatGoesBothWays(): void
    {
        $queued = $this->queueATodo();

        $claimed = Todo::claim($queued, '2026-08-01');

        self::assertSame('todo/progress/' . basename($queued['path']), $claimed);
        $inHand = $this->ownTodos(Todo::progress())[0];
        self::assertSame('todo/' . self::MARKER, $inHand['branch']);
        self::assertSame('2026-08-01', $inHand['claimed']);
        self::assertSame($queued['title'], $inHand['title']);
        self::assertSame($queued['body'], $inHand['body'], 'the step is what somebody else has to start from');
        self::assertSame($queued['serves'], $inHand['serves']);

        $released = Todo::release($inHand);

        self::assertSame($queued['path'], $released, 'a released todo comes back somewhere else');
        $back = $this->ownTodos(Todo::items())[0];
        self::assertSame([], $this->ownTodos(Todo::progress()));
        self::assertSame('', $back['branch'], 'a released todo keeps a branch nobody is on');
        self::assertSame('', $back['claimed']);
        self::assertSame($queued['body'], $back['body']);
        self::assertSame($queued['priority'], $back['priority'], 'a release re-ranks what nobody asked it to');
    }

    /**
     * The other way out of `progress/`, and the one the first parallel run
     * produced: the work came home and a question was left over.
     *
     * `progress/` is for as long as a branch is live. Once the branch is merged
     * and deleted, a claim still sitting there names a branch nobody can look
     * at — and the todo itself is blocked on a person, which is the state
     * `waiting/` already existed for. Which of the two it is, is written in the
     * claim by the session that hit the question, so nothing has to be asked.
     */
    #[Test]
    public function aClaimThatCarriesAQuestionIsReleasedIntoWaiting(): void
    {
        $this->claimInProgress('todo/' . self::MARKER, '2026-08-01', 'which of the two shapes is wanted.');
        $inHand = $this->ownTodos(Todo::progress())[0];

        $released = Todo::release($inHand);

        self::assertSame('todo/waiting/' . self::MARKER . '.md', $released);
        $waiting = $this->ownTodos(Todo::waiting())[0];
        self::assertSame('which of the two shapes is wanted.', $waiting['waitingOn'], 'the question is what the state carries');
        self::assertSame('', $waiting['branch'], 'a waiting todo names a branch somebody is about to delete');
        self::assertSame('', $waiting['claimed']);
        self::assertSame([], $this->ownTodos(Todo::items()), 'a todo blocked on a person is queued behind work nobody is blocked on');
    }

    /**
     * `bin/cli todo:next` is where a session starts, and that has to keep being
     * true where several sessions start at once. A worktree standing on a claim
     * is handed that claim; a checkout standing on no claim is handed the
     * queue, which is every session this repository had before there were two.
     *
     * The failure this holds off is a quiet one. A session handed the front of
     * the queue instead of its own claim reads a real todo, starts real work,
     * and is the second person doing it.
     *
     * git is stubbed rather than asked, so the case says the same in a
     * worktree standing on a real claim as it does on `main`. It used to ask
     * whichever checkout the suite was in, and then had to assert around
     * whatever that answered.
     */
    #[Test]
    public function aWorktreeStandingOnAClaimIsHandedThatClaim(): void
    {
        // The queue is made before the first read, so `claimed()` answers from
        // this case's own rather than from whatever the checkout is carrying.
        $this->ownQueue();

        $branch = 'todo/' . self::MARKER;
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturn(
            ['ok' => true, 'exitCode' => 0, 'output' => $branch . "\n", 'error' => ''],
        );
        Checkouts::useRunner($git);
        $before = Todo::claimed();

        $this->claimInProgress($branch);
        $onTheClaim = Todo::claimed();

        self::assertNull($before, 'a claim was matched before one was written');
        self::assertNotNull($onTheClaim, 'a checkout standing on a claim is handed the queue');
        self::assertSame($branch, $onTheClaim['branch']);
        self::assertSame('progress', $onTheClaim['kind']);
    }

    /**
     * A worktree standing on no claim is the setup that went wrong, and the
     * only thing that can say so is that it is a worktree at all.
     *
     * `claimed()` answers null for it and null for the main checkout on `main`,
     * and one of those is every session this repository had before there were
     * two. So `bin/cli todo:next` refuses in the first case and hands over the
     * queue in the second, and this is the question it tells them apart by.
     *
     * What `linked()` does with git's two answers is the part that can be
     * wrong, and it is what this holds: the directories are compared with
     * trailing slashes off, and either call failing is read as "not a worktree"
     * rather than as one. It used to answer by making a real worktree and a
     * real branch in whichever checkout the suite was running in, which is a
     * process and a write — `R-COD-003`.
     *
     * What a stub cannot say is whether the local git has
     * `--path-format=absolute` at all. That is a property of the machine rather
     * than of this code: where it is missing both calls fail, which is the last
     * case below, and the refusal quietly stops happening. A real run is what
     * would show that.
     *
     * @param array{0: int, 1: string} $own
     * @param array{0: int, 1: string} $shared
     */
    #[Test]
    #[DataProvider('whatGitAnswersAboutTwoDirectories')]
    public function aWorktreeIsToldApartFromTheCheckoutItWasCutFrom(array $own, array $shared, bool $linked): void
    {
        $root = '/somewhere/checkout';
        $git = self::createStub(CommandRunner::class);
        // Two calls, in the order `linked()` makes them: its own git dir, then
        // the one it shares. `willReturnOnConsecutiveCalls` is what says that
        // without repeating the argument lists the method signature has.
        $git->method('run')->willReturnOnConsecutiveCalls(
            ['ok' => $own[0] === 0, 'exitCode' => $own[0], 'output' => $own[1], 'error' => ''],
            ['ok' => $shared[0] === 0, 'exitCode' => $shared[0], 'output' => $shared[1], 'error' => ''],
        );
        Checkouts::useRunner($git);

        self::assertSame($linked, Todo::linked($root));
    }

    /**
     * @return array<string, array{0: array{0: int, 1: string}, 1: array{0: int, 1: string}, 2: bool}>
     */
    public static function whatGitAnswersAboutTwoDirectories(): array
    {
        return [
            'a worktree keeps its own git dir under the one they share' => [
                [0, "/repo/.git/worktrees/claim\n"],
                [0, "/repo/.git\n"],
                true,
            ],
            'the checkout they were cut from answers the same directory twice' => [
                [0, "/repo/.git\n"],
                [0, "/repo/.git\n"],
                false,
            ],
            'a trailing slash on one of them is not a difference' => [
                [0, "/repo/.git\n"],
                [0, "/repo/.git/\n"],
                false,
            ],
            'the shared one answered nothing, so nothing is claimed' => [
                [0, "/repo/.git/worktrees/claim\n"],
                [128, "fatal: unknown option\n"],
                false,
            ],
            'both answered nothing, which is a git without the flag' => [
                [128, "fatal: unknown option\n"],
                [128, "fatal: unknown option\n"],
                false,
            ],
        ];
    }

    /**
     * The priority decides, and the age decides the rest. Written as the one
     * case where the two disagree: the newest todo is the highest one, so a
     * queue read by age alone would hand it over last, and one read by priority
     * alone could not tell the two `low` ones apart.
     */
    #[Test]
    public function theQueueIsReadByPriorityAndThenByAge(): void
    {
        $older = $this->queueATodo('low', '2026-07-01-090000');
        $newer = $this->queueATodo('low', '2026-07-02-090000');
        $ordinary = $this->queueATodo('normal', '2026-07-03-090000');
        $urgent = $this->queueATodo('high', '2026-07-04-090000');

        $queued = array_column($this->ownTodos(Todo::items()), 'path');

        self::assertSame(
            [$urgent['path'], $ordinary['path'], $older['path'], $newer['path']],
            $queued,
            'the queue is read in an order its priorities and stamps do not have',
        );
    }

    /**
     * Every todo in a stage says where it stands, and one that recurs does not:
     * the clock is what orders an appointment, so a word beside it would answer
     * the same question twice.
     *
     * The point of requiring it is that it can then be missed. While absence
     * meant "nobody has judged this", a priority somebody forgot and one left
     * off on purpose were the same file, and no check could name either.
     */
    #[Test]
    public function everyTodoInAStageSaysWhereItStands(): void
    {
        foreach (array_merge(Todo::items(), Todo::progress(), Todo::waiting()) as $todo) {
            self::assertContains($todo['priority'], Todo::PRIORITIES, $todo['path'] . ' says nothing about where it stands');
        }

        foreach (Todo::recurring() as $todo) {
            self::assertSame('', $todo['priority'], $todo['path'] . ' recurs and is prioritised as well');
        }
    }

    /**
     * What the queue answers for is read from the queue alone. The page listing
     * what is deliberately *not* queued names ids too, and counting those makes
     * an entry nobody has taken on look taken on — which is the one thing
     * `bin/cli unresolved:list` exists to say out loud. Nor does a recurring todo
     * take anything on: it watches a directory, and the same directory being
     * named by a queued todo is the difference between noticing that decisions
     * are standing and sorting them.
     */
    #[Test]
    public function onlyTheQueueAnswersForAnything(): void
    {
        $served = Todo::serves();

        foreach (Todo::references() as $reference) {
            self::assertSame([], $reference['serves'], $reference['path'] . ' is not a todo and serves something');
        }
        foreach (Todo::recurring() as $todo) {
            foreach ($todo['serves'] as $what) {
                self::assertStringEndsWith('/', $what, $todo['path'] . ' recurs and takes on ' . $what);
            }
        }

        self::assertSame($served, array_unique($served));
    }

    /**
     * A todo prints as an imperative paragraph, and the two things that decide
     * whether the change is right happen before its first sentence: reading
     * what it serves against what the code does now, and settling a question
     * from a source instead of from recall. Neither leaves a trace — the diff
     * of a todo worked from the checkouts is the diff of one worked from
     * memory — so what can be held is that the procedure exists and that the
     * command hands it over with the work rather than leaving it to be looked
     * up. `R-FBK-009` says why; `D-FBK-007` says what it bets on.
     */
    #[Test]
    public function everyTodoIsHandedWithThePageThatSaysHowOneIsWorked(): void
    {
        $page = Paths::root() . '/' . Todo::PROCEDURE;

        self::assertFileExists($page, Todo::PROCEDURE . ' is handed over with every todo and does not exist');
        // The map links it the way a reader standing in documentation/ would,
        // which is the path from there rather than the bare filename: the
        // procedures are grouped by subject and the page sits in one of them.
        self::assertStringContainsString(
            '(' . substr(Todo::PROCEDURE, strlen('documentation/')) . ')',
            (string) file_get_contents(Paths::root() . '/documentation/readme.md'),
            Todo::PROCEDURE . ' is not listed with the other procedures',
        );
        self::assertStringContainsString(
            'Todo::PROCEDURE',
            (string) file_get_contents(Paths::root() . '/src/Upkeep/Command/TodoNext.php'),
            '`bin/cli todo:next` hands over no todo with ' . Todo::PROCEDURE,
        );
    }

    /**
     * The same for the page a claim is handed over with. `bin/cli todo:claim`
     * moves files and names branches, which is the half this repository owns;
     * the worktree, what a question that arrives mid-work leaves behind and who
     * merges are not things a command can carry out. A claim taken without them
     * is a lock nobody knows how to release.
     */
    #[Test]
    public function everyClaimIsHandedWithThePageThatSaysHowSeveralAreWorked(): void
    {
        self::assertFileExists(
            Paths::root() . '/' . Todo::PARALLEL,
            Todo::PARALLEL . ' is handed over with every claim and does not exist',
        );
        self::assertStringContainsString(
            '(' . substr(Todo::PARALLEL, strlen('documentation/')) . ')',
            (string) file_get_contents(Paths::root() . '/documentation/readme.md'),
            Todo::PARALLEL . ' is not listed with the other procedures',
        );
        self::assertStringContainsString(
            'Todo::PARALLEL',
            (string) file_get_contents(Paths::root() . '/src/Upkeep/Command/TodoClaim.php'),
            '`bin/cli todo:claim` hands over no claim with ' . Todo::PARALLEL,
        );
    }
}
