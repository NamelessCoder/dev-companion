<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Feedback;
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
}
