<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Knowledge\ArchitectureHints;
use Typo3CmsMcp\Knowledge\Documents;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Knowledge\TaskIntents;

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

    /**
     * `binding` says who an answer obliges, `provenance` says what a covered
     * topic is worth outside the core, and D-KNW-3 keeps them two fields on one
     * condition: no value reads naturally on both. What holds them apart is
     * `installation`, which is not an obligation at all — it says the answer is
     * read from the installation rather than from a snapshot. A fourth value
     * that does read on both would mean they were one axis after all, and the
     * merge is the entry that was right.
     *
     * VersionsTest holds `binding` to its one value and ScopeTest holds
     * `provenance` to its three, each for its own reason. Neither can see the
     * pair, which is the thing the decision actually turns on, so it is
     * asserted once here: a session widening either vocabulary fails on this
     * test and is handed the question — does the new value read on both axes?
     *
     * The comparison is on the spelling. `core` and `core-only` are the overlap
     * D-KNW-3 already looked at and kept, so a normalised form would have
     * failed on the day it was written; what is exact is that no value is
     * spelled into both vocabularies, and the pinned sets above are what catch
     * a fourth arriving under any spelling.
     */
    #[Test]
    public function whoAnAnswerObligesAndWhatItIsWorthStayTwoVocabularies(): void
    {
        $binding = [];
        foreach (ArchitectureHints::load() as $hint) {
            foreach (array_merge([$hint], $hint['hints']) as $entry) {
                $binding[] = $entry['binding'] ?? null;
            }
        }
        foreach (TaskIntents::load() as $intent) {
            $binding[] = $intent['binding'];
        }
        $binding = array_values(array_unique(array_filter($binding, static fn(?string $value): bool => $value !== null)));
        $provenance = array_values(array_unique(array_column(Scope::read()['covers'], 'provenance')));
        sort($binding);
        sort($provenance);

        self::assertSame([ArchitectureHints::BINDING_CORE], $binding, 'D-KNW-3: `binding` has grown a value');
        self::assertSame(
            ['core-only', 'installation', 'transferable'],
            $provenance,
            'D-KNW-3: `provenance` has grown a value',
        );
        self::assertSame(
            [],
            array_values(array_intersect($binding, $provenance)),
            'D-KNW-3: a value is spelled on both axes, so they are one axis and the merge is the entry',
        );
    }
}
