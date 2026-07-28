<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Drafts a TYPO3 core commit message and checks it against the contribution
 * rules. Pure logic — reads nothing and touches no checkout.
 *
 * The draft is emitted ready to use, so everything the rules demand of a commit
 * message has to hold for what this class returns: an agent copies the block
 * verbatim, and a defect in it lands in the patch. That is why the body is
 * wrapped at 72 characters here instead of only being complained about.
 */
final class CommitMessage
{
    /** Column the body is wrapped at, per the TYPO3 commit message rules. */
    public const BODY_WIDTH = 72;

    /** Keywords a contributor may use; [SECURITY] belongs to the Security Team. */
    private const KEYWORDS = ['BUGFIX', 'FEATURE', 'TASK', 'DOCS'];

    /** Trailers this class understands; anything else is carried through as written. */
    private const KNOWN_TRAILERS = ['resolves', 'related', 'releases'];

    /**
     * @param array{
     *   changeType?: string,
     *   summary: string,
     *   issue?: ?string,
     *   issues?: array<int, string>,
     *   relatedIssues?: array<int, string>,
     *   releases?: array<int, string>,
     *   body?: ?string,
     *   isBreaking?: bool,
     *   isDeprecation?: bool,
     *   extraTrailers?: array<int, string>
     * } $input
     * @return array{message: string, checks: array<int, array{level: string, message: string}>}
     */
    public static function create(array $input): array
    {
        $changeType = trim((string) ($input['changeType'] ?? ''));
        $summary = self::normalizeSummary((string) $input['summary']);
        $isBreaking = (bool) ($input['isBreaking'] ?? false);
        $isDeprecation = (bool) ($input['isDeprecation'] ?? false);

        $issues = [];
        foreach (array_merge([$input['issue'] ?? null], $input['issues'] ?? []) as $issue) {
            $normalized = self::normalizeIssue(is_string($issue) ? $issue : null);
            if ($normalized !== null) {
                $issues[$normalized] = true;
            }
        }
        $issues = array_keys($issues);

        $relatedIssues = [];
        foreach ($input['relatedIssues'] ?? [] as $related) {
            $normalized = self::normalizeIssue($related);
            if ($normalized !== null) {
                $relatedIssues[$normalized] = true;
            }
        }
        $relatedIssues = array_keys($relatedIssues);

        $releases = $input['releases'] ?? [];
        if ($releases === []) {
            $releases = ['main'];
        }

        $prefix = ($isBreaking ? '[!!!]' : '') . '[' . ($changeType === '' ? 'KEYWORD' : $changeType) . ']';
        $subject = $prefix . ' ' . $summary;
        $body = self::wrapBody(isset($input['body']) ? (string) $input['body'] : '');

        $parts = [$subject];
        if ($body !== '') {
            $parts[] = "\n" . $body;
        }
        $parts[] = '';
        if ($issues === []) {
            $parts[] = 'Resolves: #ISSUE_NUMBER';
        }
        foreach ($issues as $issue) {
            $parts[] = 'Resolves: ' . $issue;
        }
        foreach ($relatedIssues as $related) {
            $parts[] = 'Related: ' . $related;
        }
        $parts[] = 'Releases: ' . implode(', ', $releases);
        foreach ($input['extraTrailers'] ?? [] as $trailer) {
            $parts[] = $trailer;
        }

        return [
            'message' => implode("\n", $parts),
            'checks' => self::checks(
                $changeType,
                $subject,
                $summary,
                $body,
                $issues,
                $isBreaking,
                $isDeprecation,
                $releases,
            ),
        ];
    }

    /**
     * Splits an existing commit message into the parts create() works on, so a
     * message written by hand (or amended on an existing patch set) can be
     * checked as a whole instead of being reassembled from fields.
     *
     * Trailers this class does not know — `Change-Id:` above all, which the
     * commit hook owns and an amend must keep — are carried through untouched.
     *
     * @return array{
     *     input: array<string, mixed>,
     *     checks: array<int, array{level: string, message: string}>
     * }
     */
    public static function parse(string $message): array
    {
        $lines = preg_split('/\R/', trim($message)) ?: [];
        $checks = [];

        $subject = trim(array_shift($lines) ?? '');
        if ($subject === '') {
            throw new \InvalidArgumentException('The commit message is empty.');
        }

        $changeType = '';
        $isBreaking = false;
        $summary = $subject;
        if (preg_match('/^(\[!!!\])?\[([A-Za-z]+)\]\s*(.*)$/', $subject, $matches) === 1) {
            $isBreaking = $matches[1] !== '';
            $changeType = strtoupper($matches[2]);
            $summary = trim($matches[3]);

            if (!in_array($changeType, self::KEYWORDS, true)) {
                $checks[] = [
                    'level' => 'error',
                    'message' => $changeType === 'SECURITY'
                        ? '[SECURITY] is reserved for the TYPO3 Security Team. Use [BUGFIX] or [TASK].'
                        : sprintf('Unknown keyword [%s]. Use [BUGFIX], [FEATURE], [TASK], or [DOCS].', $changeType),
                ];
                $changeType = '';
            }
        } else {
            $checks[] = [
                'level' => 'error',
                'message' => 'The summary line must start with a TYPO3 keyword, for example "[BUGFIX] Fix ...".',
            ];
        }

        if ($lines !== [] && trim($lines[0]) !== '') {
            $checks[] = [
                'level' => 'warning',
                'message' => 'Separate the summary line and the body with a blank line.',
            ];
        }

        // Everything from the last trailer block to the end belongs to the
        // trailers. A line only counts as one when it carries a known trailer
        // name or a hyphenated git-style one (Change-Id, Reviewed-by), so a
        // body sentence like "Note: ..." stays body text.
        $trailerLines = [];
        while ($lines !== []) {
            $last = trim((string) end($lines));
            if ($last === '') {
                array_pop($lines);
                continue;
            }
            if (!self::isTrailer($last)) {
                break;
            }
            array_unshift($trailerLines, $last);
            array_pop($lines);
        }

        $issues = [];
        $relatedIssues = [];
        $releases = [];
        $extraTrailers = [];
        foreach ($trailerLines as $trailer) {
            [$name, $value] = array_map('trim', explode(':', $trailer, 2));
            $key = strtolower($name);
            if (!in_array($key, self::KNOWN_TRAILERS, true)) {
                $extraTrailers[] = $trailer;
                continue;
            }
            match ($key) {
                'resolves' => $issues[] = $value,
                'related' => $relatedIssues[] = $value,
                'releases' => $releases = array_values(array_filter(array_map(
                    'trim',
                    explode(',', $value)
                ), static fn(string $release): bool => $release !== '')),
            };
        }

        if ($releases === []) {
            $checks[] = [
                'level' => 'warning',
                'message' => 'No Releases: line found. Add the target versions, for example "Releases: main, 13.4".',
            ];
        }

        return [
            'input' => [
                'changeType' => $changeType,
                'summary' => $summary,
                'issues' => $issues,
                'relatedIssues' => $relatedIssues,
                'releases' => $releases,
                'body' => implode("\n", $lines),
                'isBreaking' => $isBreaking,
                'extraTrailers' => $extraTrailers,
            ],
            'checks' => $checks,
        ];
    }

