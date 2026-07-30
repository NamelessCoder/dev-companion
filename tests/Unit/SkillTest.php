<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;

final class SkillTest extends TestCase
{
    private const ROUTING_SKILLS = [
        'typo3-content-element-development' => [
            'typo3_project_scope',
            'typo3_extension_scope',
            'typo3_task_guide',
            'typo3_architecture_lookup',
            'typo3_documentation_lookup',
            'typo3_label_lookup',
            'typo3_icon_lookup',
        ],
        'typo3-extension-testing' => [
            'typo3_project_scope',
            'typo3_extension_scope',
            'typo3_task_guide',
            'typo3_architecture_lookup',
            'typo3_documentation_lookup',
        ],
        'typo3-extension-conformance' => [
            'typo3_project_scope',
            'typo3_extension_scope',
            'typo3_task_guide',
            'typo3_architecture_lookup',
            'typo3_documentation_lookup',
            'typo3_changelog_lookup',
        ],
        'typo3-extension-documentation' => [
            'typo3_project_scope',
            'typo3_extension_scope',
            'typo3_architecture_lookup',
            'typo3_documentation_lookup',
            'typo3_label_lookup',
            'typo3_translation_domain_lookup',
        ],
    ];

    #[Test]
    public function theBackendModuleSkillRoutesThroughTheOwnersOfItsFactsInOrder(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
        );
        $tools = [
            'typo3_project_scope',
            'typo3_extension_scope',
            'typo3_server_scope',
            'typo3_backend_module_lookup',
            'typo3_icon_lookup',
            'typo3_label_lookup',
            'typo3_translation_domain_lookup',
            'typo3_component_lookup',
            'typo3_documentation_lookup',
            'typo3_architecture_lookup',
        ];

        $position = -1;
        foreach ($tools as $tool) {
            $next = strpos($skill, $tool);
            self::assertNotFalse($next, $tool . ' is not routed from the backend module skill');
            self::assertGreaterThan($position, $next, $tool . ' is routed in the wrong order');
            $position = $next;
        }
    }

    #[Test]
    public function theSkillOwnsRoutingRatherThanVersionedFacts(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
        );

        self::assertStringContainsString('Keep this skill as routing only', $skill);
        self::assertStringNotContainsString('<core:', $skill);
        self::assertStringNotContainsString('Configuration/Backend/Modules.php', $skill);
        self::assertDoesNotMatchRegularExpression('/TYPO3 v?\d+/', $skill);
    }

    #[Test]
    public function extensionSkillsRouteThroughTheirPrimaryEvidenceSourcesInOrder(): void
    {
        foreach (self::ROUTING_SKILLS as $name => $tools) {
            $skill = (string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md');
            $position = -1;
            foreach ($tools as $tool) {
                $next = strpos($skill, $tool);
                self::assertNotFalse($next, $tool . ' is not routed from ' . $name);
                self::assertGreaterThan($position, $next, $tool . ' is routed in the wrong order in ' . $name);
                $position = $next;
            }
        }
    }

    #[Test]
    public function extensionSkillsOwnRoutingRatherThanVersionedFacts(): void
    {
        foreach (array_keys(self::ROUTING_SKILLS) as $name) {
            $skill = (string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md');
            self::assertStringContainsString('Keep this skill as routing', $skill, $name);
            self::assertDoesNotMatchRegularExpression('/TYPO3 v?\d+/', $skill, $name);
        }
    }

    #[Test]
    public function judgmentHeavySkillsLoadTheirChecklistOnDemand(): void
    {
        foreach ([
            'typo3-content-element-development',
            'typo3-extension-testing',
            'typo3-extension-conformance',
            'typo3-extension-documentation',
        ] as $name) {
            $skillDirectory = Paths::root() . '/skills/' . $name;
            $skill = (string) file_get_contents($skillDirectory . '/SKILL.md');

            self::assertFileExists($skillDirectory . '/references/checklist.md');
            self::assertStringContainsString(
                '[references/checklist.md](references/checklist.md)',
                $skill,
                $name,
            );
        }
    }

    #[Test]
    public function taskSkillsStateTheirOwnershipBoundaries(): void
    {
        $expectations = [
            'typo3-content-element-development' => 'This skill owns content-element architecture and implementation.',
            'typo3-extension-testing' => 'This skill owns test changes and test execution.',
            'typo3-extension-conformance' => 'This skill owns assessment and prioritization.',
            'typo3-extension-documentation' => 'This skill owns documentation and user-facing wording changes.',
        ];

        foreach ($expectations as $name => $expectation) {
            $skill = (string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md');
            self::assertStringContainsString($expectation, $skill);
        }
    }

    #[Test]
    public function coreTestGuidanceIsGuardedByTheServerProfile(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
        );

        self::assertStringContainsString('all/core contribution profile', $skill);
        self::assertStringContainsString('It is unavailable in the', $skill);
        self::assertStringContainsString('project profile', $skill);
        self::assertLessThan(
            strpos($skill, 'typo3_test_run_guide'),
            strpos($skill, 'typo3_server_scope'),
        );
    }

    #[Test]
    public function forwardScenariosExerciseTaskSkillBehavior(): void
    {
        $scenarios = (string) file_get_contents(Paths::root() . '/scenarios/task-skills.md');

        foreach (['SKILL-01', 'SKILL-02', 'SKILL-03', 'SKILL-04'] as $id) {
            self::assertStringContainsString('## ' . $id, $scenarios);
        }
        self::assertSame(4, substr_count($scenarios, '**What has to come out of it**'));
        self::assertSame(4, substr_count($scenarios, '**How it fails**'));
        self::assertStringNotContainsString('typo3_task_guide', $scenarios);
        self::assertStringNotContainsString('typo3_project_scope', $scenarios);
    }
}
