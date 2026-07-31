---
id: D-ANS-1
date: 2026-07-29
status: standing
---

# D-ANS-1 — The unanswered result keeps its shape and gains a reason

**The unavailable case keeps the result shape every other answer has and carries
its reason in an `unavailable` object, with `found` null rather than false.**

Two notes asked for the unavailable case to stop looking like an empty one. One
proposed dropping `matchCount`, `icons`, `found` altogether and returning an
error-shaped object instead, or renaming `answeredBy: "nothing"` to something
that cannot be read as "no source had it".

- **Decided:** the shape stays, an `unavailable` object carries the reason, and
  `found` is null rather than false when nothing was consulted. A field a schema
  requires has to be present on every path through the tool, and dropping keys
  in one case would make the declared output schema a shape a client cannot
  rely on — which is the same defect one level up.
- **Assumed:** a caller that reads `unavailable.reason` is better served than
  one that has to interpret an enum value, so renaming `nothing` buys little
  and breaks every client that already matches on it.
- **Wrong if:** clients ignore `unavailable` and still read a miss as a registry
  answer. `isError: true` on the result is then the next lever — bluntest, and
  it would make the answer an error rather than an answer, which is why it was
  not taken first.
