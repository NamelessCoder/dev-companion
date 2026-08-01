<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Server;

use Typo3CmsMcp\Feedback\Channel;
use Typo3CmsMcp\Installation\Instance;

/**
 * Which half of this server a client is offered.
 *
 * The boundary runs through the middle of this server rather than around it:
 * the contribution process and the scripts of the core repository are core-only,
 * the conventions transfer, and the installation-backed answers are a property
 * of the installation being read. Saying so per topic — which is what the
 * `provenance` field in knowledge/server-scope.json does — tells a site
 * developer which answers are worth having; it does not stop the core half from
 * being offered, and the tool list is the first thing a client pays for.
 *
 * So the list itself varies. In a Composer project the core contribution
 * surface is left out, because a repository that has no Build/Scripts/, no
 * Gerrit remote and no Forge issue cannot follow any of it. Everything else
 * stays: the conventions hold wherever TYPO3 is written, and the installation is
 * exactly what a project has.
 *
 * The mechanism is not new — Channel::isAvailable() has always hidden two tools
 * outside a standalone checkout — only the reason is.
 */
final class Profile
{
    /** Everything this server has, the core contribution surface included. */
    public const ALL = 'all';

    /** A project or extension repository: the same server without that surface. */
    public const PROJECT = 'project';

    /** Names the profile outright, whatever the installation would suggest. */
    public const VARIABLE = 'TYPO3_MCP_PROFILE';

    /** Comma-separated tool names the caller does not want offered. */
    public const EXCLUDE_VARIABLE = 'TYPO3_MCP_EXCLUDE_TOOLS';

    /** Chosen by the caller. */
    public const VIA_ENVIRONMENT = 'environment';

    /** Followed from the kind of installation that was found. */
    public const VIA_INSTALLATION = 'installation';

    /**
     * The tools the project profile leaves out.
     *
     * Each one answers from the core repository's own material and has no
     * counterpart elsewhere: the review rules and the Gerrit workflow, the
     * scripts below Build/Scripts/, the runTests.sh suites. What those tools
     * also carry and what does transfer is reachable without them — the commit
     * conventions through typo3_commit_message_guide with workflow="project",
     * the backend CSS and subsystem conventions through
     * typo3_architecture_lookup, and every document verbatim through its
     * typo3://core resource.
     *
     * @var array<int, string>
     */
    private const CORE_ONLY_TOOLS = [
        'typo3_rule_lookup',
        'typo3_script_lookup',
        'typo3_test_run_guide',
    ];

    /** Which profile is in effect. */
    public static function active(): string
    {
        [$named] = self::fromEnvironment();
        if ($named !== null) {
            return $named;
        }

        // A core checkout gets everything, and so does a session with no
        // installation at all: one may still appear — the agent runs composer
        // install — and a tool list that shrank on discovery would be a contract
        // changing mid-session.
        return (Instance::describe()['kind'] ?? '') === Instance::KIND_COMPOSER_PROJECT
            ? self::PROJECT
            : self::ALL;
    }

    /** Whether the profile was chosen or followed from the installation. */
    public static function via(): string
    {
        return self::fromEnvironment()[0] === null ? self::VIA_INSTALLATION : self::VIA_ENVIRONMENT;
    }

    /**
     * What is wrong with the configuration, if anything. Empty otherwise.
     *
     * Unlike a misconfigured root, a profile nobody can read is not fatal: the
     * derived one is used. But it is said out loud, because silence looks
     * exactly like not having set the variable, which is the one thing the
     * caller knows it did.
     */
    public static function misconfiguration(): string
    {
        return self::fromEnvironment()[1];
    }

    /** Whether this profile offers a tool by that name. */
    public static function offers(string $tool): bool
    {
        return !in_array($tool, self::omitted(), true);
    }

    /**
     * The tools this profile leaves out, so the answer can name them rather
     * than let a client wonder where they went.
     *
     * @return array<int, string>
     */
    public static function omitted(): array
    {
        $omitted = self::active() === self::PROJECT ? self::CORE_ONLY_TOOLS : [];
        $excluded = getenv(self::EXCLUDE_VARIABLE);
        if (is_string($excluded)) {
            foreach (preg_split('/[,\s]+/', strtolower(trim($excluded))) ?: [] as $tool) {
                if ($tool !== '') {
                    $omitted[] = $tool;
                }
            }
        }

        return array_values(array_unique($omitted));
    }

    /**
     * @return array{0: string|null, 1: string} the named profile, and what is
     *         wrong with what was named
     */
    private static function fromEnvironment(): array
    {
        $configured = getenv(self::VARIABLE);
        if (!is_string($configured) || trim($configured) === '') {
            return [null, ''];
        }

        $named = strtolower(trim($configured));
        if (in_array($named, [self::ALL, self::PROJECT], true)) {
            return [$named, ''];
        }

        return [null, sprintf(
            '%s is set to "%s", which is not one of: %s. The profile is derived from the installation instead',
            self::VARIABLE,
            trim($configured),
            implode(', ', [self::ALL, self::PROJECT]),
        )];
    }
}
