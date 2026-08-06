<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * A core checkout this repository writes, holding nothing but what it is.
 *
 * Eight tools read nothing an installation contains. What reaches their answer
 * is `knowledge/` and two facts about the root the session stands in: that it
 * is the core monorepo rather than a project, and which TYPO3 major that is.
 * Both are declarations, so this writes them and nothing else — three files, no
 * packages, no console, no changelog.
 *
 * That emptiness is the point rather than a shortcut. A root with content would
 * put that content into an answer, and the pages derived from it would show
 * this fixture where a reader expects TYPO3 — the failure `D-DOC-012`'s first
 * **Wrong if** names, on pages that claim to be checked. What may be derived is
 * exactly what an empty root cannot colour, and `ToolAnswersTest` holds the set
 * to that by rendering it from two roots that share this identity and agree on
 * nothing else.
 *
 * `Fixture` is the other one and answers the opposite question: what a booted
 * TYPO3 says. Neither replaces the core checkout below `.checkouts/`, which is
 * what the recorded pages still answer from, because only a real one carries
 * what TYPO3 actually ships.
 */
final class CoreFixture
{
    /** Where it is written: beside the other fixture, ignored by git, rewritten whole. */
    public static function root(): string
    {
        return Fixture::directory() . '/checkout';
    }

    /**
     * The version it states it is: the newest covered line that is released, at
     * its first patch.
     *
     * The patch level is written rather than guessed at because nothing derived
     * from here may carry it. `typo3_translation_domain_lookup` prints the
     * installation's exact version into its answer and is out of the derived
     * set for that reason, measured on 2026-08-04.
     */
    public static function typo3Version(): string
    {
        return Environments::branch() . '.0';
    }

    /** Writes it whole and hands back the root it was written to. */
    public static function write(): string
    {
        $root = self::root();
        $core = $root . '/typo3/sysext/core';

        self::put($root . '/composer.json', self::json([
            'name' => 'typo3/cms',
            'description' => 'Written by TYPO3\\DevCompanion\\Upkeep\\CoreFixture so an answer that reads no installation '
                . 'can be derived. It is not a TYPO3 checkout and holds none of one.',
            'type' => 'typo3-cms-core',
        ]));
        self::put($core . '/composer.json', self::json([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ]));
        self::put($core . '/Classes/Information/Typo3Version.php', sprintf(
            "<?php\n\nnamespace TYPO3\\CMS\\Core\\Information;\n\nclass Typo3Version\n{\n"
            . "    protected const VERSION = '%s';\n}\n",
            self::typo3Version(),
        ));

        return $root;
    }

    /** @param array<string, mixed> $value */
    private static function json(array $value): string
    {
        return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function put(string $path, string $contents): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0o777, true);
        }
        file_put_contents($path, $contents);
    }
}
