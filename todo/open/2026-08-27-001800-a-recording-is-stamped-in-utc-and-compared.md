# A recording is stamped in UTC and compared against a local day

**Serves:** D-DOC-058
**Priority:** normal

The two halves of the drift report read different clocks, so for the hours
between midnight local and midnight UTC the report claims a staleness that is
not there. `ToolRecord` stamps the page with PHP's `date('Y-m-d')`, which runs
in UTC here; `ToolAnswers::sourcesMovedOn()` takes git's day, which is local.
Measured on 2026-08-27 at 00:16 CEST: `bin/cli tools:record` wrote
`Recorded on 2026-08-26` into all 18 pages, and `bin/cli tools:check` read them
as older than sources that had moved on 2026-08-27 — a fresh recording reporting
itself behind.

Settle which day a recording is stamped in and make both halves read it, rather
than making the comparison tolerant of a day's difference: a report that
forgives one day cannot see a recording that is one day stale.

This is the first **Wrong if** of `D-DOC-058` arriving early. That entry expects
the report to read "behind" nearly always because almost every branch touches
`knowledge/` or `src/`, and to be ignored for it; the clock skew adds a second
reason to ignore it that has nothing to do with what the report is for.
