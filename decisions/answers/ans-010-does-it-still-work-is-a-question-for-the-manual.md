---
id: D-ANS-010
date: 2026-08-02
status: open
---

# D-ANS-010 — "Does it still work" is a question for the manual, not the changelog

**"Does this still work in version N" is routed to the manual for that version,
not to the changelog, which is silent on everything nothing changed.**

A changelog records change events. A pattern that has worked unaltered for ten
majors has no entry, so an empty result reads as "nothing found" where the
correct answer is "still supported".

## Evidence

- `feedback/2026-07-31-174524`, re-run on 2026-08-02 against
  `/home/benji/projects/bootstrap_package`. `typo3_changelog_lookup` with
  `query: "BackendLayout"` returns 11 entries now rather than the one the
  feedback reports — `D-ANS-006` landed since, and the query reaches "backend
  layout" however it is spelled. None of the 11 answers what the session asked.
- `typo3_documentation_lookup` with `targetVersion: "14"` and the query
  `backend layout` returns the two pages that do answer it, first and second.
  Reading the TSconfig one with `page` settles it in that call: `identifier` is
  documented as what the page content DataProcessor addresses a column by, "a
  more meaningful representation than just colPos", while `colPos` is what
  carries the content elements. The session read `GridColumn.php` in the
  installed core by hand and still recorded the finding as unverified.
- `bin/cli hints:probe "BackendLayout"` reaches nothing about backend layouts,
  and neither `knowledge/` nor `skills/` carries the words at all. That is not
  the gap. The manual owns this subject and answers it, so there is nothing to
  write here — only something to point at.
- The routing block pointed the other way. `typo3_changelog_lookup` was routed
  for "before asking what a version changed **or whether an API is still
  there**", and the entry for `typo3_documentation_lookup` named a source —
  "Needing the official API, reference or tutorial documentation" — which a
  session holding a behaviour question does not recognise as its own.
- The same session reported the same shape twice more.
  `feedback/2026-07-31-174526` ends "No lookup covers whether such a
  registration is still consumed in the active version: I had to read installed
  vendor core", and `feedback/2026-07-31-174529` names the pair "per-version
  behavior questions".

## Decided

- The judgement is **step 3 of the ladder**, routing, not the missing capability
  the feedback proposes. The tool that answers exists and answered in one call;
  nothing pointed the question shape at it.
- The routing half is **closed on the spot**. The false clause is out of the
  changelog entry and an entry for the shape now names
  `typo3_documentation_lookup` with `targetVersion`.
  [judging.md](../../documentation/feedback/judging.md) puts that on the
  autonomous side: it touches no `src/`, no declared schema and no skill
  contract, and it writes no statement about TYPO3.
- The skill half is **queued**, because the routing block reaches a session only
  through `typo3_server_scope`. The order this session actually followed is
  `skills/base.md`, where the changelog sweep is a numbered step and
  `typo3_documentation_lookup` is a conditional bullet under it. That is a skill
  contract and is reviewed rather than improvised.
- Recorded here rather than against `typo3_changelog_lookup`, because the
  property belongs to every lookup over a record of events. What is asked of the
  changelog is what happened; whether something holds today is asked of the
  reference for the version.

## Assumed

- That the manual answers this class of question at the covered versions. One
  case is verified above and the second instance, the inert
  `contentRenderingTemplates` registration, is not — a magic key nothing
  consumes may well be documented nowhere.
- That a routing entry phrased as a question reaches a caller that an entry
  phrased as a source did not. Nothing measures which of the two a session
  matches itself against.

## Wrong if

- A session follows the new routing entry, calls `typo3_documentation_lookup` at
  the target version, and still has to read the installed core by hand. Then the
  manual is not the answer for this shape and the feedback's own proposal — a
  capability that resolves behaviour rather than change — is what was missing
  after all.
- The skill half lands and a later conformance review reports the same "I had to
  read installed vendor core" ending. Then the order was not what kept the tool
  from firing.
- A feedback disputes the changelog's silence the other way — an entry that
  exists and was not reached — which would make this a matching problem rather
  than a routing one.

## Covered by

- `ScopeTest::everyToolNamedInTheScopeExists`
- `SkillTest::theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks`

## Since then

