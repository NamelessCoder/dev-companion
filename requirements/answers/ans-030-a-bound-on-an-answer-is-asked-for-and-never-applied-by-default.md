---
id: R-ANS-030
status: held
restsOn: [D-ANS-064]
---

# R-ANS-030 — A bound on an answer is asked for and never applied by default

**Where an answer is made smaller, the caller asks for it, and what was left
out is counted in the answer either way.**

A default bound is applied to the caller who needed the whole thing, and it is
applied silently — the answer is the same shape whether it was cut or not. What
makes a bound safe is that a caller reading one record keeps what it had, and
that a caller who asked for less can see how much less it got.

## From

`feedback/2026-08-07-231213` and `2026-08-07-233524`, 2026-08-07. Selecting one
real bug out of thirty candidates is per-issue judgement whose evidence is in
the comments, and the reporting session says it could not have afforded to read
them across ten. The same session filed the journal as what saved it three
times over, and a second session put numbers on that: the decisive note on
14858 was the sixteenth of sixteen and on 15984 the twelfth of sixteen. So
"the most recent N" is not the shape and neither is sampling — what may be
dropped is what a reader was never going to use.

**Built on 2026-08-08.** `typo3_forge_lookup` takes `notes: "people"`, which
drops the notes a review bot wrote and nothing else. Measured against
forge.typo3.org the same day, issue 14858's journal falls from 2573 to 1480
characters, and all eight notes a person wrote come back where fifteen of
sixteen did before — the pings were what the bound was spending itself on. The
change numbers those notes carry are a field of their own by then, so the
filter costs no handle.

## Held by

- `ForgeTest::theJournalComesBackWholeUnlessACallerAsksForLessOfIt`
- `ForgeTest::thePingsAreWhatALimitedReaderDropsAndTheChangesSurviveThem`

The count of what was dropped is what says the bot list has gone stale, and no
test can hold that: an author nobody has named passes the filter, and the
answer being zero is the only thing that shows it.
