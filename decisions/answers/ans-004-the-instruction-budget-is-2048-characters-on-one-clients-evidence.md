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
  of the connection and `Coverage::INSTRUCTIONS_BUDGET` a negotiated value
  rather than a constant — or if clients start reporting truncation to the
  server, at which point the server can say what it lost instead of guessing
  what it may spend.

## Since then

The number has become what blocks rather than what was spent. Measured on
2026-08-21, the longest assembly stands at 2028 characters of the 2048, and
`feedback/2026-08-19-090401` asks for a statement of the boundary that does not
fit in the twenty left —
[`D-AUD-011`](../audience/aud-011-the-instructions-index-the-question-each-tool-answers-because-a-name-is-all-a-deferring-client-shows.md)
is the reading, and the third card in a row to end at this constant.

Nothing has re-measured it. This entry took 2048 from one client's release runs
on 2026-07-31, and the client reporting above is a different one that delivered
the block whole and reports no truncation at all. So the case that has arrived
is the direction the **Wrong if** is not written for: not a client that keeps
less, but one that may keep more while the server writes to the smaller number.
What would settle it is one session reporting the length of the `instructions`
its client actually kept, which no session in this checkout can produce.