The skill half landed on 2026-08-02, as a sentence on the sweep rather than as a
step of its own. Two things settled that. The sweep can state its query set
before a file is opened because step 2 derives it, and a version-behaviour
question has none until the reading raises the pattern — so a sixth step would
be a call every task pays for with nothing to put in it. And the failure was not
that the question went unasked but that an empty changelog was read as its
answer, which is a statement about what step 5 is worth and belongs on step 5.
`skills/base.md` now carries it, which reaches every published skill through the
copy the installer writes; `typo3-extension-conformance` had the narrower
condition of the two — the manual "where an official API or configuration detail
decides the finding" — and now names the question shape as well.

The `Assumed` above got its second reading in the same run. Re-run from this
checkout on 2026-08-02, `typo3_documentation_lookup` at `targetVersion: "14"`
with `backend layout` still returns the two answering pages first and second,
against 14.3. The query for the second instance, the `ext_localconf.php`
content-rendering registration, returns TypoScript rendering objects and PSR-14
events and nothing that answers it — so the assumption holds for one shape and
not the other, and the wording says a miss in the manual is a finding rather
than a licence to reconstruct the contract from the installed core.

The boundary this entry draws was reported from the other side, and the first
**Wrong if** is not what fired. `feedback/2026-08-01-003933` reports a session
that guessed at a ViewHelper contract instead of reading the installed source,
and its `003356` sibling reports the same session reading vendor source before
the lookups. Neither followed the routing first, so neither is the case that
**Wrong if** asks about. What the pair shows is that the wording above decides
this only for a reviewing session: "the finding says the question could not be
settled" is not a sentence a session building a template can write.
[`D-SKL-004`](../task-skills/skl-004-what-a-task-does-when-the-lookups-run-out-is-written-for-a-review.md)
carries that half, and holds the sentence it queues to the distinction this
entry rests on — one installation's implementation is not what TYPO3 supports.

## Since then

The second and the third **Wrong if** fired together on 2026-08-03, in one
report, and neither answer is the one they name.

`feedback/2026-08-03-144349` is a core patch review rather than a conformance
audit, and it ends the same way: an empty changelog, then grep over the
installed core. The skill half had landed the day before, and the session quotes
it back — "base.md explicitly routes 'does this still work in version N' to
`typo3_documentation_lookup`, and I never called it". So the order reached the
session, was read, and did not fire at the moment the silence arrived. That says
where the sentence stands rather than what it says: `skills/base.md` and the
routing block are read before the task, and the miss is where the caller is
standing when the changelog comes back empty.

The third **Wrong if** is the sharper half. This silence was not genuine: the
changelog carries
`13.0 Deprecation: TYPO3 backend entry point script deprecated (#87889)`, which
says the deprecated thing is the script `/typo3/index.php` and that the route
path becomes configurable — the load-bearing question of that review. Re-run on
2026-08-03 from `/home/benji/projects/typo3-cms`, the reported query returns
nothing and the miss already offers `typo3 backend entry point`, which returns
that entry alone. So the matching is sound again, and this time the query that
reaches was computed and printed rather than left to the caller.

The entry stands, and the boundary beside it gains a case. A silence produced by
asking with one word too many is not one the manual is owed, and the miss is
what tells the two apart before the routing does. `D-ANS-043` is where that
lands, and it is why the corpus sentence goes after the offered re-query rather
than in place of it. The session's own account routes it the other way — it
reads its changelog call as the wrong corpus for the question — and on this
evidence that is not what happened.

The third **Wrong if** fired the day the skill half landed, and the answer is
not the one it names. `feedback/2026-07-31-194459` disputes the changelog's
silence the other way — an entry that exists and was not reached — and
`feedback/2026-07-31-194819` carries the queries that missed it. Re-run on
2026-08-02 from `site-new`, `#109412` is reached in one call by `type`,
`version` and `tag`, so the matching is sound and the query shape
`skills/base.md` prescribes is what is not. This entry stands with a boundary
beside it: the manual answers a silence that is genuine, and a silence produced
by asking wrongly is not one. `D-SKL-003` carries that half.

## Since then

The first **Wrong if** fired on 2026-08-03, in half, and what decides the half
is the corpus rather than the routing. `feedback/2026-08-03-164805` audited
`EXT:guidedtour` against a 14.3.5 installation, followed the routing to
`typo3_documentation_lookup`, and read `PageRenderer.php` by hand anyway.

