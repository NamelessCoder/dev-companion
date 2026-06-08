<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Mcp\Schema\ResourceDefinition;
use Mcp\Schema\Tool;
use Mcp\Server;
use Mcp\Server\Session\SessionStoreInterface;
use Typo3CmsMcp\Mcp\ResourceHandler;
use Typo3CmsMcp\Mcp\ToolHandler;

/**
 * Builds the MCP server on the official mcp/sdk, wiring the existing knowledge
 * base (Tools, Knowledge) to SDK tool and resource handlers. Transport-agnostic:
 * the stdio and HTTP entrypoints share this one definition so both answer
 * identically.
 */
final class ServerFactory
{
    public const SERVER_NAME = 'typo3-cms-mcp';
    public const SERVER_VERSION = '0.3.0';

    /**
     * @param ?SessionStoreInterface $sessionStore Persistent session backend for stateful
     *                                              transports (HTTP). Omit for stdio, which
     *                                              uses the in-memory default.
     */
    public static function create(?SessionStoreInterface $sessionStore = null): Server
    {
        $builder = Server::builder()
            ->setServerInfo(self::SERVER_NAME, self::SERVER_VERSION);

        if ($sessionStore !== null) {
            $builder->setSession($sessionStore);
        }

        foreach (Tools::definitions() as $definition) {
            $tool = new Tool(
                $definition['name'],
                null,
                $definition['inputSchema'],
                $definition['description'],
                null,
            );
            $builder->add($tool, new ToolHandler($definition['name']));
        }

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

        foreach (Knowledge::documents() as $document) {
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
