---
id: D-DOC-059
title: 'The recording report reads both its days in UTC'
date: 2026-08-27
status: open
coveredBy:
  - ToolAnswersTest::bothDaysTheReportComparesAreTheUtcOne
---

# D-DOC-059 — The recording report reads both its days in UTC

**The day a recording stamps itself with and the day git says `knowledge/` or
`src/` moved are both the UTC day of an instant.**

`D-DOC-058` has `bin/cli tools:check` compare those two days without saying
which clock either is on, and they were on different ones.

## Evidence

- Measured on 2026-08-27 at 00:16 CEST: `bin/cli tools:record` wrote
  `Recorded on 2026-08-26` into all 18 pages, and `bin/cli tools:check` read
  every one of them as older than sources that had moved on 2026-08-27. A
  recording a minute old reported itself a day behind.
- The stamp was PHP's `date('Y-m-d')`, which answers in `date.timezone` — unset
  on this machine, so UTC — and the comparison was git's `%cs`, which answers in
  the zone the committer's clock carried.
- The repository already dates one thing it did not observe itself in UTC:
  `Publication\Ter` writes a release's upload day with `gmdate()`, and
  `typo3_ter_lookup` says so in the field's own description.

## Decided

- **UTC, through one function.** `ToolAnswers::day()` makes both days, so a
  second clock cannot enter one half without the other.
- **git is asked for the instant, not for the day.** `%ct` is a moment every
  reader converts the same way; `%cs` is the day the committer's clock said,
  which is a third clock beside the two being compared.
- Rejected: the local day on both halves. PHP reads no zone but the one
  configuration hands it, so "local" would be one machine's ini rather than
  where anybody is standing, and two recorders would still write two days for
  one moment.
- Rejected: forgiving a day's difference in the comparison. A report that
  forgives one day cannot see a recording that is one day stale.
- The page says the day and not the zone. Nothing compares it by hand — the
  command does that — so the zone would be six characters on every page for a
  question no reader puts.

## Assumed

- That a page dated in UTC reads as current to whoever wrote it. For the hours
  between midnight local and midnight UTC it carries yesterday's day, which
  makes a fresh recording look a day old rather than making the report wrong.

## Wrong if

- Somebody reads `Recorded on <day>` as their own day, finds it behind their
  calendar, and re-records a page that was current — the confusion not writing
  the zone was supposed to be too small to cause.
- A recording is stamped from something other than `ToolAnswers::day()`: the
  `tools:record` argument takes any day, and one passed by hand is on whatever
  clock the caller had.
- The comparison turns out to need an hour rather than a day, because a
  recording and a commit to `knowledge/` inside one UTC day read as current
  whichever came first — which is what `D-DOC-058` assumed.
