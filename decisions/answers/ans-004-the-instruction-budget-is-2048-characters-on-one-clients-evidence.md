---
id: D-ANS-004
date: 2026-07-31
status: open
---

# D-ANS-004 — The instruction budget is 2048 characters, on one client's evidence

**2048 characters is treated as the limit every client keeps, although exactly
one client has been measured.**

The instructions were cut to fit it rather than the limit being made
configurable.

Both release runs of 2026-07-31 logged
`Server instructions truncated from 3662 to 2048 chars`. Nothing in the protocol
states a limit, and no other client has been measured.

## Decided

- Cut the text. The instructions had grown to 3253 stored characters and said
  several things twice; the version binding is stated in the tools' own answers
  and the core-profile enumeration in `typo3_server_scope`, so what was removed
  is mostly what was already somewhere else. A server that fits the smallest
  known budget needs no negotiation with any client.

## Assumed

- 2048 is a floor rather than one client's number, and a client that keeps more
  loses nothing by being sent less. The cost of being wrong in this direction is
  a shorter statement than necessary; in the other it is silence about the thing
  that was cut.

## Wrong if

- A client is found that truncates below 2048, which makes the budget a property
  of the connection and `Scope::INSTRUCTIONS_BUDGET` a negotiated value rather
  than a constant — or if clients start reporting truncation to the server, at
  which point the server can say what it lost instead of guessing what it may
  spend.
