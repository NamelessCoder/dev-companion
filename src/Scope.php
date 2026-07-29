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
        . 'extension. That is covered here, as far as the core\'s own conventions reach into it; what belongs to '
        . 'the core repository alone (the changelog, the Gerrit workflow, the runTests.sh suites) has no '
        . 'counterpart there and is left out of the answer rather than handed over.';

    /**
     * Directories an extension is laid out in. A path that begins with one of
     * them is inside a package, and no core file is ever named that way from
     * the core root: there everything is below typo3/sysext/<key>/ or Build/.
     *
     * @var array<int, string>
     */
    private const EXTENSION_LAYOUT = [
        'classes/', 'configuration/', 'resources/',
        'ext_localconf.php', 'ext_tables.php', 'ext_emconf.php',
    ];

    /**
     * Whether the task is about something other than the TYPO3 core.
     *
     * The conventions here are the core's own, and several of them — the
     * changelog, the Gerrit workflow, the runTests.sh suites — do not exist
     * outside it. Answering a project-extension question with a core patch
     * checklist is worse than saying so.
     *
     * The evidence is structural where structure exists, because wording is
     * the weakest of the signals: "bootstrap_package" says everything about
     * which repository this is and matches none of the phrases below.
     *
     * @param array<int, string> $paths
     */
    public static function isOutsideCore(array $paths, string $text = '', string $area = ''): bool
    {
        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);

        // A sysext path is the only marker strong enough to end the question
        // outright. The prose ones are not, and cannot be: "not TYPO3 core, a
        // composer package under vendor bk2k" names the core in order to rule
        // it out, and reads to a substring search exactly like claiming it.
        if (str_contains($haystack, 'typo3/sysext/')) {
            return false;
        }

        foreach (self::OUTSIDE_CORE as $marker) {
            if (str_contains($haystack, $marker)) {
                return true;
            }
        }

        // An area that names an installed package which is not a system
        // extension. The installation is asked because only it knows: the same
        // key is a system extension in one and a vendor package in the next.
        // An area it does not have at all says nothing — "FormEngine" and
        // "backend forms" are areas too.
        $systemExtension = $area === '' ? null : Instance::isSystemExtension($area);
        if ($systemExtension === false) {
            return true;
        }

        foreach ($paths as $path) {
            $lowered = ltrim(mb_strtolower(str_replace('\\', '/', $path)), './');
            foreach (self::EXTENSION_LAYOUT as $prefix) {
                if (str_starts_with($lowered, $prefix)) {
                    return true;
                }
            }
        }

        // Naming a system extension as the area, or naming the contribution
        // workflow, is evidence in the other direction. Both beat the weakest
        // signal there is — which installation the session happens to sit in —
        // and neither beats a marker above, because those describe the work
        // while these only accompany it.
        if ($systemExtension === true || self::isCoreWork($paths, $text)) {
            return false;
        }

        // That signal. A Composer project is not a core checkout, and work done
        // in one is not core work unless something above said it was.
        return (Instance::describe()['kind'] ?? '') === Instance::KIND_COMPOSER_PROJECT;
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
        if (Profile::omitted() !== []) {
            // In front of it, not behind: the stored sentence opens with the
            // core contribution catalog, which is the half this client is not
            // being offered, and a client told what the server is and only then
            // that half of it is missing has been told and then corrected.
            $instructions = sprintf(
                'You are offered the "%s" profile of this server: the core contribution surface — the review rules, '
                . 'the Gerrit workflow, the runTests.sh suites — is not in your tool list, because a project or '
                . 'extension repository has none of it, and %s=%s offers it anyway. Otherwise: %s',
                Profile::active(),
                Profile::VARIABLE,
                Profile::ALL,
                $instructions,
            );
        }
        if (Feedback::isAvailable()) {
            $instructions .= ' Every tool here is read-only except typo3_feedback_record, '
                . 'which creates a new markdown note under feedback/ and writes nothing else.';
        }

        return $instructions;
    }

    /**
     * The scope as the active profile presents it.
     *
     * The stored file describes the whole server. A client that is offered half
     * of it must not be handed the other half as its map: a topic whose answers
     * are core-only is gone with the tools that answered it, and no entry
     * anywhere points at a tool this client cannot call.
     *
     * @return array{
     *     purpose: string,
     *     instructions: string,
     *     covers: array<int, array{topic: string, depth: string, tools: array<int, string>, source: string, provenance: string}>,
     *     doesNotCover: array<int, array{topic: string, why: string, instead: string}>,
     *     checkoutDiscovery: array<int, array{establish: string, how: string}>,
     *     routing: array<int, array{when: string, call: string}>
     * }
     */
    public static function offered(): array
    {
        $scope = self::read();
        if (Profile::omitted() === []) {
            return $scope;
        }

        $covers = [];
        foreach ($scope['covers'] as $entry) {
            if ($entry['provenance'] === 'core-only') {
                continue;
            }
            $entry['tools'] = array_values(array_filter($entry['tools'], Profile::offers(...)));
            if ($entry['tools'] !== []) {
                $covers[] = $entry;
            }
        }
        $scope['covers'] = $covers;

        $offered = static fn(array $entry): bool => !self::namesOmittedTool(implode(' ', $entry));
        $scope['doesNotCover'] = array_values(array_filter($scope['doesNotCover'], $offered));
        $scope['checkoutDiscovery'] = array_values(array_filter($scope['checkoutDiscovery'], $offered));
        $scope['routing'] = array_values(array_filter($scope['routing'], $offered));

        return $scope;
    }

    /** Whether a rendered entry sends the caller to a tool it is not offered. */
    private static function namesOmittedTool(string $text): bool
    {
        foreach (Profile::omitted() as $tool) {
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
     *     covers: array<int, array{topic: string, depth: string, tools: array<int, string>, source: string, provenance: string}>,
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
                // What the answer is worth outside the core: the boundary runs
                // through the middle of this server, not around it.
                'provenance' => (string) ($entry['provenance'] ?? ''),
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
