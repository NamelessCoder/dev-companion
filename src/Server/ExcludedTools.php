<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Server;

use Typo3CmsMcp\Tool\Registry;

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
    public const VARIABLE = 'TYPO3_MCP_EXCLUDE_TOOLS';

    /**
     * The one tool that cannot be excluded: it is what tells a client why the
     * list is shorter than the documentation says, and a client that has lost
     * it cannot tell a configured server from a broken one.
     */
    private const ALWAYS_OFFERED = 'typo3_server_scope';

    /**
     * Whether the registry is being asked what it has, in which case this list
     * answers empty — see unknown(), which is the only thing that sets it.
     */
    private static bool $asking = false;

    /** Whether the caller left a tool by that name in the list. */
    public static function offers(string $tool): bool
    {
        return !in_array($tool, self::all(), true);
    }

    /**
     * What was excluded, so an answer can name it rather than let a client
     * wonder where a tool went.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        $configured = self::$asking ? false : getenv(self::VARIABLE);
        if (!is_string($configured)) {
            return [];
        }

        $excluded = array_filter(
            preg_split('/[,\s]+/', strtolower(trim($configured))) ?: [],
            static fn(string $tool): bool => $tool !== '' && $tool !== self::ALWAYS_OFFERED,
        );

        return array_values(array_unique($excluded));
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
     * The registry is what a name is real against, and the list it hands out is
     * already the shortened one — so it is asked with this class answering
     * empty, which is the only way to see a tool that was excluded rather than
     * one that does not exist.
     *
     * @return array<int, string>
     */
    public static function unknown(): array
    {
        $configured = self::all();
        if ($configured === []) {
            return [];
        }

        self::$asking = true;
        try {
            $offered = array_column(Registry::definitions(), 'name');
        } finally {
            self::$asking = false;
        }

        return array_values(array_diff($configured, $offered));
    }
}
