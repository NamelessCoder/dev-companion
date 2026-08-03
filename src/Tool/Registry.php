<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Feedback\Channel;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Server\ExcludedTools;

/**
 * Every tool this server has, and the only place one is switched on.
 *
 * What a tool is called, takes, returns and answers lives in the class that
 * answers it; this is the list, in the order a client is offered them. Two
 * things narrow that list: what the caller excluded, and the feedback channel,
 * which exists only in a standalone checkout. Nothing else does — which
 * repository the server was started in shapes what an answer says, never
 * whether the tool that says it is there.
 */
final class Registry
{
    /**
     * In the order a client sees them: orientation first, then the guides and
     * lookups, then what describes the repository being worked in.
     *
     * @var array<int, class-string<Tool>>
     */
    private const TOOLS = [
        ServerScope::class,
        RuleLookup::class,
        ScriptLookup::class,
        TaskGuide::class,
        TestRunGuide::class,
        HintLookup::class,
        DocumentationLookup::class,
        ForgeLookup::class,
        GerritLookup::class,
        ComponentLookup::class,
        SystemExtensionLookup::class,
        ReferenceList::class,
        TranslationDomainLookup::class,
        LabelLookup::class,
        FluidNamespaceList::class,
        ConfigurationLookup::class,
        SchemaLookup::class,
        BackendModuleLookup::class,
        IconLookup::class,
        ChangelogLookup::class,
        ProjectScope::class,
        ExtensionScope::class,
        CatalogScope::class,
        CommitMessageGuide::class,
    ];

    /**
     * Offered from a standalone checkout alone — see the feedback channel.
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
            self::TOOLS,
            static fn(string $tool): bool => ExcludedTools::offers($tool::name()),
        ));

        if (Channel::isAvailable()) {
            array_push($offered, ...self::FEEDBACK);
        }

        return $offered;
    }
}
