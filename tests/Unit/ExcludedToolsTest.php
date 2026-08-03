<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Knowledge\Coverage;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Server\ExcludedTools;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tool\Registry;

final class ExcludedToolsTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheExclusionsAndTheInstance(): void
    {
        putenv(ExcludedTools::VARIABLE);
        Instance::discoverFrom(null);
    }

    #[Test]
    #[DataProvider('everyKindOfRepositoryAToolListIsAskedFrom')]
    public function theKindOfRepositoryNeverShortensTheToolList(string $kind): void
    {
        // What the profile used to do here, and the reason it is gone: a
        // Composer project cannot follow the core's contribution process, but
        // whether a task is core work is a property of the task, and a caller
        // writing a core patch from a site installation was left with an answer
        // routing to a tool that had been taken away.
        Instance::discoverFrom($this->rootOf($kind));

        $offered = $this->toolNames();
        self::assertContains('typo3_rule_lookup', $offered);
        self::assertContains('typo3_script_lookup', $offered);
        self::assertContains('typo3_test_run_guide', $offered);
    }

    /** @return array<string, array{0: string}> */
    public static function everyKindOfRepositoryAToolListIsAskedFrom(): array
    {
        return [
            'a Composer project' => ['project'],
            'a core checkout' => ['core'],
            'no installation at all' => ['none'],
        ];
    }

    /** The roots are built per case, so the provider names one rather than making it. */
    private function rootOf(string $kind): ?string
    {
        return match ($kind) {
            'project' => $this->composerProject(),
            'core' => $this->coreCheckout(),
            default => null,
        };
    }

    #[Test]
    public function aCoreShapedTaskFromAProjectIsAnsweredAndTheToolItRoutesToIsThere(): void
    {
        Instance::discoverFrom($this->composerProject());

        $guide = Registry::call('typo3_task_guide', [
            'task' => 'Fix a QueryBuilder bug in typo3/sysext/core and push it to Gerrit',
            'changeType' => 'bugfix',
        ]);

        // The failure D-AUD-002 recorded from E-SITE: the answer is core work,
        // names the targeted runTests.sh invocation, and the client could not
        // call the tool it was sent to.
        self::assertStringContainsString('typo3_test_run_guide', $guide->text);
        self::assertContains('typo3_test_run_guide', $this->toolNames());
    }

    #[Test]
    public function onlyTheCallerShortensTheList(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_icon_lookup, typo3_label_lookup');

        $offered = $this->toolNames();
        self::assertNotContains('typo3_icon_lookup', $offered);
        self::assertNotContains('typo3_label_lookup', $offered);
        self::assertContains('typo3_hint_lookup', $offered);
    }

    #[Test]
    public function theScopeNamesWhatTheCallerExcluded(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_icon_lookup, typo3_label_lookup');

        $scope = Registry::call('typo3_server_scope', []);

        self::assertSame(['typo3_icon_lookup', 'typo3_label_lookup'], $scope->data['excludedTools']['names']);
        self::assertSame(ExcludedTools::VARIABLE, $scope->data['excludedTools']['variable']);
        self::assertStringContainsString('typo3_icon_lookup', $scope->text);
        self::assertStringContainsString(ExcludedTools::VARIABLE, $scope->text);
    }

    #[Test]
    public function theScopeSaysNothingAboutExclusionsWhereThereAreNone(): void
    {
        $scope = Registry::call('typo3_server_scope', []);

        self::assertSame([], $scope->data['excludedTools']['names']);
        self::assertStringNotContainsString(ExcludedTools::VARIABLE, $scope->text);
    }

    #[Test]
    public function theScopeNeverPointsAtAToolTheCallerExcluded(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_test_run_guide, typo3_rule_lookup');

        $rendered = json_encode(Coverage::offered(), JSON_THROW_ON_ERROR);

        foreach (ExcludedTools::all() as $tool) {
            self::assertStringNotContainsString($tool, $rendered, $tool . ' is not offered but still routed to');
        }
    }

    #[Test]
    public function aTopicStaysAsLongAsSomethingStillAnswersIt(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_test_run_guide, typo3_rule_lookup, typo3_script_lookup');

        // The scope of a topic says which kind of work its answers are for. It
        // never decided whether the topic is offered — that was the profile
        // reading the repository where the task was meant.
        self::assertContains(Scope::Any, array_column(Coverage::offered()['covers'], 'scope'));
    }

    #[Test]
    public function theToolThatExplainsAShortListCannotBeExcluded(): void
    {
        putenv(ExcludedTools::VARIABLE . '=typo3_server_scope');

        self::assertSame([], ExcludedTools::all());
        self::assertContains('typo3_server_scope', $this->toolNames());
    }

    /** @return array<int, string> */
    private function toolNames(): array
    {
        return array_column(Registry::definitions(), 'name');
    }
}
