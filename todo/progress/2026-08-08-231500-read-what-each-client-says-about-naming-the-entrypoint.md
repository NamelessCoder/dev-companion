# Read what each client says about naming the entrypoint

**Serves:** feedback/2026-08-08-184226-install-writes-a-machine-specific-absolute.md
**Priority:** high
**Branch:** todo/read-what-each-client-says-about-naming-the-entrypoint
**Claimed:** 2026-08-08

The defect stands: a project that has this server as a Composer dependency and
no DDEV gets the absolute host path in its client entry, with
`vendor/bin/typo3-dev-companion` sitting there. Measured 2026-08-08.

**The one-branch fix this card opened with does not work, and that is settled**
— `D-DIS-015` is revoked, `D-DIS-016` replaces it. A relative `args` entry
resolves against a working directory the MCP specification does not define, and
four clients read that day gave three different answers to what it is. Claude
Code documents no working directory and tells servers not to depend on one. So
the shape of a shareable entry is a property of each client.

## What is left to read

Seven of the eleven, from their own documentation, for two facts each: whether
the working directory a stdio server is spawned in is documented, and whether
the client resolves a variable naming the project root in `command` or `args`.
Amp, Junie, Codex, Factory, Kiro, Zed and Grok. Where a client does not say,
that is recorded as unestablished — a guess here is a `.mcp.json` in somebody's
repository that starts a server for nobody.

The four already read are in `D-DIS-016` and belong in
`documentation/clients/installing.md` beside the restart-and-approval answers,
which is the same shape of per-client table and the same reason for one.

## Then

`jsonServer()` takes a shape per client rather than one path, and `ddev exec`
stays what it is — the wrapper that supplies the working directory, which is why
that branch was right all along. `InstallerTest` gains a case per shape.

Independently of all of it, and right under every answer: where the entry is
machine-specific, `install` says so on its `REMAINING` line, names the file, and
says it is a candidate for the project's `.gitignore`. That is the floor, it
needs no reading, and it is the same sentence
`todo/waiting/2026-08-08-231600-decide-what-a-standalone-install-writes-into-a-shared-file.md`
carries as its first option — so do it once, for both halves.

**Run:** `bin/cli todo:next`
