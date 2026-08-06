<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Schema\Content\TextContent;
use Mcp\Schema\Result\CallToolResult;
use Mcp\Server\ClientGateway;
use Mcp\Server\Handler\ToolHandlerInterface;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * Bridges one registered tool to TYPO3\DevCompanion\Tool\Registry.
 *
 * The official SDK passes the raw (validated) argument bag to execute(); it is
 * handed straight to Registry::call(), so every behaviour and every rendering
 * stays in the tool that owns it and nothing about a tool is decided here. Both
 * halves of the answer are returned: the text as the tool's content, the same
 * answer as `structuredContent` matching the output schema that tool declares.
 * A thrown exception is turned into an MCP tool error by the SDK.
 */
final class ToolHandler implements ToolHandlerInterface
{
    public function __construct(private readonly string $name) {}

    /**
     * @param array<string, mixed> $arguments
     */
    public function execute(array $arguments, ClientGateway $gateway): CallToolResult
    {
        $result = Registry::call($this->name, $arguments);

        return new CallToolResult(
            [new TextContent($result->text)],
            structuredContent: $result->data,
        );
    }
}
