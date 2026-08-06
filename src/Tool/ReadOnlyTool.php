<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

/**
 * A tool that only reads bundled knowledge or the installation it was started
 * in: same arguments, same answer, no side effect, nothing outside this
 * package.
 *
 * That is every tool but typo3_feedback_record, which is why the annotations
 * are stated once here rather than twenty-one times. A tool that reaches
 * outside — typo3_documentation_lookup does — overrides the one hint that
 * changes and keeps the rest.
 */
abstract class ReadOnlyTool implements Tool
{
    public static function annotations(): array
    {
        return [
            'readOnlyHint' => true,
            'destructiveHint' => false,
            'idempotentHint' => true,
            'openWorldHint' => false,
        ];
    }

    /**
     * The input schema of a tool that takes no arguments.
     *
     * @return array<string, mixed>
     */
    protected static function noArguments(): array
    {
        return ['type' => 'object', 'properties' => new \stdClass()];
    }
}
