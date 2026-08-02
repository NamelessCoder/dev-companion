<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Typo3CmsMcp\Tests\Support\QueuedTodo;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Command\TodoClaim;
use Typo3CmsMcp\Upkeep\Todo;

final class CliTest extends TestCase
{
    use QueuedTodo;

    /**
     * The queue is worked before what recurs every session, and this is the
     * test because the failure was silent for as long as it existed. A sighting
     * is due while anything is unjudged, feedback arrive from every session
     * everywhere, and one session judges a handful — so with the sightings
     * asked first, every session opened on the same one and no queued item was
     * ever reached. Nothing was broken, nothing failed, and the queue simply
     * never came up.
     *
     * The queue it needs is written rather than assumed. An empty one is the
     * state the sightings are *for*, so a case about what happens while items
     * wait cannot also demand that the repository always have some.
     */
    #[Test]
    public function theSightingsWaitForAnEmptyQueue(): void
    {
        $this->queueATodo();

        self::assertNotSame([], Todo::sightings(), 'nothing recurs every session, so nothing could wait for the queue');

        $buffer = new BufferedOutput();
        Cli::application()->doRun(new StringInput('todo:next'), $buffer);
        $printed = $buffer->fetch();

        foreach (Todo::sightings() as $sighting) {
            self::assertStringNotContainsString(
                $sighting['title'],
                $printed,
                '"' . $sighting['title'] . '" is a sighting and came up while the queue still had ' . count(Todo::items()) . ' items',
            );
        }
    }

    /**
     * Every recurring todo is one of the two, and the cadence is what says
     * which: a clock makes it an appointment, `session` makes it a sighting.
     * `bin/cli todo:next` asks the one group before the queue and the other after
     * it, so a todo in neither would be asked at no point at all.
     */
    #[Test]
    public function whatRecursIsEitherAnAppointmentOrASighting(): void
    {
        $split = array_merge(Todo::appointments(), Todo::sightings());

        self::assertEqualsCanonicalizing(
            array_column(Todo::recurring(), 'title'),
            array_column($split, 'title'),
            'the two groups are not the recurring todos',
        );
    }

    /**
     * The message every session of a parallel run is started with, held to the
     * one property that makes it sendable: there is nothing in it to fill in.
     *
     * It is a test rather than a note because of how the failure looked. The
     * message went out with `<absolute path to the worktree>` still in it, and
     * the session on the other end had no way to tell an unfilled blank from
     * something it was being told — the placeholder is not made safer here, it
     * is absent. What the message therefore cannot name is the todo, the branch
     * or the directory, and all three are read out of the checkout instead.
     */
    #[Test]
    public function theMessageASessionIsStartedWithHasNothingToFillIn(): void
    {
        self::assertDoesNotMatchRegularExpression(
            '/<[a-z][^>]*>/',
            Todo::BRIEFING,
            'the message carries a placeholder, and a caller who leaves it standing sends it',
        );
        self::assertStringContainsString(
            'bin/cli todo:next --worktree',
            Todo::BRIEFING,
            'the message names no way for the session to find out which todo is its own',
        );
    }

    /**
     * The other half of that output, held to the same property.
     *
     * Taking the blanks out of the message left one above it: *with that
     * worktree as its working directory* is a property a person satisfies, and
     * the run of 2026-08-02 satisfied it with the directory that was already
     * open — the checkout the worktrees are cut from. Every session it started
     * was refused, correctly, hours of setup later. So each worktree is a line
     * to run, absolute, and the message says where it starts, because a caller
     * with nothing to compose composes nothing wrong.
     */
    #[Test]
    public function whereTheSessionsAreStartedIsPrintedRatherThanDescribed(): void
    {
        $handover = TodoClaim::handover('/checkout', ['.worktrees/first', '.worktrees/second']);

        self::assertStringContainsString('cd /checkout/.worktrees/first', $handover);
        self::assertStringContainsString(
            'cd /checkout/.worktrees/second',
            $handover,
            'a session is left to work out its own directory, which is the blank this replaced',
        );
        self::assertStringContainsString(
            Todo::BRIEFING,
            (string) preg_replace('/^ {4}/m', '', $handover),
            'the message the sessions are sent is not in what the caller is handed',
        );
        self::assertStringContainsString(
            TodoClaim::LAUNCH,
            $handover,
            'nothing says the step can stop being the caller\'s, so it stays the caller\'s forever',
        );
    }

    /**
     * A session that says it is one of several is never handed the queue.
     *
     * Everything else `todo:next` does is right here and wrong by one step: the
     * front of the queue is a real todo, correctly read, and somebody else is
     * already writing it. The two cases the run of 2026-08-01 produced are a
     * worktree cut before the claim was on `main` and a session started in the
     * main checkout at all, and neither is visible from inside the session —
     * which is why the answer is the command's rather than the prompt's.
     *
     * It is written to hold wherever the suite runs, because it runs in the
     * worktrees too: standing on a claim the answer is that claim, standing on
     * none it is a refusal, and the queue is neither.
     */
    #[Test]
    public function whatIsAskedForOneOfSeveralSessionsIsNeverTheQueue(): void
    {
        // The queue is what must not be handed over, so there has to be one to
        // withhold — and the repository is allowed to have none of its own.
        $this->queueATodo();

        $buffer = new BufferedOutput();
        $exit = Cli::application()->doRun(new StringInput('todo:next --worktree'), $buffer);
        $printed = $buffer->fetch();

        $claim = Todo::claimed();
        if ($claim === null) {
            self::assertSame(1, $exit, 'a session standing on no claim was served rather than stopped');
            self::assertStringNotContainsString(Todo::items()[0]['title'], $printed, 'the queue was handed over anyway');

            return;
        }

        self::assertSame(0, $exit);
        self::assertStringContainsString($claim['title'], $printed, 'the claim under the session is not what it was handed');
        self::assertStringContainsString($claim['branch'], $printed, 'nothing says which branch the work is committed on');
    }

    /**
     * What `knows()` answers is whether the console can run a todo's `Run:`
     * line, and a todo that names a command nobody registered is a step no
     * session can take. The console is asked rather than a list kept beside it,
     * so a command that is renamed takes the answer with it.
     */
    #[Test]
    public function itKnowsTheCommandsTheConsoleHasAndNoOthers(): void
    {
        self::assertTrue(Cli::knows('bin/cli todo:next'));
        self::assertTrue(Cli::knows('bin/cli feedback:archive some-feedback.md'));
        self::assertFalse(Cli::knows('bin/cli todo:invented'));
        // The subject on its own is no command: it was one word of two before
        // the console named them `<subject>:<verb>`, and a todo still saying so
        // is a todo whose step nothing runs.
        self::assertFalse(Cli::knows('bin/cli todo next'));
        // Anything that is not this command is somebody else's to run.
        self::assertTrue(Cli::knows('composer test'));
    }
}
