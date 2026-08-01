<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Installation;

/**
 * What the installation itself says, once it has booted.
 *
 * Some registries are only ever assembled at runtime. An icon list built in a
 * foreach, a table added by a PHP call, a content element whose identifier
 * comes out of a variable — none of them exists in a file a reader could parse,
 * and a review that compares a parsed list against the tree reports defects
 * nobody has. So TYPO3 is booted and its container is asked, which is the same
 * move `Typo3Cli` makes for the questions a console command covers.
 *
 * Three answers, and the middle one is why this class exists:
 *
 * - **full** — the container came up with every extension in it, and what it
 *   reports is the truth about this installation.
 * - **failsafe** — TYPO3 booted, but without essential configuration, so only
 *   core packages are in the container. Every registry still answers, and
 *   every answer is a subset that looks like the whole. It is never handed on
 *   as a result; it is a reason to fall back and say so.
 * - **unreachable** — no console could be resolved, no interpreter derived, or
 *   the boot failed. Also a reason, never silence.
 *
 * The reading is done once and then kept, because it costs a boot and one
 * reading answers every topic. A reading that is not full is kept only for as
 * long as the console it was taken through stays the same: a caller that reads
 * "the DDEV project is stopped" and starts it is resolved through DDEV
 * afterwards, and asks again — which is the rule `Typo3Cli::resolve()` and
 * `Instance::describe()` follow, applied to the thing that changes here.
 */
final class Typo3Runtime
{
    /** The container came up whole; what it reports is this installation. */
    public const STATE_FULL = 'full';

    /** TYPO3 booted without its configuration: core only, and it looks complete. */
    public const STATE_FAILSAFE = 'failsafe';

    /** Nothing was asked, and the reason says what stood in the way. */
    public const STATE_UNREACHABLE = 'unreachable';

    /** @var array{state: string, reason: string, topics: array<string, mixed>}|null */
    private static ?array $answer = null;

    /** The console this reading was taken through; null while there is none. */
    private static ?string $through = null;

    /**
     * What the running installation reports, or why it did not.
     *
     * @return array{state: string, reason: string, topics: array<string, mixed>}
     */
    public static function ask(): array
    {
        $through = Typo3Cli::resolve()['via'] ?? '';
        // A full reading is the installation and is kept. One that is not gets
        // kept too — an answer costs a boot, and four topics are read out of
        // one answer — but only for as long as the way in stays the same: the
        // caller that reads "the DDEV project is stopped", starts it, and asks
        // again is resolved through DDEV rather than through this machine's PHP,
        // and that is the reading worth taking twice. What this does not catch
        // is a system configured mid-session, which keeps its failsafe reading
        // until the server is restarted.
        if (self::$answer !== null && (self::$answer['state'] === self::STATE_FULL || self::$through === $through)) {
            return self::$answer;
        }

        self::$through = $through;

        return self::$answer = self::read();
    }

    /**
     * One topic of a full reading, or null when there was none.
     *
     * Null and an empty topic are different answers, and the caller that falls
     * back needs the difference: nothing registered is a fact, nothing asked is
     * a gap with a reason attached.
     */
    public static function topic(string $name): mixed
    {
        $answer = self::ask();

        return $answer['state'] === self::STATE_FULL ? ($answer['topics'][$name] ?? null) : null;
    }

    /** Why there is no full reading. Empty when there is one. */
    public static function reason(): string
    {
        $answer = self::ask();

        return $answer['state'] === self::STATE_FULL ? '' : $answer['reason'];
    }

    /** Drops the memoized reading; for tests that move between installations. */
    public static function forget(): void
    {
        self::$answer = null;
        self::$through = null;
    }

    /**
     * The extension key a runtime entry names, or null where it names none.
     *
     * TCA and the icon registry are the installation's, not any package's, and
     * an answer about one extension cannot be assembled from a list belonging
     * to all of them. What every entry does carry is a reference into the
     * package that owns it — `LLL:EXT:news/…locallang.xlf:plugin.list` on a
     * label or a ctrl title, `EXT:news/Resources/Public/Icons/list.svg` on an
     * icon — and that reference is evidence rather than a naming convention.
     * Where there is none, the entry belongs to the installation.
     */
    public static function extensionIn(string $reference): ?string
    {
        return preg_match('#(?:^|:)EXT:([a-z0-9_]+)/#i', $reference, $match) === 1
            ? strtolower($match[1])
            : null;
    }

    /** @return array{state: string, reason: string, topics: array<string, mixed>} */
    private static function read(): array
    {
        $root = Instance::root();
        if ($root === null) {
            return self::nothing('no TYPO3 installation was found to boot');
        }

        $result = Typo3Cli::php(self::payload($root));
        if (!$result['ok']) {
            $error = trim($result['error']) !== '' ? trim($result['error']) : trim($result['output']);

            return self::nothing($error === '' ? 'the installation could not be booted' : $error);
        }

        $decoded = json_decode(trim($result['output']), true);
        if (!is_array($decoded) || !isset($decoded['state'])) {
            return self::nothing('the installation booted and answered with something other than JSON');
        }

        return [
            'state' => (string) $decoded['state'],
            'reason' => (string) ($decoded['reason'] ?? ''),
            'topics' => is_array($decoded['topics'] ?? null) ? $decoded['topics'] : [],
        ];
    }

    /**
     * The probe with the autoloader of this installation written into it.
     *
     * The opening tag goes because the body is delivered through `php -r`,
     * which supplies its own.
     */
    private static function payload(string $root): string
    {
        $probe = (string) file_get_contents(__DIR__ . '/probe.php');
        $probe = (string) preg_replace('/^<\?php\s/', '', $probe, 1);

        return str_replace(
            "'vendor/autoload.php'",
            var_export(Typo3Cli::autoloader($root), true),
            $probe
        );
    }

    /** @return array{state: string, reason: string, topics: array<string, mixed>} */
    private static function nothing(string $reason): array
    {
        return ['state' => self::STATE_UNREACHABLE, 'reason' => $reason, 'topics' => []];
    }
}
