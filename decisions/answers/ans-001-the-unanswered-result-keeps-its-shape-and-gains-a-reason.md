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

The reading of 2026-08-02 found no client that has been seen with this shape at
all. Both recorded runs, the 69 open feedback and the archive were read for a
miss reported as a fact, and none of them carries one. The reason is not that
clients handle it. `REVIEW-01` ran in `E-SITE` and `REVIEW-02` in `E-EXT`, and
every feedback since `8152bf6` came from a directory whose installation was
reachable. The shape never appeared, so nothing had the chance to misread it.
The nearest thing the corpus holds is a note of 2026-07-31 asking that absences
keep being answered explicitly. That one is `typo3_extension_describe` reporting
a real miss, not an unanswerable one. The server still answers as this entry
decided. Driven over stdio from an empty directory today, `typo3_icon_lookup`
returns `answeredBy: "nothing"` with the `unavailable` object beside it, and
`typo3_configuration_lookup` returns `found: null`. What would settle the
**Wrong if** is `META-02`, which names `E-NONE` and `E-STOPPED` and whose **How
it fails** is this line in other words. It is held as a contract by four unit
tests, and those hold what the server emits rather than what a client reads off
it. No checkout on this machine plays either environment. So the evidence needs
a session in one of them, and the entry stays `open` until there is one.

Reading the emitted answers rather than the corpus, later the same day, found
that the shape this entry decided was not the shape being sent. Driven where
there is no installation, `typo3_icon_lookup` answers `matchCount: 0`,
`suggestionCount: 0`, `exactMatch: false` and `icons: []` — every field
identical to the miss it emits against a reachable installation, with
`answeredBy` the only thing telling the two apart. This entry made `found` null
and said why; nothing carried that reasoning to the counts and the booleans, so
`typo3_configuration_lookup` was the only tool of eight it ever reached.

`typo3_extension_describe` failed the other way round, and the line above about
it reporting a real miss is what missed it: the answer is a real miss and it is
labelled `answeredBy: "nothing"`, against an installation that had just listed
27 packages, with no `unavailable` object beside it — which the schema says is
present exactly then. A correct negative wearing the unanswerable shape is the
same defect read backwards.

Both are closed, and `R-ANS-001` now states the rule for all of them: every
count and every boolean an installation-backed tool declares is null where
nothing was consulted, and `answeredBy: "nothing"` is written in
`Result\Unanswered` and nowhere else. The `unavailable` object gained `searched`
and `misconfiguration`, which `typo3_server_scope` alone carried — a caller just
told that nothing could be asked is the one least able to guess that another
tool holds the half of the reason naming the way out.

None of it settles the **Wrong if**, which is about what a client does with a
well-formed answer and still needs the session. It changes what that session
measures: a client misreading the old payload would have answered a question
about a shape that is no longer sent.

It also puts a lever between doing nothing and `isError: true`. The
`instructions` sent at initialize never mention `answeredBy`, so a client is
told to call the installation-backed lookups and never told that a miss and an
unanswerable answer are different things. Saying it there is cheaper than
turning an answer into an error. It is not free: `R-ANS-013` holds the
instructions to a budget a client keeps, so the sentence has to displace one,
and which one is a trade nobody has made yet.

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
