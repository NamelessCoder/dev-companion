<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Knowledge;

use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Project;
use Typo3CmsMcp\Paths;

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

    /**
     * The majors an answer has to hold on at once.
     *
     * One installation runs one version, and for a site project that is the
     * whole question. An extension is the other case: it declares
     * `"typo3/cms-core": "^13.4 || ^14.3"` and one codebase serves both, so a
     * statement bound to either major is one the author needs — and the
     * difference between them is not noise, it is the constraint the code lives
     * under. Filtering such a repository to the major that happens to be
     * installed answers as if the other one did not exist, and what comes back
     * then reads as drift: the file kept for the older major, the interface not
     * yet replaced, the suppressed deprecation.
     *
     * What the caller states still wins and stays a single major — somebody who
     * says "14" is asking about 14. Only where nothing was stated does the
     * declaration decide, and where there is no declaration this is the
     * installed version, exactly as before.
     *
     * @return array<int, int>
     */
    public static function targets(?string $stated = null): array
    {
        $major = self::major($stated);
        if ($major !== null) {
            return [$major];
        }

        $declared = self::declared(Project::coreConstraint());
        if (count($declared) > 1) {
            return $declared;
        }

        $installed = Instance::typo3Major();

        return $installed === null ? [] : [$installed];
    }

    /**
     * The covered majors a Composer constraint admits.
     *
     * A constraint this cannot read yields nothing, and the caller falls back to
     * the installed version — a wrong range would be worse than the single
     * version this has always used.
     *
     * @return array<int, int>
     */
    public static function declared(?string $constraint): array
    {
        return array_values(array_filter(
            self::majors(),
            static fn(int $major): bool => self::admits($constraint, $major),
        ));
    }

    /**
     * Whether a Composer constraint admits any release of a major.
     *
     * Read by asking the constraint about one major rather than by parsing it
     * into a range: the question is only ever "does this serve v13", and the
     * answer is the same for every spelling that admits any 13.x. Which majors
     * are worth asking about is the caller's — the covered ones for
     * `typo3/cms-core`, the ones a Fluid engine could be spelled for in
     * `bin/cli catalog:check`.
     */
    public static function admits(?string $constraint, int $major): bool
    {
        $constraint = trim((string) $constraint);
        if ($constraint === '') {
            return false;
        }

        foreach (preg_split('/\s*\|\|?\s*/', $constraint) ?: [] as $alternative) {
            if (self::alternativeAdmits(trim($alternative), $major)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether one alternative of a constraint admits any release of a major.
     *
     * Every comparator in it has to, because within one alternative they are
     * combined with and — `>=13.4 <15` is one alternative, not two.
     */
    private static function alternativeAdmits(string $alternative, int $major): bool
    {
        if ($alternative === '') {
            return false;
        }
        if ($alternative === '*') {
            return true;
        }

        $comparators = preg_split('/[\s,]+/', $alternative) ?: [];
        foreach ($comparators as $comparator) {
            if (!self::comparatorAdmits(trim($comparator), $major)) {
                return false;
            }
        }

        return $comparators !== [];
    }

    /** Whether one comparator admits any release of a major. */
    private static function comparatorAdmits(string $comparator, int $major): bool
    {
        if ($comparator === '') {
            return true;
        }
        if (preg_match('/^(\^|~|>=|<=|>|<|=|v)?\s*v?(\d+)(?:\.(\d+|\*|x))?/i', $comparator, $matches) !== 1) {
            return false;
        }

        $operator = strtolower($matches[1]);
        $stated = (int) $matches[2];
        $minor = $matches[3] ?? null;

        return match ($operator) {
            // Both pin the major: a caret above 1.0, and a tilde because the
            // digit it lets grow is never the first one. TYPO3 has no 0.x.
            '^', '~' => $major === $stated,
            '>=' => $major >= $stated,
            '>' => $major >= $stated,
            // An exclusive upper bound on x.0 excludes that major, and on any
            // later minor it still admits it — <14.3 is served by 14.0.
            '<' => $minor === null || $minor === '0' ? $major < $stated : $major <= $stated,
            '<=' => $major <= $stated,
            default => $major === $stated,
        };
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
