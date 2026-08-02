---
id: D-ANS-001
date: 2026-07-29
status: open
---

# D-ANS-001 — The unanswered result keeps its shape and gains a reason

**The unavailable case keeps the result shape every other answer has and carries
its reason in an `unavailable` object, with `found` null rather than false.**

Two feedback asked for the unavailable case to stop looking like an empty one.
One proposed dropping `matchCount`, `icons`, `found` altogether and returning
an error-shaped object instead, or renaming `answeredBy: "nothing"` to
something that cannot be read as "no source had it".

## Decided

- The shape stays, an `unavailable` object carries the reason, and `found` is
  null rather than false when nothing was consulted. A field a schema requires
  has to be present on every path through the tool, and dropping keys in one
  case would make the declared output schema a shape a client cannot rely on —
  which is the same defect one level up.

## Assumed

- A caller that reads `unavailable.reason` is better served than one that has
  to interpret an enum value, so renaming `nothing` buys little and breaks
  every client that already matches on it.

## Wrong if

- Clients ignore `unavailable` and still read a miss as a registry answer.
  `isError: true` on the result is then the next lever — bluntest, and it would
  make the answer an error rather than an answer, which is why it was not taken
  first.

## Since then

The reading of 2026-08-02 found no client that has been seen with this shape at
all. Both recorded runs, the 69 open feedback and the archive were read for a
miss reported as a fact, and none of them carries one. The reason is not that
clients handle it. `REVIEW-01` ran in `E-SITE` and `REVIEW-02` in `E-EXT`, and
every feedback since `8152bf6` came from a directory whose installation was
reachable. The shape never appeared, so nothing had the chance to misread it.
The nearest thing the corpus holds is a note of 2026-07-31 asking that absences
keep being answered explicitly. That one is `typo3_extension_scope` reporting a
real miss, not an unanswerable one. The server still answers as this entry
decided. Driven over stdio from an empty directory today, `typo3_icon_lookup`
returns `answeredBy: "nothing"` with the `unavailable` object beside it, and
`typo3_configuration_lookup` returns `found: null`. What would settle the
**Wrong if** is `META-02`, which names `E-NONE` and `E-STOPPED` and whose **How
it fails** is this line in other words. It is held as a contract by four unit
tests, and those hold what the server emits rather than what a client reads off
it. No checkout on this machine plays either environment. So the evidence needs
a session in one of them, and the entry stays `standing` until there is one.
