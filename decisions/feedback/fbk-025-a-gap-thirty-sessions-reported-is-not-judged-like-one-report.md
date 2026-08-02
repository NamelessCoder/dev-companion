---
id: D-FBK-025
date: 2026-08-03
status: open
---

# D-FBK-025 — A gap thirty sessions reported is not judged like one report

**The corpus is read before the card, a judgement that lands on step 1b decides
that the tool or skill is built, and it sets the priority.**

The judging process was delivering — 175 feedback archived against 53 open — and
producing no order. Everything a session reported arrived at `low` and stayed
there, and the one report that named a whole missing domain waited behind 43
others that were also `low`.

## Evidence

- The board on 2026-08-03: 48 cards, **30** still the generic text `todo:sync`
  writes, **44** at `low`, four at `normal`, none higher. 53 open feedback, 175
  archived, 95 of 135 decisions still `open`.
- 35 of the 53 carry one directory, `/home/benji/projects/typo3-cms`, in two
  task clusters. `feedback/2026-08-01-115220` is a session proposing the skill
  the other sessions kept describing; it sat two days at `low`.
  `feedback/2026-08-02-144357` says the same thing from the creation side. Each
  had been judged alone or not at all.
- Nothing offered that reading. `directory:` is written into every feedback and
  was not carried into the listing, so `bin/cli feedback:list` printed 53 paths
  newest first and no session could see that 35 of them came from one checkout
  without grepping the front matter.
- The rule that produced the non-answer is *the answer names the gap, not the
  fix*, and its stated reason is that the judging run has established nothing
  about TYPO3. That reason is exact for step 1a and reaches nothing on 1b, where
  the evidence is runs, transcripts and skill descriptions — all of it in this
  repository. `D-SKL-005` was first written as *establish whether a core review
  earns a skill* with the corpus that answers it unread on the same board.

## Decided

- `bin/cli feedback:list` groups by the directory each feedback was written in,
  largest group first, marks the ones no todo names, and prints the model —
  because the sibling reading has to be one call or it will not happen.
  `Channel::all()` carries `directory` for it, which is a field added to a
  declared shape rather than a renamed one.
- `judging.md` opens the ladder with that reading, splits *the answer names the
  gap, not the fix* by which step it is about, and says that the judgement sets
  the priority. `todo/readme.md` says the same from the other end: a judged card
  that is still `low` is somebody's decision, not an absence.
- Where several cards are one gap, one carries the work and names the rest in
  its `**Serves:**` line. Four core cards became two on the day this was
  written.

## Assumed

- That the throughput was never the problem. 175 archived says the process
  works off what it is pointed at, so what was missing is where it points.
- That grouping by directory is the right axis. It is the axis the corpus
  happened to have; a gap reported from four different checkouts would not
  group at all, and only the model and the task shape would say they are one.

## Wrong if

- The board fills with `high` again. A priority everything carries orders
  nothing, and the rule then bought a second word for `low`.
- A 1b judgement decides a tool or a skill that the reading afterwards shows was
  the wrong shape. Then deciding before reading cost what withholding was
  protecting, and `D-SKL-005`'s own **Wrong if** is where it would show first.
