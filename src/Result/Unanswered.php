<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Result;

use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\ToolResult;

/**
 * The answer for a question only the installation could have answered, when it
 * could not be asked.
 *
 * Kept in one place because the distinction it draws is the same every time: an
 * empty result and an unanswerable question look identical, and only one of them
 * means the thing does not exist.
 */
final class Unanswered
{
    /** @param array<string, mixed> $data the fields the tool's schema requires, empty */
    public static function because(string $error, array $data): ToolResult
    {
        $diagnosis = Typo3Cli::diagnose($error);
        if ($diagnosis === '' && Typo3Cli::caveat() !== '') {
            $diagnosis = 'What is known about this console: ' . Typo3Cli::caveat() . '.';
        }

        return ToolResult::create(
            sprintf(
                "The installation could not be asked, so this is unanswered rather than empty: %s.\n%s"
                . 'typo3_server_scope reports the installation and its console.',
                $error,
                $diagnosis === '' ? '' : $diagnosis . "\n",
            ),
            $data + [
                'answeredBy' => 'nothing',
                // The reason travels with the answer, not only in the text
                // beside it: a client that renders structuredContent alone
                // would otherwise see an empty result and nothing else, which
                // is exactly what a registry that really is empty looks like.
                'unavailable' => [
                    'reason' => $error,
                    'diagnosis' => $diagnosis,
                    'settings' => [
                        'root' => Instance::ROOT_VARIABLE,
                        'console' => Typo3Cli::CONSOLE_VARIABLE,
                    ],
                ],
            ],
        );
    }
}
