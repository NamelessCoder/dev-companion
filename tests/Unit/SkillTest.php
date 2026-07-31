<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Scenarios;

final class SkillTest extends TestCase
{
    /**
     * What each skill adds to the base, in the order it adds it. The four calls
     * the base already fixes are deliberately not repeated here: a skill that
     * restates them is a skill that can drift from them, and five hand-written
     * copies of one order is what the base replaced.
     */
    private const ROUTING_SKILLS = [
        'typo3-backend-module-development' => [
            'typo3_server_scope',
            'typo3_backend_module_lookup',
            'typo3_icon_lookup',
            'typo3_label_lookup',
            'typo3_translation_domain_lookup',
            'typo3_component_lookup',
            'typo3_documentation_lookup',
        ],
        'typo3-content-element-development' => [
            'typo3_documentation_lookup',
            'typo3_label_lookup',
            'typo3_icon_lookup',
        ],
        'typo3-extension-testing' => [
            'typo3_documentation_lookup',
        ],
        'typo3-extension-conformance' => [
            'typo3_architecture_lookup',
            'typo3_documentation_lookup',
            'typo3_changelog_lookup',
        ],
        'typo3-extension-documentation' => [
            'typo3_documentation_lookup',
            'typo3_label_lookup',
            'typo3_translation_domain_lookup',
        ],
    ];

    #[Test]
    public function theBaseFixesTheOrderEveryTaskStartsIn(): void
    {
        // Three REVIEW-01 runs measured what an order that is merely stated is
        // worth. The third read its skill's checklist in the first twenty
        // seconds, then listed the file tree and spent five minutes reading it
        // before calling task_guide or a single conventions lookup. Whatever a
        // skill leaves after the reading is what the reading swallows, so the
        // four owning calls come first and the checkout comes after all of them.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $position = -1;
        foreach (['typo3_project_scope', 'typo3_extension_scope', 'typo3_task_guide', 'typo3_architecture_lookup'] as $tool) {
            $next = strpos($base, $tool);
            self::assertNotFalse($next, $tool . ' is not part of the base');
            self::assertGreaterThan($position, $next, $tool . ' is stated out of order in the base');
            $position = $next;
        }
        self::assertGreaterThan(
            $position,
            strpos($base, '**Then** read the checkout'),
            'the base sends the session into the checkout before its own calls',
        );

        // The near miss, not the omission: a runtime lookup answers what is
        // registered, never whether it is right.
        self::assertMatchesRegularExpression(
            '/confirmed by its own runtime lookup can still break\s+every rule that governs it/',
            $base,
        );
        self::assertMatchesRegularExpression(
            '/settled into the opposite of a rule is a finding, not a\s+local style/',
            $base,
        );

        // And the direction that sentence invites if it stands alone. REVIEW-02
        // reported five of six priorities against mechanisms the package ships
        // on purpose — the compile step a setting drives, the vendored copy that
        // makes a non-Composer install work, the download that keeps a font on
        // the site's own host.
        self::assertMatchesRegularExpression(
            '/A mechanism that costs something is not a defect for costing it/',
            $base,
        );
        self::assertMatchesRegularExpression(
            '/trade-off to name with its cost/',
            $base,
        );
        // One hop, like every other reference: the base is read, not followed
        // onward.
        self::assertStringNotContainsString('(references/', $base);
    }

