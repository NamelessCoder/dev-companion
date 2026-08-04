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
name under
`excludedTools.names`.

Decide where the truth is said and say it once: `ExcludedTools::all()` trimmed
to the names that are real, with the unknown ones carried separately for
`typo3_server_scope` to name as unknown rather than as excluded. The trim makes
`all()` ask the registry, so it needs memoizing on the raw variable — the tests
change it between assertions. `src/Knowledge/Coverage.php` and
`src/Tool/ServerScope.php` are the two readers, and both were outside the file
set of the session that wrote `D-AUD-005`.

Beside it, one thing that reading found and nothing covers:
`Registry::offered()` appends `FEEDBACK` after the exclusion filter, so
`typo3_feedback_record` and `typo3_feedback_list` cannot be excluded at all
where the feedback channel is available — a standalone checkout offered 25 tools
with `typo3_feedback_record` named in the variable. `R-SCO-009` says a caller
can exclude individual tools, and this is the one tool that writes. Either it is
filtered like the rest, or it joins `typo3_server_scope` as a documented
exception with a reason.

The other half of the installer is left too. A JSON entry now keeps what the
caller put in it beside the command, so an exclusion in `env` survives; the TOML
section `codex` and `grok` get is still rewritten whole, and an `env` line in
`.codex/config.toml` was gone after `install --agent=codex` on 2026-08-04.
`installTomlConfiguration` replaces a section it matched with a regex and never
parsed, so keeping the caller's lines means deciding what a line is — that is
why it was not done beside the JSON one.

`documentation/clients/installing.md` promises what setup gives a client and
does not yet say that a name nothing answers to is reported; a sentence belongs
in the `TYPO3_MCP_EXCLUDE_TOOLS` bullet once the client-side half is decided, so
both are said in the same words.

It is `normal` and not `high` because nothing is broken for the caller who reads
stderr and every tool is reachable; it is not `low` because the instructions
every session receives currently carry a statement that is not true.
