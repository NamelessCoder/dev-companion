<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Mcp;

use Mcp\Schema\Content\TextContent;
use Mcp\Schema\Result\CallToolResult;
use Mcp\Server\ClientGateway;
use Mcp\Server\Handler\ToolHandlerInterface;
use Typo3CmsMcp\Tools;

/**
 * Bridges one registered MCP tool to the existing Typo3CmsMcp\Tools dispatcher.
 *
 * The official SDK passes the raw (validated) argument bag to execute(); we hand
 * it straight to Tools::call(), so all tool behaviour and formatting stays in
 * Tools and the knowledge base. Both halves of the answer are returned: the text
 * as the tool's content, the same answer as `structuredContent` matching the
 * tool's declared output schema. A thrown exception is turned into an MCP tool
 * error by the SDK.
 */
final class ToolHandler implements ToolHandlerInterface
{
    public function __construct(private readonly string $name) {}

    /**
     * @param array<string, mixed> $arguments
     */
    public function execute(array $arguments, ClientGateway $gateway): CallToolResult
    {
        $result = Tools::call($this->name, $arguments);

        return new CallToolResult(
            [new TextContent($result->text)],
            structuredContent: $result->data,
        );
    }
}
