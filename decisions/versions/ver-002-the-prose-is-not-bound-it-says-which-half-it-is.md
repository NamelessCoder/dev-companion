---
id: D-VER-002
date: 2026-07-29
status: revoked
revokedBy: D-VER-005
---

# D-VER-002 — The prose is not bound; it says which half it is

**The prose carries no version binding and says so in every answer, naming
`typo3_hint_lookup` with `targetVersion` as where the bound form is.**

The architecture hints now carry `since`/`until` on every statement that changed
inside the covered range. The markdown documents below `knowledge/` are the long
form of the same subjects and carry nothing — the event listener attribute, the
Fluid file extension, the backend tokens and the translation domains are all
described there as the shape, with no range.

## Decided

- No binding mechanism for prose. It would need per-bullet metadata in markdown,
  a parser, a renderer and a test, for a corpus that is read whole rather than
  filtered, and the same statements are already bound where a caller acts on
  them.
- Every prose answer says so instead, in one sentence from
  `Tools::renderSections()`, and names `typo3_hint_lookup` with `targetVersion`
  as where the bound form is. One sentence in one place, so a caller who learns
  it in a rule answer finds it unchanged in a script answer.

## Assumed

- A caller that is told the prose is unfiltered will ask the hints when the
  version matters. The alternative — filtering prose sections by the ranges of
  the hints that share their subject — would guess at a mapping nobody declared.

## Wrong if

- A prose section misleads on an LTS badly enough that the sentence does not
  save it, which would mean that statement belongs in the hints rather than in
  the document.

## Covered by

- `KnowledgeTest::noProseDocumentDatesAStatementInItsSentence`
- `KnowledgeTest::noProseDocumentNamesACheckOnlySomeBranchesHave`

## Confirmed on 2026-08-02

The **Wrong if** was read against the oldest covered checkout, and it fired.
`typo3-core-scripts.md` was the document most likely to have moved since 12.4,
and its "Common Commands" section handed a 12.4 core contributor three suites
that branch does not have: `-s checkIntegrityXliff` and `-s normalizeXliff`,
which arrive in 14 and have no counterpart under any name on 12.4 or 13.4, and
`-s build`, which arrives in 13 where 12.4 splits it into `buildCss` and
`buildJavascript`. The two option bullets did the same in passing, `-n` naming
`normalizeXliff` and `-c` naming `e2e`. `typo3_script_lookup` returns that
section whole and unfiltered, so all of it arrived as an answer.

The decision holds, because its own remedy absorbed every one of them and no
binding mechanism for prose was needed. The range for a command already exists
on the suite in `test-suite-hints.json`, so the sections lost the commands and
gained a sentence sending the reader to `typo3_test_run_guide` with the
`targetVersion`; `build` gained `since: 13`, and 12.4's two halves were added
beside it. What did not hold is the second half of the sentence in
`Prose::NOT_VERSION_BOUND`: it named `typo3_hint_lookup` alone, and a 12.4
reader who followed it would have found nothing there about which suites that
branch has. It now names the test run guide for a command, and the architecture
lookup for a convention. That is still one sentence in one place.

What this leaves open is the class of prose statement that is not a command. The
guard added here compares a `-s <suite>` token against the ranges the suites
already carry, and nothing equivalent exists for a sentence describing a shape —
for that, the **Wrong if** is still read rather than run.

## Revoked on 2026-08-04

The class this entry left open arrived as a section whose body is a file the
caller writes out verbatim. Both properties this rested on fail there: the
document is where the caller acts rather than a long form of something bound
elsewhere, and there is no hint carrying the file for the range to sit on. The
remedy that absorbed the **Wrong if** in August cannot be applied either — a
command could be taken out of the prose because its range already lived on a
suite, and a file has nowhere to be taken out to.

What holds instead is
[`D-VER-005`](ver-005-a-document-section-declares-the-majors-it-holds-for.md),
whose **Wrong if** is a different list: what can go wrong now is a variant
handed over with nothing saying which to write, not a section that misleads
because nothing could filter it.
