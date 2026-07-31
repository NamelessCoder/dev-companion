<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\ArchitectureHints;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Profile;
use Typo3CmsMcp\Scope;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tools;
use Typo3CmsMcp\Typo3Cli;

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

        $extension = Tools::call('typo3_task_guide', ['task' => 'Add a content element', 'area' => 'my_sitepackage']);
        self::assertTrue($extension->data['outsideCore']);

        $systemExtension = Tools::call('typo3_task_guide', ['task' => 'Add a content element', 'area' => 'core']);
        self::assertFalse($systemExtension->data['outsideCore']);
    }

    #[Test]
    public function inASiteInstallationTheWorkIsOutsideTheCoreUnlessSomethingSaysOtherwise(): void
    {
        Instance::discoverFrom($this->composerProject());

        self::assertTrue(Scope::isOutsideCore([], 'Add a content element with a backend preview'));
        self::assertFalse(Scope::isOutsideCore(['typo3/sysext/core/Classes/Utility/GeneralUtility.php'], ''));
    }

    #[Test]
    public function inACoreCheckoutNothingIsPushedOutsideByTheInstallationAlone(): void
    {
        Instance::discoverFrom($this->coreCheckout());

        self::assertFalse(Scope::isOutsideCore([], 'Add a content element with a backend preview'));
    }

    #[Test]
    public function namingTheCoreInOrderToRuleItOutIsNotEvidenceOfCoreWork(): void
    {
        // "not TYPO3 core, a composer package under vendor bk2k" reads to a
        // substring search exactly like claiming to be the core. What decides
        // is the marker that describes the work, not the one that accompanies
        // it — so the order of the signals is the whole answer here.
        self::assertTrue(Scope::isOutsideCore(
            [],
            'Raise the compatibility of the third-party extension bootstrap_package '
            . '(not TYPO3 core, a composer package under vendor bk2k) to TYPO3 v14'
        ));
    }

    #[Test]
    public function aPathInsideAnExtensionIsRecognisedByItsShape(): void
    {
        // No core file is named that way from the core root: everything there
        // is below typo3/sysext/<key>/ or Build/.
        self::assertTrue(Scope::isOutsideCore(['Classes/DataProcessing/CardGroupProcessor.php'], ''));
        self::assertTrue(Scope::isOutsideCore(['Configuration/TCA/Overrides/200_content_element.php'], ''));
        self::assertFalse(Scope::isOutsideCore(['typo3/sysext/core/Classes/Utility/GeneralUtility.php'], ''));
        self::assertFalse(Scope::isOutsideCore(['Build/Sources/Sass/component/_card.scss'], ''));
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
        self::assertStringContainsString('in English', Tools::call('typo3_server_scope', [])->text);
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
        self::assertTrue(Scope::isOutsideCore([], 'Create a new site set in a project extension'));
        self::assertTrue(Scope::isOutsideCore(['packages/my_sitepackage/Configuration/Sets/Main/config.yaml']));

        self::assertFalse(Scope::isOutsideCore([], 'Add a reusable site set to TYPO3 core'));
        self::assertFalse(Scope::isOutsideCore(['typo3/sysext/frontend/Configuration/Sets/Fluid/config.yaml']));
        // A core path wins: a task naming both is core work that mentions the
        // other side, not the other way round.
        self::assertFalse(Scope::isOutsideCore(
            ['typo3/sysext/core/Classes/Foo.php'],
            'so that a project extension can override it'
        ));
    }

    #[Test]
    public function aTaskOutsideTheCoreIsToldSoBeforeTheChecklist(): void
    {
        $result = Tools::call('typo3_task_guide', [
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
        $result = Tools::call('typo3_task_guide', [
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
        $result = Tools::call('typo3_task_guide', [
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
        $result = Tools::call('typo3_task_guide', [
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
            Tools::call('typo3_server_scope', [])->text
        );
    }

    #[Test]
    public function theBriefPointsAtTheGuideForTheStepItEndsWith(): void
    {
        // The brief's last checklist item is the commit message in all but
        // name, and the tool that writes one was never in its next lookups. A
        // caller reads the routing table once, at the start, and commits hours
        // later out of this list.
        $core = Tools::call('typo3_task_guide', [
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
        $project = Tools::call('typo3_task_guide', [
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
        $labels = Tools::call('typo3_task_guide', [
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
        $result = Tools::call('typo3_task_guide', [
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
        $result = Tools::call('typo3_test_run_guide', [
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
        $result = Tools::call('typo3_rule_lookup', ['query' => 'site set settings definitions']);

        self::assertContains('site-sets', array_column($result->data['alsoInHints'], 'id'));
        self::assertStringContainsString('typo3_architecture_lookup', $result->text);
    }

    #[Test]
    public function noCoreScriptIsHandedToARepositoryThatDoesNotHaveIt(): void
    {
        $result = Tools::call('typo3_script_lookup', [
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
        $unstated = Tools::call('typo3_script_lookup', ['task' => 'php-cs-fixer and phpstan']);
        self::assertFalse($unstated->data['outsideCore']);
        self::assertNotSame([], $unstated->data['matches']);
        self::assertStringContainsString('run in a TYPO3 core checkout', $unstated->text);

        $stated = Tools::call('typo3_script_lookup', ['task' => 'unit tests for a typo3/sysext/core patch']);
        self::assertStringNotContainsString('run in a TYPO3 core checkout', $stated->text);
    }

    #[Test]
    public function anArchitectureHintKeepsItsAdviceOutsideTheCoreAndLosesItsCoreChecks(): void
    {
        // The conventions transfer — the commands do not.
        $result = Tools::call('typo3_architecture_lookup', [
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
            $installation = Tools::call('typo3_server_scope', [])->data['installation'];
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

        $result = Tools::call('typo3_label_lookup', ['query' => 'save']);

        self::assertSame('nothing', $result->data['answeredBy']);
        self::assertNotSame('', $result->data['unavailable']['reason']);
        self::assertSame([], $result->data['labels']);
    }

    #[Test]
    public function anUnconsultedConfigurationPathIsNotReportedAsAbsent(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();

        $result = Tools::call('typo3_configuration_lookup', ['path' => 'SYS/fluid']);

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
            array_column(Tools::definitions(), 'name'),
            ['typo3_feedback_record', 'typo3_feedback_list'],
        );
    }
}
