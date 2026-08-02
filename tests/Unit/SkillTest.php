<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\Scenarios;

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
        ],
        'typo3-extension-documentation' => [
            'typo3_documentation_lookup',
            'typo3_label_lookup',
            'typo3_translation_domain_lookup',
        ],
        'typo3-extension-release' => [
            'typo3_documentation_lookup',
        ],
        'typo3-extension-upgrade' => [
            'typo3_changelog_lookup',
            'typo3_system_extension_lookup',
            'typo3_architecture_lookup',
            'typo3_documentation_lookup',
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
        // And what the answer owes about its own evidence. Three recorded
        // REVIEW-02 runs in two repositories ran not one project-owned command
        // — ten were offered in the first checkout, five in the second — and
        // said nothing about it, so findings read out of a CI file stood beside
        // findings with a verified path and line at the same confidence.
        self::assertStringContainsString('What a finding rests on is part of the finding', $base);
        self::assertMatchesRegularExpression(
            '/a file that was read, at its path and its line; a\s+command that was run, with what it printed; a mechanism traced into an\s+installed\s+package/',
            $base,
        );
        // And what it owes to the commands the repository already declares.
        // The same three runs were told not to change files and read that as
        // permission to run nothing, while two of the checks on offer change
        // nothing by declaration and would have settled two of the findings.
        self::assertMatchesRegularExpression(
            '/Where one of the project\'s own commands would settle it, run\s+it/',
            $base,
        );
        self::assertMatchesRegularExpression(
            '/a check reports and hands the code back\s+as it was, so even a task told not to change files runs it/',
            $base,
        );
        self::assertMatchesRegularExpression(
            '/an unknown — a test suite, a shell pipeline, a\s+console command — is named in the answer as evidence that is available rather\s+than run unasked/',
            $base,
        );

        // One hop, like every other reference: the base is read, not followed
        // onward.
        self::assertStringNotContainsString('(references/', $base);
    }

    #[Test]
    public function theBaseStopsTheTaskWhenTheServerIsNotConnected(): void
    {
        // Sessions have run these skills more than once with the server not
        // connected at all, and produced the shape that is worst of the three
        // available: a review in the skill's own order and voice, built out of
        // general TYPO3 knowledge, with nothing in it saying so. The skill is a
        // published file, so it loads whether the tools do or not — which makes
        // the connection a precondition of the task rather than a step in it,
        // and puts it above the order in the one file every skill starts from.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $precondition = strpos($base, '## Nothing starts until the server answers');
        self::assertNotFalse($precondition, 'the base lets a task start without the server');
        self::assertLessThan(
            (int) strpos($base, 'typo3_project_scope'),
            $precondition,
            'the base reaches for its first call before it establishes there is one',
        );
        // The two shapes a missing server has, and neither reports itself: the
        // tools are absent from the session, or the first call comes back an
        // error.
        self::assertMatchesRegularExpression(
            '/No `typo3_` tool in this session, or a first call that errors: stop/',
            $base,
        );
        // And the fallback that produced the sessions above.
        self::assertMatchesRegularExpression(
            '/Do not fall back to general TYPO3 knowledge or start reading the checkout/',
            $base,
        );
        self::assertStringContainsString('Continue only when asked to after saying so', $base);
    }

    #[Test]
    public function theDeprecationSweepRunsFromTheExtensionsSurfaceAndIsReportedWhenItFindsNothing(): void
    {
        // REVIEW-02 against an extension declaring two majors on an
        // installation a major behind: 24 $GLOBALS['TSFE'] call sites across 11
        // files, the deprecation on the installed controller, and the frontend
        // surface reported as carrying no superglobal access at all. The run
        // called changelog_lookup four times and never once with
        // type: deprecation — the one deprecated API it named, it reached
        // because a ViewHelper finding walked it there. So the sweep is a step
        // of the order and its query set comes from what the extension ships,
        // not from what the reading happens to pass.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $sweep = strpos($base, 'typo3_changelog_lookup');
        self::assertNotFalse($sweep, 'the base never sweeps the deprecations of the installed core');
        self::assertGreaterThan((int) strpos($base, 'typo3_architecture_lookup'), $sweep);
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $sweep,
            'the sweep is left until after the checkout has been read',
        );
        self::assertStringContainsString('`type: deprecation`', $base);
        self::assertMatchesRegularExpression(
            '/query set is derived from the extension\'s own\s+surface/',
            $base,
        );
        // And what the caller does with the answer: an identifier the checkout
        // does not use is not a finding, and the tag decides who has to read
        // the remaining call sites.
        self::assertStringContainsString('Verify each identifier that comes back in the', $base);
        self::assertStringContainsString('`FullyScanned` / `PartiallyScanned`', $base);

        // Written once. The conformance skill carried the weaker copy — the
        // sweep "when an upgrade or a deprecated API is in scope" — which is
        // the escape hatch that run took: nothing had put a deprecated API in
        // scope, so nothing swept.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/SKILL.md',
        );
        self::assertStringNotContainsString('typo3_changelog_lookup', $skill);

        // A sweep that is visible only when it produces a finding is
        // indistinguishable from one that never ran, which is what made that
        // run's clean frontend surface writable.
        self::assertStringContainsString('the sweep ran and came back empty', $skill);
    }

    #[Test]
    public function theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks(): void
    {
        // Two findings of one bootstrap_package review ended in "I had to read
        // installed vendor core". Both carried a "does this still work in 14"
        // question, both asked the changelog, and an empty result was read as
        // the answer — while typo3_documentation_lookup at targetVersion 14
        // with the query "backend layout" returns the two pages that settle one
        // of them, first and second, in one call. So the sweep's own step says
        // what its silence is worth, rather than a sixth step: the sweep can
        // state its query set before a file is opened because step 2 derives
        // it, and this question has no query set until the reading raises it.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $sweep = (int) strpos($base, 'typo3_changelog_lookup');
        $manual = strpos($base, 'typo3_documentation_lookup', $sweep);
        self::assertNotFalse($manual, 'the base never sends a version-behaviour question to the manual');
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $manual,
            'the manual is offered only after the checkout has been read',
        );
        self::assertMatchesRegularExpression(
            '/A changelog records change events, so a pattern nothing has touched for ten\s+majors has no entry at all/',
            $base,
        );
        self::assertStringContainsString('"Does this still work in version N"', $base);

        // And the half that keeps the routing honest. The same review's second
        // instance — an ext_localconf.php content-rendering registration — is a
        // key the manual has no page for, so a miss there is a result rather
        // than a licence to reconstruct the contract from the installed core.
        self::assertStringContainsString('the finding says', $base);
        self::assertStringContainsString('Undocumented is not unsupported', $base);
        self::assertMatchesRegularExpression(
            '/installed core shows what one version implements rather than what it\s+supports/',
            $base,
        );

        // Written once. The conformance skill's own entry carried the narrower
        // condition the failing session read past — an official API or
        // configuration detail decides the finding — which a session holding a
        // behaviour question does not match itself against.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/SKILL.md',
        );
        self::assertStringContainsString('does this still work here', $skill);
        self::assertStringNotContainsString('A changelog records change events', $skill);
    }

    #[Test]
    public function aSecurityFindingIsNotEstablishedUntilItsSinkIs(): void
    {
        // The same REVIEW-02 run reported an editor-supplied field rendered
        // unescaped as its one finding with an active security consequence.
        // Every citation under it was correct and the output is escaped anyway:
        // the six call sites sit in a ViewHelper that emits nothing, and the
        // core wraps the resolved title in htmlspecialchars() two classes
        // further on — neither of which the run opened, while it did open the
        // core ViewHelper that confirmed what it already believed.
        $checklist = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/references/checklist.md',
        );

        self::assertMatchesRegularExpression(
            '/finding about a user-controlled value is a claim about a \*\*sink\*\*\s+rather than\s+about a call site/',
            $checklist,
        );
        // Escaping is one sink and a query is another, so the gate is written
        // once for both: the run that earned it condemned a template line, and
        // a value concatenated into a statement needs the same reading.
        self::assertStringContainsString('escaping and injection are the same claim about', $checklist);
        // The half that decides that case: the opt-out the finding condemned is
        // on the path to the sink rather than the end of it, and it is there
        // because the sink escapes.
        self::assertMatchesRegularExpression('/is on the path rather than\s+at the end of it/', $checklist);
        self::assertStringContainsString('report the finding as unverified', $checklist);
        // The sinks themselves are a tool's to answer, so the checklist asks
        // rather than carrying a list that goes stale in a published copy.
        self::assertStringContainsString('`typo3_architecture_lookup` for the sinks', $checklist);
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

    #[Test]
    public function theAuthoringContractIsWrittenDownAndNamesWhatHoldsIt(): void
    {
        // How a skill is written was the half nothing held: the order a task
        // runs in is one file since 2026-07-31, while the rules that hold for a
        // skill because it *is* one lived in these assertions and in five
        // skills restating them in their own words. The page is the written
        // form; this holds the two to each other in both directions, so a rule
        // stated there with nothing behind it and an assertion added here that
        // nobody wrote down each fail. A skill is published into somebody
        // else's project, so the rules it is written under are the half no
        // forward run can measure — a run grades the answer, never the file.
        $page = (string) file_get_contents(Paths::root() . '/documentation/clients/writing-a-skill.md');

        self::assertNotSame(
            0,
            preg_match_all('/`SkillTest::(\w+)`/', $page, $matches),
            'the authoring contract names no test that holds it',
        );
        $named = array_unique($matches[1]);

        foreach ($named as $test) {
            self::assertTrue(
                method_exists(self::class, $test),
                'the authoring contract names ' . $test . ', which does not exist',
            );
        }

        $source = file(__FILE__) ?: [];
        $itself = __FUNCTION__;
        foreach ((new \ReflectionClass(self::class))->getMethods() as $method) {
            if ($method->getName() === $itself || $method->getFileName() !== __FILE__) {
                continue;
            }
            $body = implode('', array_slice(
                $source,
                $method->getStartLine() - 1,
                $method->getEndLine() - $method->getStartLine() + 1,
            ));
            // The assertions that run over the directory rather than over one
            // named skill are exactly the ones a skill written later is held to
            // without ever seeing them, which is why they are the ones the page
            // has to carry.
            if (!str_contains($body, 'self::skills()') && !str_contains($body, 'self::ROUTING_SKILLS')) {
                continue;
            }
            self::assertContains(
                $method->getName(),
                $named,
                $method->getName() . ' holds every skill and is written down nowhere',
            );
        }
    }

    /**
     * What holds for a skill because it is one, rather than because of what it
     * is about. These run over the directory, so a skill added later is held to
     * them without anybody adding it to a list here — which is the point: the
     * list is what a new skill is written without ever seeing. They are the
     * ones [documentation/clients/writing-a-skill.md](../../documentation/clients/writing-a-skill.md)
     * states, and the test above holds that page and this set to each other.
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
            if (!is_dir($directory)) {
                continue;
            }
            foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.md')->sortByName() as $reference) {
                $file = $reference->getFilename();
                self::assertStringContainsString(
                    '[references/' . $file . '](references/' . $file . ')',
                    $skill,
                    $name . ' ships references/' . $file . ' without saying when to read it',
                );
                // One hop: a reference that loads a reference is a body the
                // skill no longer decides the size of.
                self::assertStringNotContainsString(
                    '(references/',
                    (string) file_get_contents($reference->getPathname()),
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
     * than in the instruction every session pays for. Two are not among them.
     * Building a backend module is construction, and what it needs is the
     * registries, which are tools rather than a list. An upgrade is construction
     * too, and its work list is produced by the sweep rather than read off a
     * rubric — what it would otherwise judge, whether the package is sound in
     * the first place, it hands to the skill whose checklist already covers it.
     */
    #[Test]
    public function judgmentHeavySkillsKeepTheirChecklistBesideThem(): void
    {
        $judging = array_diff(
            array_keys(self::ROUTING_SKILLS),
            ['typo3-backend-module-development', 'typo3-extension-upgrade'],
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
    public function anUpgradeIsOrderedWorkAndOwnsOnlyTheCrossing(): void
    {
        // The REVIEW-02 run in an extension declaring two majors against an
        // installation a major behind moved the feedback that asked for this skill:
        // the shared-versus-version-specific decisions were not the gap, because
        // the review made them — it argued the older major's registration shapes
        // as required rather than as debt, and refused the same excuse for a
        // deprecated ViewHelper shape that works on both. What it never did was
        // work in an order: the sweep ran where a finding walked into it, and
        // the Extension Scanner was not reached at all in a checkout that has
        // one.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-upgrade/SKILL.md',
        );

        $order = [
            '[references/base.md](references/base.md)',
            '## Widen the sweep into a work list',
            '## Resolve the range, rather than assert it',
            '## The boundary of what may change',
            '## Prove it on every version it claims',
        ];
        $position = -1;
        foreach ($order as $step) {
            $next = strpos($skill, $step);
            self::assertNotFalse($next, $step . ' is not part of the upgrade workflow');
            self::assertGreaterThan($position, $next, $step . ' is stated out of order');
            $position = $next;
        }

        // It starts from the base's sweep and states only what it adds, so the
        // two scope calls that order already fixes appear nowhere here.
        self::assertStringContainsString('starts from the result of that sweep rather than restating it', $skill);
        self::assertStringNotContainsString('typo3_project_scope', $skill);
        self::assertStringNotContainsString('typo3_extension_scope', $skill);

        // What it adds to the sweep is the two sources a changelog query cannot
        // reach. The scanner because the run never touched it, and the
        // annotations because the deprecation that decided that package's next
        // major sat on the installed class rather than in an entry any of the
        // four queries matched.
        self::assertStringContainsString('`type: breaking`', $skill);
        self::assertStringContainsString('**The Extension Scanner**', $skill);
        self::assertStringContainsString('`FullyScanned` / `PartiallyScanned`', $skill);
        self::assertMatchesRegularExpression('/A clean scan for a\s+partially scanned entry is not a result/', $skill);
        self::assertStringContainsString('**The deprecation annotations on what this package actually calls**', $skill);

        // And the boundary both of those inherit: they answer from the core that
        // is installed, so the target's own changes are documentation until the
        // installation is on it.
        self::assertMatchesRegularExpression(
            '/they do not know what the target major\s+changed until the installation is on it/',
            $skill,
        );
        self::assertStringContainsString('never from memory', $skill);

        // The decision the review already made correctly, which is why it is
        // stated here as the boundary of the work rather than as a judgement to
        // arrive at.
        self::assertStringContainsString('lowest declared major decides every shape', $skill);
        self::assertStringContainsString('not debt to clean up', $skill);

        // A range is resolved by the solver, and a matrix cell that nobody ran
        // or that will not resolve is a result rather than a gap in the report.
        self::assertMatchesRegularExpression('/Let the dependency solver answer, and quote what it printed/', $skill);
        self::assertStringContainsString('as a result — it is the finding', $skill);
        self::assertStringContainsString('named as unrun', $skill);

        // What it does not own, and the skill that hands it the sweep whole.
        self::assertStringContainsString('This skill owns crossing a package', $skill);
        foreach ([
            'typo3-extension-conformance',
            'typo3-extension-testing',
            'typo3-extension-documentation',
        ] as $owner) {
            self::assertStringContainsString($owner, $skill, $owner . ' is not named where the upgrade stops');
        }
        self::assertMatchesRegularExpression(
            '/What the sweep\s+returned goes to `typo3-extension-upgrade` whole/',
            (string) file_get_contents(Paths::root() . '/skills/typo3-extension-conformance/SKILL.md'),
        );
    }

    #[Test]
    public function coreTestGuidanceIsGuardedByTheWorkAndNotByTheToolList(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
        );

        // The tool is offered everywhere, so being able to call it says nothing
        // about being able to follow the answer: runTests.sh exists in the core
        // repository alone, and that is what the skill has to gate on.
        self::assertStringContainsString('only for an actual core patch', $skill);
        self::assertStringContainsString('Never present it as a project', $skill);
        self::assertStringNotContainsString('profile', $skill);
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
        self::assertStringContainsString('every entry marked assessed, unassessed or not requested', $skill);
        self::assertStringContainsString('not a recollection at the end', $skill);

        // "Do not change files" had been read as "run nothing": the audit
        // branch named no command at all, and only the improvement branch did.
        self::assertMatchesRegularExpression(
            '/Stopping at findings is not stopping at reading/',
            $skill,
        );
        self::assertMatchesRegularExpression(
            '/marks as checks hand the code back as it was, and an audit\s+told not to change files runs them/',
            $skill,
        );
    }

    #[Test]
    public function aFocusedRequestNarrowsTheReadingAndNeverTheSurfaceList(): void
    {
        // The permission to scope a review existed one clause deep in the
        // checklist while the two steps that build and close the work list
        // never mentioned it, so a security-only review was told to write the
        // whole list and answer every entry on it anyway. The narrowing is
        // stated where the list is built now, and it reaches the reading only.
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/SKILL.md',
        );

        $list = strpos($skill, 'Write the surface list down before opening a single file');
        $narrow = strpos($skill, 'The request narrows the reading, never the');
        self::assertNotFalse($narrow, 'the conformance skill does not say what a focused request narrows');
        self::assertGreaterThan((int) $list, $narrow, 'the request narrows the list before the list exists');
        self::assertLessThan(
            strpos($skill, 'Read the checkout for what none of those can know'),
            $narrow,
            'the narrowing is stated after the checkout is open, where the reading it saves is already done',
        );

        // What the list is narrowed by stays the kind of checkout, and the
        // entries the request left out stay on it under a state of their own.
        self::assertStringContainsString('narrowed to the ones this kind of checkout can have', $skill);
        self::assertStringContainsString('mark the rest **not', $skill);
        self::assertMatchesRegularExpression(
            '/A request that names no\s+surface is not a focused one/',
            $skill,
        );

        // The report is where the two states are told apart, and the number is
        // read off the step that writes the list: it said step 5 for two days
        // after the block was renumbered to three.
        self::assertStringContainsString('the surface list written in step 3', $skill);
        self::assertMatchesRegularExpression(
            '/Unassessed and not requested both mean nothing was established there, and they\s+are not the same thing/',
            $skill,
        );
        self::assertStringContainsString('let neither read as clean', $skill);

        // And the clause that was outranked now points at the same list, so a
        // session reading the reference alone does not narrow it there.
        self::assertStringContainsString(
            'the surface list below is written whole',
            (string) file_get_contents(
                Paths::root() . '/skills/typo3-extension-conformance/references/checklist.md',
            ),
        );
    }

    #[Test]
    public function theCheckLayerIsMeasuredAgainstACompleteOneRatherThanWhatIsDeclared(): void
    {
        // The file-tree trap again, one surface further in. Two REVIEW-02 runs
        // in a checkout with no analyser, no analysis step and no baseline
        // produced no finding about static analysis, and the second of them had
        // run both declared checks and reported their ceiling: the surface read
        // "declared validation commands", so what the repository does not
        // declare was not a surface and its absence could not be a finding.
        $checklist = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/references/checklist.md',
        );

        self::assertStringNotContainsString('declared validation commands', $checklist);
        self::assertStringContainsString('## The check layer', $checklist);
        self::assertMatchesRegularExpression(
            '/commands a repository declares are where this surface is read, never what\s+it is/',
            $checklist,
        );

        // The expectation is the same one the skill that establishes a missing
        // check measures against, named by what each check establishes rather
        // than by the tool behind it.
        foreach ([
            'Syntax',
            'Static analysis',
            'Coding standards',
            'Manifests and dependencies',
            'Shipped configuration and data',
            'Shipped frontend assets',
        ] as $check) {
            self::assertStringContainsString(
                '- **' . $check . '**',
                $checklist,
                $check . ' is not part of what a complete check layer covers',
            );
        }

        // What decides whether a check applies is what the package ships, not
        // what it declares a command for — otherwise the surface is back where
        // it was, and the missing one reads as an optional subsystem the
        // opening line already excuses.
        self::assertMatchesRegularExpression(
            '/no command covers is a gap in the layer rather than an optional subsystem, and\s+that absence is the finding/',
            $checklist,
        );
        self::assertStringContainsString('the ceiling of what', $checklist);

        // The routing b0eded4 established stays: the review names the gap and
        // hands it on. The tool per check is not repeated here — it is one
        // package name in two published skills otherwise, and the review does
        // not need it to see that a check is missing.
        self::assertStringContainsString('`typo3-extension-testing`', $checklist);
        foreach (['phpstan', 'php-cs-fixer', 'eslint', 'stylelint'] as $tool) {
            self::assertStringNotContainsString(
                $tool,
                $checklist,
                $tool . ' is named where the checklist only has to see the check is missing',
            );
        }
    }

    /**
     * The release skill exists because a green checkout is not the thing that
     * ships, and the run that earned it measured the gap: in one extension the
     * version-control export shipped 1558 files honouring the export-ignore
     * attributes committed beside them, while the registry tool's own archive
     * shipped 1559 — the two extra were tracked editor configuration, because
     * that tool filters by a list of its own and never reads those attributes.
     * One commit, two registries, two file sets, and nothing in the repository
     * saying so. Verifying one archive, or verifying either against the working
     * tree, cannot see it: the comparison has to be between the archives.
     */
    #[Test]
    public function aReleaseVerifiesTheArtifactAgainstEachRegistrysOwnExclusions(): void
    {
        $directory = Paths::root() . '/skills/typo3-extension-release';
        $skill = (string) file_get_contents($directory . '/SKILL.md');
        $checklist = (string) file_get_contents($directory . '/references/checklist.md');

        // The target is settled before an archive exists, or the verification
        // runs against the wrong rules and every later step inherits that.
        $target = strpos($skill, '## Establish scope, target and evidence');
        $artifact = strpos($skill, '## The artifact is not the checkout');
        $verify = strpos($skill, '## Verify the candidate');
        $report = strpos($skill, '## Report, and stop before publishing');
        self::assertNotFalse($target);
        self::assertNotFalse($artifact);
        self::assertLessThan($artifact, $target);
        self::assertLessThan($verify, $artifact);
        self::assertLessThan($report, $verify);

        // The comparison, which is the only reading that finds this class of
        // defect. Against the working tree the two mechanisms agree by
        // construction.
        self::assertStringContainsString('compare the file lists against each other, not', $skill);
        self::assertStringContainsString('Compare the lists against **each other**', $checklist);
        self::assertMatchesRegularExpression(
            '/does not\s+read (those|the export)\s*attributes/',
            $skill . $checklist,
        );

        // Publication is the boundary: it changes state this workflow cannot
        // undo, and an unclear target is the one place where continuing on an
        // assumption publishes the assumption.
        self::assertStringContainsString('explicit request', $skill);
        self::assertStringContainsString('publication steps deliberately not', $skill);
        self::assertMatchesRegularExpression(
            '/An unclear target is a question, never a guess/',
            $skill,
        );

        // The registry rules themselves are asked for, never carried: they
        // change on their own schedule and a published file cannot.
        self::assertStringContainsString('how to check, never what the rule', $skill);
        // And the packaging tools stay out of the instruction every session
        // pays for — one package name in a published file is the fact no
        // release of this server corrects.
        foreach (['tailor', 'git archive', '.gitattributes'] as $name) {
            self::assertStringNotContainsString($name, $skill, $name . ' is named where the skill only has to see there are two mechanisms');
        }
    }

    #[Test]
    public function contractCasesExerciseTaskSkillBehavior(): void
    {
        $cases = Scenarios::contracts();

        $ids = ['SKILL-01', 'SKILL-02', 'SKILL-03', 'SKILL-04', 'SKILL-05', 'SKILL-06', 'SKILL-07', 'SKILL-08', 'SKILL-09', 'SKILL-10'];
        foreach ($ids as $id) {
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
        foreach (Finder::create()->files()->in(Paths::root() . '/skills')->depth(1)->name('SKILL.md')->sortByName() as $path) {
            $skills[$path->getRelativePath()] = (string) file_get_contents($path->getPathname());
        }

        self::assertNotSame([], $skills);

        return $skills;
    }
}
