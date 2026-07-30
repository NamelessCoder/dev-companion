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

    /**
     * What holds for a skill because it is one, rather than because of what it
     * is about. These four run over the directory, so a skill added later is
     * held to them without anybody adding it to a list here — which is the
     * point: the list is what a new skill is written without ever seeing.
     */
    #[Test]
    public function everySkillIsPublishedUnderTheNameItCallsItself(): void
    {
        foreach (self::skills() as $name => $skill) {
            self::assertStringContainsString("\nname: " . $name . "\n", $skill, $name . ' is filed under another name');
            self::assertMatchesRegularExpression(
                '/\ndescription: \S.{40,}\n/',
                $skill,
                $name . ' has no description a client could route on',
            );
        }
    }

    #[Test]
    public function everySkillStatesWhatItOwns(): void
    {
        foreach (self::skills() as $name => $skill) {
            self::assertStringContainsString('This skill owns ', $skill, $name . ' does not say what it owns');
        }
    }

    #[Test]
    public function noSkillKeepsASecondCopyOfWhatAToolOwns(): void
    {
        foreach (self::skills() as $name => $skill) {
            self::assertStringContainsString('Keep this skill as routing', $skill, $name);
            // A version number in a permanently loaded instruction is the one
            // fact that cannot be re-asked when the installation is a different
            // one, and no answer says it came from here.
            self::assertDoesNotMatchRegularExpression('/TYPO3 v?\d+/', $skill, $name);
            self::assertStringNotContainsString('<core:', $skill, $name . ' carries backend markup');
        }
    }

    #[Test]
    public function everyReferenceIsOneHopAwayAndLoadedOnDemand(): void
    {
        foreach (self::skills() as $name => $skill) {
            $directory = Paths::root() . '/skills/' . $name . '/references';
            foreach (glob($directory . '/*.md') ?: [] as $reference) {
                $file = basename($reference);
                self::assertStringContainsString(
                    '[references/' . $file . '](references/' . $file . ')',
                    $skill,
                    $name . ' ships references/' . $file . ' without saying when to read it',
                );
                // One hop: a reference that loads a reference is a body the
                // skill no longer decides the size of.
                self::assertStringNotContainsString(
                    '(references/',
                    (string) file_get_contents($reference),
                    $name . '/references/' . $file . ' sends the reader on to another reference',
                );
            }
        }
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

    /**
     * Judgment is what a checklist is for, and it is also the thing a skill
     * grows a body around: the four that carry it keep it beside them rather
     * than in the instruction every session pays for.
     */
    #[Test]
    public function judgmentHeavySkillsKeepTheirChecklistBesideThem(): void
    {
        foreach (array_keys(self::ROUTING_SKILLS) as $name) {
            self::assertFileExists(Paths::root() . '/skills/' . $name . '/references/checklist.md');
        }
    }

    #[Test]
    public function extensionTestingVerifiesItsHarnessBeforeAddingCoverage(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-testing/SKILL.md',
        );

        $verify = strpos($skill, 'Verify that the harness');
        $establish = strpos($skill, '## Establish or repair the required harness');
        $add = strpos($skill, '## Add or extend tests');
        self::assertNotFalse($verify);
        self::assertNotFalse($establish);
        self::assertNotFalse($add);
        self::assertLessThan($establish, $verify);
        self::assertLessThan($add, $establish);
        self::assertStringContainsString('for a review-only request, report the defect without changing it', $skill);
        self::assertStringContainsString('Keep unit and functional infrastructure with the extension', $skill);
        self::assertStringContainsString('Keep browser infrastructure with the runnable project', $skill);
        self::assertStringNotContainsString('Classify the work as setup', $skill);
    }

    #[Test]
    public function extensionTestingLoadsOnlyTheSelectedLayerGuide(): void
    {
        $directory = Paths::root() . '/skills/typo3-extension-testing';
        $skill = (string) file_get_contents($directory . '/SKILL.md');

        foreach (['phpunit', 'playwright'] as $guide) {
            $guidance = (string) file_get_contents($directory . '/references/' . $guide . '.md');
            self::assertStringContainsString('## Choose the folders', $guidance);
        }
        self::assertStringContainsString('read only its implementation guide', $skill);
        self::assertStringContainsString(
            'FunctionalTests.xml',
            (string) file_get_contents($directory . '/references/phpunit.md'),
        );
        self::assertStringContainsString(
            'playwright.config.ts',
            (string) file_get_contents($directory . '/references/playwright.md'),
        );
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

        foreach (['SKILL-01', 'SKILL-02', 'SKILL-03', 'SKILL-04', 'SKILL-05', 'SKILL-06'] as $id) {
            self::assertStringContainsString('## ' . $id, $scenarios);
        }
        self::assertSame(6, substr_count($scenarios, '**What has to come out of it**'));
        self::assertSame(6, substr_count($scenarios, '**How it fails**'));
        self::assertStringNotContainsString('typo3_task_guide', $scenarios);
        self::assertStringNotContainsString('typo3_project_scope', $scenarios);
    }

    /**
     * Every published skill, read from the directory the installer publishes.
     *
     * @return array<string, string>
     */
    private static function skills(): array
    {
        $skills = [];
        foreach (glob(Paths::root() . '/skills/*/SKILL.md') ?: [] as $path) {
            $skills[basename(dirname($path))] = (string) file_get_contents($path);
        }

        self::assertNotSame([], $skills);

        return $skills;
    }
}
