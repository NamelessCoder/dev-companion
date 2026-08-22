---
id: D-SKL-030
title: A review surface names the lookup that can answer it
date: 2026-08-09
status: open
---

# D-SKL-030 — A review surface names the lookup that can answer it

**The core patch review's documentation surface is routed to
`typo3_documentation_lookup`, which reaches the manual that surface is named
after.**

A review shipped the claim that a stdWrap property's documentation lives outside
the core repository, offered as the reason no documentation change was owed. The
page is one call away, and the patch makes its wording false.

## Evidence

- `feedback/2026-08-08-224516`, a review of Gerrit change 95179, "[BUGFIX] Let
  stdWrap override apply the value 0", in a core checkout. The session reports
  that the claim went into a delivered work product unchecked and that
  `typo3_documentation_lookup` was never called.
- Re-run here on 2026-08-09 through `DocumentationLookup::answer()`, with
  `queries: ["stdWrap override"]` and `targetVersion: "13.4"`. It answers, and
  its first result is the page titled `stdWrap` in `typo3/reference-typoscript`,
  at `Functions/Stdwrap.html`. The manual is reachable and the claim was wrong.
- That page carries an `override` section stating that the content is loaded
  when override returns something other than the empty string or zero. The patch
  makes zero apply, so the documented sentence becomes false and a follow-up was
  owed where the review said none was.
- `src/Manual/Documentation.php` searches four manuals and
  `typo3/reference-typoscript` is one of them.
  `DocumentationLookup::description()` names none of the four. The only book it
  mentions is the ViewHelper reference, in the `f:` special case.
- `typo3_documentation_lookup` is named in seven published skills and in neither
  core one. `typo3-core-patch-review` sends the obligations a diff raises to
  `typo3_rule_lookup` and the precedent to `typo3_changelog_lookup`, and names
  no third owner.
- The surface exists and is named for two things. `references/checklist.md`
  heads it **Documentation and changelog** and then lists the entry, its
  directory, its file name and its cross-references — every item the
  changelog's. A session disposing of the surface finds no manual in it and no
  lookup holding one.
- `R-SKL-013` already binds the reviewer: a surface reported as assessed names
  what was read. It was the skill that had nothing to name.
- `knowledge/documents/core/contribution/rules.md` says nothing about when a
  patch owes a documentation change. So what the surface routes to is a page of
  the manual, and whether the obligation itself is written down anywhere is a
  second question this entry does not answer.

## Decided

- The judgement is **step 3, routing**. The tool exists, it answers the query
  the session never made, and nothing leads to it from the surface that needed
  it.
- The documentation surface names `typo3_documentation_lookup` as what answers
  its manual half, in `references/checklist.md` and at the obligation step of
  `SKILL.md`.
- `DocumentationLookup::description()` names the four manuals it searches. A
  caller decides from the description whether a subject has a book at all, and
  the three examples `skills/base.md` offers — a ViewHelper, a TCA type, a
  TypoScript setting — are three of those books named by their subjects instead
  of by their names.
- **The feedback's own suggestion is rejected.** `typo3_server_scope` does not
  go into `skills/base.md` as a conditional step. `D-ANS-061` decided that the
  lever is the tool a session does call, on two sessions that skipped
  orientation because it felt complete, and this is the third. The base is what
  `D-SKL-001`'s **Wrong if** watches, and it has grown from 496 words to over
  1500.
- The moment the feedback names is real, and it is not the start of a session. A
  boundary is asserted while a surface is being disposed of, which is the
  skill's moment rather than the base's.

## Assumed

- That a route named at the surface is taken where an orientation tool was not.
  Both are prose the session reads; what separates them is that the surface has
  to be answered in the report and the call does not.
- That a book name means something to a caller holding a subject. A session
  reading "TypoScript Explained" while holding `stdWrap_override` still has to
  make the reduction the tool's own `insteadOf` suggests on a miss.
- That the manual half of the surface is worth a call on an ordinary patch. What
  is measured is one patch where it was worth it.

## Wrong if

- A review names `typo3_documentation_lookup` on the documentation surface,
  reports it assessed, and still misses a page the diff makes wrong. Then the
  route was not what was missing and the reading is.
- A core review calls the lookup on every patch and it answers nothing on most
  of them. Then the surface bought a call per review for a page that rarely
  exists, and the obligation belongs to the change types that alter documented
  behaviour rather than to the surface.
- A fourth session reports `typo3_server_scope` unreached for a question no
  skill's surface owns. Then the moment is not the surface, and the base is back
  on the table.

## Since then

The second question this entry left open — whether what a patch owes the manual
is written down anywhere — was answered on 2026-08-09 and the answer is a rule,
now `## Documentation` in `knowledge/documents/core/contribution/rules.md`. The
core repository carries the changelog and the manuals of the system extensions,
and a patch changes both itself; the four books this lookup searches are
maintained in the TYPO3-Documentation organisation and changed by a pull request
there. What the patch owes them is the changelog entry, which
`Documentation/Changelog/Howto.rst` names as the channel that informs the
documentation team, identically on 12.4, 13.4, 14.3 and `main`. So the claim the
review shipped was half right and drew the wrong conclusion from it: outside the
repository is where the follow-up goes rather than a reason none is owed. The
routing is `R-SKL-022`.
