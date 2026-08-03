---
id: D-DOC-007
date: 2026-08-02
status: open
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

## Covered by

- `ToolAnswersTest::noAnswerEndsTheBlockItWasWrittenInto`
- `ToolAnswersTest::everyRecordedAnswerIsJson`
- `ToolAnswersTest::theIndexReachesEveryPage`
- `ToolAnswersTest::everyToolTheTableDrivesHasARecordedAnswer`
- `ToolAnswersTest::everyToolTheTableLeavesOutSaysWhy`
- `ToolSurfaceTest::everyToolEitherLinksToItsAnswerOrSaysWhyItHasNone`

## Since then

**"Every tool in `tools.md` links to its own" was never true**, and the head of
that page said it in the sentence above the twenty-three that do. Two tools have
no recording on purpose — `typo3_feedback_record` writes, and
`typo3_feedback_list` answers with the backlog somebody else wrote — and
`ToolSurface` rendered nothing at all for them. An absence and an omission
therefore looked identical at the only place a reader could have noticed the
difference, and the head told them which of the two to believe.

It stays two tools rather than becoming twenty-five pages. Both are drivable
against a fixture directory, which is what makes the option worth stating, and
it is what the option costs that decides it: every page in this directory
carries a head asserting the day, the checkout and the console it came from, and
a fixture-driven call would carry that head over an answer that came out of a
fixture instead. The value of the directory is that its heads are true, and two
more pages is a poor trade for two that are not. The write is the second reason
and it is independent of the first: `ToolContractTest` drives the same table, so
recording that call files a real feedback into the backlog on every test run.

What replaces the blanket claim:

- Every tool either links to its recording **or states, in its own section, why
  it has none**. The renderer has no third branch: a tool with neither a page
  nor a written reason renders as saying so, which is a defect a reader can see.
- The reasons are `ToolCalls::undriven()`, beside the table that leaves those
  tools out, and `tools.md`, the recording's own map and the test all read that
  one copy.
- `ToolAnswersTest` holds the table against that list rather than against the
  two names it used to carry. A third tool dropping out is made green by writing
  the reason, which is the only work that was ever wanted here.

The map under `tool-answers/readme.md` names them too, after the list, where the
reader who scanned it for their tool has arrived. Nothing checks that half —
`D-DOC-006` is why, and it holds: a check on a recorded page is one only a
machine with `.checkouts/` can make green.
