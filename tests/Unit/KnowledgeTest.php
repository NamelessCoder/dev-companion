<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Knowledge\Coverage;
use Typo3CmsMcp\Knowledge\Documents;
use Typo3CmsMcp\Knowledge\Hints;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Knowledge\TaskIntents;
use Typo3CmsMcp\Knowledge\TestSuiteHints;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Result\Prose;
use Typo3CmsMcp\Tool\Registry;

final class KnowledgeTest extends TestCase
{
    /**
     * The suites whose script asks git for the files to inspect.
     *
     * Both take the last commit's files from `git diff-tree` and treat an empty
     * answer as nothing to do. `checkGitSubmodule` asks git too and fails
     * loudly instead, which a session can see; `checkExtensionScannerRst` was
     * reported as a suspect and reads the files itself.
     */
    private const GIT_DRIVEN_SUITES = ['cglGit', 'cglHeaderGit'];

    /**
     * A corpus of this test's own, because `D-VER-005` cannot be held against
     * the real one: no bundled document declares a binding, and one written to
     * carry a `**Since:**` for a test would be a statement in the knowledge
     * base whose purpose is the test.
     */
    private ?string $corpus = null;

    protected function tearDown(): void
    {
        Paths::useDocuments(null);
        $directories = [];
        if ($this->corpus !== null) {
            foreach (Finder::create()->files()->in($this->corpus) as $file) {
                unlink($file->getPathname());
            }
            foreach (iterator_to_array(Finder::create()->directories()->in($this->corpus), false) as $directory) {
                // Deepest first, because a directory only goes once it is empty.
                $directories[] = $directory->getPathname();
            }
            rsort($directories);
            foreach ($directories as $directory) {
                rmdir($directory);
            }
            rmdir($this->corpus);
            $this->corpus = null;
        }

        parent::tearDown();
    }

    /**
     * A second document with a vocabulary of its own comes with every corpus.
     * The matcher weighs a term by how far it separates one section from the
     * next, so in a corpus where every section says the same words nothing
     * discriminates, every weight is zero and the coverage threshold drops the
     * lot — which is a property of the fixture rather than of the binding.
     *
     * @param array<string, string> $documents Filename without .md, to content.
     */
    private function useCorpus(array $documents): void
    {
        $this->corpus = sys_get_temp_dir() . '/typo3-mcp-documents-' . bin2hex(random_bytes(6));
        mkdir($this->corpus);
        $documents['core/contribution/contrast'] = <<<'MD'
            # Contrast

            ## Pushing a patch

            the review server, the change id the hook writes, and amending

            ## The subject line

            which keyword opens it and how long a body line may be
            MD;
        foreach ($documents as $id => $content) {
            $file = $this->corpus . '/' . $id . '.md';
            if (!is_dir(dirname($file))) {
                mkdir(dirname($file), 0777, true);
            }
            file_put_contents($file, $content);
        }
        Paths::useDocuments($this->corpus);
    }

    #[Test]
    public function aBoundSectionIsKeptOnTheMajorItHoldsFor(): void
    {
        $this->useCorpus(['extension/testing/bound' => <<<'MD'
            # Bound

            ## Build/UnitTests.xml

            **Since:** 14

            the fourteen variant of the phpunit configuration

            ## Build/UnitTests.xml

            **Until:** 13

            the thirteen variant of the phpunit configuration
            MD]);

        $bodies = static fn(?int $target): array => array_column(
            Documents::search('phpunit configuration variant', [], 6, $target),
            'body',
        );

        self::assertCount(1, $bodies(14));
        self::assertStringContainsString('fourteen', $bodies(14)[0]);
        self::assertCount(1, $bodies(13));
        self::assertStringContainsString('thirteen', $bodies(13)[0]);

        // A package serving both majors needs both, and the range beside each
        // is what says which is which. Handing over two variants of one file
        // with nothing separating them is `D-VER-005`'s first **Wrong if**, so
        // the rendered answer is what the range has to reach.
        self::assertCount(2, $bodies(null));
        $both = Documents::search('phpunit configuration variant', [], 6, [13, 14]);
        self::assertCount(2, $both);

        $rendered = Prose::sections($both);
        self::assertStringContainsString('[TYPO3 v14 and newer]', $rendered);
        self::assertStringContainsString('[up to TYPO3 v13]', $rendered);
        self::assertSame(
            ['TYPO3 v14 and newer', 'up to TYPO3 v13'],
            array_column(Prose::records($both), 'versions'),
        );
    }

