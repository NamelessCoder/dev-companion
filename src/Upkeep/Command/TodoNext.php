<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Todo;

/**
 * The one thing to do now, for whoever is starting a session.
 *
 * It prints a single todo and nothing else. Everything it could print instead
 * was already written down somewhere — the feedback that arrived from outside,
 * the two directories that say what is unfinished, the file that says in which
 * order — and a session that is handed all of it reads for ten minutes before
 * it does anything. Context is not free: an agent given the queue reads it as a
 * plan and works in the wrong order, and one given five paragraphs of why a
 * todo is where it is starts by summarising them. `bin/cli todo:list` is where
 * the overview lives, for whoever wants it.
 *
 * Whether a todo is due is two questions. Has the clock come round — cheap, and
 * answered by the cadence. And is there anything to do — expensive, and
 * answered by running the todo's own `Run:` command: a command this repository
 * owns exits nonzero when it found work, so the feedback stop being the next
 * thing the moment the last one is judged, without anybody editing a todo to
 * say so. A command it does not own is named rather than run; `next` starts no
 * process that needs the network, and the cadence is what keeps it from being
 * asked twice in an afternoon.
 *
 * Three groups, in this order, because for a while the first of them ate the
 * other two. A sighting that recurs every session is due for as long as
 * anything is unjudged, and the feedback arrive faster than a session closes
 * them — so every session started by sighting, the queue was never reached,
 * and items sat in it for as long as feedback/ was not empty. What it means to
 * take something on is that it is now ahead of the deciding whether to:
 *
 * - What has a clock has an appointment. A cadence in days is a date that
 *   comes round, and missing it is missing the day, not losing a place in
 *   an order.
 * - Then the queue, in the order the queue has. It is work somebody has
 *   already judged to be worth doing, which is exactly what a sighting
 *   produces — so leaving it standing to sight more is deciding twice and
 *   doing nothing.
 * - Then, with the queue empty, what recurs every session: the feedback and
 *   the backlog, whose whole output is new entries for the queue that just
 *   ran dry.
 */
#[AsCommand(
    name: 'todo:next',
    description: 'the one todo that is due now, and nothing else',
)]
final class TodoNext
{
    public function __invoke(OutputInterface $output, Application $application): int
    {
        // A worktree standing on a claim has one todo and it is not the front
        // of the queue. Asked there, this command would hand over work
        // somebody else is already doing, and nothing about the answer would
        // look wrong — which is why the branch is read before anything else.
        $claim = Todo::claimed();
        if ($claim !== null) {
            return self::present($output, $claim, self::perform($application, $claim['run'])[0]);
        }

        foreach (Todo::appointments() as $todo) {
            if (!Todo::due($todo['every'], $todo['checked'])) {
                continue;
            }
            [$reading, $working] = self::perform($application, $todo['run']);
            if ($working) {
                return self::present($output, $todo, $reading);
            }
        }

        $items = Todo::items();
        if ($items !== []) {
            return self::present($output, $items[0], self::perform($application, $items[0]['run'])[0], count($items) - 1);
        }

        foreach (Todo::sightings() as $todo) {
            [$reading, $working] = self::perform($application, $todo['run']);
            if ($working) {
                return self::present($output, $todo, $reading);
            }
        }

        $output->writeln("Nothing is due and nothing is queued. What is waiting is in `bin/cli backlog:list`,\n"
            . 'and taking one on is a todo in todo/.');
        // An empty queue with todos in hand is not an empty repository, and the
        // difference matters here more than anywhere: this is the one branch
        // that invites a session to go find work of its own.
        if (Todo::progress() !== []) {
            $output->writeln(sprintf(
                '%d todos are in hand elsewhere and are nobody else\'s to start — `bin/cli todo:list`.',
                count(Todo::progress()),
            ));
        }
        if (Todo::waiting() !== []) {
            $output->writeln(sprintf(
                '%d todos are blocked on an answer nothing here can give — `bin/cli todo:list`.',
                count(Todo::waiting()),
            ));
        }

        return 0;
    }

