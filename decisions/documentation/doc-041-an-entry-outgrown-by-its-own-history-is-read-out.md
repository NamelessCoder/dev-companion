---
id: D-DOC-041
date: 2026-08-22
status: open
---

# D-DOC-041 — An entry outgrown by its own history is read out

**`bin/cli decisions:check` names the entries carrying more later reading than
decision, and fails on none of them.**

A rule the repository applies often collects a **Confirmed on** per application.
Nothing about that is wrong, which is why nothing had counted it.

## Evidence

- Measured on 2026-08-22 over 441 entries, 51 684 lines. The median is 105 lines
  and 373 entries carry no dated section or one, so the format is not the cost.
  30 entries carry more later reading than decision, and 13 carry four or more
  dated sections.
- `D-FBK-018` is the shape at its extreme: 107 lines of decision and 1283 of
  later reading, in nineteen sections, twelve of which stamp a date another
  already stamped. It states how a strength is read, and each section is one
  more strength read by that rule — eighteen readings, eighteen holds. That is
  what **Confirmed on** is for, and a reader who wants the rule pays for all of
  it.
- Two kinds sit in that one entry. Four sections open "the reading held a fourth
  time", "a fifth time", "a sixth time" and add no reading; the rest fire a
  **Wrong if** or add a clause. Only the second kind is a decision being
  revisited.
- The strikethrough that retires a bullet a reading overtook is used by 4
  entries of 441. `D-SKL-001` carries 57 lines of decision under ten readings
  and not one strike, so what of **Decided** still stands is the reader's
  arithmetic.
- The cost is concentrated but not only at the top: the 13 hold 30% of every
  later-reading line, and the other 428 average 110 lines of which 20 are later
  reading.

## Decided

- A report in `bin/cli decisions:check`, beside the problems and not among them.
  `Decisions::outgrown()` is the reading and
  `DecisionsTest::anEntryOutgrownByItsHistoryIsReadOutRatherThanFailedOn` holds
  it. The command still exits 0, because a long history is legitimate and a
  check that fails on one would be answered by writing shorter accounts of the
  same readings.
- The longest three are named and the rest counted, so the report does not
  become a list that grows with the corpus.
- The measure is lines rather than sections. Nineteen short confirmations and
  three long ones cost a reader the same, and it is the reader this is about.
- Nothing is rewritten by this. What the 13 need is a judgement per entry —
  which sections established something and which only counted — and that is the
  todo this leaves, not a sweep.
- Rejected: failing on a threshold. The number of readings an entry has had is
  not something its author controls, and the entry that would fail first is the
  one whose rule the repository leans on most.

## Assumed

- That a reader of an entry wants the decision and reaches the history second.
  Where the entry states a rule still being applied, the newest section may be
  what they came for, and this reports the file as costly either way.
- That the split between reading and counting is visible per section. Four were
  told apart by their opening words in `D-FBK-018`, and nothing says the other
  entries mark it as plainly.

## Wrong if

- The report is read and nothing follows, and the count climbs while the file
  says it every time. It stands at 30, and what would show this is 40 with no
  entry having been judged.
- An entry is kept short by leaving a reading unwritten, which is the failure
  this trades against and the more expensive one: an account nobody wrote is not
  shorter than a long entry, it is gone.
- The measure names the wrong entries. A 1283-line history that a reader
  genuinely wants read is not a cost, and a feedback saying so would be the
  evidence.

## Covered by

- `DecisionsTest::anEntryOutgrownByItsHistoryIsReadOutRatherThanFailedOn`

## Since then

The thirteen were judged on 2026-08-22, one commit each, and the corpus went
from 5337 lines of later reading over 30 entries to 4377 over 31.

The second **Assumed** is what the sweep corrected. The split is visible per
section only where the section says what it is: the four told apart by their
opening words in `D-FBK-018` were eight when all nineteen were read, because a
section that applies a rule established above it is counting too, however much
it settles elsewhere. Per entry the share varies from all of it to none — two
entries had nothing to collapse, `D-FBK-021` because every reading carries the
mapping of one summary that its own **Decided** says is written down so nobody
derives it twice, and `D-FBK-020` because every reading is a measurement of a
different run.

What the readings had overtaken was mostly a bullet nobody had struck. The
convention went from 4 entries to 13, and twice a bullet had to be split first:
`D-SKL-001` and `D-ANS-003` each held two falsifiers in one, which is why
thirteen readings and eleven passed over them without striking anything. Twice a
reading wrote a new **Wrong if** into its own prose, where a reader had to find
it — `D-SKL-022` and `D-ANS-010` carry it in the list now.

No collapsed reading needed an entry of its own. Each already pointed at the
decision, the requirement or the test carrying what it settled, which is what
made it collapsible.

The thirty-first entry is `D-AUD-007`, and it is the measure being right about a
file it had been reading wrongly. Five of its six readings had lost everything
but their first paragraph to the foot when the bold labels were converted into
sections, so 60 lines of history stood inside **Wrong if** and counted as
decision. The same conversion left thirty sections opening on a comma, and both
were repaired here; eighteen entries it split the same way are a todo of their
own.
