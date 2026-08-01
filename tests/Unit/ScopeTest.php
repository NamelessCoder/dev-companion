<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Knowledge\ArchitectureHints;
use Typo3CmsMcp\Knowledge\Documents;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Server\Profile;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tool\Registry;

final class ScopeTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
        putenv(Profile::VARIABLE);
    }

    #[Test]
    public function anAreaTheInstallationKnowsAsSomebodysExtensionIsOutsideTheCore(): void
    {
        // The wording gave nothing away — "bootstrap_package" is an extension
        // key and matches no phrase. The installation knows what it is.
        Instance::discoverFrom($this->composerProject());

        $extension = Registry::call('typo3_task_guide', ['task' => 'Add a content element', 'area' => 'my_sitepackage']);
        self::assertTrue($extension->data['outsideCore']);

        $systemExtension = Registry::call('typo3_task_guide', ['task' => 'Add a content element', 'area' => 'core']);
        self::assertFalse($systemExtension->data['outsideCore']);
    }

    #[Test]
    public function inASiteInstallationTheWorkIsOutsideTheCoreUnlessSomethingSaysOtherwise(): void
    {
        Instance::discoverFrom($this->composerProject());

        self::assertSame(Scope::AUDIENCE_OUTSIDE, Scope::audienceOf('', 'Add a content element with a backend preview'));
        self::assertSame(Scope::AUDIENCE_CORE, Scope::audienceOf('typo3/sysext/core/Classes/Utility/GeneralUtility.php'));
    }

    #[Test]
    public function inACoreCheckoutNothingIsPushedOutsideByTheInstallationAlone(): void
    {
        Instance::discoverFrom($this->coreCheckout());

        self::assertSame(Scope::AUDIENCE_CORE, Scope::audienceOf('', 'Add a content element with a backend preview'));
    }

    #[Test]
    public function whereNothingPlacesTheWorkTheAnswerSaysSoRatherThanAssumingTheCore(): void
    {
        // The third value. Without an installation, without a path with a shape
        // of its own and without a word either way, the old boolean answered
        // "not outside the core" — which every caller read as the core, and
        // half of them were in their own repository.
        Instance::discoverFrom(null);

        self::assertSame(Scope::AUDIENCE_UNCERTAIN, Scope::audienceOf('', 'Improve the query performance'));
        self::assertStringContainsString(
            'Nothing here says which repository',
            Registry::call('typo3_task_guide', ['task' => 'Improve the query performance'])->text,
        );
    }

    #[Test]
    public function namingTheCoreInOrderToRuleItOutIsNotEvidenceOfCoreWork(): void
    {
        // "not TYPO3 core, a composer package under vendor bk2k" reads to a
        // substring search exactly like claiming to be the core. What decides
        // is the marker that describes the work, not the one that accompanies
        // it — so the order of the signals is the whole answer here.
        self::assertSame(Scope::AUDIENCE_OUTSIDE, Scope::audienceOf(
            '',
            'Raise the compatibility of the third-party extension bootstrap_package '
            . '(not TYPO3 core, a composer package under vendor bk2k) to TYPO3 v14'
        ));
    }

    #[Test]
    public function aPathInsideAnExtensionIsRecognisedByItsShape(): void
    {
        // No core file is named that way from the core root: everything there
        // is below typo3/sysext/<key>/, and what is not is below Build/Scripts/
        // or Build/Sources/ — a bare Build/ is any repository that compiles
        // something, so it decides nothing.
        self::assertSame(Scope::AUDIENCE_OUTSIDE, Scope::audienceOf('Classes/DataProcessing/CardGroupProcessor.php'));
        self::assertSame(Scope::AUDIENCE_OUTSIDE, Scope::audienceOf('Configuration/TCA/Overrides/200_content_element.php'));
        self::assertSame(Scope::AUDIENCE_CORE, Scope::audienceOf('typo3/sysext/core/Classes/Utility/GeneralUtility.php'));
        self::assertSame(Scope::AUDIENCE_CORE, Scope::audienceOf('Build/Sources/Sass/component/_card.scss'));
    }

    #[Test]
    public function whatTheCoreKeepsInBuildIsOnlyTheCoresWhereTheRepositoryCouldBeTheCore(): void
    {
        // Build/Sources/ is the backend's Sass and TypeScript from the core
        // root — and from a site package's root it is that package's build
        // setup. What says which is the manifest at the root, which is what a
        // checkout's kind is read from.
        Instance::discoverFrom($this->composerProject());

        self::assertSame(Scope::AUDIENCE_OUTSIDE, Scope::audienceOf('Build/Sources/Sass/theme.scss'));
    }

    #[Test]
    public function theScopeNamesWhatIsCoveredAndWhatIsNot(): void
    {
        $scope = Scope::read();

        self::assertNotSame('', $scope['purpose']);
        self::assertNotSame([], $scope['covers']);
        self::assertNotSame([], $scope['doesNotCover']);
        self::assertNotSame([], $scope['routing']);
        self::assertNotSame([], $scope['checkoutDiscovery']);
    }

    #[Test]
    public function theInstructionsStateTheCheckoutBoundary(): void
    {
        $instructions = Scope::instructions();

        self::assertNotSame('', $instructions);
        self::assertStringContainsString('checkout', $instructions);
    }

    #[Test]
    public function theInstructionsFitWhatAClientKeeps(): void
    {
        // The sentence below this one is why the length is held at all: both
        // release runs of 2026-07-31 were handed instructions cut from 3662 to
        // 2048 characters, and the half that fell off ended with "in English".
        // Every profile is measured, because the one that prefixes what it is
        // not being offered is the longest.
        foreach ([Profile::ALL, Profile::PROJECT] as $profile) {
            putenv(Profile::VARIABLE . '=' . $profile);

            self::assertLessThanOrEqual(
                Scope::INSTRUCTIONS_BUDGET,
                mb_strlen(Scope::instructions()),
                sprintf('instructions of the "%s" profile', $profile),
            );
        }
    }

    #[Test]
    public function theQueryLanguageIsStatedWhereTheCallingAgentReadsIt(): void
    {
        // The one limitation the server cannot answer its way out of: the
        // corpus is English and the matching is lexical, so a query in another
        // language reaches the loanwords and nothing else. Telling the agent to
        // translate is the whole mitigation, which makes the sentence
        // load-bearing rather than decorative — and a client is free not to
        // surface the initialize instructions, so the orientation tool says it
        // too.
        self::assertStringContainsString('in English', Scope::instructions());
        self::assertStringContainsString('in English', Registry::call('typo3_server_scope', [])->text);
    }

    #[Test]
    public function theScopeInstructionsOrientTheClientBeforeItsFirstCall(): void
    {
        self::assertStringContainsString('Before writing backend markup', Scope::instructions());
        self::assertStringContainsString('Before choosing or emitting a backend icon', Scope::instructions());
        self::assertStringContainsString('Before adding or rewording a label', Scope::instructions());
    }

    #[Test]
    public function workOnAProjectExtensionIsRecognizedAsOutsideTheCore(): void
    {
        self::assertSame(Scope::AUDIENCE_OUTSIDE, Scope::audienceOf('', 'Create a new site set in a project extension'));
        self::assertSame(Scope::AUDIENCE_OUTSIDE, Scope::audienceOf('packages/my_sitepackage/Configuration/Sets/Main/config.yaml'));

        self::assertSame(Scope::AUDIENCE_CORE, Scope::audienceOf('', 'Add a reusable site set to TYPO3 core'));
        self::assertSame(Scope::AUDIENCE_CORE, Scope::audienceOf('typo3/sysext/frontend/Configuration/Sets/Fluid/config.yaml'));
        // A core path wins: a task naming both is core work that mentions the
        // other side, not the other way round.
        self::assertSame(Scope::AUDIENCE_CORE, Scope::audienceOf(
            'typo3/sysext/core/Classes/Foo.php',
            'so that a project extension can override it'
        ));
    }

    #[Test]
    public function twoPathsOfDifferentAudienceInOneCallStayApart(): void
    {
        // META-03: an extension file and a core file in one session, because
        // the bug may be in either. Folded into one string the second path was
        // answered for by the first, and which one won was the order they
        // arrived in.
        $extension = 'packages/acme_events/Classes/Domain/Repository/EventRepository.php';
        $core = 'typo3/sysext/core/Classes/Database/Query/QueryBuilder.php';

        foreach ([[$extension, $core], [$core, $extension]] as $paths) {
            $decided = array_column(Scope::audiences($paths), 'audience', 'path');
            self::assertSame(Scope::AUDIENCE_OUTSIDE, $decided[$extension], 'the core path answered for the other');
            self::assertSame(Scope::AUDIENCE_CORE, $decided[$core], 'the extension path answered for the other');
        }

        // The suites are the core's own, so the core path keeps them and the
        // extension path is named as the one they are not for.
        $suites = Registry::call('typo3_test_run_guide', [
            'query' => 'which tests do I run for this change',
            'paths' => [$extension, $core],
        ]);
        self::assertFalse($suites->data['outsideCore']);
        self::assertNotSame([], $suites->data['suites']);
        self::assertStringContainsString($extension, $suites->text);
        self::assertStringNotContainsString($core, implode("\n", array_column($suites->data['suites'], 'command')));

        // The conventions transfer to both, the commands to one: a hint
        // returned for the extension path carries no core check.
        $hints = Registry::call('typo3_architecture_lookup', [
            'task' => 'fix the query that reads the events',
            'paths' => [$extension, $core],
        ]);
        self::assertFalse($hints->data['outsideCore']);
        $decided = array_column($hints->data['audiences'], 'audience', 'path');
        self::assertSame(Scope::AUDIENCE_OUTSIDE, $decided[$extension]);
        self::assertSame(Scope::AUDIENCE_CORE, $decided[$core]);
        self::assertStringContainsString('# For ' . $extension, $hints->text);
        self::assertStringContainsString('# For ' . $core, $hints->text);
    }

    #[Test]
    public function aTaskOutsideTheCoreIsToldSoBeforeTheChecklist(): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Create a new TYPO3 site set in a project extension with config.yaml and TypoScript',
        ]);

        self::assertTrue($result->data['outsideCore']);
        self::assertStringStartsWith('This reads as work outside the TYPO3 core', $result->text);
    }

    #[Test]
    public function maintainingAnExtensionIsNotSubmittingAPatchToTheCore(): void
    {
        // "review", "push", "submit" describe maintenance work as readily as
        // they describe Gerrit. Reading one of them as a patch submission put
        // the entire core contribution workflow into an answer about a
        // third-party extension. Nothing here says which side this is, so the
        // intent is offered under its condition rather than stated.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Maintain and extend the third-party TYPO3 extension bk2k/bootstrap-package for '
                . 'TYPO3 13.4 and 14.3: review TCA, TypoScript, Fluid templates, data processors and '
                . 'upgrade wizards for compatibility and choose tests',
        ]);

        $confidence = array_column($result->data['intents'], 'confidence', 'id');
        self::assertSame('weak', $confidence['submission'] ?? null);
        self::assertStringContainsString('Possibly also: Patch submission', $result->text);
    }

    #[Test]
    public function aCorePathStillMakesTheSameWordAPatchSubmission(): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Push the fix for review',
            'area' => 'typo3/sysext/core/Classes/Utility/GeneralUtility.php',
        ]);

        $confidence = array_column($result->data['intents'], 'confidence', 'id');
        self::assertSame('strong', $confidence['submission'] ?? null);
    }

    #[Test]
    public function inASitePackageThePatchSubmissionIntentIsNotOfferedAtAll(): void
    {
        // There is no Gerrit to submit to, so this is not a weaker match — it
        // is not one.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Push the fix for review',
            'area' => 'packages/my_sitepackage/Classes/Controller/EventController.php',
        ]);

        self::assertTrue($result->data['outsideCore']);
        self::assertNotContains('submission', array_column($result->data['intents'], 'id'));
        self::assertStringNotContainsString('Change-Id', implode("\n", $result->data['checklist']));
    }

    #[Test]
    public function whatTheScopeExcludesIsNotWhatTheServerAnswers(): void
    {
        // The declared scope still ruled out project and extension work, and
        // upgrading an installation, while both had hints of their own. A
        // caller cannot tell a boundary from a gap by the size of an answer,
        // and the two ask for opposite reactions.
        $excluded = mb_strtolower(implode("\n", array_column(Scope::read()['doesNotCover'], 'topic')));

        self::assertNotNull(ArchitectureHints::byId('sitepackage-layout'));
        self::assertStringNotContainsString('extension development', $excluded);
        self::assertNotNull(ArchitectureHints::byId('installation-upgrade'));
        self::assertStringNotContainsString('upgrading an installation', $excluded);

        // And the list says what it is worth read from the other side.
        self::assertStringContainsString(
            'gap in the knowledge base',
            Registry::call('typo3_server_scope', [])->text
        );
    }

    /**
     * How the claim reads when it names who it turns away: something is put
     * beyond this server, and what it names is one of the audiences below.
     *
     * @var array<int, string>
     */
    private const BEYOND_THIS_SERVER = [
        'out of scope', 'out of this server', 'outside the scope', 'outside this server',
        'not in scope', 'is not covered', 'are not covered', 'not covered here',
        'not covered by', 'does not cover', 'do not cover',
    ];

    /**
     * The two audiences it keeps arriving at the expense of, in the words they
     * are written in. R-AUD-1 names three and the core contributor is the one
     * nobody ever excludes, so the third is not here.
     *
     * @var array<int, string>
     */
    private const THE_OTHER_AUDIENCES = [
        'extension', 'project', 'site package', 'sitepackage', 'site developer', 'installation',
    ];

    /**
     * How it reads when it names nobody: the server is confined to the core and
     * who that leaves out is left to the reader. Two of the three sentences
     * D-SCO-6 found were written this way, which is why the audience words
     * alone would not have caught them.
     *
     * @var array<int, string>
     */
    private const CONFINED_TO_THE_CORE = [
        'scoped to', 'limited to', 'restricted to', 'confined to', 'only knows',
        'only covers', 'only answers', 'only about', 'nothing but', 'exclusively', 'solely',
    ];

    /**
     * The claim in the wording it was actually found in, so the matcher below
     * is tested against something rather than only against prose that is
     * already correct.
     *
     * @var array<int, string>
     */
    private const THE_CLAIM_AS_IT_WAS_FOUND = [
        'The knowledge base is scoped to contributing to the core.',
        'Configuring an installation with TypoScript is out of this server\'s scope.',
        'This server only knows the core\'s own conventions.',
    ];

    /**
     * The same assertion as above, in the surfaces the not-covered list is not.
     *
     * That list is where the claim was written down last, not where it lived:
     * the same sentence stood in a knowledge document and in the notice every
     * tool opens with, and each was corrected on its own after somebody read
     * it. D-SCO-6 named the three no test reads — a tool description, the
     * readme, a hint — and this is that test, so the next occurrence costs a
     * failing suite rather than a session's confidence.
     *
     * What is matched is wording, which is as weak as wording always is. The
     * three sentences the decision recorded are run through the matcher first,
     * so one that has stopped recognising the claim fails here rather than
     * passing everywhere.
     */
    #[Test]
    public function noSurfaceSaysTheCoreIsTheOnlyWorkThisServerAnswersFor(): void
    {
        foreach (self::THE_CLAIM_AS_IT_WAS_FOUND as $sentence) {
            self::assertNotSame(
                [],
                self::claimsTheCoreAlone($sentence),
                'the claim is no longer recognised in the wording it was found in: ' . $sentence,
            );
        }

        foreach (self::surfacesThatDescribeTheServer() as $surface => $text) {
            self::assertSame(
                [],
                self::claimsTheCoreAlone($text),
                $surface . ' puts the extension author or the site developer outside what this server answers',
            );
        }
    }

    /**
     * The sentences of one surface that say the core is all of it.
     *
     * Sentence by sentence rather than over the whole text, because every
     * surface here names the core and names an extension, and only a sentence
     * that does both in the one construction is the claim.
     *
     * @return array<int, string>
     */
    private static function claimsTheCoreAlone(string $text): array
    {
        $claims = [];
        foreach (preg_split('/(?<=[.!?;:])\s+|\n/', $text) ?: [] as $sentence) {
            $lowered = mb_strtolower($sentence);
            $turnsAwayAnAudience = self::names($lowered, self::BEYOND_THIS_SERVER)
                && self::names($lowered, self::THE_OTHER_AUDIENCES);
            $confinesTheServer = self::names($lowered, self::CONFINED_TO_THE_CORE)
                && str_contains($lowered, 'core');

            if ($turnsAwayAnAudience || $confinesTheServer) {
                $claims[] = trim($sentence);
            }
        }

        return $claims;
    }

    /** @param array<int, string> $markers */
    private static function names(string $sentence, array $markers): bool
    {
        foreach ($markers as $marker) {
            if (str_contains($sentence, $marker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Everywhere this server states its own boundary in prose.
     *
     * The explanations under the not-covered list are left out and their topics
     * are not: a `why` is where a subject that really is outside gets its
     * reason, and the sentence that says so has to be allowed to say it. The
     * topic line is the claim itself, and it is the one field the test above
     * already reads.
     *
     * The knowledge documents are left out for the same reason — they are the
     * core's own contribution material, where "this holds for the core
     * repository" is true rather than the claim.
     *
     * @return array<string, string>
     */
    private static function surfacesThatDescribeTheServer(): array
    {
        $scope = Scope::read();
        $surfaces = ['the purpose in server-scope.json' => $scope['purpose']];

        foreach ($scope['covers'] as $entry) {
            $surfaces['the covered topic "' . $entry['topic'] . '"'] = $entry['topic'] . ' ' . $entry['depth'];
        }
        foreach ($scope['doesNotCover'] as $entry) {
            $surfaces['the not-covered topic "' . $entry['topic'] . '"'] = $entry['topic'];
        }
        foreach ($scope['routing'] as $entry) {
            $surfaces['the routing entry "' . $entry['when'] . '"'] = $entry['when'] . ' ' . $entry['call'];
        }

        // Both profiles, because the prefix that says which half a client is
        // being offered is the sentence closest to the claim of all of them.
        // The variable is forgotten again by forgetTheInstance().
        foreach ([Profile::ALL, Profile::PROJECT] as $profile) {
            putenv(Profile::VARIABLE . '=' . $profile);
            $surfaces['the instructions of the "' . $profile . '" profile'] = Scope::instructions();
        }
        putenv(Profile::VARIABLE);

        foreach (Registry::definitions() as $definition) {
            $surfaces['the description of ' . $definition['name']] = $definition['description'];
        }

        $surfaces['readme.md'] = (string) file_get_contents(Paths::root() . '/readme.md');

        foreach (ArchitectureHints::load() as $hint) {
            $surfaces['the ' . $hint['id'] . ' hint'] = $hint['title'] . ' '
                . implode(' ', array_column($hint['hints'], 'text'));
        }

        return $surfaces;
    }

    #[Test]
    public function theBriefPointsAtTheGuideForTheStepItEndsWith(): void
    {
        // The brief's last checklist item is the commit message in all but
        // name, and the tool that writes one was never in its next lookups. A
        // caller reads the routing table once, at the start, and commits hours
        // later out of this list.
        $core = Registry::call('typo3_task_guide', [
            'task' => 'Fix the DataHandler regression',
            'area' => 'typo3/sysext/core/Classes/DataHandling/DataHandler.php',
        ]);
        self::assertContains('typo3_commit_message_guide', array_column($core->data['nextTools'], 'tool'));
        self::assertStringContainsString(
            'typo3_commit_message_guide',
            implode("\n", $core->data['checklist'])
        );

        // And with the workflow that repository needs, because the default is
        // the core's and demands a Forge issue nobody there has.
        $project = Registry::call('typo3_task_guide', [
            'task' => 'Add a search to the product plugin',
            'area' => 'packages/my_sitepackage/Classes/Controller/ProductController.php',
        ]);
        self::assertTrue($project->data['outsideCore']);
        self::assertStringContainsString('workflow="project"', implode("\n", $project->data['checklist']));
    }

    #[Test]
    public function theBriefRoutesToTheToolsItsOwnSubjectsAreAnsweredBy(): void
    {
        // Forty label keys were invented in one session while typo3_label_lookup
        // was never called: the pointer was in the routing table and in the
        // hint, both read once, and the moment of need was hours later. The
        // brief is what a caller comes back to, so it carries them.
        $labels = Registry::call('typo3_task_guide', [
            'task' => 'Write the XLF language files for the sitepackage backend labels',
            'targetVersion' => '14',
        ]);
        $tools = array_column($labels->data['nextTools'], 'tool');
        self::assertContains('typo3_label_lookup', $tools);

        // And the changelog, which is where a version one has not built on
        // recently differs from the one in memory — not a retrospective lookup.
        self::assertContains('typo3_changelog_lookup', $tools);
        self::assertStringContainsString('14', implode("\n", array_column($labels->data['nextTools'], 'when')));
    }

    #[Test]
    public function aBriefOutsideTheCoreKeepsNothingThatOnlyTheCoreHas(): void
    {
        // Saying "this is outside the core" and then listing four runTests.sh
        // suites, a changelog file below typo3/sysext/ and the core branch
        // policy is not a partly right answer: the flag says the brief knew.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Add a data processor and an upgrade wizard to my site package',
            'area' => 'packages/my_sitepackage/Classes/DataProcessing/CsvProcessor.php',
            'changeType' => 'feature',
        ]);

        self::assertTrue($result->data['outsideCore']);
        self::assertSame([], $result->data['checks']);
        self::assertSame([], $result->data['testSuites']);
        self::assertSame([], $result->data['conditionalChecks']);
        foreach ($result->data['architectureHints'] as $hint) {
            self::assertSame([], $hint['checks'], $hint['id'] . ' kept its core checks');
        }
        foreach ($result->data['checklist'] as $entry) {
            self::assertFalse(Scope::isCoreOnly($entry), $entry . ' cannot be done outside the core');
        }
        foreach ($result->data['checkoutDiscovery'] as $entry) {
            self::assertFalse(Scope::isCoreOnly($entry['establish'] . ' ' . $entry['how']), $entry['establish']);
        }
        // The notice names the script once, to say it does not apply here.
        self::assertSame(1, substr_count($result->text, 'runTests.sh'));
    }

    #[Test]
    public function noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests(): void
    {
        // Every suite this guide knows is a Build/Scripts/runTests.sh
        // invocation, and that script is part of the core repository. Handed to
        // a site package, every command in the answer is unrunnable — and it
        // looks copy-pasteable, which is worse than declining.
        $result = Registry::call('typo3_test_run_guide', [
            'query' => 'how do I test my sitepackage',
            'paths' => ['packages/my_sitepackage/Classes/Command/SeedCommand.php'],
        ]);

        self::assertTrue($result->data['outsideCore']);
        self::assertSame([], $result->data['suites']);
        // The script is named in the sentence that explains why nothing is
        // returned; what must not appear is a command shaped to be run.
        self::assertStringNotContainsString('CI=true', $result->text);
        self::assertStringStartsWith('This reads as work outside the TYPO3 core', $result->text);
    }

    #[Test]
    public function aRuleQueryIsPointedAtTheHintCorpusItBelongsIn(): void
    {
        // Which of the two corpora holds a subject is this server's business:
        // site sets are an architecture hint, the Gerrit workflow is prose, and
        // the question is phrased the same way either way.
        $result = Registry::call('typo3_rule_lookup', ['query' => 'site set settings definitions']);

        self::assertContains('site-sets', array_column($result->data['alsoInHints'], 'id'));
        self::assertStringContainsString('typo3_architecture_lookup', $result->text);
    }

    /**
     * The prose corpus is the contribution process and the commit conventions
     * at once, and only the first half stops at the core repository. Dropping
     * the whole tool — which is what the project profile does — takes the
     * second half with it, and a caller writing a commit message in their own
     * repository needs exactly that.
     */
    #[Test]
    public function aRuleAnswerKeepsWhatTransfersAndWithholdsWhatDoesNot(): void
    {
        $result = Registry::call('typo3_rule_lookup', ['query' => 'commit message sitepackage']);

        self::assertTrue($result->data['outsideCore']);
        self::assertSame(
            ['typo3-commit-messages'],
            array_values(array_unique(array_column($result->data['matches'], 'documentId'))),
            'a section that holds anywhere was withheld, or one that does not was handed over',
        );
        self::assertNotSame([], $result->data['withheldDocuments'], 'the core-only documents matched and went unmentioned');
        foreach ($result->data['withheldDocuments'] as $document) {
            self::assertTrue(
                Documents::isCoreOnly($document['id']),
                $document['id'] . ' was withheld and is not the core repository\'s own',
            );
        }
    }

    /**
     * A thinner answer that does not say what it left out reads as "nobody
     * wrote this down", which is the one thing it does not mean.
     */
    #[Test]
    public function whatARuleAnswerWithheldIsNamedRatherThanMissing(): void
    {
        $outside = Registry::call('typo3_rule_lookup', ['query' => 'review readiness for my site package']);

        self::assertContains('typo3-core-rules', array_column($outside->data['withheldDocuments'], 'id'));
        self::assertStringStartsWith('This reads as work outside the TYPO3 core', $outside->text);
        // The resource stays reachable: withholding is about what an answer
        // volunteers, not about what a caller may deliberately read.
        self::assertStringContainsString('typo3://core/', $outside->text);
    }

    #[Test]
    public function insideTheCoreARuleAnswerWithholdsNothing(): void
    {
        $inside = Registry::call('typo3_rule_lookup', ['query' => 'review readiness for a typo3/sysext/core patch']);

        self::assertFalse($inside->data['outsideCore']);
        self::assertSame([], $inside->data['withheldDocuments']);
        self::assertContains('typo3-core-rules', array_column($inside->data['matches'], 'documentId'));
    }

    /**
     * Which of the two a document is comes from the scope rather than from a
     * second list, so a document the scope does not announce has no binding to
     * read — and is served as a resource and searched by the rule lookup all
     * the same. `typo3-contribution-sources` was exactly that.
     */
    #[Test]
    public function everyKnowledgeDocumentIsAnnouncedByTheScope(): void
    {
        $named = [];
        foreach (Scope::read()['covers'] as $entry) {
            if (preg_match_all('#typo3://core/([a-z0-9-]+)#', $entry['source'], $matches) === 0) {
                continue;
            }
            foreach ($matches[1] as $id) {
                $named[$id] = $entry['provenance'];
            }
        }

        foreach (Documents::documents() as $document) {
            self::assertArrayHasKey(
                $document['id'],
                $named,
                $document['id'] . ' is served and searched, and no covered topic names it',
            );
            self::assertContains($named[$document['id']], ['core-only', 'transferable']);
        }
    }

    #[Test]
    public function noCoreScriptIsHandedToARepositoryThatDoesNotHaveIt(): void
    {
        $result = Registry::call('typo3_script_lookup', [
            'task' => 'run the unit tests of my site package extension',
        ]);

        self::assertTrue($result->data['outsideCore']);
        self::assertSame([], $result->data['matches']);
        self::assertStringNotContainsString('CI=true', $result->text);
    }

    #[Test]
    public function aScriptAnswerSaysWhichRepositoryItsCommandsRunIn(): void
    {
        // Nothing in this query says either way, so the commands are offered
        // under their condition rather than stated as the answer.
        $unstated = Registry::call('typo3_script_lookup', ['task' => 'php-cs-fixer and phpstan']);
        self::assertFalse($unstated->data['outsideCore']);
        self::assertNotSame([], $unstated->data['matches']);
        self::assertStringContainsString('run in a TYPO3 core checkout', $unstated->text);

        $stated = Registry::call('typo3_script_lookup', ['task' => 'unit tests for a typo3/sysext/core patch']);
        self::assertStringNotContainsString('run in a TYPO3 core checkout', $stated->text);
    }

    #[Test]
    public function anArchitectureHintKeepsItsAdviceOutsideTheCoreAndLosesItsCoreChecks(): void
    {
        // The conventions transfer — the commands do not.
        $result = Registry::call('typo3_architecture_lookup', [
            'task' => 'add a console command to my site package',
            'paths' => ['packages/my_sitepackage/Classes/Command/SeedCommand.php'],
        ]);

        self::assertTrue($result->data['outsideCore']);
        self::assertNotSame([], $result->data['hints']);
        foreach ($result->data['hints'] as $hint) {
            self::assertSame([], $hint['checks'], $hint['id'] . ' handed over a core check');
        }
        self::assertStringNotContainsString('CI=true', $result->text);
    }

    #[Test]
    public function theInstallationDiagnosticIsDataRatherThanProse(): void
    {
        Instance::discoverFrom(sys_get_temp_dir());
        Typo3Cli::forget();

        try {
            $installation = Registry::call('typo3_server_scope', [])->data['installation'];
        } finally {
            Instance::discoverFrom(null);
            Typo3Cli::forget();
        }

        // A client that renders structuredContent and drops the text block saw
        // none of this, and read five empty results as five empty registries.
        self::assertFalse($installation['found']);
        self::assertNotSame([], $installation['searched']);
        self::assertFalse($installation['console']['reachable']);
        self::assertNotSame('', $installation['console']['reason']);
        self::assertSame(Instance::ROOT_VARIABLE, $installation['settings']['root']);
        self::assertSame(Typo3Cli::CONSOLE_VARIABLE, $installation['settings']['console']);
    }

    #[Test]
    public function anUnanswerableLookupCarriesItsReasonInTheData(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();

        $result = Registry::call('typo3_label_lookup', ['query' => 'save']);

        self::assertSame('nothing', $result->data['answeredBy']);
        self::assertNotSame('', $result->data['unavailable']['reason']);
        self::assertSame([], $result->data['labels']);
    }

    #[Test]
    public function anUnconsultedConfigurationPathIsNotReportedAsAbsent(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();

        $result = Registry::call('typo3_configuration_lookup', ['path' => 'SYS/fluid']);

        // found: false says the installation has no value there, which is a
        // statement about an installation nothing asked.
        self::assertNull($result->data['found']);
        self::assertSame('nothing', $result->data['answeredBy']);
    }

    #[Test]
    public function everyCoveredTopicSaysWhatItIsWorthOutsideTheCore(): void
    {
        // The boundary runs through the middle of this server: the installation
        // half is a property of TYPO3 installations, the conventions transfer,
        // and only the contribution process is core-only. A caller that has to
        // work that out per tool trusts either all of it or none of it.
        foreach (Scope::read()['covers'] as $entry) {
            self::assertContains(
                $entry['provenance'],
                ['core-only', 'transferable', 'installation'],
                $entry['topic'],
            );
        }
    }

    #[Test]
    public function everyToolNamedInTheScopeExists(): void
    {
        $known = $this->toolNames();
        $scope = Scope::read();

        $mentioned = [];
        foreach ($scope['covers'] as $entry) {
            $mentioned = array_merge($mentioned, $entry['tools']);
        }
        foreach (array_merge($scope['routing'], $scope['doesNotCover'], $scope['checkoutDiscovery']) as $entry) {
            preg_match_all('/typo3_[a-z_]+/', implode(' ', $entry), $matches);
            $mentioned = array_merge($mentioned, $matches[0]);
        }

        foreach (array_unique($mentioned) as $tool) {
            self::assertContains($tool, $known, $tool . ' is named in the scope but not registered');
        }
    }

    #[Test]
    public function everyToolIsReachableThroughTheScope(): void
    {
        $mentioned = [];
        foreach (Scope::read()['covers'] as $entry) {
            $mentioned = array_merge($mentioned, $entry['tools']);
        }

        foreach ($this->toolNames() as $tool) {
            if (in_array($tool, ['typo3_server_scope', 'typo3_feedback_record', 'typo3_feedback_list'], true)) {
                continue;
            }
            self::assertContains($tool, $mentioned, $tool . ' is registered but no covered topic points at it');
        }
    }

    /** @return array<int, string> */
    private function toolNames(): array
    {
        // The feedback tools only exist in a standalone checkout, but the scope
        // may name them either way.
        return array_merge(
            array_column(Registry::definitions(), 'name'),
            ['typo3_feedback_record', 'typo3_feedback_list'],
        );
    }
}
