<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Todo;

final class TodoTest extends TestCase
{
    /**
     * The file is read by a session that has read nothing else, and three kinds
     * of section in it look identical from the outside: the standing ones, the
     * queue, and the sections that are neither. The line that says which is the
     * only thing keeping "not queued, and deliberately so" from reading as the
     * next piece of work.
     */
    #[Test]
    public function everySectionSaysWhichOfTheThreeItIs(): void
    {
        $sections = Todo::sections();

        self::assertNotSame([], $sections);
        foreach ($sections as $section) {
            self::assertContains(
                $section['kind'],
                ['standing', 'item', 'reference'],
                '"' . $section['title'] . '" opens with ' . ($section['marker'] === '' ? 'nothing' : $section['marker']),
            );
        }
    }

    /**
     * `bin/cli next` performs the two readings a session owes rather than
     * naming them, so exactly one section has to be each. None and the command
     * silently stops doing half its job; two and it does it twice.
     */
    #[Test]
    public function theTwoStandingReadingsAreClaimedOnce(): void
    {
        $runs = array_column(Todo::standing(), 'runs');

        foreach ($runs as $what) {
            self::assertContains($what, Todo::STANDING, $what . ' is not something a standing section can be');
        }
        foreach (['notes', 'backlog'] as $reading) {
            self::assertSame([$reading], array_values(array_filter($runs, static fn (string $r): bool => $r === $reading)));
        }
    }

    /**
     * An item that serves nothing is an idea, and one without a next concrete
     * step is worse than no item at all: a session that reads it cannot start.
     * What it names has to be readable too — a note is deleted by the commit
     * that closes it, and an item still naming one is either finished or has a
     * part left that nobody has trimmed it down to.
     */
    #[Test]
    public function everyItemAnswersForSomethingThatCanStillBeRead(): void
    {
        $items = Todo::items();

        self::assertNotSame([], $items, 'nothing is queued, which is a state the file can be in but not silently');
        foreach ($items as $item) {
            self::assertNotSame([], $item['serves'], '"' . $item['title'] . '" serves nothing');
            self::assertNotSame('', $item['body'], '"' . $item['title'] . '" has no next concrete step');
            foreach ($item['serves'] as $what) {
                self::assertNull(
                    Todo::unreadable($what),
                    '"' . $item['title'] . '" serves ' . $what . ', ' . Todo::unreadable($what),
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
     * What the queue answers for is read from the items alone. The section
     * listing what is deliberately *not* queued names ids too, and counting
     * those makes an entry nobody has taken on look taken on — which is the one
     * thing `bin/cli backlog list` exists to say out loud.
     */
    #[Test]
    public function whatIsNotAnItemAnswersForNothing(): void
    {
        $served = Todo::serves();

        foreach (Todo::sections() as $section) {
            if ($section['kind'] === 'item') {
                continue;
            }
            self::assertSame([], $section['serves'], '"' . $section['title'] . '" is not an item and serves something');
        }

        self::assertSame($served, array_unique($served));
    }
}