    #[Test]
    public function theBindingDoesNotReachTheCallerAsPartOfWhatItBinds(): void
    {
        $this->useCorpus(['extension/testing/bound' => <<<'MD'
            # Bound

            ## Build/UnitTests.xml

            **Since:** 14
            **Until:** 14

            ```xml
            <phpunit/>
            ```
            MD]);

        $match = Documents::search('build unittests xml', [], 6, 14)[0];

        self::assertStringStartsWith('```xml', $match['body']);
        self::assertStringNotContainsString('**Since:**', $match['body']);
        self::assertStringNotContainsString('**Until:**', $match['body']);
        self::assertSame(14, $match['since']);
        self::assertSame(14, $match['until']);
    }

    #[Test]
    public function aDeclarationBelowTheFirstLineOfContentBindsNothing(): void
    {
        // The declaration has one place, so a reader never has to search a
        // section for the range it holds on — and a sentence that happens to
        // start that way stays prose.
        $this->useCorpus(['extension/testing/loose' => <<<'MD'
            # Loose

            ## A section

            a first line of content

            **Since:** 14
            MD]);

        $match = Documents::search('first line of content', [], 6, 13)[0];

        self::assertNull($match['since']);
        self::assertStringContainsString('**Since:** 14', $match['body']);
    }

    #[Test]
    public function everyBundledDocumentIsListedWithATitle(): void
    {
        $documents = Documents::documents();
        $ids = array_column($documents, 'id');

        self::assertContains('core/contribution/rules', $ids);
        self::assertContains('core/testing/scripts', $ids);
        self::assertContains('core/contribution/gerrit-workflow', $ids);

        foreach ($documents as $document) {
            self::assertNotSame('', $document['title'], $document['id'] . ' has no title');
            self::assertFileExists($document['path']);
        }
    }

    #[Test]
    public function readReturnsTheDocumentAndRejectsUnknownIds(): void
    {
        self::assertStringContainsString('# TYPO3 Core Contribution Rules', Documents::read('core/contribution/rules'));

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
        // The same rule VersionsTest holds the hints to. A section can declare
        // since/until since `D-VER-005`, and that is the whole of how it says
        // what it holds for: a version written into the sentence is still
        // invisible to the filter, so it reaches a caller on any branch. That
        // is how "Since TYPO3 v14.1 a label marked that way raises an
        // E_USER_DEPRECATED" was answering a 13.4 question — a sentence the
        // binding would have carried. A version inside an example command is a
        // different thing and stays: "git push origin HEAD:refs/for/13.4" is
        // the command.
        foreach (Documents::documents() as $document) {
            self::assertDoesNotMatchRegularExpression(
                '/\bTYPO3 v\d|\bsince v?\d|\bfrom v\d/i',
                (string) file_get_contents($document['path']),
                $document['id'] . ' dates a statement in its prose, where nothing can bind it',
            );
        }
    }

