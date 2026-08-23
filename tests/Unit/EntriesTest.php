<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Entries;

/**
 * What is written down about a piece of this code, read before it is changed.
 *
 * The attributes answer from the failing end and this is the other one, so what
 * it has to be is honest about its own reach: a name it does not find is a name
 * nobody wrote in backticks, never a statement that nothing was decided.
 */
final class EntriesTest extends TestCase
{
    /** The corpora are one list here, and an id is in exactly one of them. */
    #[Decision('D-DOC-050')]
    #[Test]
    public function bothCorporaAreOneListKeyedById(): void
    {
        $entries = Entries::all();

        self::assertNotSame([], $entries);
        foreach ($entries as $id => $entry) {
            self::assertSame($id, $entry['id']);
            self::assertNotSame('', $entry['title'], $id . ' is listed without its title');
            self::assertStringStartsWith(
                str_starts_with($id, 'D-') ? 'decisions/' : 'requirements/',
                $entry['file'],
                $id . ' is filed in the other corpus',
            );
        }
    }

    /** A path answers with what it declares, a file or a directory. */
    #[Decision('D-DOC-050')]
    #[Test]
    public function aPathAnswersWithTheClassesItDeclares(): void
    {
        self::assertSame(['Entries'], Entries::declaredBelow('src/Upkeep/Entries.php'));
        self::assertContains('Entries', Entries::declaredBelow('src/Upkeep'));
        self::assertSame([], Entries::declaredBelow('src/Upkeep/NoSuchFile.php'));
    }

    /**
     * Every entry answered for a class names it, and a class nothing names is
     * answered with nothing rather than with a guess.
     */
    #[Decision('D-DOC-050')]
    #[Test]
    public function anEntryIsAnsweredForTheClassItNames(): void
    {
        $entries = Entries::all();
        $naming = Entries::naming(['Wrap', 'Entries']);

        self::assertNotSame([], $naming, 'nothing is written about the prose the repository rewraps');
        foreach ($naming as $id => $named) {
            self::assertArrayHasKey($id, $entries);
            $body = (string) file_get_contents(Paths::root() . '/' . $entries[$id]['file']);
            foreach ($named as $class) {
                self::assertStringContainsString('`' . $class, $body, $id . ' is answered for ' . $class);
            }
        }

        self::assertSame([], Entries::naming(['NoSuchClassOfOurs']));
        self::assertSame([], Entries::naming([]));
    }

    /** A test class is listed where it names the class and holds an entry. */
    #[Decision('D-DOC-050')]
    #[Test]
    public function aTestIsListedWhereItNamesTheClassAndHoldsAnEntry(): void
    {
        $tests = Entries::testsNaming(['Wrap']);

        self::assertArrayHasKey('ProseTest', $tests);
        self::assertContains('D-DOC-035', $tests['ProseTest']);
        self::assertArrayNotHasKey('ForgeTest', $tests, 'a test that does not name it is listed');
        self::assertSame([], Entries::testsNaming([]));
    }
}
