<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\Todo;

final class TodoTest extends TestCase
{
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
            self::assertContains($todo['kind'], ['queue', 'recurring', 'waiting', 'reference'], $todo['path']);
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
        $todos = array_merge(Todo::recurring(), Todo::items(), Todo::waiting());

        self::assertNotSame([], Todo::items(), 'nothing is queued, which is a state this can be in but not silently');
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
     * its requirement would put that requirement back on the backlog for the
     * next session to queue a second time.
     */
    #[Test]
    public function whatWaitsCarriesTheQuestionItWaitsOn(): void
    {
        $served = Todo::serves();

        foreach (Todo::waiting() as $todo) {
            self::assertNotSame('', $todo['waitingOn'], $todo['path'] . ' waits and does not say on what');
            self::assertSame('', $todo['position'], $todo['path'] . ' waits and has a place in the queue');
            self::assertSame('', $todo['every'], $todo['path'] . ' waits and recurs');
            foreach ($todo['serves'] as $what) {
                self::assertContains($what, $served, $todo['path'] . ' waits and answers for nothing');
            }
        }
    }

    /**
     * The order is in the names, which is what lets a session finish a todo by
     * deleting one file and move one by renaming it. Two files claiming one
     * number leave the order to whatever the file system answers with.
     */
    #[Test]
    public function theQueueIsTheOrderItsNumbersHaveIt(): void
    {
        $numbers = [];
        foreach (Todo::items() as $item) {
            self::assertMatchesRegularExpression('/^\d+$/', $item['position'], $item['path'] . ' is queued and unnumbered');
            $numbers[] = (int) $item['position'];
        }

        self::assertSame($numbers, array_unique($numbers), 'two todos claim the same place in the queue');
        $sorted = $numbers;
        sort($sorted);
        self::assertSame($sorted, $numbers, 'the queue is read in an order its numbers do not have');
    }

    /**
     * What the queue answers for is read from the queue alone. The page listing
     * what is deliberately *not* queued names ids too, and counting those makes
     * an entry nobody has taken on look taken on — which is the one thing
     * `bin/cli backlog:list` exists to say out loud. Nor does a recurring todo
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
     * up. `R-FBK-9` says why; `D-FBK-7` says what it bets on.
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
}
