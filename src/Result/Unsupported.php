<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Result;

use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;

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
        if ($diagnosis === '' && Typo3Cli::caveat() !== '') {
            $diagnosis = 'What is known about this console: ' . Typo3Cli::caveat() . '.';
        }

        $misconfiguration = Instance::misconfiguration();
        $cause = match (true) {
            $misconfiguration !== '' => self::MISCONFIGURED,
            Instance::isAvailable() => self::NOT_ANSWERING,
            default => self::NO_INSTALLATION,
        };

        return ToolResult::create(
            sprintf(
                "This is not answerable here, which is not the same as an empty answer: %s.\n%s"
                . 'typo3_server_scope reports the installation and its console.',
                $reason,
                $diagnosis === '' ? '' : $diagnosis . "\n",
            ),
            $echo + [
                // The reason travels as data, not only in the text beside it: a
                // client that renders structuredContent alone would otherwise
                // see one key and no way to tell why.
                'unsupported' => [
                    'cause' => $cause,
                    'reason' => $reason,
                    'diagnosis' => $diagnosis,
                    // Where it looked and what was set wrong belong here too.
                    // typo3_server_scope carried both, and a caller just told
                    // that nothing could be asked is the one least able to
                    // guess that a second tool holds the way out.
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
