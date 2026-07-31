<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Requirements;

final class RequirementsTest extends TestCase
{
    /**
     * An id is the name a commit, a note and a scenario refer to a requirement
     * by. It decides the group directory and the file name, so two entries
     * cannot quietly share one — which five of them did, unnoticed, for as long
     * as the whole list was a single document.
     */
    #[Test]
    public function everyRequirementIsFoundUnderTheIdItGoesBy(): void
    {
        $files = Requirements::files();
        $requirements = Requirements::all();

        self::assertNotSame([], $requirements);
        self::assertCount(
            count($files),
            $requirements,
            'two requirement files claim the same id',
        );

        foreach ($requirements as $id => $requirement) {
            self::assertSame($id, $requirement['heading'], $id . ' has another id in its heading');
            self::assertStringStartsWith(
                strtolower(substr($id, 2)) . '-',
                $requirement['file'],
                $id . ' is not the name of its file',
            );
            self::assertSame(
                Requirements::GROUPS[substr($id, 2, 3)] ?? null,
                $requirement['group'],
                $id . ' sits in a group its prefix does not name',
            );
        }
    }

    /**
     * The bold first sentence is the requirement, and a reader who stops there
     * has read the whole demand. Everything else in the file explains it.
     */
    #[Test]
    public function everyRequirementOpensWithTheSentenceThatHasToHold(): void
    {
        foreach (Requirements::all() as $id => $requirement) {
            self::assertNotSame('', $requirement['title'], $id . ' has no title');
            self::assertNotSame('', $requirement['statement'], $id . ' states nothing');
            self::assertContains($requirement['status'], ['held', 'open'], $id . ' has no usable status');
        }
    }

    /**
     * What holds a requirement is the only thing that separates it from a wish.
     * A test named here has to exist: a requirement claiming a test that was
     * renamed away is a claim nobody answers for, and it reads exactly like one
     * that is held.
     */
    #[Test]
    public function everyRequirementNamesWhatHoldsIt(): void
    {
        $methods = $this->testMethods();
        $classes = array_unique(array_map(static fn (string $m): string => explode('::', $m)[0], $methods));

        foreach (Requirements::all() as $id => $requirement) {
            if ($requirement['status'] === 'open') {
                continue;
            }

            self::assertNotSame('', $requirement['heldBy'], $id . ' does not say what holds it');
            self::assertTrue(
                $requirement['tests'] !== [] || str_contains($requirement['heldBy'], 'not guarded'),
                $id . ' names neither a test nor that it is not guarded',
            );
            foreach ($requirement['tests'] as $test) {
                self::assertContains(
                    $test,
                    str_contains($test, '::') ? $methods : $classes,
                    $id . ' names ' . $test . ', which no test declares',
                );
            }
        }
    }

    /**
     * The listing under a group readme is generated from the files below it, so
     * a requirement added without `bin/cli requirements index` is missing from the
     * one place a reader looks first.
     */
    #[Test]
    public function everyGroupListsWhatIsInIt(): void
    {
        foreach (Requirements::GROUPS as $group) {
            $readme = Requirements::directory() . '/' . $group . '/readme.md';

            self::assertFileExists($readme);
            self::assertStringEndsWith(
                Requirements::listing($group),
                (string) file_get_contents($readme),
                $group . '/readme.md is not the listing of its files — run bin/cli requirements index',
            );
        }
    }

    /**
     * A scenario holds itself to the requirements it names. One that names a
     * requirement nobody can read any more is a claim about nothing.
     */
    #[Test]
    public function everyRequirementAScenarioNamesExists(): void
    {
        $requirements = Requirements::all();
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(Paths::root() . '/scenarios', \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($files as $file) {
            if (!$file instanceof \SplFileInfo || $file->getExtension() !== 'md') {
                continue;
            }

            preg_match_all('/`(R-[A-Z]{3}-\d+[a-z]?)`/', (string) file_get_contents($file->getPathname()), $matches);
            foreach ($matches[1] as $id) {
                self::assertArrayHasKey(
                    $id,
                    $requirements,
                    $file->getFilename() . ' names ' . $id . ', which no requirement has',
                );
            }
        }
    }

    /**
     * Every test method in this suite as `Class::method`, read from the files
     * rather than from reflection — the same list ScenariosTest holds its cases
     * to.
     *
     * @return array<int, string>
     */
    private function testMethods(): array
    {
        $methods = [];
        foreach (['Unit', 'Contract', 'Smoke'] as $suite) {
            foreach (glob(Paths::root() . '/tests/' . $suite . '/*Test.php') ?: [] as $path) {
                preg_match_all('/public function (\w+)\(/', (string) file_get_contents($path), $matches);
                foreach ($matches[1] as $method) {
                    $methods[] = basename($path, '.php') . '::' . $method;
                }
            }
        }

        return $methods;
    }
}