Re-run from `/home/benji/projects/ext-guidedtour` on 2026-08-03, the two shapes
come apart. `Infobox ViewHelper state` at `targetVersion: "14"` returns the
ViewHelper reference page first, carrying the answer whole — *Deprecated since
version 14.0 … use the enum ContextualFeedbackSeverity instead*.
`inline language labels`, `JavaScript labels backend` and
`addInlineLanguageLabelFile` return the JavaScript chapter index, the label
reference and TCA pages, and none of them names the method. The tool's own
header says why: matched against page titles and section paths, never the text
of a page. A PHP identifier has no page to be titled after.

So the entry stands and its subject narrows. What the manual answers is a
documented surface — a ViewHelper, a TCA type, a TypoScript setting. For a PHP
identifier the routing terminates nowhere, and the step after it is the class,
which `skills/base.md` names in `## When the lookups run out`.

The capability this **Wrong if** reserves stays unbuilt, and this feedback is
what measures it rather than what triggers it. Its proposal is an identifier
lookup over the installed packages, and the field it calls the part no other
source gives — an `@deprecated` docblock against a `#[\Deprecated]` attribute,
which decides whether anything is raised today — has one value. The attribute
occurs zero times in `typo3/sysext` on `.checkouts/12.4`, `13.4`, `14.3` and
`main`, and zero times in the audited installation. What marks a core
deprecation is the docblock with `trigger_error(..., E_USER_DEPRECATED)` at the
trigger site: 163 calls in 75 files on 14.3.

The rest of the proposal is answered by the tool that was already called.
`typo3_changelog_lookup` with `query: "InfoboxViewHelper STATE_ERROR"` returns
`#107648` alone and says `removed in v15.0`, which is `D-ANS-042` and
`D-ANS-020` landing. The session swept by `type`, `version` and `tag` instead —
the query shape `D-SKL-003` is about. `addInlineLanguageLabelFile` returns the
7.5 Feature entry that introduced it and no deprecation.

What is left is one class read per identifier. `D-ANS-003` declines a tool for
that and `D-FBK-027` measures it: a fact the caller reads once from a checkout
it already has open, against the four round trips that bought the Forge lookup.
The feedback is **trimmed** and two todos carry the rest. Step 1a is the
statement `deprecated-apis` does not make — `InfoboxViewHelper` carries no
`trigger_error`, so a constant deprecated by docblock alone raises nothing and
breaks at v15, which is the severity the session went to the source for. Step 4
is the sentence telling a documented surface from a PHP identifier before the
manual is called, on `skills/base.md` or on the conformance skill.

What would trigger the reservation after all is a session that has both: the
statement in reach and the identifier named in the changelog, and still no way
to tell whether the thing exists in the version it runs on.

## Since then

Step 4 landed on 2026-08-03, in `skills/base.md` and not in the conformance
skill. Both carried the routing and only one carries it once. The conformance
skill states its own condition and then defers — "the base says why the
changelog cannot answer that one" — so a bound written there would leave the
sentence it bounds standing unqualified in the file every published skill is
given. `typo3-extension-upgrade` starts from the same sweep, which is why step 5
is in the base at all, and a second copy of an order is what `D-SKL-001` exists
to prevent. It cost 79 words, 1452 to 1531, and that entry keeps the arithmetic.

The readings were re-run from `/home/benji/projects/ext-guidedtour` through this
checkout's `bin/typo3-cms-mcp` before the sentence was written, and the two
shapes still come apart. `Infobox ViewHelper state` at `targetVersion: "14"`
returns `be.infobox` from the ViewHelper reference first, carrying *Deprecated
since version 14.0 … use the enum ContextualFeedbackSeverity instead*.
`addInlineLanguageLabelFile` and `inline language labels` return the label
reference, a TCA renderType and the `addRecord` field control — pages matched on
`label` and `add` in their titles, naming the method nowhere.
`typo3_changelog_lookup` with the same identifier returns the 7.5 Feature entry
that introduced it and no deprecation, which is the answer the filing session
went to `PageRenderer.php` for.

So what the base now says at the point of the call is which corpus a question
has. A documented surface goes to the manual, an identifier to the changelog
under its own name and then to the class, and the miss-is-a-result sentence
stands for the surface it was written about. The feedback keeps its other half:
what a deprecation carrying the docblock alone raises is a todo of its own.
