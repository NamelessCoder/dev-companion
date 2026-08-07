---
id: D-ANS-060
date: 2026-08-07
status: open
---

# D-ANS-060 — A bare word in `appliesTo` reaches a path segment and outranks the subsystem

**A one-word `appliesTo` pattern is matched against the caller's paths as a
prefix, so `storage` in the FAL hint claims every path with a `Storage/`
segment.** The keyword tier then puts that hint above the ones that are about
the subsystem the path names.

Two sessions reported the same wrong answer from two tools on one day, and both
named FAL hints returned for Extbase persistence paths.

## Evidence

- `feedback/2026-08-07-132426` called `typo3_hint_lookup` with three paths under
  `typo3/sysext/extbase/Classes/Persistence/` and got `datahandler-basics`,
  `system-extension-boundaries` and `fal-storages-drivers`. It names the two it
  wanted and did not get: `persistence-reading` and `extbase-domain-mapping`,
  both present in `availableHints`.
- `feedback/2026-08-07-065259` reports the same three plus
  `extbase-domain-mapping` from `typo3_task_guide` with the same paths, hours
  earlier and in a different task. Its closing line asks why those paths select
  `fal-storages-drivers` at all.
- Re-run here on 2026-08-07 against the corpus as it is: the call returns
  `fal-storages-drivers` with `keywords: 7`, `score: 0`. Seven is the length of
  `storage`, and the score is zero — nothing in the hint's own words answers the
  query. `persistence-reading` and `extbase-domain-mapping` are not returned.
- `Hints::scoreKeywords()` is where it happens. A pattern that is a bare word is
  asked of the task with `carriesWord()` and of the paths with `carries()`, and
  the second is a prefix match with no word boundary — `D-ANS-050` set that
  split so `ThumbnailViewHelper.php` could be reached. `fal-storages-drivers` is
  the only hint in the corpus whose `appliesTo` carries the bare `storage`.
- The sort in `Hints::find()` reads `keywords` before `score`. A hint matching
  one seven-character pattern and answering nothing therefore outranks one whose
  own words answer the query and whose vocabulary nobody anticipated.
- Dropping the bare `storage` was measured rather than argued. It removes
  `fal-storages-drivers` from the Extbase call, and the three FAL queries that
  reached it through that pattern — "file storage driver configuration", "which
  storage does this file come from", "storage uid 0 public directory" — each
  still rank it first, on their text alone. The change was reverted; it is the
  todo's material, not this entry's.

## Decided

- The false positive and the two absent hints are one finding with two halves,
  and only the first half is in the corpus. Removing the pattern stops the wrong
  answer and does not produce the right one: re-measured with `storage` gone,
  `persistence-reading` and `extbase-domain-mapping` are still not returned.
- So the repair is the matcher rather than the data, and it is queued instead of
  made here. It touches `src/`, which is the line
  [judging.md](../../documentation/feedback/judging.md) draws around what a
  judging run may improvise.
- What is decided is that this is a defect and not a phrasing accident. Two
  sessions, two tools, one corpus reading; the caller named the subsystem in the
  paths, which is the least ambiguous thing a caller can give.

## Assumed

- The path is the stronger signal where the two disagree. A caller that names
  `typo3/sysext/extbase/Classes/Persistence/` has said which subsystem this is,
  and free text saying "storage" has not.
- The three FAL queries measured stand for the pattern's worth. They are the
  phrasings that reach it through `storage` and they were picked by hand, not
  swept — `bin/cli hints:coverage` is the sweep and it has not been run against
  this change.

## Wrong if

- A sweep shows FAL queries that lose their answer when the bare pattern goes,
  which would say the pattern earns its keep and the fix is entirely in the
  ranking.
- Weighting paths above free text costs a hint that is reached today only
  because a path was vague, which would say the two signals are not ordered.
- The same shape turns up for a hint with no bare-word pattern at all, which
  would say the tier order is the whole of it and `appliesTo` is innocent.

**Since then**, on 2026-08-07, the third **Wrong if** fired within the hour, and
two things above are corrected in place.

The first is that the false positive was never one hint. `datahandler-basics`
outranked the FAL hint at `keywords: 24`, on `/Persistence/` and `persistence`,
and its own words answered the query no better — `score: 0` for the pattern
half. It is a path fragment rather than a bare word, so `appliesTo` is not
innocent and neither is the shape this entry named: what claims another
subsystem's path is any short pattern, punctuated or not.

The second correction is the one that matters more, and it disproves the
**Decided** above rather than refining it. This entry took the reporting
session's word that `persistence-reading` and `extbase-domain-mapping` are "the
hints that are about the subsystem". Reading both bodies says otherwise.
`persistence-reading` is the core `QueryBuilder`, the restriction containers and
`PageRepository`'s overlays; `extbase-domain-mapping` is the model, its table
and its orderings. Neither covers `Typo3DbQueryParser`, `ColumnMap` or
`Backend::insertObject()`, which is the whole of what the paths named. Their
titles read as though they do, and that is what misled the session and this
entry after it.

So the absence is a corpus gap and not a ranking failure. Removing the two
patterns is what landed, measured on both sides: the three FAL queries that
reached `fal-storages-drivers` through the bare `storage` each still rank it
first on their text, the DataHandler path still reaches `datahandler-basics` at
`keywords: 25` without `/Persistence/`, and the `ThumbnailViewHelper.php` case
`D-ANS-050` exists for is untouched. `R-ANS-026` is held by two cases in
`HintsTest` and now demands silence over a wrong subsystem rather than a named
hint, because there is no right one to name.

What is queued is the half this cannot reach. A pattern that counts only for its
own subsystem is the principled fix and it is a change to the matcher, which
this run measured for but did not make; the Extbase persistence hint that does
not exist needs a core checkout, and all four are missing here.
