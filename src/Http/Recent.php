<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Http;

/**
 * What a host outside this machine answered a moment ago.
 *
 * The reading of an installation is dropped with the call it was taken for,
 * because the caller writes to the installation between two calls and a kept
 * reading answers about the state before its own edit (`D-DIS-011`). A tracker
 * and a review server are the other case: the caller does not write to them
 * through this server, they change at the pace people work, and every call goes
 * over somebody else's network to somebody else's machine. So an answer from
 * one is held for a while, and the while is chosen by the source that knows how
 * fast its own answers turn.
 *
 * What this is for is not the wait. A tool call costs the caller no tokens
 * while it runs, so a slow lookup is cheap to the agent and expensive to the
 * host: an agent walking a list of issues is one session hammering
 * forge.typo3.org, and a rate limit is an unanswered question rather than a
 * slow one. Held answers are what keeps that from happening.
 *
 * A miss is never held here. Whether it is safe to hold one is a question about
 * who can change the answer — and where the caller can, as it can by pushing a
 * patch the next lookup asks about, "there is none" is exactly the answer that
 * must not come out of a store.
 */
final class Recent
{
    /** @var array<string, array{at: int, answer: mixed}> */
    private static array $held = [];

    /** @var (\Closure(): int)|null */
    private static ?\Closure $clock = null;

    /**
     * What was held under this key and is still young enough, or null.
     *
     * @return mixed the held answer, null where there is none to give
     */
    public static function held(string $key, int $seconds): mixed
    {
        $entry = self::$held[$key] ?? null;
        if ($entry === null) {
            return null;
        }
        if (self::now() - $entry['at'] >= $seconds) {
            unset(self::$held[$key]);

            return null;
        }

        return $entry['answer'];
    }

    /** Holds one answer under a key. What may be held is the caller's judgement. */
    public static function hold(string $key, mixed $answer): void
    {
        self::$held[$key] = ['at' => self::now(), 'answer' => $answer];
    }

    /** Drops everything held; for tests, and for a recording that moves between sources. */
    public static function forget(): void
    {
        self::$held = [];
    }

    /**
     * The clock, so a test can age an entry without waiting for it.
     *
     * @param (\Closure(): int)|null $clock
     */
    public static function useClock(?\Closure $clock): void
    {
        self::$clock = $clock;
    }

    private static function now(): int
    {
        return self::$clock === null ? time() : (self::$clock)();
    }
}
