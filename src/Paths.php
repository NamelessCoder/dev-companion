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

    /**
     * The prose corpus: the markdown documents searched by typo3_rule_lookup
     * and served as typo3://core resources.
     *
     * They have a directory of their own because being one is what publishes
     * them. Every file here becomes a resource named after it, so a readme laid
     * beside the knowledge base used to become `typo3://core/readme` without
     * anybody deciding that.
     */
    public static function documents(): string
    {
        return self::knowledge() . '/documents';
    }

    public static function catalogFile(string ...$segments): string
    {
        return self::knowledge() . '/catalog/' . implode('/', $segments);
    }

    /**
     * Improvement feedback recorded by agents. Only written to in a standalone
     * checkout; see Feedback.
     */
    public static function feedback(): string
    {
        return self::root() . '/feedback';
    }

    /**
     * The feedback that were worked off. They stay readable rather than being
     * deleted: what a session reported about this server is evidence about it,
     * and the answer to it is the half nobody else can reconstruct.
     */
    public static function feedbackArchive(): string
    {
        return self::feedback() . '/archive';
    }
}
