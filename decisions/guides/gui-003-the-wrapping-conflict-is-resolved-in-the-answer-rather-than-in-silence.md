---
id: D-GUI-003
title: The wrapping conflict is resolved in the answer rather than in silence
date: 2026-08-03
status: open
coveredBy:
  - CommitMessageTest::eachJoinedRunIsReportedOnItsOwn
---

# D-GUI-003 — The wrapping conflict is resolved in the answer rather than in silence

**Where a body cannot be both wrapped at 72 characters and left as the caller
structured it, the guide states which of the two it did.** A line it joined into
a paragraph is reported, and a line it left over the width is reported at the
level the workflow's own tooling enforces.

`typo3_commit_message_guide` promises a draft that is ready to commit, "with
fenced code, indented blocks, list structure, and long URLs left intact". Under
the core workflow the two halves of that sentence cannot both hold: the commit
hook refuses every line over 72 characters, whatever it contains. Today the
guide picks one half or the other by whether the caller happened to indent, and
says nothing either way.

## Evidence

- The feedback's own query was re-run against the server as it is now
  (2026-08-03, `CommitMessageGuide::answer`, the body exactly as
  `feedback/2026-08-02-144315` records it). The four `runTests.sh` command lines
  came back joined into one reflowed paragraph — `-s cgl -n CI=true` on one line
  — and the checks returned `no-issues-found`. Nothing about that has moved
  since the report.
- The same block indented by two spaces was kept verbatim, and the checks
  reported `body-line-too-long` for line 2 at 126 characters, at `warning`. The
  block is only recognised as a block when it is indented, which the query's
  body was not: `wrapBody()` tests `preg_match('/^\s/', $line)`, so a hard line
  break at column 0 is prose to be rejoined.
- `.checkouts/main/Build/git-hooks/commit-msg`, `checkForLineLength()`, is
  `grep -q -E '^[^#].{72}'`. Every line of 73 characters or more fails, with no
  exception for indentation, for a fenced block or for a URL. The 126-character
  line above is refused, and so is any of the long URLs the description promises
  to leave intact.
- Two further open feedback from the same checkout record the same rejection
  from the other side: `feedback/2026-08-02-144848` and
  `feedback/2026-08-02-145230` both state that the hook installed by
  `composer gerrit:setup` "enforces the 72-character body limit and rejected my
  first attempt".
- The report's secondary claim did not hold as written. The 73-character line
  the hook refused was in the session's hand-restored text, not in the draft the
  guide returned, and R-GUI-001 is what makes reporting only on the draft
  correct. The conflict behind the claim is real from the other end: a draft
  that keeps an indented block is a draft the hook refuses.

## Decided

- The defect is the silence rather than the reflow. Rejoining hard-broken prose
  is what wrapping a body means, and a guide that kept every line break the
  caller wrote would stop being a wrapper.
- The check level follows the tooling that enforces it. Under `core` a body line
  over 72 characters is what the hook refuses, so it is an `error`; under
  `project` no hook runs, and `warning` is right there.
- The description stops promising both halves at once. What is ready to commit
  under the core workflow is a draft with no line over the width, and an intact
  block longer than that is the caller's to shorten.
- Rejected as the decision: the report's suggestion to keep hard line breaks
  after a `...:` lead-in line. Recognising the block is worth building, but
  which lines count as one is implementation, and a wrong guess leaves prose the
  caller broke at column 40 unwrapped forever. What is decided here is that
  neither resolution may be silent.

## Assumed

- The hook read in `.checkouts/main` is the hook a contributor has installed.
  `composer gerrit:setup` is what installs it, into the common git dir rather
  than into the checkout, so nothing here can read the installed copy. Two
  sessions quoting its rejection text is the closest thing to a run.

## Wrong if

- A caller reads the new error, shortens the line, and the hook refuses the
  commit anyway. Then the rule the guide runs is not the rule the hook runs, and
  the length check has to be taken from the hook rather than restated.
- Block recognition turns ordinary prose into text that is never rewrapped, and
  a body reaches Gerrit ragged at column 40. Then keeping the hard breaks is the
  wrong half of the trade and only the report should stay.

## Since then

The lead-in heuristic was not built and the report is the whole of the
resolution: the check names each run of caller-written lines it joined and says
that indenting keeps the breaks, so a caller who wanted the block has it in one
edit. Nothing was found that would make the guess safe — the same lead-in stands
over a command block and over a sentence broken at column 40. Measured against
the hook again on 2026-08-26 from the other side: it accepts 72 characters and
refuses 73, so the threshold is the hook's own. What the reading found instead
is that the answer never says so, which is `D-GUI-020`.
