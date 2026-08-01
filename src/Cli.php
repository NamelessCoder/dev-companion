<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Typo3CmsMcp\Cli\Backlog;
use Typo3CmsMcp\Cli\Catalog;
use Typo3CmsMcp\Cli\Checkout;
use Typo3CmsMcp\Cli\Decision;
use Typo3CmsMcp\Cli\Feedback as FeedbackSubject;
use Typo3CmsMcp\Cli\Hint;
use Typo3CmsMcp\Cli\Requirement;
use Typo3CmsMcp\Cli\Scenario;
use Typo3CmsMcp\Cli\Subject;
use Typo3CmsMcp\Cli\Todo as TodoSubject;

/**
 * Everything this repository is kept in order by, as one command.
 *
 * What this replaces is six scripts below bin/, each with a usage line of its
 * own. Nothing said what the set of them was: a command existed for whoever
 * had listed the directory, and the only overview was a block in AGENTS.md
 * that no code read and nothing held to the truth. The help below is generated
 * from the same declarations the dispatcher runs, so a command that exists is
 * a command that is listed.
 *
 * `bin/typo3-cms-mcp` is deliberately not here. That one is the product — the
 * client launches it, Composer exports it as a `bin`, and it has no business
 * carrying the upkeep of the repository it happens to live in.
 */
final class Cli
{
    /** @var array<string, class-string<Subject>> */
    private const SUBJECTS = [
        'requirements' => Requirement::class,
        'decisions' => Decision::class,
        'scenarios' => Scenario::class,
        'todo' => TodoSubject::class,
        'feedback' => FeedbackSubject::class,
        'backlog' => Backlog::class,
        'hints' => Hint::class,
        'catalog' => Catalog::class,
        'checkouts' => Checkout::class,
    ];

    /**
     * The checks `bin/cli check` runs, which are the ones that need nothing but
     * this checkout. `catalog` reads .checkouts/ and `hints coverage` reports
     * gaps rather than failures, so both are asked for by name.
     */
    private const CHECKED = ['requirements', 'decisions', 'scenarios', 'todo'];

    /** @param array<int, string> $arguments */
    public static function run(array $arguments): int
    {
        $subject = array_shift($arguments) ?? '';

        if ($subject === 'check') {
            return self::checkEverything();
        }

        if ($subject === 'next') {
            return self::next();
        }

        if (!isset(self::SUBJECTS[$subject])) {
            if ($subject !== '' && $subject !== '--help' && $subject !== '-h') {
                fwrite(STDERR, 'No such subject: ' . $subject . "\n\n");
            }
            fwrite(STDERR, self::help());

            return 2;
        }

        $class = self::SUBJECTS[$subject];
        $commands = $class::commands();
        $command = array_shift($arguments) ?? '';
        if (!isset($commands[$command])) {
            if ($command !== '') {
                fwrite(STDERR, $subject . ' has no command ' . $command . "\n\n");
            }
            fwrite(STDERR, self::describe($subject, $class));

            return 2;
        }

        return ($commands[$command][2])($arguments);
    }

    /**
     * Whether `bin/cli …` names something this command can do, for todo.md,
     * whose `Run:` lines are commands nobody runs until the day they are due.
     */
    public static function knows(string $command): bool
    {
        $words = explode(' ', $command);
        if (array_shift($words) !== 'bin/cli') {
            return true;
        }

        $subject = array_shift($words) ?? '';
        if ($subject === 'check' || $subject === 'next') {
            return $words === [];
        }

        return isset(self::SUBJECTS[$subject]) && isset(self::SUBJECTS[$subject]::commands()[$words[0] ?? '']);
    }

    /**
     * The one command that was called wrong, printed as the help writes it.
     *
     * @param class-string<Subject> $class
     */
    public static function usage(string $class, string $command): int
    {
        $subject = array_search($class, self::SUBJECTS, true);
        [$arguments] = $class::commands()[$command];
        fwrite(STDERR, sprintf("Usage: bin/cli %s %s %s\n", $subject, $command, $arguments));

        return 2;
    }

    /**
     * Every check this checkout can answer on its own, one after the other,
     * and what none of them fails on.
     *
     * The checks hold the files to their shape, and a file can be perfectly
     * shaped and still say that nobody has built the requirement or been back
     * to the decision. That state is legitimate, so it cannot be an error —
     * but it was invisible, and an entry sat in requirements/ unbuilt from the
     * day the directory was created because nothing ever read it out. The
     * block below is that reading. It changes no exit code.
     */
    private static function checkEverything(): int
    {
        $worst = 0;
        foreach (self::CHECKED as $subject) {
            printf("── %s\n", $subject);
            $worst = max($worst, (self::SUBJECTS[$subject]::commands()['check'][2])([]));
            print "\n";
        }

        print "── unresolved\n";
        (Backlog::commands()['list'][2])([]);

        return $worst;
    }

