<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Typo3CmsMcp\Cli\Catalog;
use Typo3CmsMcp\Cli\Checkout;
use Typo3CmsMcp\Cli\Decision;
use Typo3CmsMcp\Cli\Hint;
use Typo3CmsMcp\Cli\Requirement;
use Typo3CmsMcp\Cli\Scenario;
use Typo3CmsMcp\Cli\Subject;

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
        'hints' => Hint::class,
        'catalog' => Catalog::class,
        'checkouts' => Checkout::class,
    ];

    /**
     * The checks `bin/cli check` runs, which are the ones that need nothing but
     * this checkout. `catalog` reads .checkouts/ and `hints coverage` reports
     * gaps rather than failures, so both are asked for by name.
     */
    private const CHECKED = ['requirements', 'decisions', 'scenarios'];

    /** @param array<int, string> $arguments */
    public static function run(array $arguments): int
    {
        $subject = array_shift($arguments) ?? '';

        if ($subject === 'check') {
            return self::checkEverything();
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

    /** Every check this checkout can answer on its own, one after the other. */
    private static function checkEverything(): int
    {
        $worst = 0;
        foreach (self::CHECKED as $subject) {
            printf("── %s\n", $subject);
            $worst = max($worst, (self::SUBJECTS[$subject]::commands()['check'][2])([]));
            print "\n";
        }

        return $worst;
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
            "  %-14s %s, one check after the other\n",
            'check',
            implode(', ', self::CHECKED),
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
