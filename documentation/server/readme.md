# The server

What it can be asked, and where each answer comes from.

- [tools/](tools/readme.md) — every tool, one page each: what it is for, what it
  takes, which fields it answers with, and what one call to it came back with.
- [resources/](resources/readme.md) — the other surface a client is offered, the
  one that is picked out of a list rather than called mid-task.
- [answer-sources.md](tools/answer-sources.md) — the five sources an answer can
  come from, and what each one needs to be running before it can.
- [knowledge-base.md](knowledge-base.md) — what ships in `knowledge/`, which is
  where almost every answer is read from.
- [versions.md](versions.md) — how a statement that holds for some TYPO3
  versions and not others says so, and why a catalog withholds an entry instead
  of qualifying it.
- [asking-the-installation.md](asking-the-installation.md) — the order an
  installation's own answers are looked up in, how the probe is delivered, and
  what a fallback owes the caller.
- [interface-contract.md](interface-contract.md) — a community proposal for an
  MCP interface contract for TYPO3, read as a reference: what it says and what
  would change here if it were ever adopted.

The tools are the server. Everything else on this page is either the other
surface beside them or an account of where their answers come from — which is
the one thing a caller cannot see from a tool's own description, and the reason
every answer names its source, its version binding and what it left out.
