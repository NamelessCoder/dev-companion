<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Scenarios;

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
    public function backendModuleDocumentationIsAnExplicitSkillTransition(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
        );

        $verified = strpos($skill, 'implementation is verified');
        $stop = strpos($skill, 'stop this workflow');
        $activate = strpos($skill, 'Activate `typo3-extension-documentation` before editing documentation');
        self::assertNotFalse($verified);
        self::assertNotFalse($stop);
        self::assertNotFalse($activate);
        self::assertLessThan($stop, $verified);
        self::assertLessThan($activate, $stop);
        self::assertMatchesRegularExpression(
            '/belongs to that\s+extension, not to the project around it/',
            $skill,
        );
    }

    #[Test]
    public function theBaseIsEstablishedBeforeTheCheckoutIsOpened(): void
    {
        // A base that is stated but reachable in any order is not a base. Three
        // runs of REVIEW-01 established that the reading phase swallows
        // whatever the skill left after it: the third read the checklist, then
        // listed the file tree and spent five minutes in it before calling
        // task_guide or a single conventions lookup. So the four owning calls
        // and the surface list come first here, in one block, and the sentence
        // that sends the session into the files comes after all of them.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/SKILL.md',
        );

        $base = [
            'typo3_project_scope',
            'typo3_extension_scope',
            'typo3_task_guide',
            'references/checklist.md',
            'Write the surface list down before opening a single file',
        ];

        $position = -1;
        foreach ($base as $step) {
            $next = strpos($skill, $step);
            self::assertNotFalse($next, $step . ' is not part of the conformance base');
            self::assertGreaterThan($position, $next, $step . ' is stated out of order');
            $position = $next;
        }

        // The file tree is a trap where a surface has no files, so the list is
        // derived from the surfaces and never from what a listing happens to
        // show.
        self::assertMatchesRegularExpression(
            '/A surface is in scope because the checklist names it, not because\s+the file tree\s+shows it/',
            $skill,
        );
        self::assertGreaterThan(
            $position,
            strpos($skill, 'Read the checkout for what none of those can know'),
            'the skill sends the session into the checkout before its base is established',
        );
    }

    #[Test]
    public function anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk(): void
    {
        // The order is the whole requirement. A conventions lookup that happens
        // after the view has formed confirms it instead of testing it, and the
        // run that established this read three XLF files, judged them sound and
        // never asked what governs them — so the rule that calls a non-English
        // source file a defect was in the corpus, one query away, unread.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/SKILL.md',
        );

        $ask = strpos($skill, 'asked for **before** a view of the subsystem is formed');
        $lookup = strpos($skill, 'typo3_architecture_lookup');
        self::assertNotFalse($ask, 'the conformance skill does not say when the conventions are asked for');
        self::assertNotFalse($lookup);
        self::assertLessThan($lookup, $ask, 'the skill asks for conventions after naming what to read');

        // Read in both directions: the rule judges the checkout that exists,
        // not only the code about to be written.
        self::assertMatchesRegularExpression(
            '/settled into the opposite of a rule is a finding, not a local style/',
            $skill,
        );

        // The runtime lookup is the near miss, not the omission: the third run
        // reached for a translation tool and picked the one that reports what a
        // path resolves to, then filed the surface as clean.
        self::assertMatchesRegularExpression(
            '/confirmed by its own runtime\s+lookup and still break every rule that governs it/',
            $skill,
        );

        // And a surface nobody asked about is named, because silence about it
        // is indistinguishable from a clean result — read off the written list
        // rather than off what the session remembers having skipped.
        self::assertStringContainsString('**unassessed**, and unassessed is', $skill);
        self::assertStringContainsString('every entry marked assessed or unassessed', $skill);
        self::assertStringContainsString('not a recollection at the end', $skill);
    }

    #[Test]
    public function contractCasesExerciseTaskSkillBehavior(): void
    {
        $cases = Scenarios::contracts();

        foreach (['SKILL-01', 'SKILL-02', 'SKILL-03', 'SKILL-04', 'SKILL-05', 'SKILL-06', 'SKILL-07'] as $id) {
            self::assertArrayHasKey($id, $cases);
            self::assertStringStartsWith('scenarios/contracts/task-skills/', $cases[$id]['file']);
            self::assertNotSame([], $cases[$id]['outcomes'], $id . ' says nothing about what has to come out of it');
            self::assertNotSame([], $cases[$id]['failures'], $id . ' says nothing about how it fails');

            // A case names the task a user brings, never the tool or workflow
            // the answer is supposed to reach for.
            $text = implode(' ', [$cases[$id]['prompt'], ...$cases[$id]['outcomes'], ...$cases[$id]['failures']]);
            self::assertStringNotContainsString('typo3_', $text, $id . ' names a tool of this server');
        }
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
