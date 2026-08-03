# Which caches a change invalidates, and the command that clears them

**Serves:** feedback/2026-08-01-003937-cache-clearing-was-done-by-deleting-files-rm-on.md
**Priority:** low

Step 1a of the ladder, on the evidence in
[`D-KNW-027`](../../decisions/knowledge/knw-027-which-caches-a-change-invalidates-is-a-gap-this-server-owns.md):
nothing here says how a change is cleared from an installation's caches, and
the one sentence naming `typo3 cache:flush` sits behind the
`installation-upgrade` condition that excludes work on the code. Establish
first whether a changed Fluid template invalidates its own compiled entry —
the identifier belongs to standalone `typo3fluid/fluid`, which no checkout
vendors, so read the release each branch's `composer.json` pins, `^2.15.0` on
12.4, `^4.6.1` on 13.4 and `^5.3.1` on 14.3 — because a template change that
clears itself makes this a correction rather than a command. Then read
`CacheFlushCommand`, `CacheFlushTagsCommand` and `CacheWarmupCommand` against
the `groups` each entry declares in `DefaultConfiguration.php` on
`.checkouts/12.4`, `.checkouts/13.4` and `.checkouts/14.3`, for which group a
template, a TypoScript and a TCA change each needs and what deleting a cache
directory leaves behind in the database-backed `pages` group. Then write it
where such a change is worked rather than where a cache is declared —
`frontend-page-rendering` in `knowledge/hints/fluid.json` carries the
neighbouring `sendCacheHeaders` statement — with a version range only where
the three majors differ, and a requirement for what has to keep holding.
