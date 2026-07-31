<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Cli;

/**
 * One subject of `bin/cli`, and the commands it supports.
 *
 * The two methods are what the dispatcher and the help are both built from, so
 * a command that exists is a command the help lists. What used to keep those
 * two apart was a `usage()` per script that wrote the same sentences a second
 * time, and drifted from them at its leisure.
 */
interface Subject
{
    /** One line: what this subject is about. */
    public static function about(): string;

    /**
     * Every command, in the order the help lists them: what it takes, what it
     * does, and what runs it.
     *
     * @return array<string, array{0: string, 1: string, 2: callable(array<int, string>): int}>
     */
    public static function commands(): array;
}
