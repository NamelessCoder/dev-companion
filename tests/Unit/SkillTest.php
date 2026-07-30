<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;

final class SkillTest extends TestCase
{
    #[Test]
    public function theBackendModuleSkillRoutesThroughTheOwnersOfItsFactsInOrder(): void
    {
        $skill = (string) file_get_contents(
            Paths::root() . '/skills/typo3-backend-module-development/SKILL.md',
        );
        $tools = [
            'typo3_project_scope',
            'typo3_extension_scope',
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
}
