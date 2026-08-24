---
id: R-ANS-033
title: A path names the repository it is in
status: open
restsOn: [D-ANS-097]
---

# R-ANS-033 — A path names the repository it is in

**A brief given paths in one repository does not carry, in place of a hint that
binds work there, one declared for another.**

A caller that names a path has said which repository the work is in, and the
brief states that verdict back in `scopes`. Where the payload has a ceiling,
keeping a hint declared for a different repository costs one that binds this
caller — so the verdict has to reach the order and not only the label printed
beside each block.

## From

`feedback/2026-08-24-100427`, a core patch review on 2026-08-24: two `core`
paths, four hints carried, two of them declared `extension` and `project`, and
the two `core` hints the paths name left in `omittedHints`. The mirror was
measured the same day and is worse, and `feedback/2026-08-24-140340` reports the
same shape from the extension side in another checkout. `D-ANS-097` carries both
readings.

## Held by

not guarded — the ranking is queued rather than built, and the two calls
measured in `D-ANS-097` are what a test would assert.
