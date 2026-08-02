<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Knowledge;

use Typo3CmsMcp\Feedback\Channel;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Server\ExcludedTools;

/**
 * The server's own description of what it knows and which tool answers what.
 *
 * Without it an agent has to discover the shape of the server by calling tools
 * until one answers, and four prose lookups over one corpus look
 * interchangeable from the outside. The data lives in
 * knowledge/server-scope.json, next to the knowledge it describes, so a new
 * document or catalog is announced where it is added.
 *
 * `typo3_server_scope` is what hands this to a client. The word this class does
 * not take is `Scope`, which is the one vocabulary saying which kind of work an
 * answer is for — including, per topic, the answers described here.
 */
final class Coverage
{
    /**
     * How much of the statement below a client can be relied on to keep.
     *
     * Measured rather than agreed: the client the recorded runs use truncates
     * at 2048 characters and says so only in its own debug output, so a server
     * that writes more loses the end of what it wrote to nobody's error. The
     * budget covers everything assembled — the exclusion prefix and the write
     * sentence included — because the client counts what it receives.
     */
    public const INSTRUCTIONS_BUDGET = 2048;

    /**
     * The statement handed to clients at initialize time, so the boundary is
     * known before the first tool call rather than after a fruitless one.
     *
     * The write sentence is appended rather than stored, because whether this
     * server can write at all depends on the checkout it runs from — and a
     * client that is told "read-only" must not then be offered a tool that
     * creates a file.
     *
     * Everything assembled here has to fit what a client keeps: a sentence past
     * the limit is a sentence nobody reads, and neither side says so. R-ANS-13
     * holds the whole of it, prefix and suffix included, to that budget.
     */
    public static function instructions(): string
    {
        $instructions = self::read()['instructions'];
        if (Channel::isAvailable()) {
            $instructions .= ' Every tool here is read-only except typo3_feedback_record, '
                . 'which creates a new markdown feedback under feedback/ and writes nothing else.';
        }

        // In front of it, not behind: what a client is not being offered
        // belongs before the routing it is offered, and a client told where to
        // start and only then that a tool is missing has been told and then
        // corrected.
        return self::exclusionPrefix($instructions) . $instructions;
    }

    /**
     * What was left out, said in front of the routing.
     *
     * Naming them is the useful form and the one that does not fit every time:
     * the list is the caller's and can be most of the server, while the budget
     * is fixed. Past that the count is said instead and typo3_server_scope
     * holds the names, which is the one tool no caller can exclude.
     */
    private static function exclusionPrefix(string $rest): string
    {
        $excluded = ExcludedTools::all();
        if ($excluded === []) {
            return '';
        }

        $named = sprintf(
            '%s %s left out of your tool list, and so is anything that routed to %s. Otherwise: ',
            implode(', ', $excluded),
            count($excluded) === 1 ? 'is' : 'are',
            count($excluded) === 1 ? 'it' : 'them',
        );

        return mb_strlen($named . $rest) <= self::INSTRUCTIONS_BUDGET ? $named : sprintf(
            '%d tools are left out of your tool list, and so is anything that routed to them; '
            . 'typo3_server_scope names them. Otherwise: ',
            count($excluded),
        );
    }

    /**
     * The coverage as this client is actually offered it.
     *
     * The stored file describes the whole server, and by default that is what a
     * client gets. Where the caller excluded a tool, no entry may still point at
     * it: a map routing to something that is not in the list is a broken server
     * as far as a client can tell.
     *
     * @return array{
     *     purpose: string,
     *     instructions: string,
     *     covers: array<int, array{topic: string, depth: string, tools: array<int, string>, source: string, scope: Scope}>,
     *     doesNotCover: array<int, array{topic: string, why: string, instead: string}>,
     *     checkoutDiscovery: array<int, array{establish: string, how: string}>,
     *     routing: array<int, array{when: string, call: string}>
     * }
     */
    public static function offered(): array
    {
        $coverage = self::read();
        if (ExcludedTools::all() === []) {
            return $coverage;
        }

        $covers = [];
        foreach ($coverage['covers'] as $entry) {
            // The topic stays as long as something still answers it. Which kind
            // of work its answers are for is stated per topic and does not
            // decide whether the topic is here — that was the profile's
            // reasoning, and it read the repository where the task was meant.
            $entry['tools'] = array_values(array_filter($entry['tools'], ExcludedTools::offers(...)));
            if ($entry['tools'] !== []) {
                $covers[] = $entry;
            }
        }
        $coverage['covers'] = $covers;

        $offered = static fn(array $entry): bool => !self::namesExcludedTool(implode(' ', $entry));
        $coverage['doesNotCover'] = array_values(array_filter($coverage['doesNotCover'], $offered));
        $coverage['checkoutDiscovery'] = array_values(array_filter($coverage['checkoutDiscovery'], $offered));
        $coverage['routing'] = array_values(array_filter($coverage['routing'], $offered));

        return $coverage;
    }

    /** Whether a rendered entry sends the caller to a tool it is not offered. */
    private static function namesExcludedTool(string $text): bool
    {
        foreach (ExcludedTools::all() as $tool) {
            if (str_contains($text, $tool)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{
     *     purpose: string,
     *     instructions: string,
     *     covers: array<int, array{topic: string, depth: string, tools: array<int, string>, source: string, scope: Scope}>,
     *     doesNotCover: array<int, array{topic: string, why: string, instead: string}>,
     *     checkoutDiscovery: array<int, array{establish: string, how: string}>,
     *     routing: array<int, array{when: string, call: string}>
     * }
     */
    public static function read(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('server-scope.json')), true);
        if (!is_array($decoded) || !isset($decoded['covers'], $decoded['routing'])) {
            throw new \RuntimeException('Invalid server-scope.json');
        }

        return [
            'purpose' => (string) ($decoded['purpose'] ?? ''),
            'instructions' => (string) ($decoded['instructions'] ?? ''),
            'covers' => array_map(static fn(array $entry): array => [
                'topic' => (string) $entry['topic'],
                'depth' => (string) ($entry['depth'] ?? ''),
                'tools' => array_map('strval', $entry['tools'] ?? []),
                'source' => (string) ($entry['source'] ?? ''),
                // Which kind of work the answers are for: the boundary runs
                // through the middle of this server, not around it.
                'scope' => Scope::from((string) $entry['scope']),
            ], $decoded['covers']),
            'doesNotCover' => array_map(static fn(array $entry): array => [
                'topic' => (string) $entry['topic'],
                'why' => (string) ($entry['why'] ?? ''),
                'instead' => (string) ($entry['instead'] ?? ''),
            ], $decoded['doesNotCover'] ?? []),
            'checkoutDiscovery' => array_map(static fn(array $entry): array => [
                'establish' => (string) $entry['establish'],
                'how' => (string) ($entry['how'] ?? ''),
            ], $decoded['checkoutDiscovery'] ?? []),
            'routing' => array_map(static fn(array $entry): array => [
                'when' => (string) $entry['when'],
                'call' => (string) $entry['call'],
            ], $decoded['routing']),
        ];
    }
}
