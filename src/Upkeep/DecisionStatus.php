<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * What has come back about a decision since it was written.
 *
 * Not a workflow: a decision is written by the commit that implements it, so
 * every entry here is already in the code. What the status answers is the one
 * question a reader has on opening a file that may be a year old — has anybody
 * been back to the **Wrong if**, and what did they find.
 *
 * `corrected` used to be the answer to both halves of that and to a third
 * thing besides. Read out of the twelve entries carrying it on 2026-08-02, it
 * meant "reversed and replaced" on three, "the Wrong if fired" on three, "one
 * named part of it is wrong and the rest holds" on three, and on `D-DIS-003` a
 * measurement where the Wrong if had explicitly not fired. A reader could not
 * tell from the status whether to rely on the entry, which is the only thing
 * the status is for.
 */
enum DecisionStatus: string
{
    /** Recorded, and nobody has been back to it. Most entries, and legitimately so. */
    case Open = 'open';

    /** Somebody went back, and it held. */
    case Confirmed = 'confirmed';

    /** Somebody went back, and it does not hold. The entry stays; the record is the point. */
    case Revoked = 'revoked';

    /**
     * The dated line a status promises is further down the file, or '' where it
     * promises none. A status without its line is a claim about nothing.
     */
    public function line(): string
    {
        return match ($this) {
            self::Open => '',
            self::Confirmed => 'Confirmed on',
            self::Revoked => 'Revoked on',
        };
    }

    /** Whether a reader may still build on this entry. */
    public function stillHolds(): bool
    {
        return $this !== self::Revoked;
    }

    /**
     * The dated lines, in the order a file may carry them.
     *
     * An entry may carry several, because a decision has a history: `D-KNW-003`
     * was confirmed by a run on the morning of 2026-08-02 and revoked by the
     * evidence that arrived the same day. The status names the most recent one,
     * so what a reader relies on is the last line rather than the only one.
     *
     * @return array<int, string>
     */
    public static function lines(): array
    {
        return array_values(array_filter(array_map(
            static fn(self $status): string => $status->line(),
            self::cases(),
        )));
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn(self $status): string => $status->value, self::cases());
    }
}
