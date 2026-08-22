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
    /** @var array{0: array<string, true>, 1: array<string, true>}|null */
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
     * The scan, once per process. Both callers run over the whole corpus, so
     * reading the tree twice is the difference between one pass and two.
     *
     * @return array{0: array<string, true>, 1: array<string, true>}
     */
    private static function read(): array
    {
        if (self::$declared !== null) {
            return self::$declared;
        }

        $classes = [];
        $members = [];
        foreach (['src', 'tests'] as $tree) {
            foreach (Finder::create()->files()->in(Paths::root() . '/' . $tree)->name('*.php')->sortByName() as $file) {
                $code = (string) file_get_contents($file->getPathname());
                if (preg_match('/^(?:final |abstract |readonly )*(?:class|enum|interface|trait) (\w+)/m', $code, $named) !== 1) {
                    continue;
                }
                $classes[$named[1]] = true;
                foreach (['/function (\w+)\s*\(/', '/const (\w+)/', '/(?:public|private|protected)[^;{]*\$(\w+)/', '/case (\w+)/'] as $pattern) {
                    preg_match_all($pattern, $code, $found);
                    foreach ($found[1] as $member) {
                        $members[$named[1] . '::' . $member] = true;
                    }
                }
            }
        }

        return self::$declared = [$classes, $members];
    }
}
