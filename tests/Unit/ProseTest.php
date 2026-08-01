<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Upkeep\Prose;

/**
 * That the prose rule is measured, and that the one place it is held holds.
 *
 * The measure itself reports rather than fails — a long sentence in a body can
 * be the right sentence. The opening of a requirement or a decision is the
 * exception, because a reader who stops after it is supposed to know what was
 * settled, and 47 of them had run past the point where anybody could.
 */
final class ProseTest extends TestCase
{
    #[Test]
    public function everyRequirementAndDecisionOpensWithASentenceAReaderCanStopAfter(): void
    {
        $over = Prose::leadsOverTheMeasure();

        self::assertSame([], $over, implode("\n", array_map(
            static fn(array $lead): string => sprintf('%s opens with %d words: %s', $lead['id'], $lead['words'], $lead['text']),
            $over,
        )));
    }

    /**
     * `feedback/` is what this deliberately leaves out. A feedback is written
     * by a session somewhere else, and measuring it against this repository's
     * rule would report on the wrong author.
     */
    #[Test]
    public function theCorpusIsTheProseThisRepositoryWritesAboutItself(): void
    {
        $documents = Prose::documents();

        self::assertContains('AGENTS.md', $documents);
        self::assertContains('documentation/readme.md', $documents);
        self::assertNotEmpty(array_filter($documents, static fn(string $file): bool => str_starts_with($file, 'requirements/')));
        self::assertSame([], array_filter($documents, static fn(string $file): bool => str_starts_with($file, 'feedback/')));
    }

    /**
     * What is measured is what a reader reads. A table row and a code block are
     * neither sentences nor prose, and counting them would make the number say
     * that the files with the most examples are the worst written.
     */
    #[Test]
    public function nothingButProseIsCountedAsASentence(): void
    {
        $counted = [];
        foreach (Prose::documents() as $document) {
            foreach (Prose::measure($document)['over'] as $sentence) {
                $counted[] = $sentence['text'];
            }
        }

        self::assertNotEmpty($counted, 'the measure found nothing at all, which means it read nothing');
        foreach ($counted as $sentence) {
            self::assertStringStartsNotWith('|', $sentence);
            self::assertStringStartsNotWith('#', $sentence);
            self::assertStringStartsNotWith('>', $sentence);
            self::assertStringNotContainsString('```', $sentence);
        }
    }
}