    private static function isTrailer(string $line): bool
    {
        if (preg_match('/^([A-Za-z][A-Za-z-]*):\s*\S/', $line, $matches) !== 1) {
            return false;
        }

        return in_array(strtolower($matches[1]), self::KNOWN_TRAILERS, true)
            || str_contains($matches[1], '-');
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
     * @param array<int, string> $issues
     * @param array<int, string> $releases
     * @return array<int, array{level: string, message: string}>
     */
    private static function checks(
        string $changeType,
        string $subject,
        string $summary,
        string $body,
        array $issues,
        bool $isBreaking,
        bool $isDeprecation,
        array $releases
    ): array {
        $checks = [];

        if ($issues === []) {
            $checks[] = ['level' => 'error', 'message' => 'A Forge issue is required. Add a Resolves: #12345 line.'];
        }

        $length = mb_strlen($subject);
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

        foreach (self::overlongBodyLines($body) as $line) {
            $checks[] = [
                'level' => 'warning',
                'message' => sprintf(
                    'Body line %d is %d characters long and could not be wrapped at %d characters '
                    . '(a URL, a code line, or another unbreakable token). Shorten it if it is prose.',
                    $line['number'],
                    $line['length'],
                    self::BODY_WIDTH,
                ),
            ];
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

    /**
     * Wraps the body at 72 characters, the width the core rules ask for.
     *
     * Only prose is reflowed. Fenced code, indented blocks, and list items keep
     * their structure, and a word longer than the width — a URL, a class name,
     * a command — is never broken: it goes on a line of its own and is reported
     * by the checks instead.
     */
    private static function wrapBody(string $body): string
    {
        $lines = preg_split('/\R/', trim($body)) ?: [];

        $output = [];
        $paragraph = [];
        $inFence = false;

        $flush = static function () use (&$paragraph, &$output): void {
            if ($paragraph !== []) {
                $output[] = self::wrapParagraph(implode(' ', $paragraph), '', '');
                $paragraph = [];
            }
        };

        foreach ($lines as $line) {
            $line = rtrim($line);

            if (str_starts_with(ltrim($line), '```')) {
                $flush();
                $inFence = !$inFence;
                $output[] = $line;
                continue;
            }

            if ($inFence || preg_match('/^\s/', $line) === 1) {
                $flush();
                $output[] = $line;
                continue;
            }

            if (trim($line) === '') {
                $flush();
                $output[] = '';
                continue;
            }

            if (preg_match('/^([-*+]\s+|\d+[.)]\s+)(.*)$/', $line, $matches) === 1) {
                $flush();
                $output[] = self::wrapParagraph(
                    $matches[2],
                    $matches[1],
                    str_repeat(' ', mb_strlen($matches[1])),
                );
                continue;
            }

            $paragraph[] = $line;
        }

        $flush();

        return rtrim(implode("\n", $output));
    }

    /** Greedy word wrap that never splits a word. */
    private static function wrapParagraph(string $text, string $firstPrefix, string $continuationPrefix): string
    {
        $lines = [];
        $current = null;

        foreach (preg_split('/\s+/', trim($text)) ?: [] as $word) {
            if ($word === '') {
                continue;
            }
            if ($current === null) {
                $current = $firstPrefix . $word;
                continue;
            }
            if (mb_strlen($current) + 1 + mb_strlen($word) <= self::BODY_WIDTH) {
                $current .= ' ' . $word;
                continue;
            }
            $lines[] = $current;
            $current = $continuationPrefix . $word;
        }

        if ($current !== null) {
            $lines[] = $current;
        }

        return implode("\n", $lines);
    }

    /**
     * Lines the wrapping could not bring below the width, with their position in
     * the body.
     *
     * @return array<int, array{number: int, length: int}>
     */
    private static function overlongBodyLines(string $body): array
    {
        $overlong = [];
        foreach (preg_split('/\R/', $body) ?: [] as $index => $line) {
            $length = mb_strlen($line);
            if ($length > self::BODY_WIDTH) {
                $overlong[] = ['number' => $index + 1, 'length' => $length];
            }
        }

        return $overlong;
    }
}
