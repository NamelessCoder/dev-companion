# The layout list names a class that was deleted

**Serves:** decisions/audience/
**Priority:** low

`AGENTS.md` line 13 lists `src/Server/Profile.php` as "which half of the server
a client is offered (`TYPO3_MCP_PROFILE`)". There is no such file: `src/Server/`
holds `Entrypoint`, `ExcludedTools`, `Factory` and `Installer`, and `D-AUD-004`
replaced the two profiles with `TYPO3_MCP_EXCLUDE_TOOLS` — `TYPO3_MCP_PROFILE`
appears in no code, no knowledge file and no skill. The layout list is one of
the four things that describe this server outward, and a session reading it
looks for a class nobody can open. Delete the line, and check in the same pass
whether the `src/Server/` entries around it still say what those classes do.
Found while writing `documentation/resources/readme.md`, in a session whose file
set did not include `AGENTS.md`.
