---
id: D-KNW-3
date: 2026-07-30
status: standing
---

# D-KNW-3 — `provenance` is not the third spelling of `binding`, and stays

**The task intents move to `binding` and `provenance` stays as it is, because
its `installation` value is not an obligation at all.**

The plan was one field name for who an answer obliges, because it looked spelled
three ways: `binding: "core"` on the hints, `coreOnly: true` on the task
intents, `provenance: "core-only"` in `knowledge/server-scope.json`. Two of them
are the same axis. The third is not, and reading its values is what showed it.

- **Decided:** the intents move to `binding`, because `coreOnly: true` asks
  exactly the question `binding` asks and answers it in a boolean — which is the
  shape that cannot carry a second audience the day one is needed.
- **Decided:** `provenance` stays as it is. Its values are `core-only`,
  `transferable` and `installation`, and the third is not an obligation at all —
  it says the answer is read from the installation rather than from a snapshot.
  Folding it into `binding` would either drop that value or make "installation"
  something a caller is obliged by. Two fields overlapping on one value are not
  one field with a naming problem.
- **Wrong if:** a fourth value arrives on either side that reads naturally in
  both — then they are the same axis after all and the merge is the entry that
  was right.
