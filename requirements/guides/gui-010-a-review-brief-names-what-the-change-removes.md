---
id: R-GUI-010
title: 'A review brief names what the change removes'
status: held
restsOn: [D-GUI-004]
---

# R-GUI-010 — A review brief names what the change removes

**A brief for work that changes nothing states what the change removes or
renames, and what each removal owes, without any word of the task asking for
it.**

`R-GUI-006` gave a review its own shape; what that shape says is this. The one
intent that carries the scanner matcher, the changelog file and the `[!!!]`
prefix is `breaking`, and it is reached by "breaking", "remove public",
"@internal" or "public api" in the task text. A review's task text names the
subject of the patch, because what the diff takes away is what the review is
about to find out. So no review reaches that intent, and a brief that waits to
be asked is silent about the surface exactly where it matters. Stating it
unconditionally is the only shape that reaches the caller who does not yet know
what they are looking at.

The enumeration holds wherever a change is reviewed; the matcher entry, the
changelog file and the two `.rst` checks are the core's own and are left out
where the work is not in it, like every other core-only item of the brief.

## From

A first review of core patch `7175fcaf7fe` ("[TASK] Replace GD-based error
thumbnails with static SVG placeholder"): the checklist that came back directed
nothing at what the diff removed, and the breaking aspect of removing
`GifBuilder::getTemporaryImageWithText()` was under-stated until the user pushed
back (`feedback/2026-08-01-115711`, 2026-08-01). Re-run on 2026-08-03 after
`R-GUI-006` was held: the review shape came back and still named no removal.

## Held by

- `HintsTest::aReviewBriefNamesWhatTheChangeRemoves`
