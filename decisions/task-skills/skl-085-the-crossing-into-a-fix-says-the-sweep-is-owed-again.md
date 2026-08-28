---
id: D-SKL-085
title: The crossing into a fix says the sweep is owed again
date: 2026-08-28
status: open
coveredBy:
  - SkillTest::theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks
---

# D-SKL-085 — The crossing into a fix says the sweep is owed again

**`typo3-extension-health` says at the step it starts writing that a list built
out of a review still owes the deprecation sweep.**

`skills/base.md` grants the exemption and closes it in the same paragraph. That
paragraph is read while the session is deciding to skip the sweep, and the
moment it applies is minutes later in another workflow.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-074058`](../../feedback/archive/2026-08-28-074058-the-deprecation-sweep-exemption-has-no-second.md).
  It reviewed a pull request under `typo3-extension-patch-review`, skipped the
  sweep under the exemption and said so in its report. Asked for the fix, it
  re-entered through `typo3-extension-health`, crossed into two more workflows,
  committed, and never called `typo3_changelog_lookup` on either declared major.
- **It names why**, and the reason is not the wording of the rule: `base.md` was
  already in its context from the review, so re-entering did not re-read it, and
  none of the step lists it then worked asks whether the sweep now owes.
- **The rule was there and is unchanged.** "The exemption ends where the
  workflow produces a change. A review asked to make the change is that other
  workflow, and it starts this order again holding the files it is about to
  write."
- **The skill marks the crossing itself.** Step 10 calls it the transition, and
  step 9 is where the items start being worked — the last moment before anything
  is written.
- **`D-SKL-049` carries the general form of this** and defers it: a closing gate
  naming what a workflow still owes, asked of the maintainer on 2026-08-19 and
  again on 2026-08-27, answered *wait* both times. Its three prior sightings
  were each corrected at their own point of use instead, which is what this is.

## Decided

- **The clause goes on step 9**, not on step 5 where the report puts it. The
  sweep is one call per declared major over the paths the items name, so what it
  needs is the list finished and nothing written yet, which is step 9 exactly.
- **Against the report's second half**, that every skill opening with "work
  through references/base.md" distinguish reading the document from running its
  steps again. The sweep is per major and per session rather than per file, so
  re-entry on its own owes nothing; what owes is an exemption that was taken,
  and that is the one condition the clause names.
- **Against a closing gate here**, which is `D-SKL-049`'s question and is
  waiting on an answer this does not pre-empt.
- Held by `SkillTest::theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks`,
  where this skill's own sentences about the sweep are already asserted.

## Assumed

- That a session re-entering through this skill reads step 9, having not re-read
  the document step 1 names. The report says the step lists are what it worked
  from.

## Wrong if

- A session reports running the sweep twice, once in the review and again here,
  because the clause reads as unconditional. The condition is the exemption
  having been taken, and it is one clause away from being missed.
- A review that took the exemption reaches the change through some other
  workflow than this one — the maintainer asks the testing skill directly — and
  the clause is in the wrong file. Then the gate `D-SKL-049` defers is what this
  wanted after all.
