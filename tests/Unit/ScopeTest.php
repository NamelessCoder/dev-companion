<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Scope;
use Typo3CmsMcp\Tools;

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
