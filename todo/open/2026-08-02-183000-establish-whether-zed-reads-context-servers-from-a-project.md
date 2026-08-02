# Establish whether Zed reads context_servers from a project .zed/settings.json

**Serves:** R-DIS-013
**Priority:** normal

Reading all eleven clients' documentation on 2026-08-02 for `R-DIS-023` turned
up one that is not a gap in what the install *says* but possibly in what it
*writes*: `Installer::AGENTS` puts Zed's entry under `context_servers` in
`.zed/settings.json`, a project-local file, and Zed's own MCP documentation
(https://zed.dev/docs/ai/mcp) describes `context_servers` only in the settings
file it opens with `zed: open settings file`, saying nothing about a project
one. Every other client's project path is documented as a project path. So
establish from Zed's own documentation and, where that stays silent, from a Zed
installation, whether a `context_servers` entry in a project `.zed/settings.json`
is read at all; if it is not, the entry this installer writes for `--agent=zed`
reaches nothing and the fix is the path rather than the message.
`documentation/clients/installing.md` records the question as unestablished
today, and the installer's own output says the same to whoever runs it, so this
is what would replace both with an answer.
