<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * What the records claim about the code they name.
 *
 * A backticked name this repository owns is a claim that the thing exists now,
 * and four guards already say that of four kinds: a tool in `ToolNamingTest`,
 * a test method and a requirement id in `DecisionsTest`, a decision id and a
 * test in `bin/cli requirements:check`. This is the fifth, a member of one of
 * this repository's own classes, and it went unwatched — 24 of the 1673 such
 * references were false on 2026-08-22, every one of them in `decisions/`.
 *
 * The escape is `D-DOC-040`'s and needs no list: a name being talked about
 * rather than pointed at is written plainly, which is what an entry recording
 * that a member is gone does.
 */
final class RecordsTest extends TestCase
{
    /** Where a name is a claim about the code as it is now. */
    private const CORPORA = ['decisions', 'requirements', 'documentation', 'todo', 'skills', 'knowledge'];

    /**
     * Members every class has without declaring one. A magic method is the
     * language's, and the three enum readers are generated for a backed case
     * list — `Scope::from()` is named by `D-KNW-005` and declared nowhere.
     */
    private const LANGUAGE_MEMBERS = ['from', 'tryFrom', 'cases', 'class'];

    /**
     * A member the records name in backticks exists on the class they name.
     *
     * The two the corpus proved necessary are both about a name that looks like
     * ours and is not: a magic method, and a class this repository shares a name
     * with — `Site::__construct()` in `D-KNW-097` is TYPO3's `Site` and not the
     * one below `src/Upkeep/`. Nothing tells those apart from the name, so a
     * class we share is a hole this states rather than closes.
     */
    #[Test]
    public function everyMemberTheRecordsNameInBackticksExists(): void
    {
        [$classes, $members] = $this->declared();

        $missing = [];
        foreach ($this->recordFiles() as $path) {
            preg_match_all('/`(\w+)::(\w+)\(?\)?`/', (string) file_get_contents($path), $matches, PREG_SET_ORDER);
            foreach ($matches as $named) {
                [, $class, $member] = $named;
                if (!isset($classes[$class]) || isset($members[$class . '::' . $member])) {
                    continue;
                }
                if (str_starts_with($member, '__') || in_array($member, self::LANGUAGE_MEMBERS, true)) {
                    continue;
                }
                $missing[] = basename($path) . ': ' . $class . '::' . $member;
            }
        }

        self::assertSame([], array_values(array_unique($missing)), 'named in backticks by a record, and the class has no such member');
    }

    /**
     * Every class this repository declares, and every member it declares on
     * one, read from the files rather than from reflection — the corpus names
     * private members as readily as public ones.
     *
     * @return array{0: array<string, true>, 1: array<string, true>}
     */
    private function declared(): array
    {
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

        return [$classes, $members];
    }

    /** @return array<int, string> */
    private function recordFiles(): array
    {
        $paths = [];
        foreach (self::CORPORA as $corpus) {
            foreach (Finder::create()->files()->in(Paths::root() . '/' . $corpus)->name(['*.md', '*.rst', '*.json'])->sortByName() as $file) {
                $paths[] = $file->getPathname();
            }
        }

        return $paths;
    }
}
