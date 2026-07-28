<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Resolves paths to the bundled knowledge base. The project root is the parent
 * of the src/ directory; the knowledge/ directory lives next to it.
 */
final class Paths
{
    public static function root(): string
    {
        return dirname(__DIR__);
    }

    public static function knowledge(): string
    {
        return self::root() . '/knowledge';
    }

    public static function knowledgeFile(string ...$segments): string
    {
        return self::knowledge() . '/' . implode('/', $segments);
    }

    public static function catalogFile(string ...$segments): string
    {
        return self::knowledge() . '/catalog/' . implode('/', $segments);
    }
}
