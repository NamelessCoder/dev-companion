See [AGENTS.md](AGENTS.md) — the conventions for working on this repository are
tool-neutral and live there.

The line below hands that file to this client rather than sending it to fetch
one: an `@` path is inlined where this file is loaded, once and cached, and a
session that is given the conventions does not spend a call on them and does not
spend a second one after every compaction. Measured over the twenty sessions
before 2026-08-08, eighteen read AGENTS.md whole and twenty-eight of those reads
were the same session reading it again.

@AGENTS.md
