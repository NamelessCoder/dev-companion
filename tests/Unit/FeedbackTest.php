<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Feedback;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tools;

/**
 * Feedback is the one part of the server that writes, so these tests write too.
 * Every feedback recorded here is removed again in tearDown; the marker in the
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
        $written = [
            ...(glob(Paths::feedback() . '/*.md') ?: []),
            ...(glob(Paths::feedbackArchive() . '/*.md') ?: []),
        ];
        foreach ($written as $file) {
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
        // directory, which is what makes the feedback checkable later.
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
        // Half the feedback are about what a session did rather than about what an
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
        // The write never fails on the attribution — the feedback is worth more
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

    /**
     * A session files one feedback per subject and files them in one breath, so
     * they open on the same sentence — the one that says which session this
     * is — and that sentence is longer than a filename has room for. Eight
     * feedback of 2026-08-01 were named
     * `debrief-of-the-typo3-14-testimonials-session` to the character, differing
     * by their timestamp alone, and they were about eight different things.
     *
     * The first of a series keeps the opening, because nothing yet says it is
     * one. Every feedback after it is named after what it alone says.
     */
    #[Test]
    public function notesThatOpenAlikeAreNamedAfterWhatTellsThemApart(): void
    {
        $opening = self::MARKER . ' debrief of the session, missed item: ';

        $first = Feedback::record(['observation' => $opening . 'nothing said which pid the records go to']);
        $second = Feedback::record(['observation' => $opening . 'the fixture harness was written by hand']);
        $third = Feedback::record(['observation' => $opening . 'clearing the cache meant deleting files']);

        self::assertStringContainsString('debrief-of-the-session', $first);
        self::assertStringContainsString('the-fixture-harness-was-written', $second);
        self::assertStringContainsString('clearing-the-cache-meant-deleting', $third);
        self::assertStringNotContainsString('debrief-of-the-session', $second, 'the name says what both feedback say');
        self::assertStringNotContainsString('debrief-of-the-session', $third, 'the name says what both feedback say');
    }

    #[Test]
    public function recordedNotesAreListedNewestFirst(): void
    {
        $file = Feedback::record(['observation' => self::MARKER . ' a listed feedback']);

        $found = Feedback::all('open', null, 100);
        $files = array_column($found, 'file');

        self::assertContains($file, $files);
        foreach ($found as $feedback) {
            self::assertSame('open', $feedback['status']);
            self::assertNotSame('', $feedback['title']);
        }
    }

    #[Test]
    public function aNoteThatWasWorkedOffKeepsEverythingItSaid(): void
    {
        // Closing a feedback used to mean deleting it, which left the agent that
        // wrote it seeing the file simply stop existing — and left the closed
        // half of the list as bare filenames, with the front matter that says
        // what the feedback was about gone with the file.
        $file = Feedback::record([
            'observation' => self::MARKER . ' the archive keeps what the feedback said',
            'category' => 'tool-gap',
            'tool' => 'typo3_component_lookup',
        ]);

        $archived = Feedback::archive($file);
        self::assertSame('feedback/archive/' . basename($file), $archived);
        self::assertFileDoesNotExist(Paths::root() . '/' . $file);
        self::assertStringContainsString(
            'status: closed',
            (string) file_get_contents(Paths::root() . '/' . $archived),
        );

        self::assertNotContains($file, array_column(Feedback::all('open', null, 200), 'file'));
        $feedback = self::noteFor($archived);
        self::assertSame('closed', $feedback['status']);
        self::assertSame('tool-gap', $feedback['category']);
        self::assertSame(['typo3_component_lookup'], $feedback['tools']);

        // And the filters reach it, which is the half the deleted feedback lost.
        self::assertContains(
            $archived,
            array_column(Feedback::all('closed', 'tool-gap', 200, 'typo3_component_lookup'), 'file'),
        );
    }

    #[Test]
    public function aNoteThatWasWorkedOffIsStillAnswerableFor(): void
    {
        // What came of a feedback is the commit that archived it, which is the one
        // thing the agent that reported the gap cannot see for itself: without
        // it the same gap is reported again, and a request that shipped in the
        // meantime is dropped silently.
        $closed = array_filter(
            Feedback::all('closed', null, 20),
            static fn(array $feedback): bool => !str_contains($feedback['title'], self::MARKER),
        );
        if ($closed === []) {
            self::markTestSkipped('No feedback has been worked off in this checkout yet.');
        }

        foreach ($closed as $feedback) {
            self::assertSame('closed', $feedback['status']);
            self::assertStringStartsWith('feedback/archive/', $feedback['file']);
            self::assertNotNull($feedback['closedBy']);
            self::assertNotSame('', $feedback['closedBy']['commit']);
            // The subject is the sentence that says what happened to it.
            self::assertNotSame('', $feedback['closedBy']['subject']);
        }

        // An open feedback is in the same list and says it is open.
        $file = Feedback::record(['observation' => self::MARKER . ' open beside the closed ones']);
        $all = Feedback::all('all', null, 200);
        self::assertContains($file, array_column($all, 'file'));
        self::assertContains('closed', array_column($all, 'status'));
    }

    #[Test]
    public function onlyAnOpenNoteCanBeArchived(): void
    {
        $file = Feedback::record(['observation' => self::MARKER . ' archived once']);
        Feedback::archive($file);

        // The same feedback twice is the mistake this catches: the second call
        // would otherwise overwrite the answer the first one recorded.
        $this->expectException(\InvalidArgumentException::class);
        Feedback::archive($file);
    }

    #[Test]
    public function theListCanBeRestrictedToACategory(): void
    {
        Feedback::record(['observation' => self::MARKER . ' a bug feedback', 'category' => 'bug']);

        foreach (Feedback::all('all', 'bug', 100) as $feedback) {
            self::assertSame('bug', $feedback['category']);
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

        $feedback = self::noteFor($file);
        self::assertSame(['typo3_label_lookup', 'typo3_icon_lookup'], $feedback['tools']);
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

        // The obvious thing to want from a backlog: every feedback about one tool,
        // including the ones that name it alongside others.
        $files = array_column(Feedback::all('all', null, 100, 'typo3_icon_lookup'), 'file');
        self::assertContains($file, $files);

        foreach (Feedback::all('all', null, 100, 'typo3_icon_lookup') as $feedback) {
            self::assertContains('typo3_icon_lookup', $feedback['tools']);
        }
        self::assertNotContains($file, array_column(Feedback::all('all', null, 100, 'typo3_rule_lookup'), 'file'));
    }

    /**
     * @return array{file: string, date: string, category: string, status: string, model: string, tool: string, tools: array<int, string>, title: string}
     */
    private static function noteFor(string $file): array
    {
        foreach (Feedback::all('all', null, 200) as $feedback) {
            if ($feedback['file'] === $file) {
                return $feedback;
            }
        }

        self::fail($file . ' was written but is not listed');
    }
}
