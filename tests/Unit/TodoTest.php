<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Todo;

final class TodoTest extends TestCase
{
    /**
     * The file is read by a session that has read nothing else, and the
     * sections in it look identical from the outside: what recurs, the queue,
     * and what is neither. The head that says which is the only thing keeping
     * "not queued, and deliberately so" from reading as the next piece of work.
     */
    #[Test]
    public function everySectionSaysWhatItIs(): void
    {
        $sections = Todo::sections();

        self::assertNotSame([], $sections);
        foreach ($sections as $section) {
            self::assertContains(
                $section['kind'],
                ['todo', 'reference'],
                '"' . $section['title'] . '" opens with ' . ($section['head'] === '' ? 'nothing' : $section['head']),
            );
            self::assertSame([], $section['strays'], '"' . $section['title'] . '" opens with lines that are no field');
        }
    }

    /**
     * `bin/cli next` performs the readings a session owes rather than naming
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
     * date it counts from is one PHP can read.
     */
    #[Test]
    public function whatRecursOnAClockCarriesADateItCanBeCountedFrom(): void
    {
        foreach (Todo::recurring() as $todo) {
            if ($todo['every'] === 'session') {
                self::assertSame('', $todo['checked'], '"' . $todo['title'] . '" recurs every session and is dated');
                self::assertTrue(Todo::due($todo['every'], $todo['checked']));
                continue;
            }

            self::assertMatchesRegularExpression('/^\d+ days?$/', $todo['every'], '"' . $todo['title'] . '"');
            self::assertIsInt(strtotime($todo['checked']), '"' . $todo['title'] . '" was last checked ' . $todo['checked']);
        }

        self::assertFalse(Todo::due('7 days', '2026-07-01', '2026-07-05'));
        self::assertTrue(Todo::due('7 days', '2026-07-01', '2026-07-08'));
        self::assertTrue(Todo::due('7 days', '', '2026-07-05'), 'a todo nobody has dated is one that gets looked at');
    }

    /**
     * A todo that serves nothing is an idea, and one without a next concrete
     * step is worse than no todo at all: a session that reads it cannot start.
     * What it names has to be readable too — a note is deleted by the commit
     * that closes it, and a todo still naming one is either finished or has a
     * part left that nobody has trimmed it down to.
     */
    #[Test]
    public function everyTodoAnswersForSomethingThatCanStillBeRead(): void
    {
        $todos = array_merge(Todo::recurring(), Todo::items());

        self::assertNotSame([], Todo::items(), 'nothing is queued, which is a state the file can be in but not silently');
        foreach ($todos as $todo) {
            self::assertNotSame([], $todo['serves'], '"' . $todo['title'] . '" serves nothing');
            self::assertNotSame('', $todo['body'], '"' . $todo['title'] . '" has no next concrete step');
            foreach ($todo['serves'] as $what) {
                self::assertNull(
                    Todo::unreadable($what),
                    '"' . $todo['title'] . '" serves ' . $what . ', ' . Todo::unreadable($what),
                );
            }
        }
    }

    /**
     * The order is the order: the queue is what the file has, in the sequence
     * the file has it, because moving something up is how a change of plan gets
     * written down before the work starts.
     */
    #[Test]
    public function theQueueIsTheOrderTheFileHasIt(): void
    {
        $contents = (string) file_get_contents(Todo::file());

        $previous = -1;
        foreach (Todo::sections() as $section) {
            $position = strpos($contents, '## ' . $section['title']);
            self::assertIsInt($position, '"' . $section['title'] . '" is not a heading of the file');
            self::assertGreaterThan($previous, $position, '"' . $section['title'] . '" is read out of order');
            $previous = $position;
        }
    }

    /**
     * What the queue answers for is read from the queue alone. The section
     * listing what is deliberately *not* queued names ids too, and counting
     * those makes an entry nobody has taken on look taken on — which is the one
     * thing `bin/cli backlog list` exists to say out loud. Nor does a recurring
     * todo take anything on: it watches a directory, and the same directory
     * being named by a todo in the queue is the difference between noticing
     * that decisions are standing and sorting them.
     */
    #[Test]
    public function onlyTheQueueAnswersForAnything(): void
    {
        $served = Todo::serves();

        foreach (Todo::references() as $reference) {
            self::assertSame([], $reference['serves'], '"' . $reference['title'] . '" is not a todo and serves something');
        }
        foreach (Todo::recurring() as $todo) {
            foreach ($todo['serves'] as $what) {
                self::assertStringEndsWith('/', $what, '"' . $todo['title'] . '" recurs and takes on ' . $what);
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
        self::assertStringContainsString(
            '[' . basename(Todo::PROCEDURE) . '](' . basename(Todo::PROCEDURE) . ')',
            (string) file_get_contents(Paths::root() . '/documentation/readme.md'),
            Todo::PROCEDURE . ' is not listed with the other procedures',
        );
        self::assertStringContainsString(
            'Todo::PROCEDURE',
            (string) file_get_contents(Paths::root() . '/src/Cli.php'),
            '`bin/cli next` hands over no todo with ' . Todo::PROCEDURE,
        );
    }
}
