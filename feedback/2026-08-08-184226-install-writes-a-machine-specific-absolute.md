---
date: 2026-08-08T18:42:26+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: install
directory: /home/benji/projects/typo3-cms
---

# install writes a machine-specific absolute entrypoint into the one file every client documents as...

## Observation

Task: writing a TYPO3 core patch that extends the core's `.gitignore` so the files AI clients leave in a checkout stay untracked. Establishing which paths this package produces raised the question of whether those paths are the right ones, and the answer is that for the standalone setup they are not.

`Installer::AGENTS` writes the MCP entry to `.mcp.json` (generic and claude), `.amp/settings.json`, `.junie/mcp/mcp.json`, `.cursor/mcp.json`, `.codex/config.toml`, `.vscode/mcp.json`, `.factory/mcp.json`, `.kiro/settings/mcp.json`, `opencode.json`, `.zed/settings.json` and `.grok/config.toml`. `installedEntrypoint()` writes a path relative to the project root when the server is a dependency of it (`bin-dir` or `vendor/bin`), and the absolute host path when it is not — which is exactly the standalone setup `documentation/clients/installing.md` opens with. In the core checkout this produced `.mcp.json` naming `/home/<user>/projects/typo3-cms-mcp/bin/typo3-dev-companion`, a value true on one machine.

Every one of those eleven targets is documented by its own client as the shared, committed, team-level location, and the client documentation read on 2026-08-08 says so in almost the same words: VS Code "include this file in source control to share MCP server configurations with your team"; Cursor project-level "for team-shared tooling", with secrets kept in the global `~/.cursor/mcp.json` that "is never committed"; Factory "project-level `.factory/mcp.json` is committed to the repo"; Junie "can be checked into version control and shared across all team members"; opencode "project configuration is also safe to be checked into Git"; Kiro workspace versus user; Amp workspace overriding user; Grok `--scope project` for servers that "ship with the repo"; Claude Code project scope, "`.mcp.json` at the repo root and checked into version control". So the package writes a value that cannot be shared into the file that exists to be shared, and it does that in every project it is installed into standalone.

Claude Code is the only one of them that documents a third scope for exactly this case: local scope, stored in `~/.claude.json` under the project's path, private to the person, bound to that project only, and the default of `claude mcp add`. The installer does not use it. For the other ten there is no private per-project file — only project and user scope, and user scope loses the project binding — so for those the honest outcome is the entry plus a word about it.

Nothing says that word. `install` prints a per-client `REMAINING` line about approval and about a session that must be restarted, but nothing about the file it just modified being machine-specific and a candidate for the consuming project's `.gitignore`. That the question is a real one for this package is already established by its own behaviour elsewhere: it writes `IGNORE_ALL` into the state directory and into every skill directory it publishes, so those artefacts stay invisible to git while the MCP entry, the only artefact whose content is host-specific, is the one left in a tracked-by-convention file.

## Query

`typo3-dev-companion install` and `install --agent=<client>` run from a TYPO3 core checkout where this package is not a Composer dependency but a checkout elsewhere; the resulting `.mcp.json` read back, and `src/Server/Installer.php` read at `AGENTS`, `jsonServer()` and `installedEntrypoint()`.

## Suggestion

Split the two cases the entrypoint already distinguishes. When `installedEntrypoint()` returns a relative path, the entry is genuinely shareable and the project-scope file is the right target — keep it as it is. When it returns null and the absolute path is written, the entry belongs where that client keeps private per-project configuration: for `--agent=claude` that is local scope in `~/.claude.json` under the project path, equivalent to `claude mcp add --scope local`, which leaves the repository untouched altogether.

For the ten clients with no private per-project scope, say it rather than write it silently: a line beside the reported entry naming the file, that its command is an absolute path valid on this machine only, and that it is therefore a candidate for the project's `.gitignore`. `REMAINING` is already the place where per-client "what is left" is said and would carry it without a new mechanism.

A `--scope` argument on `install` would make the choice explicit where a client offers one, and refusing to write an absolute entrypoint into a shared file without it would keep the default honest.
