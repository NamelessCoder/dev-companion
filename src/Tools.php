<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Typo3CmsMcp\Tool\ArchitectureLookup;
use Typo3CmsMcp\Tool\BackendModuleLookup;
use Typo3CmsMcp\Tool\CatalogScope;
use Typo3CmsMcp\Tool\ChangelogLookup;
use Typo3CmsMcp\Tool\CommitMessageGuide;
use Typo3CmsMcp\Tool\ComponentLookup;
use Typo3CmsMcp\Tool\ConfigurationLookup;
use Typo3CmsMcp\Tool\DocumentationLookup;
use Typo3CmsMcp\Tool\ExtensionScope;
use Typo3CmsMcp\Tool\FeedbackList;
use Typo3CmsMcp\Tool\FeedbackRecord;
use Typo3CmsMcp\Tool\FluidNamespaceList;
use Typo3CmsMcp\Tool\IconLookup;
use Typo3CmsMcp\Tool\LabelLookup;
use Typo3CmsMcp\Tool\ProjectScope;
use Typo3CmsMcp\Tool\ReferenceList;
use Typo3CmsMcp\Tool\RuleLookup;
use Typo3CmsMcp\Tool\ScriptLookup;
use Typo3CmsMcp\Tool\ServerScope;
use Typo3CmsMcp\Tool\SystemExtensionLookup;
use Typo3CmsMcp\Tool\TaskGuide;
use Typo3CmsMcp\Tool\TestRunGuide;
use Typo3CmsMcp\Tool\Tool;
use Typo3CmsMcp\Tool\TranslationDomainLookup;

/**
 * Every tool this server has, and the only place one is switched on.
 *
 * What a tool is called, takes, returns and answers lives in the class that
 * answers it; this is the list, in the order a client is offered them. Two
 * things narrow that list, and both are properties of the checkout the server
 * was started in rather than of any tool: the profile, which withholds the core
 * contribution surface where it cannot be followed, and the feedback channel,
 * which exists only in a standalone checkout.
 */
final class Tools
{
    /**
     * In the order a client sees them: orientation first, then the guides and
     * lookups, then what describes the repository being worked in.
     *
     * @var array<int, class-string<Tool>>
     */
    private const REGISTRY = [
        ServerScope::class,
        RuleLookup::class,
        ScriptLookup::class,
        TaskGuide::class,
        TestRunGuide::class,
        ArchitectureLookup::class,
        DocumentationLookup::class,
        ComponentLookup::class,
        SystemExtensionLookup::class,
        ReferenceList::class,
        TranslationDomainLookup::class,
        LabelLookup::class,
        FluidNamespaceList::class,
        ConfigurationLookup::class,
        BackendModuleLookup::class,
        IconLookup::class,
        ChangelogLookup::class,
        ProjectScope::class,
        ExtensionScope::class,
        CatalogScope::class,
        CommitMessageGuide::class,
    ];

    /**
     * Offered from a standalone checkout alone — see Feedback.
     *
     * @var array<int, class-string<Tool>>
     */
    private const FEEDBACK = [
        FeedbackRecord::class,
        FeedbackList::class,
    ];

    /**
     * @return array<int, array{
     *     name: string,
     *     description: string,
     *     inputSchema: array<string, mixed>,
     *     annotations: array<string, bool>,
     *     outputSchema: array<string, mixed>|null
     * }>
     */
    public static function definitions(): array
    {
        return array_map(static fn(string $tool): array => [
            'name' => $tool::name(),
            'description' => $tool::description(),
            'inputSchema' => $tool::inputSchema(),
            'annotations' => $tool::annotations(),
            'outputSchema' => $tool::outputSchema(),
        ], self::offered());
    }

    /** @param array<string, mixed> $args */
    public static function call(string $name, array $args): ToolResult
    {
        foreach (self::offered() as $tool) {
            if ($tool::name() === $name) {
                return $tool::answer($args);
            }
        }

        throw new \InvalidArgumentException(sprintf('Unknown tool: %s', $name));
    }

    /**
     * The tools this client is being offered, in order.
     *
     * @return array<int, class-string<Tool>>
     */
    private static function offered(): array
    {
        $offered = array_values(array_filter(
            self::REGISTRY,
            // The core contribution surface is left out where it cannot be
            // followed — see Profile.
            static fn(string $tool): bool => Profile::offers($tool::name()),
        ));

        if (Feedback::isAvailable()) {
            array_push($offered, ...self::FEEDBACK);
        }

        return $offered;
    }
}
