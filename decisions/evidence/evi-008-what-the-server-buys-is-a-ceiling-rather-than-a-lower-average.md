---
id: D-EVI-008
date: 2026-08-18
status: open
---

# D-EVI-008 — What the server buys is a ceiling rather than a lower average

**Measured against sessions without it, this server left the median cost of a
lookup roughly unchanged and collapsed the spread — so the promise says
predictability, not saving.**

The readme promises knowledge, version binding and skills, and says nothing
about the shape of the saving, so somebody weighing whether to install it
guesses at one.

## Evidence

- A comparison outside this repository, on one TYPO3 13.4 installation with one
  fixture extension, one model, five runs per cell, across four task families.
  This checkout cannot re-run it and holds none of its data: everything below is
  read off that report, and a session wanting to check a number has to go to
  where it was measured.
- Lookup tasks: the turn count fell by about two fifths and the wall time by
  about half. The median cost fell 18 percent and does not survive dropping the
  strongest of the four tasks, at which point it is one percent the other way.
  Four tasks won and four lost.
- The one effect that is not a coin toss is the spread. On the widest task the
  runs without the server ranged 37-fold from cheapest to dearest and the runs
  with it 1.4-fold. That is a ceiling, and a ceiling is what a person paying per
  session actually buys.
- Code-change tasks went the other way: 2.8 times the cost, four times the input
  tokens, twelve turns against nine, and the two distributions do not overlap.
  All three tasks were changes inside an existing extension, which is the shape
  that had no task intent until the commit that added one.
- Nothing about correctness was measured at all. Every recorded failure in that
  sweep was a defect of its own harness, and its author says in as many words
  that no success rate or hallucination rate may be quoted from it.

## Decided

- The readme says what the shape of the saving is, in one sentence, and says it
  as a ceiling. A number is not put there: it was measured elsewhere, on one
  project, and a figure in a promise outlives the run it came from.
- The measurement is not copied into `knowledge/` and no tool answers from it.
  It is a fact about this server rather than about TYPO3, and the corpus is for
  the second kind.
- Nothing here claims the server makes a session cheaper. Where the median moved
  it moved by less than the choice of task, and saying otherwise would be a
  promise the next reader measures and finds false.

## Assumed

- That predictability is worth stating to somebody deciding whether to install
  it. A ceiling is what a budget is set against, and the alternative reading is
  that only the average is ever looked at.
- That the four lookup tasks are representative of what this server is asked.
  Eleven of its tools were never called in that sweep, because it had no task
  for them, so half the surface is unmeasured rather than measured as neutral.

## Wrong if

- A second project reports a lower median instead, with the spread unchanged.
  Then the saving is the average after all and the sentence names the wrong
  thing.
- The spread collapses just as far without the server once its own harness
  defects are repaired, which would make the ceiling a property of the
  measurement rather than of the answers.
- Correctness turns out to move in the other direction. Nothing here has been
  measured for it, and a server that makes a session predictable and wrong is
  not one this sentence should recommend.
