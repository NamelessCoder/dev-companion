---
id: D-SKL-070
title: A description is held to a length of its own
date: 2026-08-24
status: open
coveredBy:
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
---

# D-SKL-070 — A description is held to a length of its own

**Each skill description is held to 360 characters, and the listing is held to
no total at all.**

A fixed sum blocks the next skill for the length of the ones written before it,
which is what happened to the fourteenth.

`D-SKL-026` set a ceiling over the listing and `D-SKL-064` raised it, saying in
the same entry what it could not do: no fixed total absorbs another skill. The
fourteenth arrived and was blocked exactly as that entry predicted.

## Evidence

- Measured on 2026-08-24. The listing costs 3944 of the 3970 ceiling and the
  asset build description costs 461, so 435 had to come out of fourteen
  descriptions that were already written.
- **Shortening does not reach.** An honest trim of the four longest yields 67
  with no routing token removed. The other ten are lists of triggers —
  `data.xml`, `Playwright`, `PHPStan`, `Gerrit`, `TCA`, `sitepackage` — and a
  description reaches a session by exactly those words.
- `typo3-core-patch-checkout` is not trimmed at all: `D-SKL-026`'s **Since
  then** records a session that stopped activating it the last time its clause
  was cut.
- The ceiling has moved twice, from 3600 to 3970 and now again, each time by the
  amount the next skill needed. A number that follows what happened is a record
  rather than a limit.
- Thirteen of the fourteen descriptions are already under 360. The fourteenth
  came down from 429 to 345 to be published, and no trigger came out with it.

## Decided

- The cap is per description, at 360. What it holds is a description growing
  without bound, which is what crowds a listing and is the thing the ratchet was
  written for.
- The total is not held. How many skills this server publishes is decided by
  which domains earn one, and it was never decided by arithmetic over the
  descriptions already written.
- `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` replaces the test that
  summed them, and the entries that named the old one name the new one.
- Rejected: raising the ceiling a third time. Rejected too: merging two more
  skills, which bought one publication last time and left the next one at the
  same wall.

## Assumed

- That 360 is a length a description can say what it needs in. Thirteen do, and
  the fourteenth came down without losing a trigger, which is the whole of the
  evidence.
- That the total is somebody else's limit rather than nobody's. A client drops
  whole descriptions where they do not fit, least used first (`D-SKL-026`), so
  there is a real sum somewhere and this entry does not know where.

## Wrong if

- A published skill stops being activated because its description was cut to
  360, which is what `D-SKL-026` recorded happening once already.
- A client drops a description because the listing outgrew what it keeps. That
  is the limit this gives up holding, and nobody has measured it since
  2026-08-08 — the measurement is what would say whether giving it up was safe.
- The descriptions stay short and the count grows until the listing is the
  problem the total was guarding against, one skill at a time.
