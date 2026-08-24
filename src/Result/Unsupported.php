<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;

/**
 * The answer to a question this server cannot answer here.
 *
 * Not an error: nothing failed. The question is about an installation, and from
 * this directory there is none to ask — which the server knows precisely and
 * can say, so it says that and nothing else. No count, no flag, no empty list
 * standing in for a result, because every one of those is read as the answer by
 * a client that got as far as the fields. What it says beside the cause is
 * which state the repository the caller stands in is in, which claims nothing
 * about the question that was asked and is what tells a precondition from a
 * dead end (`D-ANS-105`).
 *
 * Kept in one place because the distinction it draws is the same every time: an
 * empty result and an unsupported question look identical, and only one of them
 * means the thing does not exist.
 */
final class Unsupported
{
    /** Nothing to ask from here; searched says where it looked. */
    public const NO_INSTALLATION = 'no-installation';

    /** One was named and could not be used, so nothing was searched for. */
    public const MISCONFIGURED = 'misconfigured';

    /** One was found and its console did not answer. */
    public const NOT_ANSWERING = 'installation-not-answering';

    /** Packages are installed below the root that was found, so an install is not what is missing. */
    public const REPOSITORY_INSTALLED = 'installed';

    /** The repository declares TYPO3 and nothing is installed below it yet. */
    public const REPOSITORY_NOT_INSTALLED = 'not-installed';

    /** Nothing the walk reached declares TYPO3, so nothing installed here would answer the question. */
    public const REPOSITORY_UNDECLARED = 'undeclared';

    /**
     * What the text says where the repository is waiting for its install.
     *
     * The state and what changes it, never an instruction to install: a caller
     * that reads a refusal as a prescription installs a repository nobody asked
     * it to (`D-ANS-105`). Worded as `typo3_project_describe` words the same
     * state, because one state said two ways is two states to a reader.
     */
    private const INSTALL_PENDING = 'This repository declares TYPO3 and nothing is installed below it yet, so this '
        . 'answer changes once composer install has run.';

    /**
     * @param array<string, mixed> $echo the caller's own arguments handed back,
     *     and never anything about the installation. It is what keeps a field
     *     in the required list of a schema whose two shapes JSON Schema cannot
     *     otherwise relate: the SDK validator has no oneOf, so the answer
     *     fields leave required and this one stays.
     */
    public static function because(string $reason, array $echo = []): ToolResult
    {
        $diagnosis = Typo3Cli::diagnose($reason);
        // Read once, and only where the failure did not diagnose itself.
        // `caveat()` re-enters `resolve()`, which does not remember a caveated
        // resolution (`R-DIS-009`), so every read of it costs a
        // `ddev describe -j` while the project is stopped — the guard and the
        // interpolation were two of them. Measured against
        // `.environments/e-site-13.4` with its project down on 2026-08-04:
        // three describes and 1.35s per unanswerable tool call, one for the run
        // and two here, at 0.25s each. It is also the pair that could disagree,
        // since each resolved for itself: a project coming up between them left
        // the guard true and nothing to interpolate.
        $caveat = $diagnosis === '' ? Typo3Cli::caveat() : '';
        if ($caveat !== '') {
            $diagnosis = 'What is known about this console: ' . $caveat . '.';
        }

        $misconfiguration = Instance::misconfiguration();
        $cause = match (true) {
            $misconfiguration !== '' => self::MISCONFIGURED,
            Instance::isAvailable() => self::NOT_ANSWERING,
            default => self::NO_INSTALLATION,
        };

        $repositoryState = self::repositoryState($misconfiguration);

        $text = sprintf('This is not answerable here, which is not the same as an empty answer: %s.', $reason);
        if ($diagnosis !== '') {
            $text .= "\n" . $diagnosis;
        }
        // Said in the text for the one state that ends by itself, and in the
        // data for all of them. A sentence on every refusal charges the callers
        // it tells nothing new.
        if ($repositoryState === self::REPOSITORY_NOT_INSTALLED) {
            $text .= "\n" . self::INSTALL_PENDING;
        }

        return ToolResult::create(
            $text,
            $echo + [
                // The reason travels as data, not only in the text beside it: a
                // client that renders structuredContent alone would otherwise
                // see one key and no way to tell why.
                'unsupported' => [
                    'cause' => $cause,
                    'reason' => $reason,
                    // What stopped this call says nothing about whether the
                    // state ends, so the state is beside it.
                    'repositoryState' => $repositoryState,
                    'diagnosis' => $diagnosis,
                    // Where it looked and what was set wrong belong here too.
                    // typo3_server_scope carried both, and this answer is the
                    // whole diagnostic rather than a pointer at it — the
                    // pointer cost a round trip for what the caller was already
                    // holding, which `D-ANS-083` measured.
                    'searched' => Instance::searched(),
                    'misconfiguration' => $misconfiguration === '' ? null : $misconfiguration,
                    'settings' => [
                        'root' => Instance::ROOT_VARIABLE,
                        'console' => Typo3Cli::CONSOLE_VARIABLE,
                    ],
                ],
            ],
        );
    }

    /**
     * Which state the repository the caller stands in is in, or null where
     * nothing was looked at.
     *
     * `cause` says what stopped this call and never whether that state ends, so
     * a refusal before `composer install` read as permanent and the session
     * answered the two hours after it out of a core checkout it happened to
     * have (`D-ANS-105`). What places a repository stays `Instance::project()`:
     * widened here, an ordinary PHP checkout would be told an install is
     * pending.
     */
    private static function repositoryState(string $misconfiguration): ?string
    {
        if (Instance::project() !== null) {
            return Instance::packages() === [] ? self::REPOSITORY_NOT_INSTALLED : self::REPOSITORY_INSTALLED;
        }

        // A named root that could not be used searched nothing, and an
        // entrypoint that handed no directory in placed nothing. Neither has
        // seen a repository to report the state of.
        return $misconfiguration !== '' || Instance::startedFrom() === null
            ? null
            : self::REPOSITORY_UNDECLARED;
    }
}
