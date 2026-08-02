<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

/**
 * Whether a requirement is built, and whether anything holds it there.
 *
 * Two of the three are written in the front matter and the third is derived,
 * which is why this is not simply the `status` field: a requirement that says
 * it is `held` and names no test is not held by anything, and from a listing
 * it looks exactly like one that is. `Held` is the only state that promises a
 * reader something will fail if the sentence stops being true.
 *
 * None of the three is an error. `bin/cli backlog:list` reads them out and
 * fails on none of them, because whether an entry is worth working off is a
 * judgement — see `D-FBK-1`.
 */
enum RequirementState: string
{
    /** Written down and not built. The backlog. */
    case Open = 'open';

    /** Built, and nothing would catch it going wrong. */
    case NotGuarded = 'not guarded';

    /** Built, and the tests it names hold it there. */
    case Held = 'held';

    /**
     * The states that may be written in the front matter. `not guarded` is
     * never one of them: it is what a claim of `held` turns out to be when the
     * entry names no test, and an entry may not claim it of itself.
     *
     * @return array<int, self>
     */
    public static function written(): array
    {
        return [self::Open, self::Held];
    }

    /**
     * @return array<int, string>
     */
    public static function writtenValues(): array
    {
        return array_map(static fn(self $state): string => $state->value, self::written());
    }

    /** Whether the sentence is built at all. */
    public function isBuilt(): bool
    {
        return $this !== self::Open;
    }

    /** Whether something would fail if the sentence stopped being true. */
    public function isGuarded(): bool
    {
        return $this === self::Held;
    }
}
