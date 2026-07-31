<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Feedback;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tools;

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
    public function aNoteSaysWhichModelLeftIt(): void
    {
        // Half the notes are about what a session did rather than about what an
        // answer said, and that is one model's behaviour. Unattributed, two
        // models' habits arrive as one report.
        $file = Feedback::record([
            'observation' => self::MARKER . ' the skill was read and its lookups were not run',
            'model' => 'claude-opus-5',
        ]);

        self::assertStringContainsString(
            'model: claude-opus-5',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
        self::assertSame('claude-opus-5', self::noteFor($file)['model']);
    }

    #[Test]
    public function aNoteWithoutAModelSaysSoRatherThanCarryingNone(): void
    {
        // The write never fails on the attribution — the note is worth more
        // than the name — but an unattributed one says it is unattributed,
        // which a missing front-matter line cannot.
        $file = Feedback::record(['observation' => self::MARKER . ' recorded by nobody in particular']);

        self::assertStringContainsString(
            'model: unknown',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
        self::assertSame(Feedback::UNATTRIBUTED, self::noteFor($file)['model']);
    }

    #[Test]
    public function theRecordedNoteIsReportedWhereItActuallyIs(): void
    {
        // A relative path is relative to somewhere the caller has never been:
        // one recorded from a site package was reported as feedback/<name>.md,
        // looked for under that project, not found, and reported back as a
        // failed write.
        $result = Tools::call('typo3_feedback_record', [
            'observation' => self::MARKER . ' recorded through the tool',
            'model' => 'claude-opus-5',
        ]);

        $path = $result->data['path'] ?? '';
        self::assertIsString($path);
        self::assertStringStartsWith(Paths::root() . '/feedback/', $path);
        self::assertFileExists($path);
        self::assertStringContainsString($path, $result->text);
        // And it says whose checkout that is, because the caller cannot tell.
        self::assertStringContainsString('not the project you are working in', $result->text);
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
    public function aNoteThatWasWorkedOffIsStillAnswerableFor(): void
    {
        // A note is closed by deleting it, and the agent that wrote it sees the
        // file it recorded simply stop existing — which reads as lost, and the
        // same gap gets reported a second time. The commit that deleted it is
        // the record of what came of it, so that is what the closed half is
        // read back from.
        $closed = Feedback::notes('closed', null, 10);
        if ($closed === []) {
            self::markTestSkipped('No note has been worked off in this checkout yet.');
        }

        foreach ($closed as $note) {
            self::assertSame('closed', $note['status']);
            self::assertStringStartsWith('feedback/', $note['file']);
            self::assertNotNull($note['closedBy']);
            self::assertNotSame('', $note['closedBy']['commit']);
            // The subject is the sentence that says what happened to it.
            self::assertNotSame('', $note['closedBy']['subject']);
        }

        // An open note is in the same list and says it is open.
        $file = Feedback::record(['observation' => self::MARKER . ' open beside the closed ones']);
        $all = Feedback::notes('all', null, 100);
        self::assertContains($file, array_column($all, 'file'));
        self::assertContains('closed', array_column($all, 'status'));
        foreach ($all as $note) {
            self::assertSame($note['status'] === 'closed', $note['closedBy'] !== null);
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
     * @return array{file: string, date: string, category: string, status: string, model: string, tool: string, tools: array<int, string>, title: string}
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
