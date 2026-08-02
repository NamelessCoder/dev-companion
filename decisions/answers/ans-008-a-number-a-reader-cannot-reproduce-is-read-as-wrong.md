---
id: D-ANS-008
date: 2026-08-02
status: open
---

# D-ANS-008 — A number a reader cannot reproduce is read as wrong

**A count in an answer says what it counted, because a caller who checks it and
gets a different number reports the answer as false.**

The first feedback judged off the board was exactly this: a correct count,
checked against the obvious command, disagreeing — and filed as a wrong answer.

## Evidence

- `feedback/2026-07-31-172754`, re-run against
  `/home/benji/projects/bootstrap_package` on 2026-08-02. The tool reported
  `Updates (27)`; the session counted 21 and reported the count as overstated.
  Both numbers are right: 21 PHP files sit directly in `Classes/Updates/` and
  six more in `Classes/Updates/Criteria/`, which `Extension::countPhpFiles()`
  includes because its Finder carries no `depth(0)`.
- The count was not stale either. `Criteria/` was last touched in that checkout
  on 2024-10-10 and its HEAD is 2026-06-30, so the directory was there when the
  feedback was written. Nothing about the answer had changed since.
- Neither place the number appears says which it is. The rendered line is
  `Updates (27)` with no qualifier, and the schema says `PHP files below it`,
  which reads as "directly in" as easily as "anywhere under".

## Decided

- The judgement is **step 4 of the ladder**, wording, not a wrong answer and not
  a gap. What failed is that the number could not be reproduced, and a reader
  who cannot reproduce a number does not conclude they measured something else
  — they conclude the answer is wrong. This one arrived as a `wrong-answer`
  feedback from a session that had done nothing careless.
- It is **queued rather than closed on the spot**, because the fix touches
  `src/` and a declared `outputSchema`, which
  [judging.md](../../documentation/feedback/judging.md) puts on the reviewed
  side of that line.
- The judgement names the gap and not the fix. Whether the count should become
  the shallow one, so that `ls` reproduces it, or stay deep and say so, is a
  question about what the section is for, and the todo settles it against the
  tool rather than here.
- Recorded as its own entry rather than against the tool, because the property
  is not about this count. Every kind under `Classes/` is counted by the same
  method, and any number this server states has the same exposure.

## Assumed

- That callers check. This one did, and reported the difference rather than
  taking the tool's word — which is the behaviour worth designing for even
  though nothing measures how often it happens.
- That saying what was counted is enough, and that a reader given "27 files,
  including subdirectories" reproduces it. The alternative, matching the number
  a reader would produce unaided, assumes what command they would reach for.

## Wrong if

- A second feedback disputes a different number the same way, which would mean
  this is a property of every count here and belongs in a requirement rather
  than one todo.
- The qualifier lands and a later feedback still reports the count as wrong.
  Then what was missing was not the wording but the number, and the shallow
  count was the right one all along.
- Nothing else in this server states a number a caller could check, which would
  make the generalisation above one case wearing a rule's clothes.
