<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Drafts a TYPO3 core commit message and checks it against the contribution
 * rules. Pure logic — reads nothing and touches no checkout.
 */
final class CommitMessage
{
    /**
     * @param array{
     *   changeType: string,
     *   summary: string,
     *   issue?: ?string,
     *   relatedIssues?: array<int, string>,
     *   releases?: array<int, string>,
     *   body?: ?string,
     *   isBreaking?: bool,
     *   isDeprecation?: bool
     * } $input
     * @return array{message: string, checks: array<int, array{level: string, message: string}>}
     */
    public static function create(array $input): array
    {
        $changeType = (string) $input['changeType'];
        $summary = self::normalizeSummary((string) $input['summary']);
        $issue = self::normalizeIssue($input['issue'] ?? null);
        $isBreaking = (bool) ($input['isBreaking'] ?? false);
        $isDeprecation = (bool) ($input['isDeprecation'] ?? false);

        $relatedIssues = [];
        foreach ($input['relatedIssues'] ?? [] as $related) {
            $normalized = self::normalizeIssue($related);
            if ($normalized !== null) {
                $relatedIssues[] = $normalized;
            }
        }

        $releases = $input['releases'] ?? [];
        if ($releases === []) {
            $releases = ['main'];
        }

        $prefix = ($isBreaking ? '[!!!]' : '') . '[' . $changeType . ']';
        $subject = $prefix . ' ' . $summary;
        $body = isset($input['body']) ? trim((string) $input['body']) : '';

        $parts = [$subject];
        if ($body !== '') {
            $parts[] = "\n" . self::wrapBody($body);
        }
        $parts[] = '';
        $parts[] = $issue !== null ? 'Resolves: ' . $issue : 'Resolves: #ISSUE_NUMBER';
        foreach ($relatedIssues as $related) {
            $parts[] = 'Related: ' . $related;
        }
        $parts[] = 'Releases: ' . implode(', ', $releases);

        return [
            'message' => implode("\n", $parts),
            'checks' => self::checks($changeType, $subject, $summary, $issue, $isBreaking, $isDeprecation, $releases),
        ];
    }

    public static function normalizeIssue(?string $issue): ?string
    {
        $trimmed = trim((string) $issue);
        if ($trimmed === '') {
            return null;
        }

        return str_starts_with($trimmed, '#') ? $trimmed : '#' . $trimmed;
    }

    /**
     * @param array<int, string> $releases
     * @return array<int, array{level: string, message: string}>
     */
    private static function checks(
        string $changeType,
        string $subject,
        string $summary,
        ?string $issue,
        bool $isBreaking,
        bool $isDeprecation,
        array $releases
    ): array {
        $checks = [];

        if ($issue === null) {
            $checks[] = ['level' => 'error', 'message' => 'A Forge issue is required. Add a Resolves: #12345 line.'];
        }

        $length = strlen($subject);
        if ($length > 72) {
            $checks[] = ['level' => 'error', 'message' => sprintf('The summary line is %d characters long. Keep it below 72 characters.', $length)];
        } elseif ($length > 52) {
            $checks[] = ['level' => 'warning', 'message' => sprintf('The summary line is %d characters long. Below 52 characters is preferred.', $length)];
        }

        if (!preg_match('/^[A-Z]/', $summary)) {
            $checks[] = ['level' => 'warning', 'message' => 'Start the summary text with a capital letter after the keyword.'];
        }

        if (str_contains($summary, 'EXT:')) {
            $checks[] = ['level' => 'warning', 'message' => 'Avoid EXT:... in the summary when the changed files already show the system extension context.'];
        }

        if ($isDeprecation && $isBreaking) {
            $checks[] = ['level' => 'error', 'message' => 'Deprecations must not use the [!!!] breaking prefix.'];
        }

        if ($isDeprecation && !in_array($changeType, ['TASK', 'FEATURE'], true)) {
            $checks[] = ['level' => 'error', 'message' => 'Deprecations may only use [TASK] or [FEATURE].'];
        }

        if ($isBreaking || $isDeprecation) {
            $checks[] = [
                'level' => 'warning',
                'message' => 'Breaking changes and deprecations require a changelog RST file below typo3/sysext/core/Documentation/Changelog/. Validate it with ./Build/Scripts/runTests.sh -s checkRst.',
            ];
        }

        if ($isBreaking) {
            foreach ($releases as $release) {
                if ($release !== 'main') {
                    $checks[] = ['level' => 'warning', 'message' => 'Breaking changes should usually target main. Confirm older release targets with the release managers.'];
                    break;
                }
            }
        }

        if ($checks === []) {
            $checks[] = ['level' => 'info', 'message' => 'No commit message readiness issues found by the local checks.'];
        }

        return $checks;
    }

    private static function normalizeSummary(string $summary): string
    {
        return preg_replace('/\s+/', ' ', trim($summary)) ?? trim($summary);
    }

    private static function wrapBody(string $body): string
    {
        $lines = array_map('rtrim', preg_split('/\n/', $body) ?: []);
        return implode("\n", $lines);
    }
}
