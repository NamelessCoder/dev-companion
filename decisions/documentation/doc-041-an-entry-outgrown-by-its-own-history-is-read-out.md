---
id: D-DOC-041
title: An entry outgrown by its own history is read out
date: 2026-08-22
status: revoked
revokedBy: D-DOC-066
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
  A reading of its own carried it, held by a test of its own, both gone with the
  revocation below. The command still exits 0, because a long history is
  legitimate and a check that fails on one would be answered by writing shorter
  accounts of the same readings.
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

## Since then

The thirteen were judged one commit each, and the corpus went from 5337 lines of
later reading to 4377. The second **Assumed** is what the sweep corrected: the
split is visible per section only where the section says what it is, and per
entry the share runs from all of it to none — two had nothing to collapse, one
because every reading carries a mapping its own **Decided** says to write down,
the other because every reading measures a different run.

What the readings had overtaken was mostly a bullet nobody had struck. Twice a
bullet had to be split first, because it held two falsifiers in one, and twice a
reading had written a new **Wrong if** into its own prose where a reader had to
find it. No collapsed reading needed an entry of its own: each already pointed
at what carried its finding, which is what made it collapsible.

## Revoked on 2026-08-28

The report is per dated section now rather than per entry, and the ratio this
measured is gone with it. What the two counted is the same cost; what changed is
that a reader can act on the new one, because it names the section to compact
rather than the entry to feel bad about. `D-DOC-066` carries the measure and the
rule under it.
