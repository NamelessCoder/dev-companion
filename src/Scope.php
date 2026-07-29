<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * The server's own description of what it knows and which tool answers what.
 *
 * Without it an agent has to discover the shape of the server by calling tools
 * until one answers, and four prose lookups over one corpus look
 * interchangeable from the outside. The data lives in
 * knowledge/server-scope.json, next to the knowledge it describes, so a new
 * document or catalog is announced where it is added.
 */
final class Scope
{
    /**
     * Paths and phrases that place a task outside core contribution.
     *
     * typo3conf/ext and vendor are where an installation's extensions live,
     * packages and extensions are where a project keeps its own.
     *
     * @var array<int, string>
     */
    private const OUTSIDE_CORE = [
        'packages/', 'typo3conf/ext/', 'vendor/', 'extensions/',
        'project extension', 'site package', 'sitepackage', 'custom extension',
        'third-party extension', 'own extension',
    ];

    /**
     * Paths and phrases that place a task inside core contribution.
     *
     * Positive evidence, and deliberately narrow: it decides whether the core's
     * own contribution process may be stated as applying, and the absence of
     * outside-core markers is not evidence of anything.
     *
     * @var array<int, string>
     */
    private const CORE_WORK = [
        'typo3/sysext/', 'gerrit', 'change-id', 'review.typo3.org', 'forge.typo3.org',
        'typo3 core', 'core patch', 'core contribution',
    ];

    /**
     * What every tool says first once it has recognised work outside the core.
     *
     * One sentence in one place, because three tools now say it and a caller
     * that learns to recognise it in one answer has to find it unchanged in the
     * next. Each tool appends what follows from it for its own payload.
     */
    public const OUTSIDE_CORE_NOTICE = 'This reads as work outside the TYPO3 core — a project or third-party '
        . 'extension. This server only knows the core\'s own conventions, and several of them (the changelog, '
        . 'the Gerrit workflow, the runTests.sh suites) have no counterpart there.';

    /**
     * Whether the task is about something other than the TYPO3 core.
     *
     * The conventions here are the core's own, and several of them — the
     * changelog, the Gerrit workflow, the runTests.sh suites — do not exist
     * outside it. Answering a project-extension question with a core patch
     * checklist is worse than saying so.
     *
     * @param array<int, string> $paths
     */
    public static function isOutsideCore(array $paths, string $text = ''): bool
    {
        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);
        if (str_contains($haystack, 'typo3/sysext/')) {
            return false;
        }

        foreach (self::OUTSIDE_CORE as $marker) {
            if (str_contains($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Things that exist in the core repository and nowhere else. A line of
     * advice naming one of them cannot be followed outside it.
     *
     * @var array<int, string>
     */
    private const CORE_ONLY_ARTIFACTS = [
        'typo3/sysext/', 'build/scripts/', 'runtests.sh', 'gerrit', 'change-id',
        'refs/for/', 'forge.typo3.org', 'core checkout', 'core branch', 'core root',
        'core team',
    ];

    /**
     * Whether a single step, check or checklist item is core-only.
     *
     * The distinction the notice draws in prose has to be drawn in the payload
     * too, and it is drawn per line rather than per section: a checklist mixes
     * "reproduce the bug with a failing test" — true anywhere — with "add a
     * changelog file under typo3/sysext/", which is a path that does not exist
     * in the repository the caller is in.
     */
    public static function isCoreOnly(string $text): bool
    {
        $haystack = mb_strtolower($text);
        foreach (self::CORE_ONLY_ARTIFACTS as $artifact) {
            if (str_contains($haystack, $artifact)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether anything in the task actually says this is core work.
     *
     * `isOutsideCore()` returning false means no marker was found, which is the
     * state most tasks are in — "review the TCA of an extension" says nothing
     * either way. Where an answer would state the core's own process as
     * applying, that silence is not enough, so this asks for the evidence
     * rather than for the absence of the opposite.
     *
     * @param array<int, string> $paths
     */
    public static function isCoreWork(array $paths, string $text = ''): bool
    {
        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);
        foreach (self::CORE_WORK as $marker) {
            if (str_contains($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The statement handed to clients at initialize time, so the boundary is
     * known before the first tool call rather than after a fruitless one.
     *
     * The write sentence is appended rather than stored, because whether this
     * server can write at all depends on the checkout it runs from — and a
     * client that is told "read-only" must not then be offered a tool that
     * creates a file.
     */
    public static function instructions(): string
    {
        $instructions = self::read()['instructions'];
        if (Feedback::isAvailable()) {
            $instructions .= ' Every tool here is read-only except typo3_feedback_record, '
                . 'which creates a new markdown note under feedback/ and writes nothing else.';
        }

        return $instructions;
    }

    /**
     * @return array{
     *     purpose: string,
     *     instructions: string,
     *     covers: array<int, array{topic: string, depth: string, tools: array<int, string>, source: string}>,
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
