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
 * a client that got as far as the fields.
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

        $text = sprintf('This is not answerable here, which is not the same as an empty answer: %s.', $reason);

        return ToolResult::create(
            $diagnosis === '' ? $text : $text . "\n" . $diagnosis,
            $echo + [
                // The reason travels as data, not only in the text beside it: a
                // client that renders structuredContent alone would otherwise
                // see one key and no way to tell why.
                'unsupported' => [
                    'cause' => $cause,
                    'reason' => $reason,
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
}
