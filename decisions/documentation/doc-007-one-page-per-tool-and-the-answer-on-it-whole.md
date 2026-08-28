---
id: D-DOC-007
title: One page per tool, and the answer on it whole
date: 2026-08-02
status: open
coveredBy:
  - ToolAnswersTest::everyCallOnAPageCarriesItsArgumentsAndItsAnswers
  - ToolAnswersTest::everyRecordedAnswerIsJson
  - ToolAnswersTest::everyToolTheTableDrivesHasARecordedAnswer
  - ToolAnswersTest::everyToolTheTableLeavesOutSaysWhy
  - ToolSurfaceTest::everyToolCarriesItsAnswerOrSaysWhyItHasNone
  - ToolSurfaceTest::theIndexReachesEveryToolAndTheDirectoryHoldsNoOther
---

# D-DOC-007 — One page per tool, and the answer on it whole

**The recording is a directory: `documentation/clients/tool-answers/<tool>.md`,
each answer as the client received it, and every tool in `tools.md` links to its
own.**

`D-DOC-006` put all of it on one page, and the page is what forced the cut. Both
go together: a reader arrives with one tool in hand, and neither the twenty-one
others nor `… 14 more` where the entries were is what they came for.

## Evidence

- The one page was 2,849 lines and was reported missing by the person who asked
  for it, on the day it landed. It was linked once, from its head, and read as
  one long file rather than as an answer per tool.
- Split and uncut it is 8,473 lines over 23 files. The largest is
  `typo3_component_lookup` at 1,339 lines, the median page is under 200, and the
  whole directory is 432 KB — where the cut was decided against 42 calls sharing
  one page.
- Four of the 43 recorded answers ended the block they were written into. Half
  of them are markdown themselves — the script notes hand back commands in
  fenced blocks — and the answer's own closing fence closed the recording's. The
  count that says so is what is left outside the blocks: 43 calls, 39 `Data:`
  headings. Counting blocks does not say it, because the leaked pair reopens and
  the total comes out right.

## Decided

- One file per tool, named for the tool, and `readme.md` beside them as the map.
  The name is the one a caller already knows, which is the rule for anything
  visible outside this checkout.
- Each page carries the head — the day, the installation, whether the console
  answered — because it is the page somebody arrives on, and `D-DOC-006` is why
  a recording without it is an assertion about nothing.
- The answers are recorded whole. The cut was a property of one page holding all
  of them, and what it cut is the half a recording exists to show.
- A block is fenced with more backticks than anything inside it, computed per
  block rather than fixed, so an answer that is itself markdown cannot end it.
- `tools.md` links per tool rather than once in its head. A link in a head is
  not followed by a reader who is already at the tool they came for.
- `tools:record` deletes a page no call writes any more. The command is the only
  thing that knows the table shrank.

## Assumed

- A 1,339-line page is one a reader opens and searches. Nothing was measured
  about that, and the three largest are the ones it rests on.

## Wrong if

- A page is opened and the answer still cannot be seen, because 1,300 lines of
  JSON is the same problem the one page had at 2,800. Then the cut comes back
  per page, on the two or three that need it, rather than everywhere.
- A tool's recording is re-run alone and the pages start carrying different
  days. Nothing in the command can do that today; a hand-edited page can.

## Since then

"Every tool in `tools.md` links to its own" was never true, and the head of that
page is what said so. The fencing rule went with the markdown (`D-DOC-029`).
