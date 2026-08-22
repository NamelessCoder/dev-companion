---
id: D-SKL-003
title: "A sweep is bounded by the changelog's own axes"
date: 2026-08-02
status: open
---

# D-SKL-003 — A sweep is bounded by the changelog's own axes

**The deprecation sweep is bounded by the version, the type and the tag the
changelog itself carries, never by a query set derived from what the extension
ships.**

`skills/base.md` step 5 prescribes that query set. The words come from the
caller's package and are matched, all of them at once, against titles the core
wrote about its own code, so the sweep the order exists to guarantee returns
nothing and the silence reads as a clean bill for the next major.

## Evidence

- `feedback/2026-07-31-194459` reports the sweep producing nothing "because the
  changelog tool could not match the queries". `feedback/2026-07-31-194819`, a
  different model in the same project on the same day, carries the queries:
  `form set yaml registration deprecated` and
  `form sets discover yaml configuration`, both empty.
- Re-run on 2026-08-02 from `/home/benji/projects/site-new` through
  `bin/typo3-dev-companion`, against TYPO3 14.3. The first query still returns
  nothing, and the per-word reach line says why — "form" reaches 63 entries,
  "deprecated" 87, "yaml" 2, and no entry carries all five.
- The same call bounded by the changelog's own axes answers it.
  `type: deprecation` with `version: 14` and no query at all returns 75 entries;
  adding `tag: ext:form` returns 6, among them
  `14.2 Deprecation: TypoScript-based form YAML registration (#109412)` — the
  entry the word queries missed, tagged `FullyScanned`.
- Neither bound is in `skills/base.md`. Step 5 names `type: deprecation` and
  then fixes the rest to "the symbols and registration shapes step 2 reported",
  and the sentence "the query set is derived from the extension's own surface"
  is asserted verbatim by
  `SkillTest::theDeprecationSweepRunsFromTheExtensionsSurfaceAndIsReportedWhenItFindsNothing`.
- The tool says both. Its description calls the `tag` field "what a sweep is
  bounded by where words are not", and the `query` field says "omit to list a
  version or a type as a whole". `D-ANS-006` established that reading when it
  added the field.
- The feedback's own proposal was tested and does not hold. `typo3_hint_lookup`
  for the sitepackage's YAML path on 14 returns the site-set, page-rendering and
  layout hints and no statement about `#109412`. The hints answer conventions;
  the entry the sweep is for was reached by the tag and by nothing else.

## Decided

- **Step 4 of the ladder, wording.** The rule was delivered and the session
  followed it: it called the right tool with the right `type` and lost the sweep
  to the query shape the step itself prescribes. No verb is missing —
  `D-ANS-006` shipped the enumeration and the tag — and no routing entry would
  have changed the call.
- **Queued rather than closed on the spot.** `skills/base.md` is a skill
  contract installed into somebody else's project, which
  [judging.md](../../documentation/records/judging.rst) puts on the far side of
  the autonomous line. The change also has to move `R-SKL-005` and the assertion
  above, which currently hold the wording that is wrong.
- **The feedback's suggestion is rejected, on the evidence above.** Architecture
  hints are not sufficient evidence for an empty sweep. What makes the
  substitution attractive is that the hints are filtered to the target version
  and carry predecessor statements, so they read like a version answer; what
  they carry is where a convention holds, and a deprecation nothing in the
  corpus describes is not in them.
- The overlap the feedback names is real and is not a licence. Both lookups
  speak about version boundaries, and only one of them is a record of what
  changed.
- Recorded here rather than against `typo3_changelog_lookup`. The tool answers
  the sweep in one call today; what is wrong is the order that asks for it.

## Assumed

- That a step naming the two bounds is enough for a session that has the
  extension's surface in hand and would otherwise type it. Nothing measures
  which wording a caller matches itself against, which is the same assumption
  `D-ANS-010` left open about routing.
- That the tags are worth bounding a sweep by for a project sitepackage. They
  name the system extension a change is **in**, so a sweep over a package that
  touches five of them is five calls, and `D-ANS-006` already established that
  an extension key of the caller's own matches none of them.

