# Say an unknown exclusion where the client reads it

**Serves:** R-SCO-009, decisions/audience/
**Priority:** normal

`D-AUD-005` put the stderr half in: a name in `TYPO3_MCP_EXCLUDE_TOOLS` that no
tool answers to is written to stderr before the transport starts, and the server
starts anyway. The half it deliberately left out is what the client is told, and
today the client is told something false. `ExcludedTools::all()` is still what
the caller wrote, so with `TYPO3_MCP_EXCLUDE_TOOLS=typo3_project_scope` the
initialize instructions open with "typo3_project_scope is left out of your tool
list, and so is anything that routed to it" — measured on 2026-08-04 — while the
tool is there under its new name. An agent reads that as a capability it does
not have, and it is paid for out of the 2048 characters
`Coverage::INSTRUCTIONS_BUDGET` allows. `typo3_server_scope` reports the same
name under `excludedTools.names`.

Decide where the truth is said and say it once: `ExcludedTools::all()` trimmed
to the names that are real, with the unknown ones carried separately for
`typo3_server_scope` to name as unknown rather than as excluded. The trim makes
`all()` ask the registry, so it needs memoizing on the raw variable — the tests
change it between assertions. `src/Knowledge/Coverage.php` and
`src/Tool/ServerScope.php` are the two readers, and both were outside the file
set of the session that wrote `D-AUD-005`.

A name that is real but cannot be excluded tells the client the same falsehood,
so the trim has a second input. Measured on 2026-08-04 in this checkout with
`TYPO3_MCP_EXCLUDE_TOOLS=typo3_feedback_record`: 26 tools offered including that
one, `excludedTools.names` reporting it, and the instructions opening
"typo3_feedback_record is left out of your tool list". `typo3_server_scope` is
already dropped in `ExcludedTools::all()`; the feedback tools are not, because
`Registry::offered()` appends them past the filter. Whatever the trim asks the
registry, it has to be the offered list — which already answers this, since
those two are in it.

That the feedback tools cannot be excluded is not the defect this card carried
until 2026-08-04. `typo3_feedback_record` writes into this checkout, not into
the caller's installation, and the channel is a development tool for building
this server: `D-FBK-042` is the reasoning and `R-SCO-009` now names both
exceptions. Nothing about `Registry::offered()` is to be changed here.

The other half of the installer is left too. A JSON entry now keeps what the
caller put in it beside the command, so an exclusion in `env` survives; the TOML
section `codex` and `grok` get is still rewritten whole, and an `env` line in
`.codex/config.toml` was gone after `install --agent=codex` on 2026-08-04.
`installTomlConfiguration` replaces a section it matched with a regex and never
parsed, so keeping the caller's lines means deciding what a line is — that is
why it was not done beside the JSON one.

`documentation/clients/installing.md` promises what setup gives a client and
does not yet say that a name nothing answers to is reported, nor that three
tools cannot be excluded; both sentences belong in the `TYPO3_MCP_EXCLUDE_TOOLS`
bullet once the client-side half is decided, so all of it is said in the same
words.

It is `normal` and not `high` because nothing is broken for the caller who reads
stderr and every tool is reachable; it is not `low` because the instructions
every session receives currently carry a statement that is not true.
