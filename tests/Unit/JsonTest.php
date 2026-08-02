<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tests\Support\Editorconfig;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Json;

/**
 * The form the knowledge base's JSON is written in, and what formatting it may
 * not change.
 *
 * A formatter is trusted the way a whitespace commit is trusted — nobody reads
 * the diff — so what is held here is not the indentation but everything the
 * indentation is not allowed to take with it: an empty object, the order the
 * keys were written in, a number as it was written.
 */
final class JsonTest extends TestCase
{
    #[Test]
    public function theFormIsWhatEditorconfigSaysWithSlashesAndUnicodeLeftAlone(): void
    {
        self::assertSame(
            <<<'JSON'
                {
                  "url": "https://docs.typo3.org/",
                  "written": "Größe",
                  "nested": {
                    "deeper": true
                  }
                }

                JSON,
            Json::format('{"url":"https:\/\/docs.typo3.org\/","written":"Größe","nested":{"deeper":true}}'),
        );
    }

    /**
     * The indentation is stated in `.editorconfig` and nowhere else. Every
     * editor that opens one of these files obeys that file already, so a
     * formatter disagreeing with it would undo the line somebody typed by hand
     * — and each would keep undoing the other.
     */
    #[Test]
    public function theIndentIsTheOneEditorconfigStates(): void
    {
        self::assertSame(
            Editorconfig::indentFor('versions.json'),
            Json::INDENT,
            'the formatter and .editorconfig disagree about the indentation',
        );
    }

    /**
     * The one that would have gone unnoticed: decoded associatively, `{}` and
     * `[]` are the same PHP value, and every empty object in the corpus comes
     * back as an empty array.
     */
    #[Test]
    public function anEmptyObjectDoesNotBecomeAnEmptyArray(): void
    {
        self::assertSame("{\n  \"object\": {},\n  \"list\": []\n}\n", Json::format('{"object":{},"list":[]}'));
    }

    /**
     * In server-scope.json and in the hints the order of the keys is the order
     * an answer is read out in, so it is data and not layout.
     */
    #[Test]
    public function theOrderTheKeysWereWrittenInIsKept(): void
    {
        self::assertSame("{\n  \"b\": 1,\n  \"a\": 2\n}\n", Json::format('{"b":1,"a":2}'));
    }

    #[Test]
    public function aNumberSurvivesAsItWasWritten(): void
    {
        self::assertSame("{\n  \"since\": 13,\n  \"weight\": 1.0\n}\n", Json::format('{"since":13,"weight":1.0}'));
    }

    #[Test]
    public function whatIsNotJsonIsRefusedRatherThanRewritten(): void
    {
        $this->expectException(\JsonException::class);

        Json::format('{"trailing": "comma",}');
    }

    /**
     * The corpus is what a person edits. `scenarios/runs/` is JSON as well and
     * `scenario:record` writes it, so a formatter over it would be a second
     * author of the same file.
     */
    #[Test]
    public function theCorpusIsEveryJsonBelowKnowledgeAndNothingElse(): void
    {
        $found = [];
        foreach (Finder::create()->files()->in(Paths::knowledge())->name('*.json') as $file) {
            $found[] = substr($file->getPathname(), strlen(Paths::root()) + 1);
        }

        self::assertNotSame([], $found, 'knowledge/ holds no JSON, so this test says nothing');
        self::assertEqualsCanonicalizing($found, Json::files());
        self::assertSame([], array_filter(Json::files(), static fn(string $file): bool => !str_starts_with($file, 'knowledge/')));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function knowledgeFiles(): array
    {
        return array_combine(Json::files(), array_map(static fn(string $file): array => [$file], Json::files()));
    }

    /**
     * What the corpus means, before and after. This is the test that has to
     * pass before the files are brought to the form in a commit nobody will
     * read line by line.
     */
    #[Test]
    #[DataProvider('knowledgeFiles')]
    public function formattingAKnowledgeFileChangesNothingButItsLayout(string $file): void
    {
        $contents = (string) file_get_contents(Paths::root() . '/' . $file);

        self::assertEquals(
            json_decode($contents, false, 512, JSON_THROW_ON_ERROR),
            json_decode(Json::format($contents), false, 512, JSON_THROW_ON_ERROR),
            $file . ' says something else once formatted',
        );
    }

    /**
     * The corpus, held to the form. A file edited by hand and left at whatever
     * indentation the editor used is a diff that shows every line as changed,
     * and the statement somebody meant to edit is somewhere in it — which is
     * the failure this is here to prevent, and it is invisible in review.
     */
    #[Test]
    #[DataProvider('knowledgeFiles')]
    public function everyFileIsWrittenInTheForm(string $file): void
    {
        $contents = (string) file_get_contents(Paths::root() . '/' . $file);

        self::assertSame($contents, Json::format($contents), $file . ' is not in the form; bin/cli knowledge:format writes it');
    }

    /**
     * A formatter that is not idempotent rewrites a file on every run, and every
     * commit then carries a diff nobody made.
     */
    #[Test]
    #[DataProvider('knowledgeFiles')]
    public function aFormattedFileIsLeftAloneTheSecondTime(string $file): void
    {
        $formatted = Json::format((string) file_get_contents(Paths::root() . '/' . $file));

        self::assertSame($formatted, Json::format($formatted), $file . ' formats differently the second time');
    }

    /**
     * A path is matched against the corpus rather than resolved into a file to
     * write, so the command formats what it holds and nothing else.
     */
    #[Test]
    public function aPathOutsideTheKnowledgeBaseIsFormattedByNobody(): void
    {
        $buffer = new BufferedOutput();

        $exit = Cli::application()->doRun(new StringInput('knowledge:format composer.json'), $buffer);

        self::assertSame(1, $exit);
        self::assertStringContainsString('composer.json', $buffer->fetch());
    }
}