    #[Test]
    public function everySkillStartsFromTheBaseBeforeItsOwnEvidence(): void
    {
        foreach (self::skills() as $name => $skill) {
            $base = strpos($skill, '[references/base.md](references/base.md)');
            self::assertNotFalse($base, $name . ' does not route through the base');

            $first = self::ROUTING_SKILLS[$name][0] ?? null;
            self::assertNotNull($first, $name . ' has no routing of its own recorded');
            self::assertLessThan(
                strpos($skill, $first),
                $base,
                $name . ' reaches for its own tools before the base is established',
            );
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
    public function everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder(): void
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
     * than in the instruction every session pays for. Building a backend module
     * is not one of them — it is construction, and what it needs is the
     * registries, which are tools rather than a list.
     */
    #[Test]
    public function judgmentHeavySkillsKeepTheirChecklistBesideThem(): void
    {
        $judging = array_diff(
            array_keys(self::ROUTING_SKILLS),
            ['typo3-backend-module-development'],
        );

        foreach ($judging as $name) {
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
    public function extensionTestingEstablishesStaticQualityAndKeepsCheckingApartFromFixing(): void
    {
        // Two recorded REVIEW-02 runs bound this from both sides. Against an
        // extension whose PHPStan and baseline exist, the review read them and
        // found gaps inside them; against one with a fixer, a lint step and no
        // analyser at all, static analysis was never named — the missing
        // workflow surfaced as a missing test workflow and landed here, where
        // the one sentence on the subject sent it back.
        $directory = Paths::root() . '/skills/typo3-extension-testing';
        $skill = (string) file_get_contents($directory . '/SKILL.md');
        $guidance = (string) file_get_contents($directory . '/references/static-quality.md');

        self::assertStringNotContainsString('only when the project already uses them', $skill);
        self::assertMatchesRegularExpression(
            '/establishes them whether or not the\s+project already runs them/',
            $skill,
        );
        self::assertStringContainsString(
            '[references/static-quality.md](references/static-quality.md)',
            $skill,
        );

        // What the branch is worth is decided by four answers, and each of them
        // is a way the work goes wrong when it is left unsaid: a fixer wired
        // into the check, a new error parked in the baseline, formatting that
        // walks into vendored files, and a core suite translated by analogy.
        // And the run that never named static analysis needs the expectation to
        // measure the checkout against, or "what is missing" has no answer: the
        // leading finding there was a 2×4 matrix of version-independent steps,
        // which is the same evidence read from the other end.
        self::assertStringContainsString('This is the expectation the checkout is measured against', $guidance);
        self::assertStringContainsString('every cell runs only', $guidance);

        // The expectation names its tools, or "establish static analysis" is
        // advice the reader still has to source. They sit in the reference
        // rather than in the skill: a name every session carries is a name that
        // cannot be re-asked, and this list is read once per task that needs it.
        foreach (['phpstan/phpstan', 'php-cs-fixer', 'typo3/coding-standards', 'phplint', 'typoscript-lint', 'composer validate', 'eslint', 'stylelint'] as $tool) {
            self::assertStringContainsString($tool, $guidance, $tool . ' is not named where a project without a check starts');
        }
        // A package name in a published skill is the one thing no release of
        // this server corrects, and the analyser extension for TYPO3 is where
        // that bites: the core runs phpstan on itself without one, because
        // makeInstance() carries the @template annotation that used to be the
        // extension's job — checked on 12.4, 13.4, 14.3 and main.
        self::assertStringNotContainsString('saschaegerer', $guidance);
        self::assertStringContainsString('still maintained before adding', $guidance);
        // And the sentence that keeps the list from becoming the requirement:
        // it is the default where nothing covers the check, and it loses to
        // whatever the project already runs for the same one.
        self::assertMatchesRegularExpression(
            '/default per check where the checkout covers it with\s+nothing, never as a replacement for what it already runs/',
            $guidance,
        );

        self::assertStringContainsString('Keep checking and fixing apart', $guidance);
        self::assertStringContainsString('never receives an error the change in hand introduced', $guidance);
        self::assertStringContainsString('first-party paths the project intends it', $guidance);

        // The core's own build script is named once, in the skill, where the
        // harness step it belongs to is. Repeating it in an extension-facing
        // reference gives a tool that exists only in the core mono repository
        // the weight of a thing an extension might have.
        self::assertStringNotContainsString('runTests.sh', $guidance);
        self::assertSame(
            1,
            substr_count($skill, 'runTests.sh'),
            'the core build script is named more than once in an extension skill',
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
            'references/base.md',
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

        foreach (['SKILL-01', 'SKILL-02', 'SKILL-03', 'SKILL-04', 'SKILL-05', 'SKILL-06', 'SKILL-07', 'SKILL-08'] as $id) {
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
