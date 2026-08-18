<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion;

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
     * and served as typo3://guides resources.
     *
     * They have a directory of their own because lying in it is what publishes
     * them, and a readme laid beside the knowledge base became
     * `typo3://guides/readme` without anybody deciding that. What publishes one
     * today is the shape below this directory rather than the directory alone
     * — `<scope>/<topic>/<name>.md`, so a file at any other depth is read by
     * nobody.
     */
    public static function documents(): string
    {
        return self::$documents ?? self::knowledge() . '/documents';
    }

    /**
     * A prose corpus other than this checkout's, which only a test asks for.
     *
     * `R-COD-003`: a unit test writes into no directory this repository keeps.
     * What the section binding of `D-VER-005` does cannot be held against the
     * corpus itself — a document written to carry a `**Since:**` for a test
     * would be a statement in the knowledge base whose purpose is the test,
     * and every guard over that directory would have to make an exception for
     * it.
     */
    private static ?string $documents = null;

    /** Where the prose corpus is read, for as long as a test says so. */
    public static function useDocuments(?string $directory): void
    {
        self::$documents = $directory;
    }

    public static function catalogFile(string ...$segments): string
    {
        return self::knowledge() . '/catalog/' . implode('/', $segments);
    }

    /**
     * A feedback store somewhere other than this checkout's, which only a test
     * asks for.
     *
     * `R-COD-003`: a unit test writes into no directory this repository keeps.
     * The cases that hold recording, filtering and archiving have to write a
     * feedback to have one, and they used to write it into the real `feedback/`,
     * which leaves a fixture in the corpus whenever a run does not finish. The
     * archive follows it, because the two are one store.
     */
    private static ?string $feedback = null;

    /** Where feedback is read and written, for as long as a test says so. */
    public static function useFeedback(?string $directory): void
    {
        self::$feedback = $directory;
    }

    /**
     * The same redirect for a server started as a subprocess, which a static
     * setter cannot reach. The stdio smoke test records through the real
     * binary, and without this the file lands in the corpus this repository
     * keeps.
     */
    public const FEEDBACK_VARIABLE = 'TYPO3_DEV_COMPANION_FEEDBACK_DIR';

    /**
     * Improvement feedback recorded by agents. Only written to in a standalone
     * checkout; see Feedback.
     */
    public static function feedback(): string
    {
        return self::$feedback
            ?? (($stated = getenv(self::FEEDBACK_VARIABLE)) === false || $stated === '' ? self::root() . '/feedback' : $stated);
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
