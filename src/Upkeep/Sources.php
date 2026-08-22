<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * The PHP this repository declares, as the records name it.
 *
 * Two readings want it and neither wants the other's copy: `RecordsTest` holds
 * a backticked `Class::member` to a class that has it, and
 * `Decisions::uncovered()` asks which entries point at this code without naming
 * a test that would catch it moving. Both need the same list, and a second copy
 * of it is the duplication those two readings exist to find.
 *
 * Read from the files rather than through reflection, because the records name
 * a private member as readily as a public one, and because loading every class
 * to ask about it is a cost a check pays on every run.
 */
final class Sources
{
    /** @var array{0: array<string, true>, 1: array<string, true>, 2: array<string, string>}|null */
    private static ?array $declared = null;

    /**
     * Every class name below `src/` and `tests/`.
     *
     * @return array<string, true>
     */
    public static function classes(): array
    {
        return self::read()[0];
    }

    /**
     * Every `Class::member` those files declare — a method, a constant, a
     * property or an enum case, which is the whole of what a record can point
     * at with two colons.
     *
     * @return array<string, true>
     */
    public static function members(): array
    {
        return self::read()[1];
    }

    /**
     * What a test method says: its docblock and its body, or the whole file
     * where the entry names a class rather than a member.
     *
     * Found by walking back from the declaration rather than by splitting the
     * file on an indented `/**`, which is what a run of `composer cgl` moves.
     * A reading that changes with the formatter is a reading nobody can quote.
     *
     * The body is in it because that is where this corpus writes half its
     * reasons: 38 of the 346 names a docblock-only reading called silent on
     * 2026-08-22 carry the id in a comment beside the assertion it explains,
     * and a docblock repeating it is the second copy `AGENTS.md` sends to the
     * id instead. The member ends at the first `}` in the column a method
     * closes in, which nothing nested reaches.
     */
    public static function saidAt(string $test): string
    {
        [$class, $method] = array_pad(explode('::', $test), 2, '');
        $file = self::files()[$class] ?? null;
        if ($file === null) {
            return '';
        }

        $code = (string) file_get_contents($file);
        if ($method === '') {
            return $code;
        }

        $at = strpos($code, 'function ' . $method . '(');
        if ($at === false) {
            return '';
        }

        $opens = self::whereTheRunAboveStarts(substr($code, 0, $at));
        $ends = strpos($code, "\n    }\n", $at);

        return substr($code, $opens, ($ends === false ? strlen($code) : $ends) - $opens);
    }

    /**
     * The file each class is declared in.
     *
     * @return array<string, string>
     */
    public static function files(): array
    {
        return self::read()[2];
    }

    /**
     * Where the run of comments and attributes above a declaration begins.
     *
     * The last `/**` is not it: a method carrying both a prose docblock and a
     * `/** @param ... *\/` line has its reason in the first of the two, and a
     * reading that starts at the second says the entry is unnamed while the
     * name is two lines above.
     */
    private static function whereTheRunAboveStarts(string $head): int
    {
        $lines = explode("\n", $head);
        $opens = strlen($head) - strlen($lines[count($lines) - 1]);
        for ($index = count($lines) - 2; $index >= 0; $index--) {
            $line = trim($lines[$index]);
            if ($line !== '' && !str_starts_with($line, '#[') && !str_starts_with($line, '*') && !str_starts_with($line, '/*') && !str_starts_with($line, '//')) {
                break;
            }
            $opens -= strlen($lines[$index]) + 1;
        }

        return $opens;
    }

    /**
     * The scan, once per process. Both callers run over the whole corpus, so
     * reading the tree twice is the difference between one pass and two.
     *
     * @return array{0: array<string, true>, 1: array<string, true>, 2: array<string, string>}
     */
    private static function read(): array
    {
        if (self::$declared !== null) {
            return self::$declared;
        }

        $classes = [];
        $members = [];
        $files = [];
        foreach (['src', 'tests'] as $tree) {
            foreach (Finder::create()->files()->in(Paths::root() . '/' . $tree)->name('*.php')->sortByName() as $file) {
                $code = (string) file_get_contents($file->getPathname());
                if (preg_match('/^(?:final |abstract |readonly )*(?:class|enum|interface|trait) (\w+)/m', $code, $named) !== 1) {
                    continue;
                }
                $classes[$named[1]] = true;
                $files[$named[1]] = $file->getPathname();
                foreach (['/function (\w+)\s*\(/', '/const (\w+)/', '/(?:public|private|protected)[^;{]*\$(\w+)/', '/case (\w+)/'] as $pattern) {
                    preg_match_all($pattern, $code, $found);
                    foreach ($found[1] as $member) {
                        $members[$named[1] . '::' . $member] = true;
                    }
                }
            }
        }

        return self::$declared = [$classes, $members, $files];
    }
}
