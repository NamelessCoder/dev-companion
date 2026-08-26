---
id: D-DOC-058
title: 'A recording is reported against the day its sources moved'
date: 2026-08-26
status: open
coveredBy:
  - ToolAnswersTest::everyRecordedPageSaysWhichDayItWasAnsweredOn
  - ToolAnswersTest::theDayTheSourcesMovedIsWhatGitSaysOrNothing
---

# D-DOC-058 — A recording is reported against the day its sources moved

**`bin/cli tools:check` names how many recorded pages were answered before this
repository last changed `knowledge/` or `src/`, and fails on none of them.**

`D-DOC-006` decided that nothing checks the recorded half of a tool page, and
its second **Wrong if** is the recording ageing with nothing that asks for a new
one. This is the asking.

## Evidence

- The gap is not a worry. `D-DOC-016` measured it in the corpus on 2026-08-04:
  two derived pages had been recorded on 2026-08-03 and `knowledge/` had moved
  since, so both moved when they were re-derived.
- Measured in this worktree on 2026-08-26: 17 of the 18 recorded pages were
  older than the last commit touching `knowledge/` or `src/`, the oldest by two
  days.
- The pages carry three different days already, because `tools:record` takes a
  list of tools and narrows to it.
- The drift `ToolAnswersTest` can already see is the shape — every recorded
  answer is held to the schema its tool declares (`D-DOC-012`). What no check
  reads is the content: a hint rewritten or a sentence rephrased moves an answer
  and leaves every assertion green.

## Decided

- **The report reads two days, and calls nothing.** The day a page carries in
  its own opening sentence, against the day git says `knowledge/` or `src/` last
  moved. Both are readable in any checkout, so the report needs no installation,
  no host and no `.checkouts/` — which is what `D-DOC-016`'s third **Wrong if**
  asks of this command.
- **`knowledge/` and `src/` together.** The first is what the answers are
  composed from and the second is what composes them, and a commit to either can
  move an answer.
- It changes no exit code. A recording is evidence about a day, and the machine
  that could produce a new one is not the machine CI runs on — `D-DOC-006`.
- It sits in `tools:check` rather than in `unresolved:list`, which reads
  `requirements/` and `decisions/` and would gain a third subject. The reader
  who can act on this is already looking at the surface, and
  `bin/cli repository:check` runs `tools:check` either way.
- Rejected: re-answering the calls and comparing. That is `tools:record` without
  the writing, and it needs the checkouts and the fixture console — the growth
  `D-DOC-016`'s third **Wrong if** names. Four tools also answer from a host
  that moves without any commit here, so the comparison would report drift no
  session caused.
- Rejected: a day per page in the report. Re-recording is one command over the
  whole tree, so a verdict per page answers a question nobody can act on per
  page. The count and the oldest day are what decides whether to run it.
- Nothing is said where git cannot answer. A checkout without history is a
  question that was never put, and a page is not behind because nobody could
  ask.

## Assumed

- That whoever merges runs `bin/cli tools:check` or `bin/cli repository:check`.
  Nothing makes them, which is the same assumption `D-DOC-006` makes about the
  reader who re-runs the command.
- That a day is granular enough. A recording and a commit to `knowledge/` on the
  same day read as current here, whichever came first.

## Wrong if

- The report says the pages are behind on nearly every run, because almost every
  branch touches `knowledge/` or `src/`, and whoever merges stops reading it.
  Then the predicate is too coarse and what is needed is a per-tool one, which
  costs the calls this entry rejected.
- Somebody runs `bin/cli tools:record` to clear the report on a branch that
  changed one sentence, which is the commit `D-DOC-034` refuses for a different
  reason and the todo behind this entry said belongs to whoever merges.
- A recorded answer goes stale from something neither `knowledge/` nor `src/`
  holds — the manuals, the tracker, a package a checkout carries — and the
  report calls those pages current.
