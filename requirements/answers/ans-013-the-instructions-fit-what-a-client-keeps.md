---
id: R-ANS-013
title: 'The instructions fit what a client keeps'
status: held
heldBy:
  - ScopeTest::theInstructionsFitWhatAClientKeeps
  - StdioServerTest::theServerAnnouncesItselfWithItsBoundary
---

# R-ANS-013 — The instructions fit what a client keeps

**The statement sent at initialize stays within the budget a client can be
relied on to keep, counted over everything assembled — the profile prefix and
the write sentence included.**

A client that truncates says so to its own debug output and to nobody else, so
the sentences past the limit are not missing in a way anything reports: the
server believes it said them and the agent never read them. What falls off is
the end, which is where the statements that qualify everything before them sit —
[`R-AUD-006`](../audience/aud-006-the-query-language-is-english.md) was written
down as "the entire mitigation" and was the first thing cut. Length is therefore
a property of the instructions rather than of the client, and it is held here so
that the next sentence added has to displace one.

## From

Two release runs in `E-EXT` (2026-07-31) whose client cut 3662 characters to
2048, dropping the English-query sentence, the version binding and
`typo3_server_scope` from every session it ran.

## Held by

- `ScopeTest::theInstructionsFitWhatAClientKeeps` for every profile, and
- `StdioServerTest::theServerAnnouncesItselfWithItsBoundary` on the string a
- Client is handed over the wire
