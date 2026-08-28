---
id: D-ANS-001
title: The unanswered result keeps its shape and gains a reason
date: 2026-07-29
status: revoked
revokedBy: D-ANS-005
---

# D-ANS-001 — The unanswered result keeps its shape and gains a reason

**The unavailable case keeps the result shape every other answer has and carries
its reason in an `unavailable` object, with `found` null rather than false.**

Two feedback asked for the unavailable case to stop looking like an empty one.
One proposed dropping `matchCount`, `icons`, `found` altogether and returning an
error-shaped object instead, or renaming `answeredBy: "nothing"` to something
that cannot be read as "no source had it".

## Decided

- The shape stays, an `unavailable` object carries the reason, and `found` is
  null rather than false when nothing was consulted. A field a schema requires
  has to be present on every path through the tool, and dropping keys in one
  case would make the declared output schema a shape a client cannot rely on —
  which is the same defect one level up.

## Assumed

- A caller that reads `unavailable.reason` is better served than one that has to
  interpret an enum value, so renaming `nothing` buys little and breaks every
  client that already matches on it.

## Wrong if

- Clients ignore `unavailable` and still read a miss as a registry answer.
  `isError: true` on the result is then the next lever — bluntest, and it would
  make the answer an error rather than an answer, which is why it was not taken
  first.

## Since then

The reading found no client that has been seen with this shape at all: both
recorded runs, the open feedback and the archive were read for a miss reported
as a fact, and none carries one. The reason is not that clients handle it —
every session since came from a directory whose installation was reachable, so
the shape never appeared.

The server still answers as this entry decided, driven over stdio from an empty
directory. What would settle the **Wrong if** is a contract case naming the two
environments, held by four unit tests that read what the server emits rather
than what a client reads off it. So the evidence needs a session in one of them.

## Revoked on 2026-08-02

The shape was not worth keeping, and holding it was what forced the nulls. A
tool that cannot ask has to emit every field its schema requires, and those
fields are the answer — a count, a flag, a list. Withholding what they said
turned the counts and the flags nullable, which is a result shape kept by faking
the numbers out of it. `D-ANS-005` replaces the result instead: `unsupported`
with a cause and a reason, and beside it only the caller's own arguments.

What this entry got right is in the successor unchanged — the reason belongs in
the data, an empty answer and an unanswerable one are not the same thing, and
`found: false` is a statement nobody may make without asking. What it got wrong
is where that reasoning stops. It applied to one field of one tool, and the rule
is about every field of all of them.

The **Wrong if** never fired and was never observed; it is inherited rather than
settled, and the session it needs is still `todo/waiting/`.

## Since then

The session ran on 2026-08-04, in the `E-NONE` this checkout now makes itself,
and what it met was `D-ANS-005`'s shape rather than this one. The inherited
**Wrong if** did not fire: the client reported that the installation could not
be asked and named the two settings that end it. What the run established is
recorded on the successor.
