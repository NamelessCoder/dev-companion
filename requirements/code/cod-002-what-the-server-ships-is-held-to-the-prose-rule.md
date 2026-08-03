---
id: R-COD-002
status: held
restsOn: [D-DOC-002]
---

# R-COD-002 — What the server ships is held to the prose rule

**What the server ships to a client is written to the same prose rule as what
this repository writes about itself.**

The tool descriptions, the schema field texts and the `instructions` sent at
initialize are prose this repository is answerable for, and a caller pays for
all of it before it has asked anything. One point per sentence, the rule before
the reason, no sentence restating the one above it — the same measure
[AGENTS.md](../../AGENTS.md) states, applied where the reader is a machine that
cannot skim.

## From

The session of 2026-08-03 that went looking for prose to cut. What a client is
offered at connect measured 118202 characters, of which 14502 were the 26 tool
descriptions and 11507 the input schema fields; cutting only what restated
something already in the payload took 1219 of them off. The restatement was
found by reading, not by a report: `bin/cli prose:check` reads `AGENTS.md`,
`readme.md` and the markdown below seven directories, and reaches no file in
`src/`.

## Held by

- The measure is not guarded. `bin/cli prose:check` reads the markdown corpus
  and does not reach the tool payload, so no count exists for the half a caller
  actually pays for, and a description that grows back is caught by whoever
  rereads it.
