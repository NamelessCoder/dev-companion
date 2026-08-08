---
id: D-SKL-029
date: 2026-08-09
status: open
---

# D-SKL-029 — Precedent is listed by the changelog's own axes before it is asked for in words

**Precedent for a review is listed by the changelog's own type and version with
no query at all, and the checkout answers only what that listing cannot.**

The precedent bullet of `skills/typo3-core-patch-review` prescribes a title-word
query and, where the words miss, `Documentation/Changelog` in the checkout. Both
sessions that have followed it lost the entry their review turned on to the
words and found it by hand, while the same tool returns it in one call with no
query at all.

## Evidence

- `feedback/2026-08-08-224429` reviewed Gerrit change 95179, a BUGFIX making
  stdWrap's `override` apply the value `0`, and its question was whether such a
  change owes an Important entry on an LTS line.
  `typo3_changelog_lookup(query: "stdWrap override")` came back empty, and the
  session settled it with
  `ls Documentation/Changelog/13.4.x/ | grep -i important`, a read of the file,
  and `git log --all --grep 106401`.
- Re-run on 2026-08-09 from `/home/benji/projects/typo3-cms` through
  `bin/typo3-dev-companion`. The query answers the same: no entry carries both
  words, "stdwrap" reaches 43 entries and "override" 22.
- The same call with `type: important`, `version: 13.4` and no query returns 20
  entries in one call,
  `13.4.x Important: Treat 0 as a defined value for nullable datetime fields (#106401)`
  eighth among them. It carries the stated title and the index tags, which the
  session's `ls` did not print.
- The session names that call itself. It did not make it because the
  query-omitted mode is taught in `skills/base.md` step 5, which is the
  deprecation sweep, framed on `type: deprecation` throughout — and that step is
  conditioned on producing a change, which a review that stops at findings does
  not.
- It is the second session to lose a precedent the same way.
  `feedback/2026-08-01-115112` reported the lookup missing the precedent of a
  public-method removal and the grep finding it. The bullet was written for that
  report on 2026-08-03 and is held by
  `SkillTest::aPrecedentIsListedByTypeAndVersionBeforeItIsAskedForInWords`; it
  names the title-word query and the checkout, and no listing.
- The commit keyword the feedback asks to index by is not in what the tool
  reads. `Changelog::directory()` reads the core package's own
  `Documentation/Changelog`, which ships no git history, and the entries above
  the installed major come from docs.typo3.org, which publishes the RST and no
  commit at all.
- The change kind is one existing call away per candidate.
  `typo3_forge_lookup("106401")` answers `Bug`, and the commit in
  `/home/benji/projects/typo3-cms` is
  `[BUGFIX] Treat 0 as a defined value for nullable datetime fields`.
- What a listing costs depends on the line. On 13.4 the four types hold 0
  breaking, 7 deprecations, 1 feature and 20 important entries; on 14 the same
  two types hold 36 and 99, against a `limit` that defaults to 20 and caps at
  50.

## Decided

- **Step 4 of the ladder, wording.** The tool answers this today, the review
  called it, and the mode it needed is stated in the tool's own `query` field.
  What routed the session past it is the step it was standing in, which offers
  words and then the checkout.
- **Queued rather than closed on the spot.** The bullet is a skill contract
  installed into somebody else's project, and the assertions above hold the
  sentences that would move.
- **The listing is named before the checkout, not instead of it.** A released
  line publishes few entries per type, so the whole of what the core did of that
  kind is readable; an open major is not, and there the tag is the bound
  `D-SKL-003` already established.
- **Indexing the changelog by the commit keyword is not the route.** The source
  the tool reads carries no commit, so the keyword would have to come from a
  checkout most callers do not have or from a Forge round trip per entry, and a
  reviewer needs it for the handful of entries its reading picked rather than
  for all of them.
- **`skills/base.md` is not where this is answered.** Generalising step 5 would
  put a listing every task pays for into a sweep that step already conditions
  and bounds.

## Assumed

- That the Forge tracker states the change kind the commit keyword carries.
  Verified on #106401 alone, which is a coincidence rather than a rule, and it
  is what the todo reads before the step sends a reviewer there.
- That a reviewer who has the type and the line can pick the precedent out of a
  listing by title. That is what the session did with 20 file names and no
  titles at all.

## Wrong if

- A review lists a type and a version, gets the cap back, and argues from the
  nearest entry it happened to see. Then the listing is owed the tag on every
  line rather than on an open major, and the step says so.
- A third session with the reworded bullet still settles precedent by grep. Then
  the words were not what cost the first two, and the skill is not where this is
  answered.
- The tracker turns out not to state what the commit keyword says. Then the
  change kind is a reading of the commit, the checkout is what holds it, and the
  step routes there for that half.

## Since then

The wording landed on 2026-08-09, in the precedent bullet of
`skills/typo3-core-patch-review`, and the assertion that held the old one is
`SkillTest::aPrecedentIsListedByTypeAndVersionBeforeItIsAskedForInWords`. The
step now names three sources in order — the listing by `type` and `version`, the
title words, the checkout — and says which line to list: a released one the
change is backported to, or a major before that, which is the same sentence as
the old bullet's refusal of a filter set to the branch under review. The `tag`
is owed on a major still collecting entries; the counts were read again in
`.checkouts/14.3` on the day, where 14.0 through 14.3.x hold 99 breaking and 34
important entries against a `limit` that caps at 50.

The assumption above does not hold, and the third **Wrong if** is what the step
says instead. It was measured over 128 changelog entries of all four types,
sampled from `13.4.x` and from 14.0 through 14.3, each read twice: the Forge
tracker of its issue through `/issues.json`, and the keyword of the commit that
added its file, from `git log --all --diff-filter=A` in the checkout the entry
ships in. The two agree on 101 of the 128 — 20 of 26 important, 20 of 25
breaking, 15 of 19 deprecation, 46 of 58 feature — and the disagreement runs in
both directions. Issues #103140 and #105007 are filed as features and were added
by `[BUGFIX]` commits, which is the shape of the review's own question, and the
other way round is #105653, filed as a bug and added by a `[TASK]`. So the
tracker answers what the issue was filed as, the commit answers what the change
was, and a review arguing that an earlier bugfix of this kind owed an entry
reads the second.

The security entries are the case that has no tracker answer at all. #108604 and
#109585 are important entries of security releases, and forge.typo3.org answers
401 for both. `typo3_forge_lookup` reports that as `unavailable` with cause
`source-not-answering`, which reads as an outage and is a permanent property of
those issues, so a reviewer meeting it would either retry or drop the entry. The
step says so in one sentence.
