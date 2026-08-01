<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\ToolResult;

/**
 * One tool: what a client is told about it, and what it answers.
 *
 * Everything a caller can see of a tool is declared in the class that answers
 * it — the description it is chosen by, the arguments it takes, the shape of
 * the data it returns, and the answer itself. A description that stops
 * describing the answer is then a change to one file rather than a drift
 * between three.
 *
 * What these are is the MCP primitive the protocol calls a tool — the SDK
 * declares it as Mcp\Schema\Tool, beside Prompt and Resource. So the word here
 * is the protocol's, and the qualifier that says which kind of tool is meant is
 * already the root namespace. Nothing is a "server tool": a tool is defined by
 * the protocol rather than by the side offering it, and both sides speak of the
 * same one.
 *
 * Typo3CmsMcp\Tools is the list of them, and the only place a tool is switched
 * on.
 */
interface Tool
{
    /** typo3_<subject>_<verb>, with the verb from the closed list ToolNamingTest holds. */
    public static function name(): string;

    /** What a client chooses this tool by. It is the only documentation most of them read. */
    public static function description(): string;

    /** @return array<string, mixed> */
    public static function inputSchema(): array;

    /**
     * The contract of the data half: a field named here has to be present on
     * every path through the tool, misses included.
     *
     * @return array<string, mixed>
     */
    public static function outputSchema(): array;

    /**
     * What the tool does to the world, as the MCP annotations state it.
     *
     * @return array<string, bool>
     */
    public static function annotations(): array;

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult;
}
