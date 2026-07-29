<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * The TYPO3 versions this knowledge base covers, and what a statement bound to
 * one of them is worth to a given caller.
 *
 * A convention is not timeless. The one that is current on the development line
 * may not exist on the LTS a site runs, and handing it over anyway produces
 * code that fails at runtime, silently — a translation domain that resolves to
 * nothing, a content column nothing can address. So a statement carries the
 * majors it holds for, and the answer either drops it or renders the range
 * beside it.
 *
 * Which versions are covered is declared in knowledge/versions.json and read
 * from there by everything that needs the list.
 */
final class Versions
{
    /**
     * @return array<int, array{major: int, branch: string, status: string}>
     */
    public static function covered(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('versions.json')), true);
        if (!is_array($decoded) || !isset($decoded['covers']) || !is_array($decoded['covers'])) {
            throw new \RuntimeException('Invalid versions.json');
        }

        $covered = array_map(static fn(array $entry): array => [
            'major' => (int) $entry['major'],
            'branch' => (string) $entry['branch'],
            'status' => (string) ($entry['status'] ?? ''),
        ], $decoded['covers']);
        usort($covered, static fn(array $a, array $b): int => $a['major'] <=> $b['major']);

        return $covered;
    }

    /** @return array<int, int> */
    public static function majors(): array
    {
        return array_column(self::covered(), 'major');
    }

    /**
     * The version an answer is composed for: what the caller stated, else what
     * the installation being read runs, else nothing.
     *
     * Nothing is a legitimate state and not an error — a knowledge base with no
     * installation around it still answers, and then every statement comes back
     * with the range it holds for instead of being filtered by one.
     */
    public static function target(?string $stated = null): ?int
    {
        $major = self::major($stated);

        return $major ?? Instance::typo3Major();
    }

    /** The major in a version string, or null when there is none to read. */
    public static function major(?string $version): ?int
    {
        $version = trim((string) $version);
        if (preg_match('/^v?(\d+)/', $version, $matches) !== 1) {
            return null;
        }

        return (int) $matches[1];
    }

    /**
     * Whether a statement bound to [since, until] holds on the target version.
     *
     * Without a target nothing is filtered out: the caller is told the range
     * instead, which is the honest answer when nobody said which version this
     * is for.
     */
    public static function holds(?int $since, ?int $until, ?int $target): bool
    {
        if ($target === null) {
            return true;
        }

        return ($since === null || $target >= $since) && ($until === null || $target <= $until);
    }

    /**
     * How a bound statement says what it is bound to, or an empty string when
     * it is bound to nothing.
     *
     * Rendered beside the statement rather than woven into it, so the sentence
     * stays the same sentence on every version it holds for.
     */
    public static function label(?int $since, ?int $until): string
    {
        if ($since === null && $until === null) {
            return '';
        }
        if ($until === null) {
            return sprintf('TYPO3 v%d and newer', $since);
        }
        if ($since === null) {
            return sprintf('up to TYPO3 v%d', $until);
        }
        if ($since === $until) {
            return sprintf('TYPO3 v%d only', $since);
        }

        return sprintf('TYPO3 v%d to v%d', $since, $until);
    }

    /**
     * The branch a major is covered by, for pointing at what to verify against.
     */
    public static function branch(int $major): ?string
    {
        foreach (self::covered() as $entry) {
            if ($entry['major'] === $major) {
                return $entry['branch'];
            }
        }

        return null;
    }
}