    /**
     * The one thing to do now, for whoever is starting a session.
     *
     * It prints a single todo and nothing else. Everything it could print
     * instead was already written down somewhere — the notes that arrived from
     * outside, the two directories that say what is unfinished, the file that
     * says in which order — and a session that is handed all of it reads for
     * ten minutes before it does anything. Context is not free: an agent given
     * the queue reads it as a plan and works in the wrong order, and one given
     * five paragraphs of why a todo is where it is starts by summarising them.
     * `bin/cli todo list` is where the overview lives, for whoever wants it.
     *
     * Whether a todo is due is two questions. Has the clock come round — cheap,
     * and answered by the cadence. And is there anything to do — expensive, and
     * answered by running the todo's own `Run:` command: a command this
     * repository owns exits nonzero when it found work, so the notes stop being
     * the next thing the moment the last one is judged, without anybody editing
     * todo.md to say so. A command it does not own is named rather than run;
     * `next` starts no process that needs the network, and the cadence is what
     * keeps it from being asked twice in an afternoon.
     *
     * Three groups, in this order, because for a while the first of them ate
     * the other two. A sighting that recurs every session is due for as long as
     * anything is unjudged, and the notes arrive faster than a session closes
     * them — so every session started by sighting, the queue was never reached,
     * and items sat in it for as long as feedback/ was not empty. What it means
     * to take something on is that it is now ahead of the deciding whether to:
     *
     * - What has a clock has an appointment. A cadence in days is a date that
     *   comes round, and missing it is missing the day, not losing a place in
     *   an order.
     * - Then the queue, in the order the queue has. It is work somebody has
     *   already judged to be worth doing, which is exactly what a sighting
     *   produces — so leaving it standing to sight more is deciding twice and
     *   doing nothing.
     * - Then, with the queue empty, what recurs every session: the notes and
     *   the backlog, whose whole output is new entries for the queue that just
     *   ran dry.
     */
    private static function next(): int
    {
        foreach (Todo::appointments() as $todo) {
            if (!Todo::due($todo['every'], $todo['checked'])) {
                continue;
            }
            [$output, $working] = self::perform($todo['run']);
            if ($working) {
                return self::present($todo, $output);
            }
        }

        $items = Todo::items();
        if ($items !== []) {
            return self::present($items[0], self::perform($items[0]['run'])[0], count($items) - 1);
        }

        foreach (Todo::sightings() as $todo) {
            [$output, $working] = self::perform($todo['run']);
            if ($working) {
                return self::present($todo, $output);
            }
        }

        print "Nothing is due and nothing is queued. What is waiting is in `bin/cli backlog list`,\n"
            . "and taking one on is a todo in todo.md.\n";

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
     * @param array{title: string, every: string, serves: array<int, string>, body: string, ...} $todo
     */
    private static function present(array $todo, string $output, ?int $after = null): int
    {
        $meta = ['serves ' . implode(', ', $todo['serves'])];
        $meta[] = $todo['every'] === '' ? 'queued' : 'every ' . $todo['every'];
        if ($after !== null && $after > 0) {
            $meta[] = $after . ' more after it — `bin/cli todo list`';
        }

        printf("%s\n%s\n", $todo['title'], implode(' · ', $meta));
        if (trim($output) !== '') {
            printf("\n%s", self::indent($output));
        }
        printf("\n%s\n", $todo['body']);
        printf(
            "\nRead what it serves and what the code does now before changing either; settle what\n"
            . "the step turns on rather than recalling it, and ask where nothing here can answer:\n"
            . "%s.\n%s",
            Todo::PROCEDURE,
            match (true) {
                $todo['every'] === '' => "Done means todo.md says so: deleted, or trimmed to the part that is left.\n",
                $todo['every'] === 'session' => "It stands, so nothing is deleted. What it settles belongs where that is kept.\n",
                default => "It stands, so nothing is deleted — write today's date into `**Checked:**`.\n",
            },
        );

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
    private static function perform(array $run): array
    {
        $output = '';
        $working = $run === [];
        foreach ($run as $command) {
            if (!str_starts_with($command, 'bin/cli ')) {
                $output .= $command . "\n";
                $working = true;
                continue;
            }

            $status = 0;
            $output .= self::capture(static function () use ($command, &$status): void {
                $status = self::run(array_slice(explode(' ', $command), 1));
            });
            $working = $working || $status !== 0;
        }

        return [$output, $working];
    }

    /** What a callable printed, so a reading written for stdout can be placed. */
    private static function capture(callable $print): string
    {
        ob_start();
        $print();

        return (string) ob_get_clean();
    }

    private static function indent(string $block): string
    {
        return (string) preg_replace('/^(?!$)/m', '    ', rtrim($block) . "\n");
    }

    /** What this command supports, read off the subjects themselves. */
    private static function help(): string
    {
        $help = "Usage: bin/cli <subject> <command> [arguments]\n\n"
            . "The upkeep of this repository. `bin/typo3-cms-mcp` is the server itself.\n\n";
        foreach (self::SUBJECTS as $subject => $class) {
            $help .= self::describe($subject, $class);
        }

        return $help . sprintf(
            "  %-14s %s, and what none of them fails on\n  %-14s %s\n",
            'check',
            implode(', ', self::CHECKED),
            'next',
            'the one todo that is due now, and nothing else',
        );
    }

    /**
     * One subject, its commands, and what each of them does.
     *
     * @param class-string<Subject> $class
     */
    private static function describe(string $subject, string $class): string
    {
        $block = sprintf("  %-14s %s\n", $subject, $class::about());
        foreach ($class::commands() as $command => [$arguments, $about]) {
            $block .= sprintf("    %-22s %s\n", trim($command . ' ' . $arguments), $about);
        }

        return $block . "\n";
    }
}
