<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Knowledge\ArchitectureHints;
use Typo3CmsMcp\Knowledge\Documents;

final class KnowledgeTest extends TestCase
{
    #[Test]
    public function everyBundledDocumentIsListedWithATitle(): void
    {
        $documents = Documents::documents();
        $ids = array_column($documents, 'id');

        self::assertContains('typo3-core-rules', $ids);
        self::assertContains('typo3-core-scripts', $ids);
        self::assertContains('typo3-gerrit-workflow', $ids);

        foreach ($documents as $document) {
            self::assertNotSame('', $document['title'], $document['id'] . ' has no title');
            self::assertFileExists($document['path']);
        }
    }

    #[Test]
    public function readReturnsTheDocumentAndRejectsUnknownIds(): void
    {
        self::assertStringContainsString('# TYPO3 Core Contribution Rules', Documents::read('typo3-core-rules'));

        $this->expectException(\RuntimeException::class);
        Documents::read('does-not-exist');
    }

    #[Test]
    public function aMatchedSectionCarriesItsSourceAndCoverage(): void
    {
        $results = Documents::search('deprecation');

        self::assertNotSame([], $results);
        foreach ($results as $result) {
            self::assertGreaterThanOrEqual(0.5, $result['coverage'], 'sections below the coverage threshold are noise');
            self::assertNotSame('', $result['body']);
        }
    }

    #[Test]
    public function noProseDocumentDatesAStatementInItsSentence(): void
    {
        // The same rule VersionsTest holds the hints to, for the corpus it
        // cannot see. A statement in markdown carries no since/until, so a
        // version written into the sentence cannot be filtered and typo3_rule_
        // lookup — which has no targetVersion and searches every document —
        // hands it to a caller on any branch. That is how "Since TYPO3 v14.1 a
        // label marked that way raises an E_USER_DEPRECATED" was answering a
        // 13.4 question. A version inside an example command is a different
        // thing and stays: "git push origin HEAD:refs/for/13.4" is the command.
        foreach (Documents::documents() as $document) {
            self::assertDoesNotMatchRegularExpression(
                '/\bTYPO3 v\d|\bsince v?\d|\bfrom v\d/i',
                (string) file_get_contents($document['path']),
                $document['id'] . ' dates a statement in its prose, where nothing can bind it',
            );
        }
    }

    #[Test]
    public function theDiscriminatingTermsOfAQueryDecideTheAnswer(): void
    {
        // "site set settings definitions" was answered with the backend's Sass
        // class naming at a confident three quarters of the query terms:
        // "content", "structure" and "element" are everywhere, and every term
        // counted the same. The subject now lives in the hint corpus rather
        // than in prose, and the weighting is the same weighting — so the case
        // is asked of the corpus that holds the answer.
        $result = ArchitectureHints::find([], 'site set settings definitions', 6);

        self::assertSame('site-sets', $result['matchedHints'][0]['id']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(
                ArchitectureHints::CATEGORY_CSS,
                $hint['category'],
                $hint['id'] . ' answers a TypoScript question with backend CSS',
            );
        }
    }

    #[Test]
    public function aTermMatchesAWordRatherThanAnythingThatContainsIt(): void
    {
        // "set" used to match "offset" and "reset", "site" to match
        // "composite". Stems still match every form of their word.
        $carriers = static fn(string $query): array => array_column(Documents::search($query), 'heading');

        self::assertContains('Release Branches and Backports', $carriers('release branches'));
        self::assertSame([], Documents::search('ffset'));
    }

    #[Test]
    public function anAnswerAboutAuthoringPointsAtTheReadingSideOfTheSameThing(): void
    {
        // "deprecation" was answered with how to write one — correct for a core
        // contributor, inverted for the reader who wants to know what a version
        // deprecated, and nothing said which of the two it was.
        $bodies = implode("\n", array_column(Documents::search('deprecation'), 'body'));

        self::assertStringContainsString('Extension Scanner', $bodies);
    }

    #[Test]
    public function anUnrelatedQueryAnswersWithNothingRatherThanTheNearestProse(): void
    {
        self::assertSame([], Documents::search('quantum entanglement pineapple'));
    }

    #[Test]
    public function wordFormsOfTheSameWordFindTheSameSection(): void
    {
        $headings = static fn(string $query): array => array_column(Documents::search($query), 'heading');

        self::assertSame($headings('deprecation'), $headings('deprecations'));
        self::assertSame($headings('deprecation'), $headings('deprecate'));
    }

    #[Test]
    public function theSearchCanBeRestrictedToDocuments(): void
    {
        $results = Documents::search('functional tests', ['typo3-core-scripts']);

        self::assertNotSame([], $results);
        foreach ($results as $result) {
            self::assertSame('typo3-core-scripts', $result['id']);
        }
    }

    #[Test]
    public function codeFencesSurviveTheSectionSplit(): void
    {
        $results = Documents::search('unit tests', ['typo3-core-scripts']);

        $bodies = implode("\n", array_column($results, 'body'));
        self::assertStringContainsString('```', $bodies, 'commands are only usable with their code fence intact');
        self::assertSame(0, substr_count($bodies, '```') % 2, 'a section must not end inside a code fence');
    }

    #[Test]
    public function everyDocumentReportsTheTopicsItCovers(): void
    {
        foreach (Documents::topics() as $document) {
            self::assertNotSame([], $document['topics'], $document['id'] . ' reports no topics');
        }
    }
}
