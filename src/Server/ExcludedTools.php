<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Server;

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
        $configured = getenv(self::VARIABLE);
        if (!is_string($configured)) {
            return [];
        }

        $excluded = array_filter(
            preg_split('/[,\s]+/', strtolower(trim($configured))) ?: [],
            static fn(string $tool): bool => $tool !== '' && $tool !== self::ALWAYS_OFFERED,
        );

        return array_values(array_unique($excluded));
    }
}
