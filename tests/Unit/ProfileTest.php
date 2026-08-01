<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Profile;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tools;

final class ProfileTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheProfileAndTheInstance(): void
    {
        putenv(Profile::VARIABLE);
        putenv(Profile::EXCLUDE_VARIABLE);
        Instance::discoverFrom(null);
    }

    #[Test]
    public function inAProjectTheCoreContributionSurfaceIsNotOffered(): void
    {
        Instance::discoverFrom($this->composerProject());

        self::assertSame(Profile::PROJECT, Profile::active());

        $offered = $this->toolNames();
        self::assertNotContains('typo3_rule_lookup', $offered);
        self::assertNotContains('typo3_script_lookup', $offered);
        self::assertNotContains('typo3_test_run_guide', $offered);

        // What transfers, and what only the installation knows, is the half
        // this profile exists to keep.
        self::assertContains('typo3_architecture_lookup', $offered);
        self::assertContains('typo3_label_lookup', $offered);
        self::assertContains('typo3_project_scope', $offered);
        self::assertContains('typo3_commit_message_guide', $offered);
    }

    #[Test]
    public function inACoreCheckoutAndWithoutAnInstallationEverythingIsOffered(): void
    {
        Instance::discoverFrom($this->coreCheckout());
        self::assertSame(Profile::ALL, Profile::active());
        self::assertContains('typo3_rule_lookup', $this->toolNames());

        // A session with nothing to read may still grow an installation —
        // composer install is the ordinary case — so it is not narrowed either.
        Instance::discoverFrom(null);
        self::assertSame(Profile::ALL, Profile::active());
        self::assertContains('typo3_test_run_guide', $this->toolNames());
    }

    #[Test]
    public function theProfileIsNamedOutrightWhateverTheInstallationSays(): void
    {
        Instance::discoverFrom($this->coreCheckout());
        putenv(Profile::VARIABLE . '=' . Profile::PROJECT);

        self::assertSame(Profile::PROJECT, Profile::active());
        self::assertSame(Profile::VIA_ENVIRONMENT, Profile::via());
        self::assertNotContains('typo3_rule_lookup', $this->toolNames());

        putenv(Profile::VARIABLE . '=' . Profile::ALL);
        self::assertSame(Profile::ALL, Profile::active());
    }

    #[Test]
    public function aProfileThatDoesNotExistIsSaidOutLoudRatherThanFollowed(): void
    {
        Instance::discoverFrom($this->composerProject());
        putenv(Profile::VARIABLE . '=site-developer');

        // Not fatal — the derived profile carries on — but silence would look
        // exactly like not having set the variable.
        self::assertSame(Profile::PROJECT, Profile::active());
        self::assertSame(Profile::VIA_INSTALLATION, Profile::via());
        self::assertStringContainsString('site-developer', Profile::misconfiguration());

        $result = Tools::call('typo3_server_scope', []);
        self::assertStringContainsString('site-developer', $result->text);
        self::assertStringContainsString('site-developer', (string) $result->data['profile']['misconfiguration']);
    }

    #[Test]
    public function theScopeNamesTheActiveProfileAndWhatItLeavesOut(): void
    {
        Instance::discoverFrom($this->composerProject());

        $result = Tools::call('typo3_server_scope', []);

        self::assertSame(Profile::PROJECT, $result->data['profile']['active']);
        self::assertSame(Profile::VIA_INSTALLATION, $result->data['profile']['via']);
        self::assertContains('typo3_rule_lookup', $result->data['profile']['omits']);
        self::assertStringContainsString('typo3_rule_lookup', $result->text);
        self::assertStringContainsString(Profile::VARIABLE, $result->text);
    }

    #[Test]
    public function individualToolsCanBeExcludedAfterTheProfileIsChosen(): void
    {
        putenv(Profile::VARIABLE . '=' . Profile::ALL);
        putenv(Profile::EXCLUDE_VARIABLE . '=typo3_icon_lookup, typo3_label_lookup');

        $offered = $this->toolNames();
        self::assertNotContains('typo3_icon_lookup', $offered);
        self::assertNotContains('typo3_label_lookup', $offered);
        self::assertContains('typo3_architecture_lookup', $offered);

        $scope = Tools::call('typo3_server_scope', []);
        self::assertSame(['typo3_icon_lookup', 'typo3_label_lookup'], $scope->data['profile']['omits']);
        self::assertStringContainsString('typo3_icon_lookup', $scope->text);
        self::assertStringContainsString('typo3_label_lookup', $scope->text);
    }

    #[Test]
    public function theScopeNeverPointsAtAToolThisProfileDoesNotOffer(): void
    {
        Instance::discoverFrom($this->composerProject());

        $scope = Scope::offered();
        $rendered = json_encode($scope, JSON_THROW_ON_ERROR);

        foreach (Profile::omitted() as $tool) {
            self::assertStringNotContainsString($tool, (string) $rendered, $tool . ' is not offered but still routed to');
        }

        // The map has to survive the filtering: a topic per remaining half.
        $provenances = array_column($scope['covers'], 'provenance');
        self::assertNotContains('core-only', $provenances);
        self::assertContains('transferable', $provenances);
        self::assertContains('installation', $provenances);
    }

    #[Test]
    public function everyProfileKeepsTheToolThatExplainsTheProfile(): void
    {
        // A client that sees a shorter list has to be able to find out why, and
        // this is the only tool that says so.
        foreach ([Profile::ALL, Profile::PROJECT] as $profile) {
            putenv(Profile::VARIABLE . '=' . $profile);
            self::assertContains('typo3_server_scope', $this->toolNames(), $profile);
        }
    }

    /** @return array<int, string> */
    private function toolNames(): array
    {
        return array_column(Tools::definitions(), 'name');
    }
}
