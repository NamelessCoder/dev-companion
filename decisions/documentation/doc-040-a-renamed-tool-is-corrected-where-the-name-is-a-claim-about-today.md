---
id: D-DOC-040
date: 2026-08-22
status: open
---

# D-DOC-040 — A renamed tool is corrected where the name is a claim about today

**A renamed tool is written under its current name wherever a reader would go
and call it, and keeps the old one where the sentence is about that name.**

`ToolNamingTest` held the knowledge base and the skills to the registry and
nothing else, so every other corpus went stale unwatched.

## Evidence

- Measured on 2026-08-22 against the 28 registered tools. `decisions/` named
  `typo3_project_scope` 70 times, `typo3_extension_scope` 47 and
  `typo3_architecture_lookup` 40, across 56 files. `knowledge/`, `skills/`,
  `requirements/` and `todo/` were clean, and `documentation/` named one
  hypothetical tool in an illustration.
- The three are renames on record: `typo3_project_scope` and
  `typo3_extension_scope` became `typo3_project_describe` and
  `typo3_extension_describe` at `a4470ee` (`D-SCO-011`), and
  `typo3_architecture_lookup` became `typo3_hint_lookup` at `7553cb3`.
- Seven further names in `decisions/` belong to no tool and never did:
  `typo3_document_list`, `typo3_skeleton_lookup`,
  `typo3_migration_availability`, `typo3_debrief_guide` and
  `typo3_convention_lookup` are proposals that were declined, and
  `typo3_versions` and `typo3_logo` are a TER field and a file name. A decision
  that records a rejection names the thing it rejected.
- `scenarios/runs/` had already solved it the other way. Each of the three
  recorded runs keeps the spelling its session used and closes with a line
  naming the current one — "`typo3_hint_lookup` was called
  `typo3_architecture_lookup` when this run was recorded".

## Decided

- The 157 mentions in `decisions/` are corrected. Four passages keep the old
  spelling because the sentence is about it: `D-SCO-011`, which is the rename;
  `D-AUD-005`, whose finding is an exclusion variable set to a name no tool has;
  and two lines of `D-FBK-018`, one an exact quotation of a shipped file at a
  named commit and one the rename itself. `D-AUD-003` names both, because the
  claim is what a commit put into the `instructions`.
- `ToolNamingTest` widens to `documentation/`, `requirements/`, `todo/` and
  `scenarios/` outside `runs/` — the corpora where a name is a claim about
  today. All four were clean, so the guard holds a boundary rather than
  reporting a breach.
- `feedback/` and `scenarios/runs/` stay out. Both are a session's own account
  of a date, and rewriting one edits the evidence. The run's closing line is the
  form that reconciles them, and it costs one sentence per file.
- ~~`decisions/` stays out of the guard too, and this is the trade: the corpus
  legitimately names tools that were proposed and rejected, so nothing can tell
  one of those from a name that went stale without a list of every rename ever
  made. Three renames did not earn that list.~~ Reversed the same day, see
  **Since then**.
- The illustration in `interface-contract.rst` names `typo3_rule_lookup` rather
  than an invented tool. The sentence is about a name with an underscore in it,
  which a real one shows as well and the guard can then read.

## Assumed

- That the three are all the renames. They are what the measurement found, and a
  rename whose old name nobody wrote down anywhere would leave no trace to
  count.
- That a reader of a decision wants the tool they can call. The alternative is
  that they want the name the entry was written under, which is what
  `scenarios/runs/` assumes about a trace — the difference being that a trace is
  a list of calls and a decision is mostly prose about them.

## Wrong if

- ~~The next rename goes stale in `decisions/` anyway, because nothing guards it
  and this correction was a sweep rather than a habit. That is the known hole,
  and what would close it is the list of renames this declined to start.~~
  Closed the same day, see **Since then**.
- A corrected sentence turns out to have been about the old name after all. The
  four exceptions were found by reading every line the sweep changed that sat
  beside a date, a commit or the word "renamed"; a fifth would show up as an
  entry that stopped making sense.

## Covered by

- `ToolNamingTest::everyToolNameWrittenInTheKnowledgeBaseIsRegistered`

## Since then

The maintainer asked the same day whether a tool that does not exist can be
legitimate, and it cannot. What `decisions/` holds is not a tool but a
**proposal** that was declined, and writing one in the backticks a callable tool
wears is the deception a stale name is: a reader goes and calls it, and nothing
can tell the two apart. The five were prose all along — "a debrief guide tool",
"a skeleton lookup was drafted and dropped" — and none of those sentences wanted
the identifier.

With them written as what they are, the reason this entry gave for leaving
`decisions/` unguarded is gone, and the list of every rename it declined to
start is not needed either.
`ToolNamingTest::everyToolNameADecisionSpellsIsRegisteredOrIsTheSubject` reads
the corpus for the **tool shape** — a subject and one of the six verbs — so the
TER's own `typo3_versions` field and a `typo3_logo.png` in a Fluid example are
not read as names at all, and no exception is owed to either.

What is owed an exception is the passage where a superseded name is the subject
of the sentence, and there are five: this entry, whose evidence is the names as
they were counted, and the four **Decided** already names. They are listed in
the test with the reason each one is there, and that list shrinks as those
entries stop being about a rename rather than growing with every rename made.
