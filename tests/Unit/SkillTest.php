<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\TaskIntents;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Server\Installer;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Fixture;
use TYPO3\DevCompanion\Upkeep\Scenarios;

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
            'typo3_commit_message_guide',
        ],
        'typo3-content-element-development' => [
            'typo3_documentation_lookup',
            'typo3_label_lookup',
            'typo3_icon_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-extension-testing' => [
            'typo3_documentation_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-development-installation' => [
            'typo3_server_scope',
            'typo3_documentation_lookup',
            'typo3_configuration_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-core-patch-review' => [
            'typo3_hint_lookup',
            'typo3_forge_lookup',
            'typo3_gerrit_lookup',
            'typo3_rule_lookup',
            'typo3_changelog_lookup',
            'typo3_documentation_lookup',
            'typo3_test_run_guide',
            'typo3_script_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-core-issue-triage' => [
            'typo3_forge_lookup',
            'typo3_gerrit_lookup',
            'typo3_changelog_lookup',
            'typo3_test_run_guide',
            'typo3_script_lookup',
            'typo3_rule_lookup',
        ],
        'typo3-core-patch-checkout' => [
            'typo3_gerrit_lookup',
            'typo3_rule_lookup',
            'typo3_test_run_guide',
        ],
        'typo3-core-patch-development' => [
            'typo3_rule_lookup',
            'typo3_forge_lookup',
            'typo3_gerrit_lookup',
            'typo3_test_run_guide',
            'typo3_hint_lookup',
            'typo3_script_lookup',
            'typo3_commit_message_guide',
        ],
        'typo3-extension-conformance' => [
            'typo3_hint_lookup',
            'typo3_documentation_lookup',
        ],
        'typo3-extension-documentation' => [
            'typo3_documentation_lookup',
            'typo3_label_lookup',
            'typo3_translation_domain_lookup',
            'typo3_commit_message_guide',
        ],
        // One tool of its own, because what this skill contributes is an order
        // rather than evidence: the findings are the audit's and every change
        // is made in the workflow that owns it. What is left for it to route to
        // is the commit, and the commit is the whole of the checkpoint the
        // order turns on — `D-SKL-016`.
        'typo3-extension-cleanup' => [
            'typo3_commit_message_guide',
        ],
        'typo3-extension-upgrade' => [
            'typo3_changelog_lookup',
            'typo3_system_extension_lookup',
            'typo3_hint_lookup',
            'typo3_documentation_lookup',
            'typo3_commit_message_guide',
        ],
    ];

    /**
     * The skills whose workflow ends in a change to a repository that is not
     * the core's, read off each body on 2026-08-04 — `D-SKL-014`. The two core
     * skills are not among them: both name the guide already and both commit in
     * the core, where the argument's default is the right one.
     *
     * `typo3-extension-conformance` is not among them either. It is pure
     * analysis and its body makes no change of any kind, which is the second
     * **Wrong if** of `D-SKL-014`: a commit line in a review's answer is what
     * `R-GUI-006` exists to keep out of one.
     */
    private const COMMITTING_SKILLS = [
        'typo3-backend-module-development',
        'typo3-content-element-development',
        'typo3-development-installation',
        'typo3-extension-documentation',
        'typo3-extension-cleanup',
        'typo3-extension-testing',
        'typo3-extension-upgrade',
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
        foreach (['typo3_project_describe', 'typo3_extension_describe', 'typo3_task_guide', 'typo3_hint_lookup'] as $tool) {
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

        // Step 1 says what its answer ends with, because a session that follows
        // the order literally reads the step before it reads the payload — and
        // four sessions in a week finished without learning the guides exist
        // (`feedback/2026-08-07-233512`).
        self::assertStringContainsString(
            'the whole procedures this server carries, as ids',
            self::flat($base),
        );

        // The near miss, not the omission: a runtime lookup answers what is
        // registered, never whether it is right.
        self::assertStringContainsString(
            'confirmed by its own runtime lookup can still break every rule that governs it',
            self::flat($base),
        );
        self::assertStringContainsString(
            'settled into the opposite of a rule is a finding, not a local style',
            self::flat($base),
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
        self::assertStringContainsString(
            'a file that was read, at its path and its line; a command that was run, with what it printed; a mechanism traced into an installed package',
            self::flat($base),
        );
        // And what it owes to the commands the repository already declares.
        // The same three runs were told not to change files and read that as
        // permission to run nothing, while two of the checks on offer change
        // nothing by declaration and would have settled two of the findings.
        self::assertMatchesRegularExpression(
            '/Where one of the project\'s own commands would settle it, run\s+it/',
            $base,
        );
        self::assertStringContainsString(
            'a check reports and hands the code back as it was, so even a task told not to change files runs it',
            self::flat($base),
        );
        self::assertStringContainsString(
            'an unknown — a test suite, a shell pipeline, a console command — is named in the answer as evidence that is available rather than run unasked',
            self::flat($base),
        );

        // One hop, like every other reference: the base is read, not followed
        // onward.
        self::assertStringNotContainsString('(references/', $base);
    }

    /**
     * Every call the base fixes, held to what it sends the session to read out
     * of the answer.
     *
     * The rest of the authoring contract is read off the file, which makes it a
     * proxy: the wording stays where it is while the answer behind it moves.
     * And a skill does not only name a tool — the base has a session read
     * whether a declared command is a check, a change or unknown, the test
     * layers and the source language each XLF declares, and the sentence that
     * says whether the hint step is still owed. A tool that stopped reporting
     * one of those fails nothing: the skill still names it,
     * `everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder` still passes, and
     * the session is sent to a key that is not there.
     *
     * So the four are called, in the order the base fixes them and threaded the
     * way a session reads them — the extension key comes out of step 1, the
     * hint id comes out of step 3. The two that need an installation are asked
     * of the one this repository writes, which is what makes this answerable on
     * any machine rather than on the author's (`D-SKL-025`).
     */
    #[Test]
    public function everyCallTheBaseFixesAnswersWithWhatItSendsTheSessionToRead(): void
    {
        $base = self::flat((string) file_get_contents(Paths::root() . '/skills/base.md'));

        Instance::discoverFrom(Fixture::write());
        Typo3Cli::forget();
        Typo3Runtime::forget();
        Icons::forget();

        // Step 1: the installation, its two versions, the extensions that are
        // the project's own, its sites, and the commands it declares.
        $project = Registry::call('typo3_project_describe', [])->data;
        self::assertArrayNotHasKey('unsupported', $project, 'the written installation could not be described');
        foreach (['typo3Version', 'phpConstraint', 'extensions', 'sites', 'commands', 'guides'] as $key) {
            self::assertArrayHasKey($key, $project, 'step 1 sends the session to read ' . $key);
        }

        // The marking a task told not to change files reads before it runs
        // anything. The three words are the base's own, so what is held is that
        // every command carries one of them: a fourth marking, or one renamed,
        // makes that sentence false in every published copy of the base.
        self::assertStringContainsString(
            'marks each command it lists **check**, **change** or **unknown**',
            $base,
        );
        self::assertNotSame([], $project['commands'], 'the installation declares no command to be marked');
        foreach ($project['commands'] as $command) {
            self::assertContains(
                $command['runs'],
                ['check', 'change', 'unknown'],
                $command['command'] . ' is marked nothing the base names',
            );
        }

        // What step 1 ends with, and what the base says each entry is worth:
        // one typo3_rule_lookup by documentId, which is the only route to a
        // whole procedure where the client renders no resource list. An id
        // that stopped resolving there names a procedure this server carries
        // and cannot hand over.
        self::assertNotSame([], $project['guides'], 'step 1 ends without the procedures it says it ends with');
        foreach ($project['guides'] as $guide) {
            $document = Registry::call('typo3_rule_lookup', ['documentId' => $guide['id']])->data;
            self::assertSame(
                [$guide['id']],
                array_column($document['matches'], 'documentId'),
                $guide['id'] . ' is named as a whole procedure and is no documentId',
            );
        }

        // Step 2: what the extension registers, and what it ships beside that.
        $own = array_values(array_filter(
            $project['extensions'],
            static fn(array $extension): bool => $extension['origin'] === 'project',
        ));
        self::assertNotSame([], $own, 'step 1 reports no extension of the project\'s own for step 2 to describe');

        $extension = Registry::call('typo3_extension_describe', ['extension' => $own[0]['key']])->data;
        self::assertArrayNotHasKey('unsupported', $extension, $own[0]['key'] . ' could not be described');
        // All four, present or absent: this installation ships no manual, no
        // README and no test layer, so the keys being there on it is the half
        // the base means by what an extension does *not* ship being answered
        // too — the half no file listing gives you.
        foreach (['manual', 'readme', 'tests', 'languageFiles'] as $artifact) {
            self::assertArrayHasKey(
                $artifact,
                $extension['artifacts'],
                'step 2 sends the session to read ' . $artifact,
            );
        }
        self::assertNotSame([], $extension['artifacts']['languageFiles'], 'the installation ships no XLF to read');
        foreach ($extension['artifacts']['languageFiles'] as $file) {
            self::assertArrayHasKey(
                'sourceLanguage',
                $file,
                $file['path'] . ' is reported without the source language the base has the session read off it',
            );
        }

        // Step 3: the brief, and the sentence step 4 is owed or not on the
        // strength of. This one stopped short of what the lookup matched.
        $paths = ['typo3/sysext/backend/Classes/Controller/PageLayoutController.php'];
        $brief = Registry::call('typo3_task_guide', [
            'task' => 'Add a backend module with icons and labels',
            'paths' => $paths,
            'changeType' => 'feature',
        ]);
        foreach (['skills', 'checks', 'checklist', 'hints', 'omittedHints'] as $key) {
            self::assertArrayHasKey($key, $brief->data, 'step 3 sends the session to read ' . $key);
        }
        self::assertNotSame([], $brief->data['hints'], 'the paths this is measured on match no hint');
        self::assertNotSame(
            [],
            $brief->data['omittedHints'],
            'the paths this is measured on stopped truncating the brief',
        );
        // Named rather than counted, because naming them is what the base sends
        // the session to fetch by id instead of repeating the query.
        foreach ($brief->data['omittedHints'] as $omitted) {
            self::assertStringContainsString(
                $omitted['id'],
                $brief->text,
                $omitted['id'] . ' was left out of the brief and is named nowhere in it',
            );
        }

        // The other branch of the same sentence, quoted off the base rather
        // than off the class that prints it: the two have to be one sentence,
        // or the base sends the session to read something it cannot find.
        $carried = Registry::call('typo3_task_guide', [
            'task' => 'Fix a bug in the data handler',
            'paths' => ['typo3/sysext/core/Classes/DataHandling/DataHandler.php'],
            'changeType' => 'bugfix',
        ]);
        self::assertSame([], $carried->data['omittedHints'], 'the paths this is measured on stopped carrying them all');
        $sentence = 'everything typo3_hint_lookup matches for these paths';
        self::assertStringContainsString($sentence, $base, 'the base quotes no sentence for a brief that carried them all');
        self::assertStringContainsString($sentence, $carried->text, 'a brief that carried them all does not say so');
        self::assertStringNotContainsString($sentence, $brief->text, 'a brief that stopped short says it carried them all');

        // Step 4: one query per subsystem with its concrete paths, and the id
        // route step 3 sends the session down where the brief already spent the
        // query.
        $hints = Registry::call('typo3_hint_lookup', ['paths' => $paths])->data;
        self::assertNotSame([], $hints['hints'], 'the subsystem step 4 is measured on matches no hint');

        $left = $brief->data['omittedHints'][0]['id'];
        $one = Registry::call('typo3_hint_lookup', ['id' => $left])->data;
        self::assertSame(
            [$left],
            array_column($one['hints'], 'id'),
            $left . ' is named as left behind and cannot be fetched by id',
        );
    }

    /** Nothing after this reads the written installation, and nothing before it did. */
    #[After]
    public function forgetTheInstallation(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
        Icons::forget();
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
            (int) strpos($base, 'typo3_project_describe'),
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
    public function theWorkflowStepRunsInEverySession(): void
    {
        // The step carried a condition from 2026-08-04 to 2026-08-11: skipped
        // where the guide's own answer had named this skill, because that
        // session holds the brief already — `D-SKL-015`. It was skipped twice by
        // sessions the condition did not cover, `feedback/2026-08-04-055715` and
        // the 2026-08-10 core patch review `D-SKL-033` names, and neither of
        // them said so. What the condition asked was which route activated the
        // skill, which is not something a session establishes about itself, so
        // it came off — `D-SKL-034`.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $step = strpos($base, '**`typo3_task_guide`**');
        self::assertNotFalse($step, 'the base no longer carries the workflow step');
        $unconditional = strpos($base, 'Run it in every session');
        self::assertNotFalse($unconditional, 'the base no longer says the workflow step is run');
        self::assertGreaterThan($step, $unconditional);
        self::assertLessThan(
            (int) strpos($base, '**`typo3_hint_lookup`**'),
            $unconditional,
            'the workflow step is stated as unconditional at another step',
        );
        self::assertStringNotContainsString('Skip it only where', $base);

        // The sweep's is the one condition the order carries, because emptiness
        // is answered by the files the session is holding.
        self::assertSame(
            1,
            substr_count(self::flat($base), 'only where'),
            'the order carries a condition on a step other than the sweep',
        );

        // The broad reading, rejected in `D-SKL-015` and not revived by taking
        // its condition off: a skill that covers the task end to end still does
        // not know the caller's paths.
        self::assertStringNotContainsString('end to end', $base);
        self::assertStringContainsString(
            'brief is built from the paths as well as the task text, and no skill knows which paths the caller is holding',
            self::flat($base),
        );
        // What a skip costs is the path-specific brief and nothing else. The
        // commit step was named here as the other half of that cost while
        // `D-SKL-014` was queued, and stopped being one on the commit that put
        // it into the bodies of the skills that own extension work — the fourth
        // **Wrong if** of `D-SKL-015`, fired 2026-08-04. The base is copied into
        // all nine skills, the review-only ones included, which is why the step
        // is theirs and not this file's.
        self::assertStringNotContainsString('typo3_commit_message_guide', $base);

        // A condition that reads as an invitation is taken as one, so the order
        // says once what a skipped prescription costs the steps around it.
        self::assertStringContainsString(
            'a prescription that gets skipped teaches the next reader to skip the ones that matter too',
            self::flat($base),
        );
    }

    #[Test]
    public function theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange(): void
    {
        // A session in `/home/benji/projects/syntax` was told to reproduce a
        // frontend defect in the extension it stood in, fix it and commit it. It
        // made 37 calls, all of them Bash, Read, Edit or Write, activated no
        // skill and called none of the 26 tools — `feedback/2026-08-04-012644`.
        // The two skills that named the commit guide were the core ones, and the
        // seven an extension author reaches for named no commit step at all,
        // which is the fourth and worst of the channels `D-GUI-002` counted.
        // `D-SKL-014` is the placement; which bodies get it was read off each
        // one.
        foreach (self::COMMITTING_SKILLS as $name) {
            $skill = (string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md');
            self::assertStringContainsString(
                'typo3_commit_message_guide',
                $skill,
                $name . ' ends in a change and names no commit step',
            );
            self::assertStringContainsString(
                'workflow="project"',
                self::flat($skill),
                $name . ' names the commit guide without the workflow it commits in',
            );
        }

        // The other side, and the one that would make this wrong: a review
        // changes nothing and commits nothing, so a commit line in it is the
        // patch checklist `R-GUI-006` exists to keep out of a review's answer.
        // `typo3-core-patch-review` reads the message a patch already carries,
        // which is why it names the guide at all, and it reads it against the
        // core's rules.
        $review = (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        );
        self::assertStringNotContainsString('workflow="project"', self::flat($review));

        // Conformance is the case the second **Wrong if** describes, and it
        // fired: the step went into the branch its body kept for requested
        // improvements, and the maintainer's reading is that the skill makes no
        // change at all. The branch is gone with it, so what holds here is the
        // absence — a review that names the guide is naming the step
        // `R-GUI-006` keeps out of a review's answer.
        $conformance = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/SKILL.md',
        ));
        self::assertStringNotContainsString('typo3_commit_message_guide', $conformance);
        self::assertStringContainsString(
            'Stop after findings. This skill changes nothing, whatever the request asked for',
            $conformance,
        );
        // And the description it is chosen on, which is the half a body cannot
        // correct: while it offered to improve a repository, the skill was
        // loaded for change requests whatever the body said.
        self::assertStringNotContainsString('improve', self::description('typo3-extension-conformance'));
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
        // of the order, and what the extension ships is what bounds it rather
        // than what the reading happens to pass.
        //
        // What it bounds it *with* is the changelog's own axes and not the
        // extension's words — D-SKL-003. Two models swept one sitepackage on
        // 2026-07-31 with the query set this step used to prescribe and both
        // got nothing; re-run on 2026-08-02, type: deprecation with version 14
        // and no query returns all 75, and tag: ext:form returns the 6 that
        // carry #109412, which the words missed at 39th place — past the
        // default limit of 20, and the tag is what makes the sweep small enough
        // to be read at all.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $sweep = strpos($base, 'typo3_changelog_lookup');
        self::assertNotFalse($sweep, 'the base never sweeps the deprecations of the installed core');
        self::assertGreaterThan((int) strpos($base, 'typo3_hint_lookup'), $sweep);
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $sweep,
            'the sweep is left until after the checkout has been read',
        );
        self::assertStringContainsString('`type: deprecation`', $base);
        self::assertStringContainsString(
            'bounded by `tag` and with the query omitted',
            self::flat($base),
        );
        // The extension's surface picks the tags and nothing else, which is the
        // half of "from the extension's surface" that survives.
        self::assertStringContainsString('Step 2 picks the tags instead', $base);
        self::assertStringContainsString('name the system extension a change is **in**', $base);
        self::assertStringContainsString(
            'An extension key of your own is not among them',
            self::flat($base),
        );
        // And the wording that cost the two sweeps is gone rather than softened.
        self::assertStringNotContainsString('query set', $base);
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
    public function theDeprecationSweepIsSkippedOnlyWhereTheChangeTouchesNoTypo3Api(): void
    {
        // The second half of `feedback/2026-08-04-055741`: the sweep was
        // prescribed and skipped on a change that added a fixer, an
        // `.editorconfig` and two CI commands. A deprecation is a statement
        // about API the package calls, so that sweep was empty before it ran —
        // at one call per declared major per tag, which is what makes this step
        // the expensive one to leave prescribed and unrun. It is the one
        // condition the order carries since step 3's came off, and it survives
        // for the reason that one did not: what a change touches is in front of
        // the session, and how the skill was activated is not (`D-SKL-034`).
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $condition = strpos($base, 'Skip the sweep only where the change touches no TYPO3 API');
        self::assertNotFalse($condition, 'the base states no condition on the deprecation sweep');
        self::assertGreaterThan((int) strpos($base, 'typo3_changelog_lookup'), $condition);
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $condition,
            'the condition on the sweep stands after the reading it bounds',
        );
        // Empty rather than merely unlikely to find anything, which is the
        // whole of what makes the call skippable.
        self::assertStringContainsString(
            'a change that calls none has nothing for the sweep to land on',
            self::flat($base),
        );
        // And what keeps a tooling task that ends up editing one PHP file from
        // reading the condition off the task it was described as.
        self::assertStringContainsString(
            'read off the files it touches and never off the task it started as',
            self::flat($base),
        );

        // The other side of the same step, and the one a read-only task falls
        // outside of: it matches neither "touches API" nor the three examples,
        // so a session following the order faithfully would have run the sweep
        // across seven tags on a triage of one issue (`D-AUD-009`,
        // `feedback/2026-08-07-233512`).
        self::assertStringContainsString(
            'A task that produces no change does not reach this step at all',
            self::flat($base),
        );
        // Written as the three examples it was, that exemption held for the
        // shape it was written for and let the next one through: a review of a
        // patch was in none of them, skipped the sweep on a diff touching
        // TYPO3 API, and read the distance from "a review of a report" as
        // deliberate (`feedback/2026-08-11-055337`, `D-SKL-037`). So what the
        // exemption states is the property and the examples illustrate it.
        self::assertStringContainsString(
            'illustrations of it rather than the list it is read off',
            self::flat($base),
        );
        // The property's own boundary, which the enumeration never had to
        // carry: a review asked to make the change is a workflow that produces
        // one, and it reaches this step holding files.
        self::assertStringContainsString(
            'The exemption ends where the workflow produces a change',
            self::flat($base),
        );
        // And the half the sighting reported against itself. Step 2 asks it
        // already; nothing asked it of the step that goes unrun.
        self::assertStringContainsString(
            'A report names the step it did not reach',
            self::flat($base),
        );
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
        // what its silence is worth, rather than a sixth step: the sweep is
        // writable before a file is opened because step 2 supplies its tags,
        // and this question has nothing to bound it until the reading raises
        // it.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $sweep = (int) strpos($base, 'typo3_changelog_lookup');
        $manual = strpos($base, 'typo3_documentation_lookup', $sweep);
        self::assertNotFalse($manual, 'the base never sends a version-behaviour question to the manual');
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $manual,
            'the manual is offered only after the checkout has been read',
        );
        self::assertStringContainsString(
            'A changelog records change events, so a pattern nothing has touched for ten majors has no entry at all',
            self::flat($base),
        );
        self::assertStringContainsString('"Does this still work in version N"', $base);

        // And the half that keeps the routing honest. The same review's second
        // instance — an ext_localconf.php content-rendering registration — is a
        // key the manual has no page for, so a miss there is a result rather
        // than a licence to reconstruct the contract from the installed core.
        self::assertStringContainsString('that is a result and not an answer', self::flat($base));
        self::assertStringContainsString('Undocumented is not unsupported', $base);

        // And what that half is worth is bounded by what the manual can be
        // asked. `feedback/2026-08-03-164805` followed this routing and read
        // `PageRenderer.php` by hand anyway: re-run from
        // `/home/benji/projects/ext-guidedtour` on 2026-08-03,
        // `Infobox ViewHelper state` at `targetVersion: "14"` returns the
        // ViewHelper reference page first, carrying the deprecation whole,
        // while `addInlineLanguageLabelFile` and `inline language labels`
        // return the label reference and TCA pages that spell "label" and
        // "add" in their titles and name the method nowhere. The tool's own
        // header says why, and the identifier route is the one that answers:
        // `typo3_changelog_lookup` with `addInlineLanguageLabelFile` returns
        // the 7.5 Feature entry that introduced it and no deprecation, which
        // is `D-ANS-042` — so the miss above is a result for a surface and the
        // wrong corpus for an identifier (`D-ANS-010`).
        self::assertStringContainsString(
            'The manual matches page titles and section paths, never the text of a page',
            self::flat($base),
        );
        self::assertStringContainsString('a PHP identifier has no page to be titled after', self::flat($base));
        $identifier = strpos($base, 'An identifier goes to');
        self::assertNotFalse($identifier, 'the base leaves a PHP identifier pointed at the manual');
        self::assertLessThan(
            (int) strpos($base, '**Then** read the checkout'),
            $identifier,
            'the identifier route is offered after the reading it exists to save',
        );
        // The limit that carries the second half now stands where the reading
        // it limits is ordered rather than here, where nobody is reading the
        // core — the section below, which `D-SKL-004` earned.
        self::assertStringContainsString(
            'what this installation does and never what TYPO3 supports',
            self::flat($base),
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
        // The bound on the same routing is written in the same one place. The
        // conformance skill defers to the base for why the changelog cannot
        // answer, and the upgrade skill starts from the same sweep, so a copy
        // here is the second hand-written order `D-SKL-001` exists to prevent.
        self::assertStringNotContainsString('page titles and section paths', $skill);
    }

    #[Test]
    public function theInstalledSourceIsTheStepAfterTheLookupsAndItsAnswerIsVersionBound(): void
    {
        // The base's one sentence for an exhausted question was written for a
        // review — "the finding says the question could not be settled" — and
        // `feedback/2026-08-01-003933` is a session building a content element
        // in `site-new`, which had no finding to write and a template that had
        // to render. It guessed at the `f:if` branch contract and changed the
        // markup until the user corrected it, while the installed source sat in
        // the checkout: `IfViewHelper` ships in `typo3fluid/fluid` rather than
        // in `typo3/cms-fluid`, and its docblock carries `<f:then>` explicitly
        // in every `f:else` example it gives (`D-SKL-004`).
        //
        // The reading is bounded rather than licensed. `D-ANS-010` refused the
        // installed core as a substitute for the manual, so what the step
        // settles is this installation and the answer says so — "read the
        // source before guessing", as the feedback proposed it, would license
        // the reconstruction that entry turned down.
        $base = (string) file_get_contents(Paths::root() . '/skills/base.md');

        $step = strpos($base, '## When the lookups run out');
        self::assertNotFalse($step, 'the base names no step for a question the lookups leave open');
        // After the lookups and after the checkout, or it is the reverse of the
        // workflow rather than the end of it.
        self::assertGreaterThan(
            (int) strpos($base, '**Then** read the checkout'),
            $step,
            'the base sends the session into the installed source before its own calls',
        );
        self::assertStringContainsString(
            'A behaviour question that survives the lookups above is read out of the installed source',
            self::flat($base),
        );
        // An act with an object, because the same position has already cost a
        // rule that was present and read past (`D-SKL-009`).
        self::assertStringContainsString(
            'the class that implements the behaviour and the one it inherits from',
            self::flat($base),
        );
        // What it replaces is what the filing session actually did, named in
        // those words rather than left to be inferred from a prohibition.
        self::assertStringContainsString('what it replaces is changing the code until it works', self::flat($base));

        // `D-ANS-010`'s boundary, carried in the sentence that orders the
        // reading: one installation's implementation is not what TYPO3
        // supports.
        self::assertStringContainsString(
            'What it settles is what this installation does and never what TYPO3 supports',
            self::flat($base),
        );
        // And both dispositions, because naming only the first is what this
        // step was queued to repair: a session that reports and a session that
        // has to produce something that works.
        self::assertStringContainsString(
            'a finding says the question could not be settled beyond the version installed',
            self::flat($base),
        );
        self::assertStringContainsString(
            'an answer built on the reading names the version it holds for',
            self::flat($base),
        );
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
        // Read with the line breaks collapsed: what is asserted is that the
        // sentence is there, and where it wraps is the formatter's business.
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/references/checklist.md',
        ));

        self::assertStringContainsString(
            'finding about a user-controlled value is a claim about a **sink** rather than about a call site',
            $checklist,
        );
        // Escaping is one sink and a query is another, so the gate is written
        // once for both: the run that earned it condemned a template line, and
        // a value concatenated into a statement needs the same reading.
        self::assertStringContainsString('escaping and injection are the same claim about', $checklist);
        // The half that decides that case: the opt-out the finding condemned is
        // on the path to the sink rather than the end of it, and it is there
        // because the sink escapes.
        self::assertStringContainsString('is on the path rather than at the end of it', $checklist);
        self::assertStringContainsString('report the finding as unverified', $checklist);
        // The sinks themselves are a tool's to answer, so the checklist asks
        // rather than carrying a list that goes stale in a published copy.
        self::assertStringContainsString('`typo3_hint_lookup` for the sinks', $checklist);
    }

    #[Test]
    public function aReviewReportsWhatItDroppedAndWhatDroppedIt(): void
    {
        // What a review let go is the half nothing recorded: a candidate dropped
        // in silence and a surface nobody opened leave the same trace in the
        // report. The conformance checklist already states the bar for one
        // subject — a security verdict has to be disproved before it can be
        // dismissed — and what makes it a bar is not the subject but who pays
        // for it being wrong, which is the reader either way (`D-SKL-007`).
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));

        self::assertStringContainsString('## What a dropped candidate owes', $checklist);
        self::assertStringContainsString('dropping is the step nothing records', $checklist);
        // The asymmetry is the whole of it: raising a candidate costs a reading,
        // dropping one costs the author a finding and announces nothing.
        self::assertStringContainsString('dropped only where something concretely disproves it', $checklist);
        self::assertStringContainsString('neither established nor disproved is reported as open', $checklist);
        // The two dismissals that go wrong: the docblock read in place of the
        // implementation, and "unlikely" standing in for "impossible".
        self::assertStringContainsString('read the implementation it describes', $checklist);
        self::assertStringContainsString('Unlikely is not disproved', $checklist);

        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));
        self::assertStringContainsString(
            'what was raised while reading and dropped, with what dropped it',
            $skill,
        );

        // The audit is held to it too, measured rather than assumed: the two
        // recorded conformance runs write four dismissals each into the answer
        // with nothing asking for them, which is a section a reader sits
        // through and not the flood the narrower scope was written against.
        $audit = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/references/checklist.md',
        ));

        self::assertStringContainsString('## What a dropped candidate owes', $audit);
        self::assertStringContainsString('dropping is the step nothing records', $audit);
        self::assertStringContainsString('dropped only where something concretely disproves it', $audit);
        self::assertStringContainsString('neither established nor disproved is reported as open', $audit);
        self::assertStringContainsString('read the implementation it describes', $audit);
        self::assertStringContainsString('Unlikely is not disproved', $audit);
        // What the audit adds and the patch review has no use for: six surfaces
        // enumerated whole mean most of them are absent in any one package, and
        // an absence already answered as not applicable would fill this section
        // with subsystems nobody entertained.
        self::assertStringContainsString(
            'A subsystem the package does not ship never enters this list',
            $audit,
        );
        // The bar came into the checklist for a security verdict alone, and what
        // makes it a bar is who pays for a wrong dismissal.
        self::assertStringContainsString('the bar is not that subject\'s', $audit);

        $auditSkill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/SKILL.md',
        ));
        self::assertStringContainsString(
            'what was raised while reading and dropped, with what dropped it',
            $auditSkill,
        );
    }

    #[Test]
    public function aReviewNamesTheSuitesItDidNotRun(): void
    {
        // Three REVIEW-03 runs in a row reported green suites and named none of
        // the ones `typo3_test_run_guide` had returned beside them, at three
        // skill lengths including the shortest — so the rule was present and
        // delivered every time. It read as a ban on claiming an unrun suite
        // passed, and an omission claims nothing, which is why every run was
        // compliant with the sentence and none with the demand. The rewrite
        // makes it an act with an object and puts the omission itself in the
        // example (`D-SKL-009`).
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        self::assertStringContainsString(
            '**It then writes out, by name, the suites on that list it did not run.**',
            $skill,
        );
        // The consequence names what the omission does to the reader, which the
        // banned sentence never said.
        self::assertStringContainsString('read as a finished verification', $skill);
        // And it ties the omission to the claim the reader already rejects.
        self::assertStringContainsString(
            'an unnamed suite is the same sentence with the words taken out',
            $skill,
        );

        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));
        self::assertStringContainsString(
            'answered with both halves: what ran, and which of the suites the guide returned nobody started',
            $checklist,
        );
    }

    #[Test]
    public function aPrecedentIsListedByTypeAndVersionBeforeItIsAskedForInWords(): void
    {
        // `feedback/2026-08-01-115716` credits `typo3_changelog_lookup` with the
        // decisive finding of that review, and `feedback/2026-08-01-115112`,
        // filed four seconds earlier by the same session, reports that the same
        // lookup could not reach it and that grepping `Documentation/Changelog`
        // did. Re-run from that checkout on 2026-08-03: `getTemporaryImageWithText`
        // reaches nothing, the session's own `GifBuilder placeholder preview
        // thumbnail` at version 15 reaches nothing, and `image generation`
        // reaches `13.0 Breaking-101955` alone, in one call — the matcher runs
        // over the file name, and the removed method is in a list inside the
        // file (`D-ANS-030`). So the step this order opens on is the one the
        // feedback got wrong, and an order that opens with a miss in the case
        // the review needed it would ship that miss into somebody's project.
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        // The words are the second step and not the first, because both reviews
        // that followed this bullet lost the entry their finding turned on to
        // them and found it by hand: `feedback/2026-08-08-224429` asked
        // `stdWrap override` and settled the review with `ls` over
        // `Documentation/Changelog/13.4.x`, where `type: important` and
        // `version: 13.4` with no query at all returns the same 20 entries in
        // one call, titles included (`D-SKL-029`).
        self::assertStringContainsString(
            '**List the kind before you search for words: `type` and `version`, and no query at all.**',
            $skill,
        );
        // Which line to list, since a filter set to the branch under review is
        // the one the paragraph below forbids.
        self::assertStringContainsString('the line the precedent would sit on', $skill);
        // And the second bound, without which a major still collecting entries
        // answers with a page of its own cap. Read in `.checkouts/14.3` on
        // 2026-08-09: 14.0 through 14.3.x hold 99 breaking and 34 important
        // entries against a `limit` that caps at 50.
        self::assertStringContainsString('holds more of a type than one answer carries', $skill);
        self::assertStringContainsString(
            '**Ask it in the words the entry is titled in, not in the identifier the diff removes.**',
            $skill,
        );
        // Why the identifier is what the reviewer is holding, and where it
        // actually sits in the entry.
        self::assertStringContainsString('carries the identifiers in a list inside the file', $skill);
        // The empty answer is the trap: it reads as "no precedent exists" from
        // both the identifier and the version filter, and it is neither.
        self::assertStringContainsString('coming back empty has established nothing', $skill);
        self::assertStringContainsString(
            'a precedent is filed under the version it landed in',
            $skill,
        );
        // And the source that answered when the lookup did not, which the server
        // cannot reach itself.
        self::assertStringContainsString('`Documentation/Changelog`, which this server does not read', $skill);
        self::assertStringContainsString('Say which of the two answered', $skill);
        // What a listed entry was is the reading a precedent argument is made
        // of, and the tracker does not answer it. Measured on 2026-08-09 over
        // 128 entries of all four types from `13.4.x` and `14.0`–`14.3`: the
        // Forge tracker and the keyword of the commit that added the entry
        // agree on 101 of them, and on 20 of the 26 important ones. It misses in
        // both directions — #103140 is filed as a Feature and was added by
        // `[BUGFIX] Allow to configure RateLimiters in message consumer`,
        // #105653 as a Bug and was added by a `[TASK]` — so `D-SKL-029`'s
        // assumption does not hold and its third **Wrong if** is what the step
        // now says.
        self::assertStringContainsString(
            '**What kind of change an entry came out of is two readings rather than one.**',
            $skill,
        );
        self::assertStringContainsString('The two disagree in both directions', $skill);
        self::assertStringContainsString('`git log --diff-filter=A` over the entry\'s own file', $skill);
        // #108604 and #109585 are important entries of a security release, and
        // the tracker answers 401 for both.
        self::assertStringContainsString('The issue behind a security entry is not public', $skill);
    }

    #[Test]
    public function anIdiomPrecedentIsSweptFromTheCheckoutRatherThanLookedUp(): void
    {
        // `feedback/2026-08-03-144457` reviewed a core commit and settled three
        // questions by grep. Two of them name a class, which the base's step
        // after the lookups reaches; "is this idiom established in core" reaches
        // nothing, because it is a sweep for call sites with no class to start
        // at — and a reviewer asks it of every alternative it proposes, which is
        // the count that feedback carries. The boundary was already written, in
        // `knowledge/server-scope.json`, where `typo3_server_scope` alone
        // returns it and no review order calls it (`D-SKL-004`).
        //
        // Read in `.checkouts/main` on 2026-08-03 rather than recalled: the lazy
        // autowire attribute stands at `core/Classes/Site/Set/SetRegistry.php:43`,
        // `form/Classes/EventListener/DataStructureIdentifierListener.php:68` and
        // `form/Classes/Domain/Configuration/PersistenceConfigurationService.php:41`,
        // while `knowledge/hints/di.json` carries the plain attribute and
        // nothing about the lazy form. So no lookup here answered it and the
        // checkout did.
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        // An act with an object, because the same position has already cost a
        // rule that was present and read past (`D-SKL-009`).
        self::assertStringContainsString(
            '**Sweep the checkout for the call sites before proposing an alternative.**',
            $skill,
        );
        // The bar that makes it a step of the review rather than a nicety.
        self::assertStringContainsString('needs precedent rather than taste', $skill);
        // Why the base's own step does not reach it, said where a reviewer who
        // has just read the base would otherwise apply it anyway.
        self::assertStringContainsString(
            'starts at the class that implements a behaviour; this question has none',
            $skill,
        );
        self::assertStringContainsString('PHP source as code is outside what this server reads', $skill);
        // What the answer is, so that "I checked" is not the report.
        self::assertStringContainsString('the call sites at their paths and lines', $skill);
        self::assertStringContainsString(
            'one is a coincidence and a spread across system extensions is a convention',
            $skill,
        );
        // And the identifier stays out: the attribute this was measured on is a
        // core fact, and a published skill is what no release of this server
        // corrects.
        self::assertStringNotContainsString('Autowire', $skill);
    }

    #[Test]
    public function obligationsThatShareADocumentAreOneRuleQuery(): void
    {
        // The skill told a reviewer to ask per obligation because "a query that
        // names two reaches neither", and the REVIEW-03 run of 2026-08-03
        // followed it: `changelog entry` then `breaking change`, both returning
        // `## Breaking Changes` and `## Changelog Files` whole. Re-run the same
        // day, `breaking change changelog entry` returns both at 100% plus the
        // three sections the two calls added between them, while four subjects
        // in one query drop `## Deprecations` below `Documents::MIN_COVERAGE`.
        // So the count was never the axis and the length is (`D-SKL-011`).
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        self::assertStringContainsString(
            '**Obligations that share a document are one call.**',
            $skill,
        );
        // The pair it was measured on, so the rule is checkable rather than
        // taken on the skill's word.
        self::assertStringContainsString(
            '`breaking change changelog entry` returns both sections whole',
            $skill,
        );
        // And the bound, which is what the withdrawn sentence was reaching for.
        self::assertStringContainsString('Length is the limit rather than the count', $skill);
        self::assertStringContainsString(
            'a query naming four obligations dropped the deprecation section',
            $skill,
        );
        // The claim about the ranker that moved under the skill is gone.
        self::assertStringNotContainsString('a query that names two reaches neither', $skill);
    }

    #[Test]
    public function aReviewReadsTheReviewThePatchIsAlreadyIn(): void
    {
        // Both tools existed and no skill routed to either. The third recorded
        // REVIEW-03 run reviewed change 95070 without asking for it: the issue
        // it resolves is called "Avoid calling ImageService methods - part 2",
        // carries no description, and its part 1 is already in origin/main —
        // so the run judged a series as a patch standing alone, and its own
        // report closed by saying the issue had not been fetched (`D-SKL-008`).
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        self::assertStringContainsString('## What the project already says about this patch', $skill);
        self::assertStringContainsString('`typo3_forge_lookup` with the issue number', $skill);
        self::assertStringContainsString('`typo3_gerrit_lookup` with the `Change-Id` the message carries', $skill);
        // The issue is where a series announces itself, which is what makes the
        // set rule reachable at all.
        self::assertStringContainsString('an issue calling itself a part tells you the patch is not', $skill);
        // An unanswered comment is the finding this step exists for.
        self::assertStringContainsString('nobody answered is a finding of its own', $skill);
        // The trap, measured on 2026-08-03 rather than assumed: the Forge issue
        // and the Gerrit change are different numbers, and swapping them does
        // not fail. `typo3_gerrit_lookup` given 95070 as an issue answers with
        // change 70860, a MERGED acceptance-test cleanup from 2021, because it
        // searches commit messages for the string; `typo3_forge_lookup` given
        // the same number answers with issue 95070, a closed 11.4 task. Both
        // report `answered` and neither payload says the number was the other
        // one's, so a review can read a 2021 change believing it read this one.
        self::assertStringContainsString('Both arguments come out of the commit message', $skill);
        self::assertStringContainsString(
            'carries the subject of the commit under review, or the number was wrong',
            $skill,
        );
        // The Change-Id is the one that survives an amend, so it is what a
        // review of a patch that will come back is told to hold.
        self::assertStringContainsString('still names it after an amend', $skill);
        // An answer of nothing is a result rather than a silence, which is what
        // keeps a not-yet-pushed patch from reading as unchecked.
        self::assertStringContainsString('an answer of nothing is a result', $skill);
        // Reading only: the server holds no credential and the review does not
        // vote on the caller's behalf.
        self::assertStringContainsString('Voting, commenting and uploading stay with the person', $skill);

        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));
        self::assertStringContainsString('**The review this patch is already in.**', $checklist);
        self::assertStringContainsString('The issue is read for that, not inferred from the message', $checklist);
    }

    /**
     * A triage looks for the test the core already wrote and switched off.
     *
     * `D-KNW-064` priced the tool this could have been and did not build it: a
     * core checkout carries nine such assertions, in four files of one
     * subsystem, which is one grep rather than an index. What the skill owes
     * instead is the step and the pattern — and the warning that
     * `markTestSkipped` is mostly the machine, since fifty of those against two
     * about a defect is a ratio that sends a session reading the wrong fifty.
     */
    #[Test]
    public function aTriageLooksForTheAssertionTheSuiteAlreadyCarries(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/SKILL.md',
        ));

        self::assertStringContainsString(
            'look for the one the core already wrote and switched off',
            $skill,
        );
        self::assertStringContainsString('grep -rn "@todo" <sysext>/Tests', $skill);
        self::assertStringContainsString('`markTestSkipped` is a different thing', $skill);
        // Before the test-writing it replaces, or it is advice arriving after
        // the work it would have saved.
        self::assertLessThan(
            strpos($skill, 'That test is a throwaway until a patch adopts it'),
            strpos($skill, 'look for the one the core already wrote'),
            'the search stands after the test it exists to avoid',
        );
    }

    /**
     * The triage skill's description promises "deciding whether a report is
     * worth taking on, and for saying what a maintainer would need before it
     * can move", and its body stopped at the verdict.
     *
     * The session that reported it had worked the procedure out and would work
     * it out again: read the revert reason out of the related issue, grep for
     * production callers of the method the reverted patch touched, and
     * establish whether the path named in the revert still routes through it.
     * On Forge 15984 that was one caller outside its own class, and the path
     * that blew up in 2012 no longer touches the method at all.
     *
     * What makes it a step rather than a note is that the trigger is in the
     * answer now: a relation carries its subject and `reviews` names the
     * changes the journal mentions, so a merged-then-reverted history is
     * visible before the checkout is opened (`R-ANS-029`).
     */
    #[Test]
    public function aTriageSaysWhatThePreviousAttemptCostBeforeItHandsOver(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/SKILL.md',
        ));

        // The general form, which is the part that transfers off this issue.
        self::assertStringContainsString(
            'A reverted core fix becomes re-attemptable when the shared consumer that made it expensive has been '
                . 'rebuilt, or when the caller set has shrunk to the one site the fix needs',
            $skill,
        );
        // The trigger, read off the answer rather than out of the reading.
        self::assertStringContainsString('A relation marked `precedes` or `duplicates` carries its subject', $skill);
        self::assertStringContainsString('`reviews` names every change the journal mentions', $skill);
        // And the boundary, because the step sits directly above the handoff
        // and a skill that starts designing here has taken the next one's work.
        self::assertStringContainsString('It is not a design and not a patch', $skill);
        // What the attempt is reached by. A second lookup of a change the issue
        // answer already carried returns its state a second time and never the
        // diff the step says it is after, so the step routes to the page the
        // fetch is on instead (`D-SKL-028`).
        self::assertGreaterThan(
            strpos($skill, 'What a previous attempt cost'),
            strpos($skill, 'typo3://guides/core/contribution/gerrit-workflow'),
            'the step does not route to the page the fetch is on',
        );
        self::assertLessThan(
            strpos($skill, 'Where the triage ends and the patch begins'),
            strpos($skill, 'What a previous attempt cost'),
            'the step stands after the handoff it feeds',
        );
    }

    /**
     * The three rules that decide whether a measurement measured anything, and
     * the sentence that sends a reproduction to be shown red first.
     *
     * Five reports credit them and none of them rested on anything:
     * `2026-08-08-224426`, which reported every suite result with what it
     * inspected rather than with the SUCCESS banner and wrote its functional
     * test red before touching `GifBuilder.php`, and `2026-08-05-033954`,
     * `2026-08-07-065401`, `2026-08-07-130037` and `2026-08-07-233418` in the
     * archive. The third bullet is itself the answer to the last of those, and
     * a rewrite could have taken all four out without a failure.
     *
     * The block is one point in three costumes — an operation that silently did
     * nothing, followed by a result that reads as evidence — so it is held as
     * one test. `2026-08-08-224426` credits `references/base.md` for two of
     * them; the installed copy is byte-identical to `skills/base.md` and
     * carries neither, which is why they are looked for here.
     */
    #[Test]
    public function aTriageIsHeldToWhatItsMeasurementsActuallyMeasured(): void
    {
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/SKILL.md',
        ));

        self::assertStringContainsString('It has to be seen failing before it is believed', $skill);
        self::assertStringContainsString('A green that ran over no files is not a green.', $skill);
        self::assertStringContainsString(
            'confirm it inspected something — the count of tests or files it names',
            $skill,
            'the green is refused without what makes one real',
        );
        self::assertStringContainsString('Once the change is committed, `git stash` measures nothing.', $skill);
    }

    /**
     * `R-SKL-020`. Both core workflows end in public — one at a tracker entry,
     * the other at a pushed change — and neither carried a branch for the case
     * where the finding is a security defect. A workflow that does not name that
     * case is one whose ordinary path runs through it: the failure is not that
     * the session judges wrong, it is that nothing asks the question, so the
     * finding is disclosed by the step that was meant to report it.
     *
     * What the process actually asks for is read rather than recalled, and it is
     * in the corpus rather than in either file: `SECURITY.md` is identical on
     * 12.4, 13.4, 14.3 and `main` apart from its supported-version matrix, and
     * it names one address for a core defect and an extension defect alike. That
     * address is what a published skill may not carry — a contact route is the
     * fact that moves, and a copy in somebody else's project is corrected by no
     * release of this server.
     */
    #[Test]
    public function aWorkflowThatEndsInPublicationStopsAtAVulnerability(): void
    {
        foreach (['typo3-core-issue-triage', 'typo3-core-patch-development'] as $name) {
            $skill = self::flat((string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md'));

            // Asked of every finding, because a rule that fires on how alarming
            // something looks fires on the findings that were never the danger.
            self::assertStringContainsString(
                '## Where the finding is a vulnerability',
                $skill,
                $name . ' names no stopping point for a finding that is a vulnerability',
            );
            self::assertStringContainsString(
                'happens to look alarming',
                $skill,
                $name . ' leaves the question to whether a finding looks alarming',
            );
            // The crossing in the sense `R-SKL-003` fixes: the verified stopping
            // point named, and the public step not taken.
            self::assertStringContainsString(
                'The stopping point is the verified reproduction',
                $skill,
                $name . ' stops without saying what has been established',
            );
            // And where it goes instead, as a call rather than as a fact.
            self::assertStringContainsString(
                '`documentId="any/security/reporting-a-vulnerability"`',
                $skill,
                $name . ' names no procedure for the report it hands over to',
            );
            self::assertStringNotContainsString(
                'security@',
                $skill,
                $name . ' carries the contact route the lookup owns',
            );
        }

        self::assertContains(
            'any/security/reporting-a-vulnerability',
            array_column(Documents::documents(), 'id'),
            'both skills route to a procedure this server does not carry',
        );

        // The triage judges, so the question is one of its verdicts rather than
        // a paragraph beside them — and it is the one asked first, because it
        // decides where the answer goes rather than what it says.
        $checklist = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-issue-triage/references/checklist.md',
        ));
        self::assertStringContainsString('## A security defect', $checklist);
        self::assertStringContainsString('The seventh is asked before the other six', $checklist);
        self::assertStringContainsString(
            'decides where the answer goes rather than what it says',
            $checklist,
        );
        // The trap this verdict has and the six others do not: waiting until the
        // finding is certain is itself the disclosure.
        self::assertStringContainsString('A finding that might be exploitable is one the team rates', $checklist);
    }

    /**
     * The mirror of the one above, on the skill that writes the patch instead
     * of judging it. `D-SKL-008` put both calls into the review and recorded, as
     * its own evidence, that development routed to neither — and the session
     * that can still be spared the work is the one about to write the code
     * (`D-SKL-010`).
     */
    #[Test]
    public function theAssessmentBeforeAPatchReadsTheIssueAndTheReviewServer(): void
    {
        // Four sessions in one week ran both calls by hand
        // (`feedback/2026-08-02-144511`, `144848`, `145217`, `145230`), and the
        // fifth filed the assessment method it had to rediscover
        // (`feedback/2026-08-02-145128`). Measured again through this branch's
        // server on 2026-08-03: `typo3_forge_lookup` with issue 105403 answers
        // `Under Review` at `next-patchlevel` against the "closing as lack of
        // feedback" the notes carry, and its relations name #99203, whose entry
        // is what gave the resource ViewHelper its cache-busting argument. The
        // route the feedback took to that fact was a Forge search on the feature
        // wording, and a `typo3_changelog_lookup` for it misses, because the
        // entry is titled for something else (`D-ANS-030`).
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-development/SKILL.md',
        ));

        self::assertStringContainsString('`typo3_forge_lookup` with the issue number', $skill);
        // What the description does not carry, which is the reason the call is
        // here rather than a reading of the report.
        self::assertStringContainsString('status and target version as they stand today', $skill);
        self::assertStringContainsString('**relations**, which are one hop from the change that introduced', $skill);
        self::assertStringContainsString('**notes**, where a maintainer said why', $skill);

        // `typo3_gerrit_lookup` with issue 105403 answers empty from a checkout
        // that holds a patch for exactly that issue, because it was pushed
        // unlisted — so the empty answer is about the review server and not
        // about the world (`D-ANS-033`), and the order that reads it otherwise
        // has been misled by a true statement.
        self::assertStringContainsString('`typo3_gerrit_lookup` with the same issue number', $skill);
        self::assertStringContainsString('**before any code is written**', $skill);
        self::assertStringContainsString('nothing public names the issue rather than that nobody has fixed it', $skill);
        // Before the code, because the outcome that cancels the work is worth
        // nothing once the work is done.
        self::assertLessThan(
            strpos($skill, '## Make the change'),
            strpos($skill, 'typo3_gerrit_lookup'),
        );

        // The three rungs. Each one changed what the filing session concluded,
        // and none is carried by the order the skill had before.
        self::assertStringContainsString('check that blocker against what the branch has today', $skill);
        self::assertStringContainsString(
            'The argument that carries a bugfix is the same inconsistency inside one version',
            $skill,
        );
        self::assertStringContainsString('Establish the blast radius here rather than meeting it while working', $skill);
        // It is an assessment step because it decides the change type, which
        // everything downstream is built on.
        self::assertStringContainsString('a change that has to announce itself, or a breaking one', $skill);
    }

    #[Test]
    public function aSurfaceReportedAsAssessedNamesWhatWasRead(): void
    {
        // The third disposition was the one that certified itself. Reporting a
        // finding and dropping a candidate both cost a reading somebody can
        // check; assessed cost one word, and a surface somebody glanced at read
        // exactly like one somebody worked through (`D-SKL-007`).
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));

        self::assertStringContainsString('A review disposes of a thing in three ways', $checklist);
        self::assertStringContainsString('all three carry what backs them', $checklist);
        // Unassessed is the cheaper honest answer and costs the same line, which
        // is what keeps the demand from being answered with a fabricated one.
        self::assertStringContainsString('where the reading did not happen the word is unassessed', $checklist);
        // The clean verdict in the rubric is held to the same bar as a finding,
        // and says so where a reader ranking one would look.
        self::assertStringContainsString(
            'It names what was read, for the same reason a finding names what it collides with',
            $checklist,
        );
    }

    #[Test]
    public function aReviewSurfaceNamesTheLookupThatCanAnswerIt(): void
    {
        // The surface was named for two things and listed one: every item under
        // **Documentation and changelog** was the changelog's, so a session
        // disposing of it found no manual in it and no lookup holding one. It
        // shipped the claim that a stdWrap property's page lives outside the
        // repository as the reason nothing was owed, and the page is one call
        // away (`D-SKL-030`).
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));

        self::assertStringContainsString('typo3_documentation_lookup', $checklist);
        // The half that is in the checkout and the half that is not, because the
        // two are disposed of differently and the surface names one word.
        self::assertStringContainsString(
            'a system extension\'s own `Documentation/` is in this checkout and changes in the patch',
            $checklist,
        );
        // What the shipped claim got wrong, in the words that answer it.
        self::assertStringContainsString(
            'outside is where the follow-up goes, not a reason none is owed',
            $checklist,
        );
        self::assertStringContainsString(
            'A review said the wording lived elsewhere and concluded that no documentation change was owed',
            $skill,
        );
        // The obligation itself stays with the document that owns it, so the
        // skill routes to it instead of restating what a patch owes a manual.
        self::assertStringContainsString('`typo3_rule_lookup` asked for `documentation`', $skill);
    }

    #[Test]
    public function aFindingSaysWhetherThePatchIntroducedIt(): void
    {
        // A finding about a line the diff only moved past sends the author to
        // repair something they did not change, and a report that does it reads
        // exactly like one that meant to (`D-SKL-007`).
        $checklist = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/references/checklist.md',
        ));

        self::assertStringContainsString('Every finding carries five things', $checklist);
        self::assertStringContainsString('**whether this patch introduced it**', $checklist);
        self::assertStringContainsString('What the patch did not introduce is reported in those words', $checklist);
        // The other half of attributing a finding: a diff is the weakest
        // evidence there is about who reaches a path, so what it shows may raise
        // a rank and never lower one.
        self::assertStringContainsString('raises a rank and never lowers one', $checklist);

        // A patch that is one of a set is read against the end of the set, by
        // opening the later patch rather than by believing a message about it.
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-review/SKILL.md',
        ));
        self::assertStringContainsString('read against the state at the end of the set', $skill);
        self::assertStringContainsString('rather than of what a message promises about it', $skill);
    }

    #[Test]
    public function aRuleQuotedAtTheIssueIsVerifiedInTheCheckout(): void
    {
        // `feedback/2026-08-02-144814`: Forge #105403 was answered with "you
        // *must not* use f:image for anything but FAL resources", and the
        // session repeated it as correct in its own assessment until the user
        // asked what it made of the statement. The checkout says something
        // weaker on 12.4, 13.4, 14.3 and `main` alike — `ImageViewHelper`'s own
        // first example is an `EXT:` path, `SvgImageViewHelperTest` renders that
        // form with width, height, `crop` and `fileExtension`, and what both
        // docblocks warn about is stability rather than support (`D-KNW-043`).
        // The instructions sent a session to the checkout for what changed, for
        // the branch and for whether a path or an identifier still exists. A
        // behavioural rule was on none of those lists, and it is the claim that
        // closed the issue.
        $skill = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-development/SKILL.md',
        ));

        // An act with an object rather than a disposition to be sceptical, which
        // is what the same position already cost once (`D-SKL-009`).
        self::assertStringContainsString('**Verify in the checkout every rule the issue quotes.**', $skill);
        // The claim is the same kind of thing as the two the base already sends
        // to the checkout, which is what makes it checkable there at all.
        self::assertStringContainsString('a claim, the way a path or an identifier is', $skill);
        // Named surfaces, because the docblock and the test are where the two
        // neighbouring ViewHelpers of that report differ.
        self::assertStringContainsString("the class it names, its docblock and the core's own tests", $skill);
        // The three strengths, and the report saying which one it found.
        self::assertStringContainsString('say which of the three carries the rule', $skill);
        self::assertStringContainsString('Carry it at the strength its own source puts on it', $skill);
        // Before the reproduction: a rule read as a prohibition ends the
        // assessment before anything is reproduced, which is what happened.
        self::assertLessThan(
            strpos($skill, '**Reproduce against the branch you are fixing**'),
            strpos($skill, '**Verify in the checkout every rule the issue quotes.**'),
        );
    }

    #[Test]
    public function aClosedIssueIsReadForWhatTheConversationDecided(): void
    {
        // `feedback/2026-08-02-144800` is a session that read Forge #105403 as
        // settled because a maintainer had closed it, called the report
        // "teilweise valide", and committed a better exception message — a
        // politer way of telling the reporter they were holding it wrong. The
        // user rejected that framing twice before the work moved. Both misreads
        // were of the same two things: a closure for lack of feedback over
        // sixteen months, which says what the exchange did rather than what the
        // need is worth, and a maintainer's alternative that drops width, height
        // and cropping, which is why the reporter had wrapped one ViewHelper in
        // the other. The step already said the comments can be product judgement
        // and the session read it, so what is added is stated the way
        // `D-SKL-009` holds a rule that gets read and not followed: an act with
        // an object, producing something the assessment carries.
        $skill = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-development/SKILL.md',
        ));

        self::assertStringContainsString(
            '**Read the closure reason and the target version for what the conversation decided, '
            . 'and write that down rather than what the report is worth.**',
            $skill,
        );
        // The reading that was actually available in the report: silence is
        // evidence about the answer as much as about the reporter.
        self::assertStringContainsString(
            'as consistent with an answer the reporter could not use as with the reporter giving up',
            $skill,
        );
        self::assertStringContainsString('a closed issue is not a finding that the need is absent', $skill);

        self::assertStringContainsString(
            '**Where a comment names an alternative, write out what the alternative drops '
            . 'against what the reported code did.**',
            $skill,
        );
        // What the writing-out is over, so that "it is not the same thing" is
        // not an answer to it.
        self::assertStringContainsString(
            'Name the arguments and the behaviour the reported code had and the replacement does not',
            $skill,
        );
        self::assertStringContainsString('closes an issue only if it does the same work', $skill);

        // The step is the wording of the reading and not a section of its own,
        // so it stays inside "Establish the issue before you believe it" and
        // ahead of the reproduction.
        $establish = strpos($skill, '## Establish the issue before you believe it');
        $closure = strpos($skill, '**Read the closure reason and the target version');
        $reproduce = strpos($skill, '**Reproduce against the branch you are fixing**');
        self::assertNotFalse($establish);
        self::assertNotFalse($closure);
        self::assertNotFalse($reproduce);
        self::assertLessThan($closure, $establish);
        self::assertLessThan($reproduce, $closure);
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
        $page = (string) file_get_contents(Paths::root() . '/documentation/contributing/writing-a-skill.rst');

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
     * ones [documentation/contributing/writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst)
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

    /**
     * A skill copied out of this repository arrives without the server it
     * routes to. `references/base.md` is written at publication rather than
     * kept here, so the first instruction of a copied skill is a link to
     * nothing and every lookup under it is a tool the session does not have.
     * The guard in the base is written for a session whose tools do not answer;
     * this is a session whose base was never delivered, and nothing in the file
     * it holds says so.
     *
     * `compatibility` is where the standard has a skill state an environment
     * requirement — optional, one to 500 characters, read on agentskills.io on
     * 2026-08-08. One line, and the same line in every skill, because what it
     * states is a fact about this package rather than about a workflow.
     *
     * It is read out of parsed front matter rather than matched in the file,
     * because a field a reader cannot parse is stated to nobody: three
     * descriptions carried an unquoted `: ` and broke the whole block for
     * every reader but this repository's own patterns.
     */
    #[Test]
    public function everySkillSaysWhichServerItNeeds(): void
    {
        $stated = [];
        foreach (self::skills() as $name => $skill) {
            $compatibility = self::frontMatter($name, $skill)['compatibility'] ?? null;
            self::assertIsString($compatibility, $name . ' does not say which server it needs');
            $stated[$name] = $compatibility;
        }
        self::assertNotSame([], $stated);

        $one = (string) reset($stated);
        foreach ($stated as $name => $compatibility) {
            self::assertSame($one, $compatibility, $name . ' says it in words of its own');
            // The standard's own bound, and a reader refuses the file over it
            // rather than truncating the line.
            self::assertLessThanOrEqual(500, strlen($compatibility), $name . ' says more than the field holds');
            self::assertStringContainsString(
                'typo3-dev-companion install',
                $compatibility,
                $name . ' names the server without saying how it is installed',
            );
            self::assertStringContainsString(
                'references/base.md',
                $compatibility,
                $name . ' leaves out the file a copied tree does not carry',
            );
        }
    }

    /**
     * The front matter carries the standard's fields and nothing else.
     *
     * `ALLOWED_FIELDS` in the reference validator is exactly `name`,
     * `description`, `license`, `compatibility`, `metadata` and
     * `allowed-tools`, and a key outside them is an error rather than a
     * warning: "Unexpected fields in frontmatter" — read on 2026-08-08. So a
     * key invented here is not a field a client ignores, it is a file a client
     * refuses, in somebody else's project where no release of this server
     * corrects it.
     *
     * The set is closed rather than checked one field at a time because the
     * failure is the key nobody thought about. `status` was the one that got
     * in, and it got in beside a test that read one field out of the block and
     * let every other one through.
     */
    #[Test]
    public function everyFrontMatterFieldIsOneTheStandardDefines(): void
    {
        $defined = ['name', 'description', 'license', 'compatibility', 'metadata', 'allowed-tools'];

        foreach (self::skills() as $name => $skill) {
            $matter = self::frontMatter($name, $skill);
            self::assertSame(
                [],
                array_diff(array_keys($matter), $defined),
                $name . ' carries a field the standard does not define',
            );

            // The one field the standard leaves open is a map of strings, so a
            // client that reads it gets what this server wrote there.
            $metadata = $matter['metadata'] ?? [];
            self::assertIsArray($metadata, $name . ' has a metadata field that is not a mapping');
            foreach ($metadata as $key => $value) {
                self::assertIsString($key, $name . ' has a metadata key that is not a string');
                self::assertIsString($value, $name . ' has a metadata value that is not a string');
            }
        }
    }

    /**
     * A draft is a skill nobody may load yet, and its own front matter is what
     * says so: `metadata` carrying this server's status key at `draft`.
     *
     * It sits under `metadata` because the standard defines six fields and the
     * reference validator refuses a frontmatter key outside them, so a top-level
     * `status:` made the one file that must not be published the one file that
     * does not validate. `metadata` is what the standard leaves to a client, and
     * the key is namespaced because it asks for that.
     *
     * That line is the decider rather than a label beside one. `Installer` used
     * to carry a list of the published names, which is a second place one fact
     * lives, and the two disagree in the direction nobody notices — a reviewed
     * draft added to the list with the marker still in its file is published and
     * reads as unfinished, and one dropped from the list while its file says
     * nothing reads as ready and can be loaded by nobody. So publishing is one
     * edit now, and this holds the derivation that made it one: every directory
     * that declares itself a draft is published to nobody, and every one that
     * does not is published.
     */
    #[Test]
    public function aDraftSaysSoInItsOwnFrontMatter(): void
    {
        $published = Installer::skills();
        self::assertNotSame([], $published, 'nothing at all is published');

        foreach (self::skills() as $name => $skill) {
            $metadata = self::frontMatter($name, $skill)['metadata'] ?? [];
            $declared = is_array($metadata)
                && ($metadata['typo3-dev-companion-status'] ?? null) === 'draft';
            self::assertSame($declared, Installer::draft($skill), $name . ' is read as a draft two ways');
            self::assertSame(
                !$declared,
                in_array($name, $published, true),
                $declared
                    ? $name . ' says it is a draft and is published anyway'
                    : $name . ' says nothing and is published to nobody',
            );
            self::assertSame(
                $declared,
                in_array($name, Installer::drafts(), true),
                $name . ' is one of the two sets and has to be the other',
            );
        }

        // The two are the whole directory and share nothing. `--drafts` adds
        // the second set to the first, so a skill in both would be published
        // twice and one in neither would be installable by no command at all.
        self::assertSame([], array_intersect($published, Installer::drafts()));
        $both = [...$published, ...Installer::drafts()];
        sort($both);
        self::assertSame(array_keys(self::skills()), $both);
    }

    /**
     * What the declaration is, on a body rather than on the directory.
     *
     * The test above holds the derivation and can only see the shapes the
     * directory happens to contain, which today is no draft at all — so the
     * reader itself is held here, on the shapes a file can take. Three of them
     * are the parser's gain over the pattern this used to be: a quoted value
     * and an inline mapping are the same declaration to every client and were
     * not to a regex, and front matter no parser can read is not a declaration
     * at all.
     */
    #[Test]
    #[DataProvider('theShapesAFrontMatterCanTake')]
    public function aDraftIsWhatDeclaresItselfOneUnderThisServersKey(bool $draft, string $body): void
    {
        self::assertSame($draft, Installer::draft($body));
    }

    /** @return array<string, array{0: bool, 1: string}> */
    public static function theShapesAFrontMatterCanTake(): array
    {
        $matter = static fn(string $lines): string => "---\nname: x\ndescription: y\n" . $lines . "---\n\nThe body.\n";

        return [
            'the declaration' => [true, $matter("metadata:\n  typo3-dev-companion-status: draft\n")],
            'its value quoted' => [true, $matter("metadata:\n  typo3-dev-companion-status: \"draft\"\n")],
            'the mapping written inline' => [true, $matter("metadata: {typo3-dev-companion-status: draft}\n")],
            'the key at another value' => [false, $matter("metadata:\n  typo3-dev-companion-status: published\n")],
            // The two spellings that came before this one. Neither may hold a
            // skill back now, because neither is a field a client reads.
            'the top-level status this replaced' => [false, $matter("status: draft\n")],
            'the generic key under metadata' => [false, $matter("metadata:\n  status: draft\n")],
            'no metadata at all' => [false, $matter('')],
            'the declaration in the body' => [
                false,
                "---\nname: x\n---\n\nmetadata:\n  typo3-dev-companion-status: draft\n",
            ],
            'front matter no parser reads' => [false, "---\nname: x\ndescription: a: b\n---\n\nThe body.\n"],
            'no front matter' => [false, "# A skill\n\nThe body.\n"],
        ];
    }

    /**
     * The description is the only part of a skill read before it is chosen, so
     * a domain named by one of its sides leaves the other side reading as
     * somebody else's work — and the body that covers it is never loaded. This
     * is the one collision that has been measured; the shape it stands for is
     * written down in the authoring contract rather than assertable over the
     * directory, because which sides a skill owns is not in the file.
     */
    #[Test]
    public function aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement(): void
    {
        // A session in `site-new` wrote a custom backend preview for a content
        // element on 2026-08-01, activated no skill and called no tool, and did
        // the work by reading vendor code — a day after the entry point reached
        // the instructions, so the channel that failed was the descriptions.
        // The task matched one word in each: the content-element skill opened on
        // "frontend content elements" with `previews` ninth of the eleven things
        // it listed, and the module skill promised "backend UI work". It belongs
        // wholly to the first, which covers it in as many words and which
        // `knowledge/task-intents.json` has matched on `backend preview` since
        // 51e5e5a.
        $element = self::description('typo3-content-element-development');
        self::assertStringNotContainsString('frontend content elements', $element);
        self::assertStringContainsString('backend preview', $element);
        self::assertStringContainsString('page module', $element);

        // And the other half of the collision: the module skill claimed the
        // whole backend and owns one room of it.
        $module = self::description('typo3-backend-module-development');
        self::assertStringNotContainsString('other TYPO3 backend UI work', $module);
        self::assertStringContainsString('is not a module', $module);
        // The crossing in its body says the same, or the file contradicts its
        // own description in somebody else's project. Read flat, because what is
        // asserted is the sentence and `prose:format` decides where its lines
        // break.
        self::assertStringContainsString(
            'before implementing a content element or its backend preview',
            self::flat((string) file_get_contents(
                Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
            )),
        );
    }

    /**
     * The same shape, measured here rather than borrowed. A session asked to
     * review change 95179 in a git worktree read `typo3-core-patch-checkout`'s
     * description — every verb of it moving the branch it stands on — as
     * another skill's case, and fetched and created the worktree by hand with
     * the review skill's routing line naming this one in front of it
     * (`feedback/2026-08-08-224413`, `D-SKL-024`). A step clause does not only
     * summarise the body, it narrows what the description names, and the budget
     * trim of the same day is what wrote this one back after a sweep had cut it
     * (`D-SKL-026`). So both halves are held: the word a user types, and the
     * body that has to answer once it did.
     */
    #[Test]
    public function aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout(): void
    {
        $checkout = self::description('typo3-core-patch-checkout');
        self::assertStringContainsString('git worktree beside it', $checkout);
        self::assertStringNotContainsString('find the change, fetch the patch set', $checkout);

        $body = self::flat((string) file_get_contents(
            Paths::root() . '/skills/typo3-core-patch-checkout/SKILL.md',
        ));
        self::assertStringContainsString('## Two ways in', $body);
        // What the worktree path costs and the branch path does not, which is
        // the half a trigger alone would route a task into a body without.
        self::assertStringContainsString('no suite runs in it until they are installed there', $body);
    }

    /**
     * The descriptions are read in one listing and paid for out of one budget:
     * Claude Code allows all of them together the characters in one percent of
     * the context window, and where they do not fit it drops whole descriptions
     * rather than shortening them, least-used first — which is every skill this
     * server publishes on a fresh install (`D-SKL-026`). So what is held is
     * their total, in the client's own arithmetic: `- <name>: <description>`,
     * one newline between entries.
     */
    #[Test]
    public function everyDescriptionIsWrittenToTheBudgetTheyShare(): void
    {
        // What the trim of 2026-08-08 left, with room for a rename. It is a
        // ratchet rather than a limit read off a client: how much of the budget
        // is left over is decided by the client's own bundled skills, which
        // took 5997 characters of the 6000 a 200k session had that day.
        $ceiling = 3600;

        $listing = count(self::skills()) - 1;
        foreach (array_keys(self::skills()) as $name) {
            $listing += mb_strlen($name) + 4 + mb_strlen(self::description($name));
        }

        self::assertLessThanOrEqual(
            $ceiling,
            $listing,
            'the listing costs ' . $listing . ' characters, and a client that runs out drops whole descriptions',
        );
    }

    /**
     * A skill exists for its readers once it is published, so a skill this
     * server names in an answer is one the caller can actually load
     * (`D-SKL-013`). The draft in `skills/` is not that: it is shown to
     * somebody first, and `typo3-development-installation` has been sitting
     * there since 2026-08-03 waiting for exactly that review.
     */
    #[Test]
    public function everySkillNamedInKnowledgeIsPublished(): void
    {
        $intents = json_decode(
            (string) file_get_contents(Paths::knowledgeFile('task-intents.json')),
            true,
        );
        self::assertIsArray($intents);

        $named = [];
        foreach ($intents as $intent) {
            foreach (['skill', 'skillCore'] as $key) {
                if (($intent[$key] ?? '') !== '') {
                    $named[] = [$intent['id'], $intent[$key]];
                }
            }
        }
        self::assertNotSame([], $named, 'no task routes to the skill that owns it');

        foreach ($named as [$intent, $skill]) {
            self::assertContains(
                $skill,
                Installer::skills(),
                $intent . ' routes to ' . $skill . ', which this server does not publish',
            );
        }
    }

    /**
     * The same question for the skills a tool writes rather than routes to.
     *
     * `typo3_task_guide` reads its names out of `task-intents.json`, which the
     * test above holds. `typo3_gerrit_lookup` names two in the answer it ends a
     * change lookup on (`D-SKL-038`) and `typo3_feedback_record` names one as
     * the example a session reports a skill by, and both are prose in a class:
     * a renamed skill leaves them pointing at nothing, and the first thing that
     * reads them is a session in somebody else's project.
     *
     * Read off the source rather than off a rendered answer, because the
     * answers that carry them are a review server away and an offline suite
     * would scan nothing.
     */
    #[Test]
    public function everySkillNamedByAToolIsPublished(): void
    {
        $named = [];
        foreach (Finder::create()->files()->in(Paths::root() . '/src/Tool')->name('*.php')->sortByName() as $file) {
            preg_match_all('/typo3-[a-z0-9]+(?:-[a-z0-9]+)+/', (string) $file->getContents(), $matches);
            foreach (array_unique($matches[0]) as $skill) {
                $named[] = [$file->getFilename(), $skill];
            }
        }

        // Or the scan matched nothing and the loop below holds nothing.
        self::assertNotSame([], $named, 'no tool names a skill at all');

        foreach ($named as [$tool, $skill]) {
            self::assertContains(
                $skill,
                Installer::skills(),
                $tool . ' names ' . $skill . ', which this server does not publish',
            );
        }
    }

    /**
     * The call the hole was found on, with the session's own arguments.
     *
     * Two things had to be true for it to answer `typo3-extension-conformance`.
     * No intent named `typo3-core-issue-triage`, so nothing could match the
     * work; and "Triage an old open core bug report" carried none of the
     * markers that say core, so every intent that did match answered with its
     * extension side — in a checkout `typo3_project_describe` had reported one
     * call earlier as `core-checkout` (`D-SKL-023`). The task names the core as
     * a tracker rather than as a patch, which is what the work ending before a
     * patch does.
     */
    #[Test]
    public function aCoreTriageReachesTheSkillThatOwnsItWithoutNamingAPath(): void
    {
        $answer = Registry::call('typo3_task_guide', [
            'task' => 'Triage an old open core bug report: establish whether it still reproduces against this checkout',
            'changeType' => 'audit',
            'targetVersion' => '15',
            'paths' => [],
        ])->data;

        self::assertSame('core', $answer['scope']);
        self::assertContains('typo3-core-issue-triage', $answer['skills']);
        self::assertNotContains('typo3-extension-conformance', $answer['skills']);
    }

    /**
     * The other direction, which is the one that fails without saying so.
     *
     * A client selects a skill on its description and `typo3_task_guide`
     * selects one on the intents, so a skill in the first and absent from the
     * second is reachable only by a caller who already knew it existed. What
     * the guide answers such a task with is the nearest intent that did match —
     * a different workflow, confidently named. A core triage was answered
     * `skills: ["typo3-extension-conformance"]` that way, with a patch-review
     * checklist, inside a checkout reported as `core-checkout` (`D-SKL-023`).
     *
     * A draft is not in this set. `Installer::skills()` is what the server
     * publishes, and a draft reachable by routing is one nobody chose — which
     * is the exemption this check has and the only one.
     */
    #[Test]
    public function everyPublishedSkillIsNamedByAnIntent(): void
    {
        $named = [];
        foreach (TaskIntents::load() as $intent) {
            foreach ([$intent['skill'], $intent['skillCore']] as $skill) {
                if ($skill !== '') {
                    $named[$skill] = true;
                }
            }
        }

        // All of them, because one at a time names the skill somebody added
        // last and the list says how far the routing has fallen behind the
        // directory.
        self::assertSame(
            [],
            array_values(array_diff(Installer::skills(), array_keys($named))),
            'published and named by no intent, so typo3_task_guide cannot route a task to it',
        );
    }

    #[Test]
    public function everySkillStatesWhatItOwns(): void
    {
        foreach (self::skills() as $name => $skill) {
            self::assertStringContainsString('This skill owns ', $skill, $name . ' does not say what it owns');
        }
    }

    /**
     * A skill is a copy in somebody else's project, so a uri it names is a
     * promise no release of this server corrects. It is also the only address a
     * session gets where the client renders no resource list, which is the case
     * `D-AUD-007` was reopened by — a dead one there is worse than none.
     */
    #[Test]
    public function everyResourceASkillNamesIsOneTheServerServes(): void
    {
        $served = array_map(
            static fn(array $document): string => Documents::uri($document['id']),
            Documents::topics(),
        );

        foreach (self::skills() as $name => $skill) {
            foreach (self::published($name, $skill) as $file => $text) {
                preg_match_all('#typo3://[a-zA-Z0-9/_-]+#', $text, $matches);
                foreach (array_unique($matches[0]) as $uri) {
                    self::assertContains(
                        $uri,
                        $served,
                        $name . '/' . $file . ' names ' . $uri . ', which this server does not serve',
                    );
                }
            }
        }
    }

    /**
     * An address is delivery to a client that renders a resource list and to no
     * other, so a step that tells a session to read a page whole hands it the
     * call instead: `typo3_rule_lookup` with that `documentId`. `D-ANS-070` is
     * the session this was read off — it held the guide ids, read the
     * `documentId` parameter description, and searched anyway. The uri may
     * stand beside the call, which is why this asserts the call is there rather
     * than that the address is gone.
     */
    #[Test]
    public function everyGuideASkillNamesIsHandedOverByTheCallThatReadsIt(): void
    {
        foreach (self::skills() as $name => $skill) {
            foreach (self::published($name, $skill) as $file => $text) {
                preg_match_all('#typo3://guides/([a-zA-Z0-9/_-]+)#', $text, $matches);
                foreach (array_unique($matches[1]) as $id) {
                    self::assertStringContainsString(
                        'documentId="' . $id . '"',
                        $text,
                        $name . '/' . $file . ' names ' . $id . ' as an address a client that lists no '
                        . 'resources cannot act on; name the call, typo3_rule_lookup with documentId="' . $id . '"',
                    );
                }
            }
        }
    }

    #[Test]
    public function noSkillKeepsASecondCopyOfWhatAToolOwns(): void
    {
        foreach (self::skills() as $name => $skill) {
            self::assertStringContainsString('Keep this skill as routing', self::flat($skill), $name);
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
     * grows a body around: the ones that carry it keep it beside them rather
     * than in the instruction every session pays for. Four are not among them.
     * Building a backend module is construction, and what it needs is the
     * registries, which are tools rather than a list. An upgrade is construction
     * too, and its work list is produced by the sweep rather than read off a
     * rubric — what it would otherwise judge, whether the package is sound in
     * the first place, it hands to the skill whose checklist already covers it.
     * Writing a core patch is the third: its work list is the issue and the
     * findings the review skill hands it, and the surfaces it would otherwise
     * check are that skill's checklist, one directory away. Bringing an
     * installation into existence is the fourth, and the one where a rubric
     * would be furthest from the evidence: its work list is the order the five
     * steps' dependencies force, and what it would otherwise judge — whether the
     * installation is right — is what a cold start answers. Remediation is the
     * fifth, and it is the upgrade's case exactly: its work list is produced by
     * the conformance report rather than read off a rubric, and what it would
     * otherwise judge — whether a finding is one, what it is worth, who owns it
     * — is the checklist one directory away, whose report it is forbidden from
     * re-deriving (`D-SKL-016`).
     */
    #[Test]
    public function judgmentHeavySkillsKeepTheirChecklistBesideThem(): void
    {
        $judging = array_diff(
            array_keys(self::ROUTING_SKILLS),
            [
                'typo3-backend-module-development',
                'typo3-extension-upgrade',
                'typo3-core-patch-development',
                'typo3-development-installation',
                'typo3-extension-cleanup',
            ],
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
        self::assertStringContainsString(
            'establishes them whether or not the project already runs them',
            self::flat($skill),
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
        self::assertStringContainsString(
            'default per check where the checkout covers it with nothing, never as a replacement for what it already runs',
            self::flat($guidance),
        );

        // The list above stops at which packages to require. What goes inside
        // the analyser's configuration is a cell of the corpus — `D-KNW-012`
        // judged a session that wrote it from recall — and this page reaches it
        // by name rather than restating it, because a skill is a file no
        // release of this server corrects.
        self::assertStringContainsString(
            '`typo3_hint_lookup` with `id=extension-static-analysis`',
            $guidance,
        );
        self::assertStringNotContainsString('phpstan-baseline.neon', $guidance);
        self::assertStringNotContainsString('tmpDir', $guidance);
        self::assertNotNull(
            Hints::byId('extension-static-analysis'),
            'the skill defers to an id the corpus does not have',
        );

        self::assertStringContainsString('Keep checking and fixing apart', $guidance);
        self::assertStringContainsString('never receives an error the change in hand introduced', $guidance);
        self::assertStringContainsString('first-party paths the project intends it', $guidance);

        // Splitting the commits is half the rule and the order is the other
        // half. `feedback/2026-08-04-055741` worked it out on its own: tooling
        // first would have landed `ci:editorconfig` on a tree whose XLF files
        // still held tabs, so the session inverted the split and ran the checks
        // at the new HEAD.
        self::assertStringContainsString(
            'the conformance commits come first and the commit that adds the check comes last, so no commit fails the check it introduces',
            self::flat($guidance),
        );
        self::assertStringContainsString('running the check at the new HEAD', self::flat($guidance));

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
        self::assertStringNotContainsString('typo3_project_describe', $skill);
        self::assertStringNotContainsString('typo3_extension_describe', $skill);

        // What it adds to the sweep is the two sources a changelog query cannot
        // reach. The scanner because the run never touched it, and the
        // annotations because the deprecation that decided that package's next
        // major sat on the installed class rather than in an entry any of the
        // four queries matched.
        self::assertStringContainsString('`type: breaking`', $skill);
        self::assertStringContainsString('**The Extension Scanner**', $skill);
        self::assertStringContainsString('`FullyScanned` / `PartiallyScanned`', $skill);
        self::assertStringContainsString(
            'A clean scan for a partially scanned entry is not a result',
            self::flat($skill),
        );
        self::assertStringContainsString('**The deprecation annotations on what this package actually calls**', $skill);

        // And the boundary both of those inherit: they answer from the core that
        // is installed, so the target's own changes are documentation until the
        // installation is on it.
        self::assertStringContainsString(
            'they do not know what the target major changed until the installation is on it',
            self::flat($skill),
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
        // Read flat: what is asserted is the sentence, and `prose:format`
        // decides where its lines break.
        self::assertStringContainsString(
            'What the sweep returned goes to `typo3-extension-upgrade` whole',
            self::flat((string) file_get_contents(
                Paths::root() . '/skills/typo3-extension-conformance/SKILL.md',
            )),
        );
    }

    /**
     * Seven feedback from two projects on one day, and `D-SKL-012` is where they
     * were read. The two projects are the two shapes this holds the skill to:
     * one repository had no installation and had to produce one, the other
     * declared its own environment and had to boot it, and a skill that carries
     * only the first sends the second back to the patch workflow both of them
     * already landed in.
     */
    #[Test]
    public function anInstallationIsBuiltInDependencyOrderAndHandsOverOnceItAnswers(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-development-installation/SKILL.md',
        );
        $flat = self::flat($skill);

        // The entry condition, and the one place this skill contradicts the
        // base's first instruction: `typo3_project_describe` in a repository with
        // no installation answers `unsupported: no-installation`, which reads
        // like the disconnected server the base stops for and is the task.
        self::assertStringContainsString(
            'no installation to describe is the task, not the disconnected server',
            $flat,
        );

        $order = [
            '[references/base.md](references/base.md)',
            '## Boot what the repository already declares',
            '## Create one where none is declared',
            '## Prove it, and how far depends on who wrote the sequence',
            '## Where this stops',
        ];
        $position = -1;
        foreach ($order as $step) {
            $next = strpos($skill, $step);
            self::assertNotFalse($next, $step . ' is not part of the installation workflow');
            self::assertGreaterThan($position, $next, $step . ' is stated out of order');
            $position = $next;
        }

        // The five steps in the order their dependencies force, which is what
        // `162745` numbered after inventing it once.
        $steps = [
            '**Make the package\'s own manifest the Composer root package.**',
            '**Declare the container.**',
            '**Install non-interactively.**',
            '**Seed the content the package is to be developed against**',
            '**Decide what the install wrote into the repository.**',
        ];
        $position = -1;
        foreach ($steps as $step) {
            $next = strpos($skill, $step);
            self::assertNotFalse($next, $step . ' is not one of the steps');
            self::assertGreaterThan($position, $next, $step . ' is stated out of order');
            $position = $next;
        }

        // The environment that generates settings knows only the services it
        // provides itself, which is the collision `162858` paid a debugging
        // cycle for. The hint owns the boundary; this owns the case.
        self::assertStringContainsString('id=project-configuration-files', $skill);
        self::assertStringContainsString('knows only the services it provides itself', $flat);

        // Named once in the description, which is what a client routes on, and
        // nowhere in the body: what that product does by default is the fact
        // that moves after this file is published into somebody else's project.
        self::assertStringContainsString('DDEV where it declares one', self::description('typo3-development-installation'));
        self::assertStringNotContainsString('DDEV', substr($skill, (int) strpos($skill, "\n---\n") + 5));

        // Both directions of the crossing, because the feedback asked for both.
        self::assertStringContainsString('typo3-extension-testing', $skill);
        self::assertStringContainsString('stop before editing that owner\'s files, and activate it', $flat);
        self::assertStringContainsString(
            'a suite that needs a served site and has none is this workflow first',
            $flat,
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
        self::assertStringContainsString('only for an actual core patch', self::flat($skill));
        self::assertStringContainsString('Never present it as a project', self::flat($skill));
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

        $flat = self::flat($skill);
        $verified = strpos($flat, 'implementation is verified');
        $stop = strpos($flat, 'stop this workflow');
        $activate = strpos($flat, 'Activate `typo3-extension-documentation` before editing documentation');
        self::assertNotFalse($verified);
        self::assertNotFalse($stop);
        self::assertNotFalse($activate);
        self::assertLessThan($stop, $verified);
        self::assertLessThan($activate, $stop);
        self::assertStringContainsString(
            'belongs to that extension, not to the project around it',
            self::flat($skill),
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
        self::assertStringContainsString(
            'A surface is in scope because the checklist names it, not because the file tree shows it',
            self::flat($skill),
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
        $lookup = strpos($skill, 'typo3_hint_lookup');
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
        self::assertStringContainsString(
            'confirmed by its own runtime lookup and still break every rule that governs it',
            self::flat($skill),
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
        self::assertStringContainsString(
            'marks as checks hand the code back as it was, and an audit told not to change files runs them',
            self::flat($skill),
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
        self::assertStringContainsString(
            'A request that names no surface is not a focused one',
            self::flat($skill),
        );

        // The report is where the two states are told apart, and the number is
        // read off the step that writes the list: it said step 5 for two days
        // after the block was renumbered to three.
        self::assertStringContainsString('the surface list written in step 3', $skill);
        self::assertStringContainsString(
            'Unassessed and not requested both mean nothing was established there, and they are not the same thing',
            self::flat($skill),
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
        self::assertStringContainsString(
            'commands a repository declares are where this surface is read, never what it is',
            self::flat($checklist),
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
        self::assertStringContainsString(
            'no command covers is a gap in the layer rather than an optional subsystem, and that absence is the finding',
            self::flat($checklist),
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

    #[Test]
    public function contractCasesExerciseTaskSkillBehavior(): void
    {
        $cases = Scenarios::contracts();

        $ids = ['SKILL-01', 'SKILL-02', 'SKILL-03', 'SKILL-04', 'SKILL-05', 'SKILL-06', 'SKILL-07', 'SKILL-08', 'SKILL-09'];
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
     * `R-SKL-018`. A crossing between two skills is a step, not a paragraph.
     *
     * Each of these named its successor in prose about ownership, and each was
     * read by a session that then did the successor's work itself: one wrote a
     * whole patch over forty turns after a triage, one edited the patch it was
     * reviewing, ran seven suites and amended the commit, and one finished a
     * push-ready patch and handed it over unreviewed. No session reports a
     * wrong outcome; all three reconstructed an order that was one call away
     * (`D-SKL-022`).
     *
     * The last of them crosses the other way, out of the skill the first two
     * cross into.
     */
    #[Test]
    public function aSkillThatHandsOverSaysToInvokeTheSuccessor(): void
    {
        $crossings = [
            'typo3-core-issue-triage' => 'typo3-core-patch-development',
            'typo3-core-patch-review' => 'typo3-core-patch-development',
            'typo3-core-patch-development' => 'typo3-core-patch-review',
        ];

        foreach ($crossings as $name => $successor) {
            $body = self::flat((string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md'));

            self::assertStringContainsString(
                'invoke `' . $successor . '`',
                $body,
                $name . ' names ' . $successor . ' without telling the session to invoke it',
            );
        }

        // And the one that reads as somebody else's patch says where a commit
        // of your own belongs, because that description is why a session asked
        // to rebase its own work correctly did not open it.
        self::assertStringContainsString(
            'typo3-core-patch-development',
            self::description('typo3-core-patch-checkout'),
        );
    }

    /**
     * One skill's front matter, as a reader of the standard gets it rather than
     * as a pattern here finds it.
     *
     * @return array<string, mixed>
     */
    private static function frontMatter(string $name, string $skill): array
    {
        self::assertSame(
            1,
            preg_match('/\A---\R(.*?)\R---\R/s', $skill, $block),
            $name . ' has no front matter',
        );

        try {
            $matter = Yaml::parse($block[1]);
        } catch (ParseException $exception) {
            self::fail($name . ' has front matter no reader of the standard can parse: ' . $exception->getMessage());
        }
        self::assertIsArray($matter, $name . ' has front matter that is not a mapping');

        /** @var array<string, mixed> $matter */
        return $matter;
    }

    private static function description(string $name): string
    {
        $skill = (string) file_get_contents(Paths::root() . '/skills/' . $name . '/SKILL.md');
        self::assertSame(
            1,
            preg_match('/\ndescription: (.+)\n/', $skill, $matches),
            $name . ' has no description',
        );

        return $matches[1];
    }

    /**
     * A skill read with its wrapping taken out.
     *
     * What these assert is the wording, not where the line ends. Matched
     * against the file as it stands, a sentence that moves one word breaks a
     * test about something the change never touched — which is what a rewrap
     * of the corpus did to six of them.
     */
    private static function flat(string $skill): string
    {
        return (string) preg_replace('/\s+/', ' ', $skill);
    }


    /**
     * Everything one skill installs into another project: its body and every
     * reference beside it.
     *
     * @return array<string, string>
     */
    private static function published(string $name, string $skill): array
    {
        $files = ['SKILL.md' => $skill];

        $directory = Paths::root() . '/skills/' . $name . '/references';
        if (!is_dir($directory)) {
            return $files;
        }
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.md')->sortByName() as $reference) {
            $files['references/' . $reference->getFilename()] = (string) file_get_contents($reference->getPathname());
        }

        return $files;
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
