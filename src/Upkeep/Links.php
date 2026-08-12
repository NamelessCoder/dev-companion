<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Paths;

/**
 * The links this repository writes between its own files, resolved.
 *
 * A decision cites the requirement it stands on, a todo cites the decision it
 * came from, a documentation page cites both — by path, so a reader can follow
 * it. Nothing read those paths back. Renaming nineteen decision files in one
 * pass rewrote 58 references across 32 files, and a single one missed would
 * have been a link that silently goes nowhere: no check fails, no test runs
 * over it, and the reader who follows it is the first to find out.
 *
 * What is checked is the path and never the anchor. A heading moves inside a
 * file often enough that holding `#a-heading` would fail on prose edits, and a
 * link to the right file with a stale fragment still lands the reader on the
 * page they were sent to.
 */
final class Links
{
    /**
     * A relative markdown link: `](path)` inline, `[id]: path` as a reference
     * definition. Both forms are in use here, and the generated listings are
     * written in the second.
     */
    private const PATTERN = '/(?:\]\(\s*([^)\s#][^)\s]*)\s*\)|^\[[^\]]+\]:\s*(\S+))/m';

    /**
     * Every dead link in the prose this repository writes about itself.
     *
     * The corpus is `Prose`'s, for the same reason it excludes `feedback/`: a
     * feedback is a session's report written somewhere else and kept as it
     * arrived, so a path that never resolved here is a fact about that session
     * rather than a defect in this repository.
     *
     * @return list<array{file: string, link: string, line: int}>
     */
    public static function dead(): array
    {
        $dead = [];
        foreach (Prose::documents() as $document) {
            $dead = array_merge($dead, self::deadIn($document));
        }

        return $dead;
    }

    /**
     * The dead links in one file, resolved against the directory that file sits
     * in, which is what a reader following the link does.
     *
     * @return list<array{file: string, link: string, line: int}>
     */
    public static function deadIn(string $file): array
    {
        $contents = file_get_contents(str_starts_with($file, '/') ? $file : Paths::root() . '/' . $file);
        if ($contents === false) {
            return [];
        }

        // The corpus is given relative to the root, and a link is resolved
        // against the directory of the file that writes it — so both are made
        // absolute here rather than left to the working directory the command
        // happened to be run from.
        $root = Paths::root() . '/';
        $absolute = str_starts_with($file, '/') ? $file : $root . $file;
        $relative = str_starts_with($absolute, $root) ? substr($absolute, strlen($root)) : $absolute;
        $directory = dirname($absolute);

        $dead = [];
        foreach (self::targets($contents) as $target => $offset) {
            $target = (string) $target;
            // A link to a heading in the same file names no path, and a
            // reference definition may be written that way too.
            if (str_starts_with($target, '#') || self::isExternal($target)) {
                continue;
            }

            $path = strtok($target, '#');
            if ($path === false) {
                continue;
            }

            if (file_exists($directory . '/' . $path) || file_exists(self::publishedFrom($directory . '/' . $path))) {
                continue;
            }

            $dead[] = [
                'file' => $relative,
                'link' => $target,
                'line' => substr_count(substr($contents, 0, $offset), "\n") + 1,
            ];
        }

        return $dead;
    }

    /**
     * The same links, each put through a function saying what it becomes.
     *
     * `documentation:prepare` writes a copy of a tree whose links were written
     * for a checkout: some have to be rewritten and the rest left exactly as
     * they stand. Which link is which is this class's question already, and a
     * second reading of what a link is would answer it differently the first
     * time either one changed.
     *
     * The function is offered the internal ones alone. An external target and a
     * bare heading are no paths this repository keeps, and what rewrites paths
     * has no business seeing them.
     *
     * @param callable(string): string $rewrite
     */
    public static function rewritten(string $contents, callable $rewrite): string
    {
        return (string) preg_replace_callback(
            self::PATTERN,
            static function (array $match) use ($rewrite): string {
                // The inline form where it matched, the reference definition
                // where it did not — and the offsets are what puts the new
                // target back exactly where the old one stood.
                $target = $match[1][0] !== '' ? $match[1] : $match[2];
                if (str_starts_with($target[0], '#') || self::isExternal($target[0])) {
                    return $match[0][0];
                }

                return substr_replace($match[0][0], $rewrite($target[0]), $target[1] - $match[0][1], strlen($target[0]));
            },
            $contents,
            flags: PREG_OFFSET_CAPTURE,
        );
    }

    /**
     * The link targets in a document, each with where it stands, so the report
     * names a line rather than a file somebody then has to search.
     *
     * @return array<string, int>
     */
    private static function targets(string $contents): array
    {
        if (preg_match_all(self::PATTERN, $contents, $matches, PREG_OFFSET_CAPTURE) === false) {
            return [];
        }

        $targets = [];
        foreach ([1, 2] as $group) {
            foreach ($matches[$group] as $match) {
                if ($match[0] !== '') {
                    $targets[$match[0]] = $match[1];
                }
            }
        }

        return $targets;
    }

    /**
     * Where a link that a skill resolves only once it is published points in
     * this checkout.
     *
     * Each `SKILL.md` opens by sending the reader to `references/base.md`, and
     * `Installer` writes that file when it publishes the skill, copying
     * `skills/base.md` into every one of them rather than sharing a file a
     * skill in somebody else's project could not reach
     * ([`D-SKL-001`](../../decisions/task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md)).
     * So the link is right and the file is absent here, and what holds it is
     * the source it will be a copy of: delete `skills/base.md` and seven links
     * go dead at the next install.
     */
    private static function publishedFrom(string $path): string
    {
        return preg_match('#/skills/[^/]+/references/base\.md$#', $path) === 1
            ? Paths::root() . '/skills/base.md'
            : $path;
    }

    /**
     * Anything that leaves this checkout is somebody else's to keep true, and a
     * network call is not what a check on a working copy may cost.
     */
    private static function isExternal(string $path): bool
    {
        return (bool) preg_match('#^(?:[a-z][a-z0-9+.-]*:|//)#i', $path);
    }
}
