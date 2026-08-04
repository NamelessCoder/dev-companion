<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Server;

use Mcp\Schema\Annotations;
use Mcp\Schema\ResourceDefinition;
use Mcp\Schema\Tool;
use Mcp\Schema\ToolAnnotations;
use Mcp\Server;
use Typo3CmsMcp\Knowledge\Coverage;
use Typo3CmsMcp\Knowledge\Documents;
use Typo3CmsMcp\Sdk\ResourceHandler;
use Typo3CmsMcp\Sdk\ToolHandler;
use Typo3CmsMcp\Tool\Registry;

/**
 * Builds the MCP server on the official mcp/sdk, wiring the existing knowledge
 * base to the SDK tool and resource handlers. Transport-agnostic,
 * so the stdio entrypoint is only one possible consumer of this definition.
 */
final class Factory
{
    public const SERVER_NAME = 'typo3-cms-mcp';
    public const SERVER_VERSION = '0.3.0';

    /**
     * How much of a session's context a resource is worth, on the scale the
     * spec gives `annotations.priority`: 1 is "most important", meaning
     * effectively required, and 0 is entirely optional. A picker sorts by it,
     * so what carries meaning is the order rather than the number.
     *
     * The index says what all the others are and is the one to take where only
     * one is taken. Below it, a document that holds whatever the caller is
     * working on is worth more than one that holds inside a core checkout
     * alone, which most sessions are not in — `R-AUD-001`.
     */
    private const INDEX_PRIORITY = 1.0;
    private const TRANSFERABLE_PRIORITY = 0.8;
    private const CORE_ONLY_PRIORITY = 0.4;

    public static function create(): Server
    {
        $builder = Server::builder()
            ->setServerInfo(self::SERVER_NAME, self::SERVER_VERSION)
            ->setInstructions(Coverage::instructions());

        foreach (Registry::definitions() as $definition) {
            $tool = new Tool(
                $definition['name'],
                null,
                $definition['inputSchema'],
                $definition['description'],
                new ToolAnnotations(
                    readOnlyHint: $definition['annotations']['readOnlyHint'],
                    destructiveHint: $definition['annotations']['destructiveHint'],
                    idempotentHint: $definition['annotations']['idempotentHint'],
                    openWorldHint: $definition['annotations']['openWorldHint'],
                ),
                outputSchema: $definition['outputSchema'],
            );
            $builder->add($tool, new ToolHandler($definition['name']));
        }

        $builder->addPrompt(
            static function (
                string $summary,
                string $changeType = 'TASK',
                string $workflow = 'core',
                string $issue = '',
            ): array {
                $arguments = [
                    'summary' => $summary,
                    'changeType' => $changeType,
                    'workflow' => $workflow,
                ];
                if ($issue !== '') {
                    $arguments['issue'] = $issue;
                }

                return ['user' => Registry::call('typo3_commit_message_guide', $arguments)->text];
            },
            name: 'commit_message',
            title: 'Draft a TYPO3 commit message',
            description: 'Turn a summary into the checked commit-message draft already provided by typo3_commit_message_guide.',
        );

        $resourceHandler = new ResourceHandler();
        foreach (self::resources() as $resource) {
            $builder->add($resource, $resourceHandler);
        }

        return $builder->build();
    }

    /**
     * What this server offers to be picked, and what the choice is made on.
     *
     * A tool is called by the model in the middle of a task and can explain
     * itself in its answer; a resource is picked out of a list by the host
     * application or by the user, who have the list and nothing else. So
     * `description`, `annotations.priority` and `size` are what a resource is
     * chosen by rather than decoration — `R-ANS-022`.
     *
     * Two fields the spec has stay absent. `annotations.audience` is the
     * client's user or the model rather than the three audiences of
     * `R-AUD-001`, and everything here is for both of them. And
     * `annotations.lastModified` is carried by no `Mcp\Schema\Annotations` in
     * mcp/sdk v0.7.0, which has `audience` and `priority` alone.
     *
     * @return array<int, ResourceDefinition>
     */
    public static function resources(): array
    {
        $resources = [
            new ResourceDefinition(
                uri: ResourceHandler::INDEX_URI,
                name: 'typo3-core-knowledge-index',
                title: 'TYPO3 core knowledge index',
                description: 'What this server covers, which tool answers what, and every document it serves. The one to read first.',
                mimeType: 'application/json',
                annotations: new Annotations(priority: self::INDEX_PRIORITY),
                size: strlen(ResourceHandler::index()),
            ),
        ];

        foreach (Documents::documents() as $document) {
            $resources[] = new ResourceDefinition(
                uri: ResourceHandler::DOCUMENT_PREFIX . $document['id'],
                name: $document['id'],
                title: $document['title'],
                description: Documents::description($document['id']),
                mimeType: 'text/markdown',
                annotations: new Annotations(priority: Documents::isCoreOnly($document['id'])
                    ? self::CORE_ONLY_PRIORITY
                    : self::TRANSFERABLE_PRIORITY),
                size: (int) filesize($document['path']),
            );
        }

        return $resources;
    }
}
