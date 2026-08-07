---
id: R-ANS-029
status: open
restsOn: [D-ANS-064]
---

# R-ANS-029 — An answer that names a record says enough of it to judge whether to open it

**Where an answer names another record this server can read, it carries what a
caller needs to decide whether to read it — not the identifier alone.**

An identifier with nothing beside it costs one call to evaluate, so a caller
holding several of them evaluates none. The record that mattered is then skipped
for the same reason as the ones that did not, and the answer looks complete
while the caller acts on less than it was handed.

## From

`feedback/2026-08-07-231225`, 2026-08-07. `typo3_forge_lookup` answered issue
15984 with four relations as `{issue, relation}` pairs. The session spent no
reads on them and told the user nothing about them; one was `#32756`, "Massive
Memory Leak in 4.5.8+ / 4.6", marked `precedes` — the issue the 2012 revert was
filed under and the record that answers what a fix would cost. It surfaced
afterwards out of a git commit message.

`feedback/2026-08-07-231146` is the same shape one field over: the Gerrit change
references an issue's journal names are prose inside a note, so
`typo3_gerrit_lookup` was never called and never even had its schema loaded.

## Held by

- `not guarded` — what would hold it is an assertion over the fields an answer
  carries beside an identifier, and which of them a relation gets is the todo's
  work.
