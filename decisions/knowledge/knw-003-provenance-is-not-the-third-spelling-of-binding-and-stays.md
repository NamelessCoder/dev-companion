---
id: D-KNW-003
title: '`provenance` is not the third spelling of `binding`, and stays'
date: 2026-07-30
status: revoked
revokedBy: D-KNW-005
coveredBy: []
---

# D-KNW-003 — `provenance` is not the third spelling of `binding`, and stays

**The task intents move to `binding` and `provenance` stays as it is, because
its `installation` value is not an obligation at all.**

The plan was one field name for who an answer obliges, because it looked spelled
three ways: `binding: "core"` on the hints, `coreOnly: true` on the task
intents, `provenance: "core-only"` in `knowledge/server-scope.json`. Two of them
are the same axis. The third is not, and reading its values is what showed it.

## Decided

- The intents move to `binding`, because `coreOnly: true` asks exactly the
  question `binding` asks and answers it in a boolean — which is the shape that
  cannot carry a second audience the day one is needed.
- `provenance` stays as it is. Its values are `core-only`, `transferable` and
  `installation`, and the third is not an obligation at all — it says the answer
  is read from the installation rather than from a snapshot. Folding it into
  `binding` would either drop that value or make "installation" something a
  caller is obliged by. Two fields overlapping on one value are not one field
  with a naming problem.

## Wrong if

- A fourth value arrives on either side that reads naturally in both — then they
  are the same axis after all and the merge is the entry that was right.

## Confirmed on 2026-08-02

No fourth value has arrived on either side. Read out of the corpus as it stands,
`binding` is one value — `core`, on 28 entries across the six
`knowledge/architecture-hints/*.json` files and `knowledge/task-intents.json`,
hint-level and statement-level alike — and `provenance` is the recorded three,
on the 16 covered topics in `knowledge/server-scope.json`: four `core-only`,
four `installation`, eight `transferable`. The move this entry decided is done:
the intents carry `binding` and no `coreOnly` boolean is left in either corpus.
The arrival of a fourth value is caught in two places that do not know about
each other, and neither holds the pair — which is what this entry turns on: a
session widening one vocabulary edits one pin and is never asked whether the new
value reads on the other axis.

## Revoked on 2026-08-02

Hours later the fourth value arrived, and it was the one this entry asked for.
Naming the three audiences of `R-AUD-001` outright — `project` and `extension`
in place of the single negation `outside-core` — reads on both axes at once, so
`binding` and `provenance` were one axis after all and the merge this entry
declined is the entry that was right. What held them apart was `installation`,
and it was never an obligation: it says where an answer is read from, which
`source` on the same topic already said. It is gone as a value, the four topics
that carried it are `any`, and all four vocabularies — `binding`, `provenance`,
`audience` and the `outsideCore` boolean — are the `Knowledge\Scope` enum. See
[`D-KNW-005`](knw-005-scope-is-the-one-word-for-which-work-a-statement-is-for.md).
