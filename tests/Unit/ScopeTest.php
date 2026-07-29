<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Scope;
use Typo3CmsMcp\Tools;
use Typo3CmsMcp\Typo3Cli;

final class ScopeTest extends TestCase
{
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
