<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Cli;

use Typo3CmsMcp\Todo as TodoFile;

/**
 * Holds todo.md to the shape `bin/cli next` reads it in.
 *
 * The file is prose and stays prose — the next concrete step is a paragraph
 * somebody wrote for somebody else to start from, and nothing here shortens it.
 * What is checked is the one line per section that says which kind of section it
 * is, and that what an item claims to serve exists. An item naming a note that
 * was closed two commits ago is the failure worth catching: the note is the
 * reason the item is in the queue, and when it goes the item is either done or
 * needs trimming to the part that is left.
 */
final class Todo implements Subject
{
    public static function about(): string
    {
        return 'the order of the work, and where the last session stopped';
    }

    public static function commands(): array
    {
        return [
            'check' => ['', 'hold every section to the line that says what kind it is', self::check(...)],
        ];
    }

    /**
     * Every section says what it is, and every item says what it answers for.
     */
    public static function check(): int
    {
        $problems = [];
        $standing = [];

        foreach (TodoFile::sections() as $section) {
            $where = '"' . $section['title'] . '"';

            if ($section['kind'] === '') {
                $problems[] = $where . ' opens with ' . ($section['marker'] === '' ? 'no line saying what it is' : $section['marker']);
                continue;
            }

            if ($section['kind'] === 'standing') {
                if (!in_array($section['runs'], TodoFile::STANDING, true)) {
                    $problems[] = $where . ' is standing on ' . ($section['runs'] === '' ? '(nothing)' : $section['runs'])
                        . ', and the ones there are: ' . implode(', ', TodoFile::STANDING);
                    continue;
                }
                $standing[$section['runs']][] = $section['title'];
                continue;
            }

            if ($section['kind'] !== 'item') {
                continue;
            }

            if ($section['serves'] === []) {
                $problems[] = $where . ' serves nothing, which makes it an idea rather than an item';
            }
            if ($section['body'] === '') {
                $problems[] = $where . ' does not say what the next concrete step is';
            }
            foreach ($section['serves'] as $what) {
                $unreadable = TodoFile::unreadable($what);
                if ($unreadable !== null) {
                    $problems[] = $where . ' serves ' . $what . ', ' . $unreadable;
                }
            }
        }

        // The two readings a session owes are the reason `bin/cli next` exists,
        // so exactly one section has to be each of them: none and the command
        // silently stops doing half its job, two and it does it twice.
        foreach (['notes', 'backlog'] as $reading) {
            $sections = $standing[$reading] ?? [];
            if ($sections === []) {
                $problems[] = 'no section is the standing `' . $reading . '` reading';
            } elseif (count($sections) > 1) {
                $problems[] = 'the standing `' . $reading . '` reading is claimed by ' . implode(' and ', $sections);
            }
        }

        foreach ($problems as $problem) {
            fwrite(STDERR, $problem . "\n");
        }
        printf(
            "%d sections, %d of them queued, %d problems\n",
            count(TodoFile::sections()),
            count(TodoFile::items()),
            count($problems),
        );

        return $problems === [] ? 0 : 1;
    }
}