    #[Test]
    public function noProseDocumentNamesACheckOnlySomeBranchesHave(): void
    {
        // The other half of the rule above, for the statement that dates itself
        // without a digit in it. `-s checkIntegrityXliff` reads as timeless and
        // arrives in 14; a 12.4 contributor asking typo3_script_lookup about
        // language files was handed it, plus `-s normalizeXliff` and `-s build`,
        // none of which that branch has. A section binding could filter that
        // now and still must not: the range of a suite already lives on the
        // suite in test-suite-hints.json, and declaring it here as well is one
        // fact in two places that can disagree. So a prose document may only
        // name a suite every covered major carries, and anything narrower stays
        // where typo3_test_run_guide filters it by targetVersion.
        $everywhere = array_intersect(...array_map(TestSuiteHints::availableOn(...), Versions::majors()));

        foreach (Documents::documents() as $document) {
            preg_match_all('/-s\s+([A-Za-z0-9_-]+)/', (string) file_get_contents($document['path']), $matches);
            foreach (array_unique($matches[1]) as $suite) {
                self::assertContains(
                    $suite,
                    $everywhere,
                    $document['id'] . ' hands over -s ' . $suite . '; prose may only name a suite that '
                        . 'test-suite-hints.json declares on every covered major',
                );
            }
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
        $result = Hints::find([], 'site set settings definitions', 6);

        self::assertSame('site-sets', $result['matchedHints'][0]['id']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(
                Hints::CATEGORY_CSS,
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
    public function aChangelogQuestionIsToldWhichTypeTheChangeOwes(): void
    {
        // R-KNW-051. The list of four says nothing about which one is being
        // written, and the type is the one part checkRst does not report: a
        // session that guessed it passes every suite. The corpus answered with
        // five bullets that named a Task- prefix no branch's validator accepts,
        // and the session behind feedback/2026-08-02-145315 picked the type by
        // reading neighbouring entries.
        $bodies = implode("\n", array_column(Documents::search('changelog file'), 'body'));

        // Four aspects of one search result rather than four cases: a
        // provider would run the same search four times and say nothing more.
        foreach (['Breaking', 'Deprecation', 'Feature', 'Important'] as $type) {
            self::assertStringContainsString($type, $bodies, 'no changelog type ' . $type);
        }
        self::assertStringContainsString('last resort', $bodies, 'nothing separates Important from the other three');

        $intent = array_values(array_filter(
            TaskIntents::load(),
            static fn(array $entry): bool => $entry['id'] === 'changelog',
        ))[0] ?? [];
        self::assertStringNotContainsString(
            'Task-',
            implode("\n", $intent['checklist'] ?? []),
            'the changelog intent hands over a prefix checkRst rejects',
        );
    }

    #[Test]
    public function theBreakingRouteStatesWhatTheScannerMatcherRequires(): void
    {
        // R-ANS-017. The matcher was stated under Deprecations alone, so a
        // reviewer asking about a removal was handed the [!!!] marker and the
        // changelog file and nothing else — D-ANS-029. The query is read off
        // the intent rather than written here, because that is the one a
        // removal actually arrives on.
        $breaking = array_values(array_filter(
            TaskIntents::load(),
            static fn(array $intent): bool => $intent['id'] === 'breaking',
        ));

        $bodies = implode("\n", array_column(Documents::search($breaking[0]['rulesQuery']), 'body'));

        self::assertStringContainsString('Configuration/ExtensionScanner/Php/', $bodies);
        self::assertStringContainsString('FullyScanned', $bodies);
    }

    #[Test]
    public function theMatcherListSaysWhatItsMissingRowsDoNotMean(): void
    {
        // R-ANS-017. The five rows name a visibility twice, on the property
        // half, and a reviewer read the list as closed over visibilities: no
        // row for a protected method, therefore no matcher can exist for one,
        // therefore the entry is NotScanned. It reported that to a core
        // reviewer as a finding and filed the list's silence as the thing that
        // corrected it (`feedback/2026-08-03-144316`, D-ANS-035). The method
        // matchers are a weak match on the call site and never see a
        // visibility, so what the list omits is what needs no row.
        $bodies = implode("\n", array_column(Documents::search('breaking change'), 'body'));

        self::assertStringContainsString('Visibility routes a property and never a method', $bodies);
        self::assertStringContainsString('getRendererInstances', $bodies);
    }

    /**
     * R-KNW-057. The query is the skill's own step arriving: `typo3-core-patch-
     * development` makes the visible-or-unlisted question mandatory and tells
     * the caller the Gerrit lookup "has both forms", and the corpus carried the
     * one that publishes alone — six sections on this query and `%private` in
     * none of them (`D-SKL-005`, 2026-08-03).
     */
    #[Test]
    public function theUnlistedPushIsAnsweredBesideTheOneThatPublishes(): void
    {
        $bodies = implode("\n", array_column(Documents::search('gerrit push private change'), 'body'));

        self::assertStringContainsString('%private', $bodies);
        self::assertStringContainsString('%wip', $bodies);
        // The two are chosen between rather than looked up, so the answer says
        // what each one does to the change and not only how it is typed.
        self::assertStringContainsString('View Private Changes', $bodies, 'nothing says who can see a private change');
        // The flag that does not come off by omitting it is the one a caller
        // cannot guess the way back out of.
        self::assertStringContainsString('%remove-private', $bodies, 'the flag that sticks is offered with no way back');
    }

    /**
     * R-KNW-057. The three readings around the same push: where this checkout
     * sends it, whether the refspec holds from a worktree, and what the state of
     * the issue behind it means. One test rather than three, because they are
     * three aspects of the step the skill calls irreversible.
     */
    #[Test]
    public function theWriteDirectionIsAnsweredAroundThePushItself(): void
    {
        $bodies = static fn(string $query): string => implode(
            "\n",
            array_column(Documents::search($query), 'body'),
        );

        // Read rather than set: the corpus had `git remote set-url --push`,
        // which is what a human runs once per clone, and no way to ask a
        // checkout where it is already pointed.
        $where = $bodies('where does this checkout push');
        self::assertStringContainsString('remote.origin.pushurl', $where);
        self::assertStringContainsString('.gitreview', $where);

        $worktree = $bodies('push from a git worktree');
        self::assertStringContainsString('refs/for/main', $worktree);
        self::assertStringContainsString('commit-msg', $worktree, 'nothing says the hook reaches a worktree');

        // The hook checks that a Resolves: line is there, not what state the
        // issue named in it is in, so nothing refuses the push.
        self::assertStringContainsString('Resolves:', $bodies('forge issue closed change'));
    }

    #[Test]
    public function aQueryThatNamesItsDocumentReachesTheSectionThatAnswersIt(): void
    {
        // D-ANS-037. "commit message summary line length" returned two Gerrit
        // workflow sections at coverage 0.525 and score 38, and the section
        // carrying the 52-character rule sat at 0.429 — the two words naming the
        // document were in no field the matcher read, so the section they
        // belong to paid for them and the sections merely saying the subject's
        // name did not.
        $results = Documents::search('commit message summary line length');

        self::assertSame(
            ['core/contribution/commit-messages', 'Summary Line'],
            [$results[0]['id'] ?? null, $results[0]['heading'] ?? null],
        );
    }

    #[Test]
    public function everyDocumentIsReachedByItsOwnTitle(): void
    {
        // The weakest thing that can be asked of this corpus, and only two of
        // the five documents did it: "TYPO3 Core Script Help" returned nothing,
        // and three titles were answered first by another document.
        foreach (Documents::documents() as $document) {
            $results = Documents::search($document['title']);

            self::assertSame(
                $document['id'],
                $results[0]['id'] ?? null,
                $document['title'] . ' does not reach ' . $document['id'],
            );
        }
    }

    #[Test]
    public function anUnrelatedQueryAnswersWithNothingRatherThanTheNearestProse(): void
    {
        self::assertSame([], Documents::search('quantum entanglement pineapple'));

        // The floor is what stops a query the corpus cannot answer from being
        // answered by whatever is nearest, and it stayed where it is when the
        // title was weighted in — D-ANS-037. This is the query that measured it
        // for the hint corpus in D-ANS-025: long enough that something always
        // carries part of it.
        self::assertSame([], Documents::search('how do I write a good sonnet'));
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
        $results = Documents::search('functional tests', ['core/testing/scripts']);

        self::assertNotSame([], $results);
        foreach ($results as $result) {
            self::assertSame('core/testing/scripts', $result['id']);
        }
    }

    #[Test]
    public function codeFencesSurviveTheSectionSplit(): void
    {
        $results = Documents::search('unit tests', ['core/testing/scripts']);

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
     * The query `feedback/2026-08-01-115115` was working on, asked from the core
     * checkout it was written in: nothing matched, nothing was withheld, and the
     * answer blamed the boundary for both — which `D-ANS-029` then quoted back
     * as what the tool answers a core query (`D-ANS-037`).
     */
    #[Test]
    public function aMissInsideTheCoreNamesTheWordsRatherThanTheBoundary(): void
    {
        $result = Registry::call('typo3_rule_lookup', [
            'query' => 'review of core patch replacing GD error thumbnails with SVG placeholder',
        ]);

        self::assertSame(0, $result->data['matchCount']);
        self::assertSame(Scope::Core->value, $result->data['scope']);
        self::assertSame([], $result->data['withheldDocuments'], 'nothing was withheld, so nothing was left out for the boundary');
        self::assertStringNotContainsString('holds outside the core', $result->text);
        self::assertStringContainsString('No knowledge section matched', $result->text);
        self::assertStringContainsString('This knowledge base covers:', $result->text);
        self::assertStringContainsString('ask again with the one that narrows best', $result->text);
        // The hints that matched are what this path already had, and they are
        // the reason it was not the miss answer in the first place.
        self::assertNotSame([], $result->data['alsoInHints']);
        self::assertStringContainsString('typo3_hint_lookup', $result->text);
    }

    /**
     * The other miss path: outside the core, with a document dropped for the
     * boundary. That is the one case the sentence is true in, and it stays.
     */
    #[Test]
    public function aMissThatWithheldADocumentSaysTheBoundaryEmptiedIt(): void
    {
        $result = Registry::call('typo3_rule_lookup', [
            'query' => 'how do I push a patch for review from my site package',
        ]);

        self::assertSame(0, $result->data['matchCount']);
        self::assertSame(['core/contribution/gerrit-workflow'], array_column($result->data['withheldDocuments'], 'id'));
        self::assertStringContainsString('No section that holds outside the core matched', $result->text);
    }

    #[Test]
    public function whatAMissOffersToAskAgainWithReturnsSections(): void
    {
        $result = Registry::call('typo3_rule_lookup', [
            'query' => 'review of core patch replacing GD error thumbnails with SVG placeholder',
        ]);

        preg_match_all('/"([^"]+)" reaches \d+ sections?/', $result->text, $offered);
        self::assertNotSame([], $offered[1], 'the miss names no part of the query that would have reached anything');

        foreach ($offered[1] as $subset) {
            self::assertGreaterThan(
                0,
                Registry::call('typo3_rule_lookup', ['query' => $subset])->data['matchCount'],
                $subset . ' is offered as the next call and returns nothing',
            );
        }
    }

    #[Test]
    public function aSubsetIsNamedInTheWordsTheQueryWasWrittenIn(): void
    {
        $query = 'review of core patch replacing GD error thumbnails with SVG placeholder';
        $written = preg_split('/\s+/', mb_strtolower($query)) ?: [];

        $subsets = Documents::largestReachingSubsets($query);

        self::assertNotSame([], $subsets);
        foreach ($subsets as $subset) {
            foreach ($subset['terms'] as $term) {
                self::assertContains($term, $written, 'a miss hands back the stem rather than the word that was typed');
            }
        }
    }

    /**
     * `binding`, `provenance` and `audience` were three fields asking one
     * question, and D-KNW-003 kept the first two apart on one condition: no value
     * reads naturally on both. `installation` is what held them apart, and it
     * was never an obligation — it says where an answer is read from, which is
     * what `source` already says.
     *
     * They are one vocabulary now, the `Scope` enum, and this is what holds it
     * to one: every scope written anywhere in the corpus has to be a case of
     * it, and a statement may not claim `uncertain`, which belongs to a path
     * nothing placed rather than to a sentence somebody wrote.
     */
    #[Test]
    public function everyScopeInTheCorpusIsOneTheEnumDeclares(): void
    {
        $written = [];
        foreach (Hints::load() as $hint) {
            foreach (array_merge([$hint], $hint['hints']) as $entry) {
                $written[] = $entry['scope'] ?? null;
            }
        }
        foreach (TaskIntents::load() as $intent) {
            $written[] = $intent['scope'];
        }
        foreach (Coverage::read()['covers'] as $entry) {
            $written[] = $entry['scope'];
        }

        foreach (array_filter($written) as $scope) {
            self::assertContains(
                $scope,
                Scope::ofKnowledge(),
                $scope->value . ' is written in the corpus and is not a scope a statement may declare',
            );
        }
    }

    /**
     * A suite that takes its file list from git carries the condition it holds
     * under, wherever it is recommended.
     *
     * `cglGit` reports SUCCESS having read no file when it is run from a git
     * worktree: `cglFixMyCommit.sh` asks git for the files of the last commit,
     * `runTests.sh` mounts the checkout alone, a worktree keeps its gitdir
     * outside that mount, and an empty list is "all is well" to the script. A
     * false green is the one failure a reading session cannot see, so the entry
     * that offers the command says where it does not hold — in the same entry,
     * because nothing carries a caller from one to the next.
     */
    #[Test]
    public function aSuiteThatAsksGitForItsFilesNamesWhereItDoesNotHold(): void
    {
        $unqualified = [];
        foreach (Finder::create()->files()->in(Paths::knowledge())->name('*.json') as $file) {
            $data = json_decode((string) file_get_contents($file->getPathname()), true, 512, JSON_THROW_ON_ERROR);
            foreach (self::entriesNaming(is_array($data) ? $data : []) as $entry) {
                if (!str_contains(strtolower(json_encode($entry, JSON_THROW_ON_ERROR)), 'worktree')) {
                    $unqualified[] = $file->getFilename() . ': ' . json_encode($entry, JSON_THROW_ON_ERROR);
                }
            }
        }

        foreach (Finder::create()->files()->in(Paths::knowledge() . '/documents')->name('*.md') as $file) {
            foreach (preg_split('/^## /m', (string) file_get_contents($file->getPathname())) ?: [] as $section) {
                if (str_contains($section, 'cglGit') && !str_contains(strtolower($section), 'worktree')) {
                    $unqualified[] = $file->getFilename() . ': ' . substr($section, 0, 60);
                }
            }
        }

        self::assertSame([], $unqualified, 'a git-driven suite is recommended without the condition it holds under');
    }

    /**
     * The invocation notes say what a checkout has to hold before any suite
     * runs, and name the command that puts it there.
     *
     * `runTests.sh` mounts the started-from directory alone, so a suite finds
     * the `vendor/` of that directory or none at all. A git worktree has none —
     * `/vendor/*` and `/bin/*` are gitignored, so git never brings them — and
     * the run stops at `bin/phpunit: not found`, which names phpunit rather than
     * the directory. The note carries the symptom for that reason: a session
     * that recognises it from the error does not have to reach the diagnosis.
     *
     * It sits with the invocation rather than in one suite entry, because it
     * holds for every suite the script offers and is read before one is chosen.
     */
    #[Test]
    public function theInvocationNotesNameTheInstallAFreshCheckoutOwes(): void
    {
        $notes = implode("\n", TestSuiteHints::invocation()['notes']);

        self::assertStringContainsString('vendor/', $notes, 'the notes do not say what a suite runs against');
        self::assertStringContainsString('composerInstall', $notes, 'the notes name no command that puts one there');
        self::assertStringContainsString(
            'bin/phpunit: not found',
            $notes,
            'the notes carry the precondition without the symptom it is recognised by',
        );

        foreach (Versions::majors() as $major) {
            self::assertContains(
                'composerInstall',
                TestSuiteHints::availableOn($major),
                'the notes hand over a suite ' . $major . ' does not have',
            );
        }

        // The prose document offering the install says the same thing. Its
        // Install Dependencies section used to offer host `composer install`
        // "after cloning TYPO3 core or changing PHP dependencies", which is
        // neither of the two cases that actually stop a run.
        $section = '';
        foreach (preg_split('/^#{2,3} /m', Documents::read('core/testing/scripts')) ?: [] as $candidate) {
            if (str_starts_with($candidate, 'Install Dependencies')) {
                $section = $candidate;
            }
        }

        self::assertStringContainsString('composerInstall', $section, 'the install section names no containerised form');
        self::assertStringContainsString('worktree', $section, 'the install section does not name the checkout that owes one');
    }

    /**
     * The innermost entries that name such a suite, so the condition is looked
     * for beside the command rather than anywhere in the file.
     *
     * @param array<mixed> $data
     *
     * @return list<array<mixed>>
     */
    private static function entriesNaming(array $data): array
    {
        $found = [];
        $children = array_filter($data, is_array(...));
        foreach ($children as $child) {
            $found = [...$found, ...self::entriesNaming($child)];
        }
        if ($found !== []) {
            return $found;
        }

        foreach (self::GIT_DRIVEN_SUITES as $suite) {
            if (str_contains(json_encode($data, JSON_THROW_ON_ERROR), $suite)) {
                return [$data];
            }
        }

        return [];
    }
}
