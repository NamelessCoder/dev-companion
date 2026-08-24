---
id: D-ANS-097
title: A bounded answer spends its slots on the hints that bind its caller
date: 2026-08-24
status: open
coveredBy:
  - HintsTest::aCoreBriefSpendsItsSlotsOnTheCoreHints
  - HintsTest::anExtensionBriefSpendsItsSlotsOnTheExtensionHints
---

# D-ANS-097 — A bounded answer spends its slots on the hints that bind its caller

**Where an answer carries only the strongest few hints, one declaring a scope
the call's paths are not ranks below one that declares theirs.**

`D-KNW-007` decided that nothing is filtered by a declared scope, and it decided
it for an answer that lists what it matched. A brief carries four hints per
group of paths, and there the same hint takes a place from one the caller is
obliged by.

## Evidence

- `feedback/2026-08-24-100427` re-run on 2026-08-24 against the corpus as it is,
  with the two paths and the task text it names. Both paths are classified
  `core`, and the brief carries `extension-asset-build` (`scope: extension`),
  `public-assets` (none), `project-build-and-scripts` (`scope: project`) and
  `backend-typescript` (`scope: core`). The session used the fourth.
- What it left is what the paths name. `omittedHints` is `backend-ui` and
  `javascript-unit-tests`, both `scope: core`: six hints matched, and the slice
  of four kept two that bind nobody in the call.
- The order is the tier `D-ANS-060` installed. `backend-typescript` matches 63
  characters of curated vocabulary and scores 0 on its own words, so it sorts
  below `extension-asset-build` at 31 and 23 and `project-build-and-scripts` at
  6 and 10. A hint about somebody else's repository answers the query's words
  because a build is described in the same words wherever it runs.
- The mirror direction is worse and was measured the same day: two paths under
  `packages/<key>/` and a functional-test task rank `core-tests` (`scope: core`)
  **first**, at 33 and 168, above `project-extension-tests` (`scope: extension`)
  at 15 and 113 — which is the hint that binds there.
- `feedback/2026-08-24-140340` is that call as the session that made it reports
  it, from another checkout. `typo3_task_guide` in a distributed extension
  returned `project-extension-tests`, `core-tests`, `site-sets`,
  `environment-placeholders` and `extension-manifest`, and the report names two
  of the five as what it used.

## Decided

- The order changes and nothing is dropped. `D-KNW-007` stands as it is written:
  an off-scope hint is somebody else's convention rather than inverted advice,
  and a project layout in a core answer is still worth reading. What it did not
  weigh is a payload with a ceiling, where keeping one is not free.
- A hint the order moves down stays reachable by name.
  `feedback/2026-08-24-140340` reports `omittedHints` and `availableHints` as
  what made a hint it could not have guessed from the title arrive at all, so
  naming it there and leaving it one call away by id is the whole of what the
  demotion may cost.
- Where the tier sits is measured rather than settled here. `D-ANS-060` is what
  happens otherwise: the corpus prune it proposed was disproved by a sweep, and
  the ranking change that did land left `bin/cli hints:coverage` byte-identical.
- The repair touches `src/`, which is the line
  [judging.rst](../../documentation/records/judging.rst) draws around what a
  judging run may improvise, so it is queued rather than made here.
- The feedback's second half is answered and trimmed off rather than queued with
  it. `site-setting` matched weakly on the bare `setting` in its `matchWeak`,
  which is what that field is for — a word that names a subject without naming
  the work — and each of its four checklist items arrived prefixed with the
  condition it holds under. Gating a weak intent on the paths, which is what the
  report asks for, would suppress a correct conditional item whenever a caller
  named a subset of the files the task touches.

## Assumed

- The group's scope is the comparison rather than the call's. `Scope::groups()`
  already splits a call's paths by scope and the brief matches hints per group,
  so each block has one scope to be measured against — `D-SCO-009` is why a call
  naming a core path and an extension path is two questions.
- The ceiling is what turns an order into a loss. `typo3_hint_lookup` returns
  the same set at its own limit whichever way it is ordered, so where the tier
  is put decides whether that lookup's order moves and not whether its answer
  does.

## Wrong if

- The reordered brief loses a hint a caller used. Two calls are the whole of the
  evidence above, and `bin/cli hints:coverage` byte-identical before and after
  is what would say no hint became unreachable.
- The scope-bearing hint turns out to be the one the caller wanted, which is the
  case `MatchedHints::scopeNotice()` was written for: a project building a
  backend module wants the backend's design system. Then the label was the whole
  answer and the order is wrong.
- The order changes and the payload does not — the same four ids in the same
  four slots. That would say the ceiling rather than the ranking is what has to
  move.

## Since then

**Built on 2026-08-24, and the payload moved.** The core call now carries
`public-assets`, `backend-typescript`, `backend-ui` and `javascript-unit-tests`,
with `extension-asset-build` and `project-build-and-scripts` named in
`omittedHints`; the extension call carries `project-extension-tests` and leaves
`core-tests` there. So the third **Wrong if** is settled and the two above it
are not: both turn on a later caller's report.

The tier is `TaskGuide::bindingFirst()`, and what it demotes is what
`MatchedHints::scopeNotice()` has something to say about — so the order and the
notice above a block answer one question, and `D-KNW-007`'s rule that `project`
and `extension` are not told apart from each other holds for both. The brief
matches once at `HintLookup::MAX_HINTS` and cuts to `HINTS_PER_GROUP` itself,
where it used to match twice: applied to a slice already taken, the tier would
reorder the four rather than choose them. `bin/cli hints:coverage` is
byte-identical.

**Half the cost this entry priced is not payable, and the half that is holds.**
The judgement of `feedback/2026-08-24-140340` is in `D-FBK-018`, and it re-ran
the calls on 2026-08-25. `availableHints` is not one call away by id: a call
that names an id has had the index withheld since `1a22d214`, so what such a
call carries is the count and the parameter that asks for the list. The
`omittedHints` half reproduces — an operations brief for that report's task
shape names `project-configuration-files` among the five it left — and it is
where the session's ids can have come from. So the demotion may cost the brief's
own pointer, and the Decided above overstates the routes by one.
