<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Feedback;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Paths;

/**
 * Feedback is the one part of the server that writes, so these tests write too.
 * Every note recorded here is removed again in tearDown; the marker in the
 * observation makes a leftover recognizable.
 */
final class FeedbackTest extends TestCase
{
    private const MARKER = 'phpunit-feedback-fixture';

    protected function setUp(): void
    {
        if (!Feedback::isAvailable()) {
            self::markTestSkipped('Feedback is only available in a standalone checkout.');
        }
    }

    protected function tearDown(): void
    {
        Instance::discoverFrom(null);
        foreach (glob(Paths::feedback() . '/*.md') ?: [] as $file) {
            if (str_contains((string) file_get_contents($file), self::MARKER)) {
                unlink($file);
            }
        }
    }

    #[Test]
    public function aNoteBecomesOneMarkdownFileWithFrontMatter(): void
    {
        $file = Feedback::record([
            'observation' => self::MARKER . ' the lookup found nothing',
            'category' => 'missing-knowledge',
            'tool' => 'typo3_component_lookup',
            'query' => 'query=badge',
            'suggestion' => 'add the component',
        ]);

        self::assertStringStartsWith('feedback/', $file);
        $contents = (string) file_get_contents(Paths::root() . '/' . $file);
        self::assertStringContainsString('category: missing-knowledge', $contents);
        self::assertStringContainsString('status: open', $contents);
        self::assertStringContainsString('tool: typo3_component_lookup', $contents);
        self::assertStringContainsString('## Observation', $contents);
        self::assertStringContainsString('## Query', $contents);
        self::assertStringContainsString('## Suggestion', $contents);
    }

    #[Test]
    public function theAgentNeverControlsWhereTheNoteIsWritten(): void
    {
        $file = Feedback::record([
            'observation' => '../../' . self::MARKER . " escape attempt\nsecond line",
        ]);

        self::assertSame('feedback/' . basename($file), $file);
        self::assertStringNotContainsString('..', $file);
        self::assertFileExists(Paths::feedback() . '/' . basename($file));
    }

    #[Test]
    public function aNoteSaysWhichDirectoryItWasWrittenFrom(): void
    {
        // What the stdio entrypoint does at startup: the session's own working
        // directory, which is what makes the note checkable later.
        Instance::discoverFrom('/home/somebody/projects/a-site');

        $file = Feedback::record(['observation' => self::MARKER . ' asked in a project']);

        self::assertStringContainsString(
            'directory: /home/somebody/projects/a-site',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
    }

    #[Test]
    public function aNoteWithoutACallerDirectoryClaimsNone(): void
    {
        // The HTTP case: no entrypoint handed a directory in, so the server's
        // own working directory is not passed off as the caller's.
        Instance::discoverFrom(null);

        $file = Feedback::record(['observation' => self::MARKER . ' asked over http']);

        self::assertStringNotContainsString(
            'directory:',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
    }

    #[Test]
    public function anUnknownCategoryFallsBackToIdea(): void
    {
        $file = Feedback::record([
            'observation' => self::MARKER . ' something',
            'category' => 'nonsense',
        ]);

        self::assertStringContainsString(
            'category: idea',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
    }

    #[Test]
    public function anEmptyObservationIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Feedback::record(['observation' => '   ']);
    }

    #[Test]
    public function recordedNotesAreListedNewestFirst(): void
    {
        $file = Feedback::record(['observation' => self::MARKER . ' a listed note']);

        $notes = Feedback::notes('open', null, 100);
        $files = array_column($notes, 'file');

        self::assertContains($file, $files);
        foreach ($notes as $note) {
            self::assertSame('open', $note['status']);
            self::assertNotSame('', $note['title']);
        }
    }

    #[Test]
    public function theListCanBeRestrictedToACategory(): void
    {
        Feedback::record(['observation' => self::MARKER . ' a bug note', 'category' => 'bug']);

        foreach (Feedback::notes('all', 'bug', 100) as $note) {
            self::assertSame('bug', $note['category']);
        }
    }

    #[Test]
    public function severalToolsStaySeveralToolsRatherThanOneWord(): void
    {
        // An observation about the four tools that go quiet together is a
        // normal one. Stripping the separator ran their names into
        // "typo3_label_lookuptypo3_icon_lookup", which no filter can match.
        $file = Feedback::record([
            'observation' => self::MARKER . ' both lookups went quiet',
            'tool' => 'typo3_label_lookup, typo3_icon_lookup',
        ]);

        self::assertStringContainsString(
            'tool: typo3_label_lookup, typo3_icon_lookup',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );

        $note = self::noteFor($file);
        self::assertSame(['typo3_label_lookup', 'typo3_icon_lookup'], $note['tools']);
    }

    #[Test]
    public function aListOfToolsIsAcceptedAsOne(): void
    {
        $file = Feedback::record([
            'observation' => self::MARKER . ' recorded with a list',
            'tool' => ['typo3_label_lookup', 'typo3_icon_lookup'],
        ]);

        self::assertSame(['typo3_label_lookup', 'typo3_icon_lookup'], self::noteFor($file)['tools']);
    }

    #[Test]
    public function theListCanBeRestrictedToOneTool(): void
    {
        $file = Feedback::record([
            'observation' => self::MARKER . ' about two tools',
            'tool' => 'typo3_label_lookup typo3_icon_lookup',
        ]);

        // The obvious thing to want from a backlog: every note about one tool,
        // including the ones that name it alongside others.
        $files = array_column(Feedback::notes('all', null, 100, 'typo3_icon_lookup'), 'file');
        self::assertContains($file, $files);

        foreach (Feedback::notes('all', null, 100, 'typo3_icon_lookup') as $note) {
            self::assertContains('typo3_icon_lookup', $note['tools']);
        }
        self::assertNotContains($file, array_column(Feedback::notes('all', null, 100, 'typo3_rule_lookup'), 'file'));
    }

    /**
     * @return array{file: string, date: string, category: string, status: string, tool: string, tools: array<int, string>, title: string}
     */
    private static function noteFor(string $file): array
    {
        foreach (Feedback::notes('all', null, 200) as $note) {
            if ($note['file'] === $file) {
                return $note;
            }
        }

        self::fail($file . ' was written but is not listed');
    }
}
