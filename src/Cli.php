<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Typo3CmsMcp\Cli\Backlog;
use Typo3CmsMcp\Cli\Catalog;
use Typo3CmsMcp\Cli\Checkout;
use Typo3CmsMcp\Cli\Decision;
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
     * What to do next, for whoever is starting a session.
     *
     * Everything this prints was already written down, and it was written down
     * in four places: the notes that arrived from outside, the two directories
     * that say what is unfinished, and the file that says in which order. A
     * session had to read all four and know how they relate before it could
     * begin, and the instruction to do so was itself prose in a fifth. What was
     * missing was never the information — it was one place to ask.
     *
     * So the standing sections are read out in the order todo.md has them, and
     * the two that are readings this checkout can perform are performed rather
     * than named. The next item is printed whole, because the paragraph under
     * the heading *is* the next concrete step and summarising it here would be
     * the second copy that goes stale. What follows it is named, not printed:
     * the queue after the first entry is context, and a session that reads it
     * as a plan will do the wrong thing in the wrong order.
     */
    private static function next(): int
    {
        print "── standing, before anything is picked up\n";
        foreach (Todo::standing() as $section) {
            printf("\n%s\n", $section['title']);
            print self::indent(self::capture(static fn (): mixed => match ($section['runs']) {
                'notes' => self::openNotes(),
                'backlog' => (Backlog::commands()['list'][2])([]),
                default => print implode("\n", $section['commands']) . "\n",
            }));
        }

        $items = Todo::items();
        if ($items === []) {
            print "\n── next\n\nNothing is queued. What is waiting is above, and taking it on is an item here.\n";

            return 0;
        }

        $next = array_shift($items);
        printf("\n── next\n\n%s\n%s\n%s\n", $next['title'], self::indent('serves ' . implode(', ', $next['serves'])), $next['body']);

        if ($items !== []) {
            print "\n── and after it, in this order\n\n";
            foreach ($items as $item) {
                printf("%s\n", $item['title']);
            }
        }

        return 0;
    }

    /**
     * The notes that arrived from outside this repository and are still open.
     *
     * Listed rather than read out: what a note says is not what it is worth
     * today, and the instruction above every one of them is to run its own
     * query against the server as it is now.
     */
    private static function openNotes(): void
    {
        $notes = Feedback::notes('open', null, 100);
        if ($notes === []) {
            print "No open notes.\n";

            return;
        }

        printf("%d open. Run each one's own query against the server as it is now.\n", count($notes));
        foreach ($notes as $note) {
            printf("%s\n", $note['file']);
        }
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
            'what to do next: the open notes, the backlog, and the item at the front of the queue',
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
