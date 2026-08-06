<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use TYPO3\DevCompanion\Tool\Registry;

/**
 * The tools a caller has asked not to be offered.
 *
 * The only subtraction this server makes, and the caller makes it. What was
 * here before decided per checkout instead: a Composer project was offered the
 * server without its core contribution surface, on the grounds that a
 * repository with no Build/Scripts/, no Gerrit remote and no Forge issue cannot
 * follow any of it. That reasoning holds for the repository and not for the
 * task, which is R-AUD-002, and the task is what an answer is shaped for — so a
 * core-shaped question asked from a site installation was answered as core work
 * and then routed to a tool that had been taken away.
 *
 * Which half of an answer is worth having is said in the answer, per topic and
 * per path, where the audience is actually known. The tool list is not the
 * place to say it: withholding a tool costs the caller the doorway and leaves
 * it the knowledge, because the documents behind these tools were never
 * filtered.
 */
final class ExcludedTools
{
    /** Comma-separated tool names the caller does not want offered. */
    public const VARIABLE = 'TYPO3_DEV_COMPANION_EXCLUDE_TOOLS';

    /**
     * The one tool that cannot be excluded: it is what tells a client why the
     * list is shorter than the documentation says, and a client that has lost
     * it cannot tell a configured server from a broken one.
     */
    private const ALWAYS_OFFERED = 'typo3_server_scope';

    /**
     * Whether the registry is being asked what it would offer with nothing
     * excluded, in which case the filter answers empty — see partition(),
     * which is the only thing that sets it.
     */
    private static bool $asking = false;

    /**
     * The partition, against the variable it was computed from.
     *
     * Computing it asks the registry twice, and the registry asks every tool
     * for its schemas — while `all()` is read several times per answer, and
     * from a variable a test changes between two of them. So it is remembered
     * against the raw string rather than against nothing.
     *
     * @var array{raw: string, excluded: array<int, string>, unknown: array<int, string>, offeredAnyway: array<int, string>}|null
     */
    private static ?array $partition = null;

    /** Whether the caller left a tool by that name in the list. */
    public static function offers(string $tool): bool
    {
        return !in_array($tool, self::filter(), true);
    }

    /**
     * The tools that are really gone, so an answer can name them rather than
     * let a client wonder where a tool went.
     *
     * What the caller wrote is not that list. A name reaches this variable that
     * no tool answers to, and one that names a tool the filter does not reach,
     * and both used to be reported here as excluded while the tool was in the
     * list the same server had just handed over — a client told it has lost a
     * capability it has, out of the instruction budget R-ANS-013 holds.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return self::partition()['excluded'];
    }

    /**
     * The names in the list that no tool of this server answers to.
     *
     * Such a name takes nothing away, and there is nothing in the list itself
     * to tell it from one that does: a renamed tool leaves its old name behind
     * looking exactly like a name that was never right, and the caller gets the
     * tool back without either side saying so. That is what `a4470ee` did to
     * `typo3_project_scope` and `typo3_extension_scope`.
     *
     * @return array<int, string>
     */
    public static function unknown(): array
    {
        return self::partition()['unknown'];
    }

    /**
     * The names in the list that this server offers anyway.
     *
     * The three R-SCO-009 names as what a caller cannot take away, reached from
     * the other side: `typo3_server_scope`, which is dropped here, and the two
     * feedback tools, which `Registry::offered()` appends past the filter —
     * D-FBK-042. Nothing is computed from that list, so an exception added
     * there arrives here without this class being told.
     *
     * @return array<int, string>
     */
    public static function offeredAnyway(): array
    {
        return self::partition()['offeredAnyway'];
    }

    /**
     * Every name the caller wrote, in one of three states.
     *
     * The registry is asked twice and the two lists are compared: what it
     * offers with the filter, and what it offers with this class answering
     * empty. A name missing from the second is one no tool answers to; a name
     * in the first is one the filter did not reach; the rest are gone, which is
     * the only claim a client can act on.
     *
     * @return array{raw: string, excluded: array<int, string>, unknown: array<int, string>, offeredAnyway: array<int, string>}
     */
    private static function partition(): array
    {
        $raw = self::raw();
        if (self::$partition !== null && self::$partition['raw'] === $raw) {
            return self::$partition;
        }

        $partition = ['raw' => $raw, 'excluded' => [], 'unknown' => [], 'offeredAnyway' => []];
        $configured = self::configured();
        if ($configured !== []) {
            self::$asking = true;
            try {
                $everything = array_column(Registry::definitions(), 'name');
            } finally {
                self::$asking = false;
            }
            $offered = array_column(Registry::definitions(), 'name');
            foreach ($configured as $tool) {
                $state = match (true) {
                    !in_array($tool, $everything, true) => 'unknown',
                    in_array($tool, $offered, true) => 'offeredAnyway',
                    default => 'excluded',
                };
                $partition[$state][] = $tool;
            }
        }

        return self::$partition = $partition;
    }

    /**
     * What the tool list is filtered by, which is what the caller wrote.
     *
     * `typo3_server_scope` is taken out here rather than in the report: the
     * caller who named it is told it was offered anyway, and the filter never
     * sees it.
     *
     * @return array<int, string>
     */
    private static function filter(): array
    {
        if (self::$asking) {
            return [];
        }

        return array_values(array_filter(
            self::configured(),
            static fn(string $tool): bool => $tool !== self::ALWAYS_OFFERED,
        ));
    }

    /**
     * The names the caller wrote, in the order they were written.
     *
     * @return array<int, string>
     */
    private static function configured(): array
    {
        $names = array_filter(
            preg_split('/[,\s]+/', strtolower(trim(self::raw()))) ?: [],
            static fn(string $tool): bool => $tool !== '',
        );

        return array_values(array_unique($names));
    }

    private static function raw(): string
    {
        $configured = getenv(self::VARIABLE);

        return is_string($configured) ? $configured : '';
    }
}