    /**
     * One todo, as much of it as there is, how it is worked, and what it
     * leaves behind.
     *
     * The closing block is the only thing here that is not the todo, and it is
     * the two halves a session gets no second chance at: what happens before
     * the first change — the reading, the research, and the question that has
     * to be asked rather than guessed — and what has to be true of the file
     * after the last one. Both are on one page; which of the three handovers
     * applies is this command's answer, because the page cannot know whether
     * the todo it is being read for is queued, standing or dated.
     *
     * @param array{title: string, kind: string, claimed: string, every: string, serves: array<int, string>, body: string, ...} $todo
     */
    private static function present(OutputInterface $output, array $todo, string $reading, ?int $after = null): int
    {
        $meta = ['serves ' . implode(', ', $todo['serves'])];
        $meta[] = match (true) {
            $todo['kind'] === 'progress' => 'in hand since ' . $todo['claimed'],
            $todo['every'] === '' => 'queued',
            default => 'every ' . $todo['every'],
        };
        if ($after !== null && $after > 0) {
            $meta[] = $after . ' more after it — `bin/cli todo:list`';
        }
        // What somebody else has in hand is named because this command cannot
        // otherwise be told apart from the one it was before: it hands over the
        // first queued todo, and a session that does not know it is one of
        // several reads that as "nothing else is happening". Its own claim is
        // not one of them — a session counting itself among the others would
        // read one claim as two sessions at work.
        $inHand = count(Todo::progress()) - ($todo['kind'] === 'progress' ? 1 : 0);
        if ($inHand > 0) {
            $meta[] = $inHand . ' in hand elsewhere — `bin/cli todo:list`';
        }
        // What waits is named by a count and nothing else. A blocked todo is
        // addressed to whoever can answer it, and if no output ever mentions
        // one it is a file nobody opens again; the paragraph still belongs to
        // the one todo this command exists to hand over.
        $waiting = count(Todo::waiting());
        if ($waiting > 0) {
            $meta[] = $waiting . ' waiting on an answer';
        }

        $output->writeln($todo['title']);
        $output->writeln(implode(' · ', $meta));
        if (trim($reading) !== '') {
            $output->writeln('');
            $output->write(self::indent($reading));
        }
        $output->writeln('');
        $output->writeln($todo['body']);
        $output->writeln('');
        $output->writeln(sprintf(
            "Read what it serves and what the code does now before changing either; settle what\n"
            . "the step turns on rather than recalling it, and ask where nothing here can answer:\n"
            . '%s.',
            Todo::PROCEDURE,
        ));
        $output->writeln(match (true) {
            $todo['kind'] === 'progress' => sprintf(
                "Done means the file says so, on this branch: deleted, or left here with the\n"
                . 'question in `**Waiting on:**` and the work behind it. %s is the rest.',
                Todo::PARALLEL,
            ),
            $todo['every'] === '' => 'Done means the file says so: deleted, or trimmed to the part that is left.',
            $todo['every'] === 'session' => 'It stands, so nothing is deleted. What it settles belongs where that is kept.',
            default => "It stands, so nothing is deleted — write today's date into `**Checked:**`.",
        });

        return 0;
    }

    /**
     * A todo's own commands, run where this repository owns them.
     *
     * Whether there is work is the command's answer rather than a guess: the
     * listings a recurring todo starts from exit nonzero when they found
     * something. Anything else is printed for the session to run, and counts as
     * work because nothing here can tell whether it is.
     *
     * @param array<int, string> $run
     *
     * @return array{0: string, 1: bool}
     */
    private static function perform(Application $application, array $run): array
    {
        $reading = '';
        $working = $run === [];
        foreach ($run as $command) {
            if (!str_starts_with($command, 'bin/cli ')) {
                $reading .= $command . "\n";
                $working = true;
                continue;
            }

            // One buffer for both streams, because what a check writes to
            // stderr is the half a session has to read, and it is placed under
            // the todo rather than beside it.
            $buffer = new BufferedOutput();
            $status = $application->doRun(new StringInput(substr($command, strlen('bin/cli '))), $buffer);
            $reading .= $buffer->fetch();
            $working = $working || $status !== 0;
        }

        return [$reading, $working];
    }

    private static function indent(string $block): string
    {
        return (string) preg_replace('/^(?!$)/m', '    ', rtrim($block) . "\n");
    }
}