## Wrong if

- A conformance review bounds the sweep by version and type, gets the 75 entries
  of one major back, and reports a finding for a deprecation the checkout never
  calls. Then the query set was doing work the step did not credit it with, and
  the fix traded a silent miss for a false positive.
- A later feedback reports the enumeration as unusable at the caller's end — too
  many entries to verify against the checkout, at every major the package
  declares. Then the bound is a filter this server owes rather than one the
  caller composes, and `D-ANS-006`'s reading of the tag needs revisiting.
- The wording lands and the same "the sweep returned nothing" ending recurs.
  Then the query shape was not what cost it, and the base is not where this is
  answered.

## Since then

The wording landed on 2026-08-02, in `skills/base.md` step 5, `R-SKL-005`,
`R-SKL-007`, `skills/typo3-extension-upgrade/SKILL.md` and the assertion that
held the old sentence. The step now names three axes and no query: the type,
each major the package declares, and a tag per call.

Which tags a sitepackage names was the half the entry left open, and it was
settled against the changelog in `.checkouts/14.3`, read with
`Changelog::read()`'s own parser. The tags are dense enough to bound a sweep
with: of the 75 deprecations of 14 every one carries at least one tag and one
carries no `ext:` tag, and 12 and 13 are the same shape — 128 and 63
deprecations, one without an `ext:` tag each. So a sweep composed of tags leaves
one entry per major outside itself, and that is the gap the annotations source
in `typo3-extension-upgrade` covers rather than a reason to widen.

`ext:` alone is a weak bound for a sitepackage, which is why the step names the
surface tags beside it. `printworks_sitepackage`, the package both feedback
swept, requires core, fluid_styled_content, form, frontend, impexp and seo; of
those, `ext:core` carries 30 of the 75 on its own and three of the six carry
none, so the six calls it declares reach 34. What that package actually is
reaches further and narrower: `TCA` 12, `Fluid` 5, `TypoScript` 3, `YAML` 3,
`TSConfig` and `FlexForm` 1 each. It also renders through Fluid and configures
the backend without requiring `typo3/cms-fluid` or `typo3/cms-backend`, whose 14
deprecations are 5 and 19 — which is why the step says *requires, renders
through or registers into* rather than reading the manifest.

The tag is also what keeps the sweep readable, and that is stronger than the
entry assumed. `limit` caps at 50 and defaults to 20, so the unbounded
enumeration of one major cannot return all 75 in a call at all, and #109412 —
the entry the word queries missed — sorts 39th. Omitting the query without the
tag would have missed it a second way.

Nothing was said about which tags exist, deliberately. One call carrying any tag
returns every tag that version and type carry, so the vocabulary is read off the
first answer; the step says that instead of listing a vocabulary this repository
would then have to keep in step with the core.

The wording generalised no further than the step it was written in.
`feedback/2026-08-08-224429`, a core patch review on 2026-08-08, names the
query-omitted mode, places it in this step, and says it did not apply it to
`type: important` because that is where it was taught. So the mode is stated
once, as a property of the sweep, and a task needing a listing for something
else reads it as somebody else's step — `D-SKL-029`.

## Since then

The second **Assumed** was measured in practice on 2026-08-21, and the
composition costs more than the enumeration it replaces.
`feedback/2026-08-19-094403` reports the step run over an extension declaring
one major: eleven tags, eleven calls, four of them returning nothing the session
used. Re-run against `.checkouts/14.3` those eleven reach 72 of the 75
deprecations of 14 and cost 69,426 characters, where the same version and type
with no tag matches all 75 in one call and would cost about 41,600 once the cap
can carry them. The tags being dense enough to bound a sweep with — what this
entry established — is also what makes a sweep composed of them converge on the
major.

The statement above stands: the axes a sweep is bounded by are still the
changelog's own, and the version and the type are two of the three. What changes
is the third, the "one call per declared major per tag" half of step 5, and
`D-ANS-093` is where that is decided.
