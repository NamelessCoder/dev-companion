<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Mcp\Schema\ResourceDefinition;
use Mcp\Schema\Tool;
use Mcp\Schema\ToolAnnotations;
use Mcp\Server;
use Typo3CmsMcp\Knowledge\Documents;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Sdk\ResourceHandler;
use Typo3CmsMcp\Sdk\ToolHandler;

/**
 * Builds the MCP server on the official mcp/sdk, wiring the existing knowledge
 * base (Tools, Knowledge) to SDK tool and resource handlers. Transport-agnostic,
 * so the stdio entrypoint is only one possible consumer of this definition.
 */
final class ServerFactory
{
    public const SERVER_NAME = 'typo3-cms-mcp';
    public const SERVER_VERSION = '0.3.0';

    public static function create(): Server
    {
        $builder = Server::builder()
            ->setServerInfo(self::SERVER_NAME, self::SERVER_VERSION)
            ->setInstructions(Scope::instructions());

        foreach (Tools::definitions() as $definition) {
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

                return ['user' => Tools::call('typo3_commit_message_guide', $arguments)->text];
            },
            name: 'commit_message',
            title: 'Draft a TYPO3 commit message',
            description: 'Turn a summary into the checked commit-message draft already provided by typo3_commit_message_guide.',
        );

        $resourceHandler = new ResourceHandler();

        $builder->add(
            new ResourceDefinition(
                uri: 'typo3://core',
                name: 'typo3-core-knowledge-index',
                title: 'TYPO3 core knowledge index',
                mimeType: 'application/json',
            ),
            $resourceHandler,
        );

        foreach (Documents::documents() as $document) {
            $builder->add(
                new ResourceDefinition(
                    uri: 'typo3://core/' . $document['id'],
                    name: $document['id'],
                    title: $document['title'],
                    mimeType: 'text/markdown',
                ),
                $resourceHandler,
            );
        }

        return $builder->build();
    }
}
