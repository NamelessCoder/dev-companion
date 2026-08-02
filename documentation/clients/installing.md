# Installing the server

Requirements: **PHP 8.2+** and Composer. The package works both ways — as a
standalone checkout and as a Composer dependency of another project. The
[readme](../../readme.md) has the short version; this page has the cases it leaves
out.

## Standalone

Clone the repository and install the dependencies once:

```bash
composer install
```

Then install the entrypoint into the current project's `.mcp.json`:

```bash
/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp install
```

For Codex, install its project configuration and task skills directly:

```bash
/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp install --agent=codex
```

Refresh them after updating this package:

```bash
/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp update
```

`update` takes `--agent=<client>` as well, but rarely needs to: `install`
records every client it set up in `typo3-cms-mcp.json`, and without an agent
`update` refreshes all of them. A project is usually worked on by more than one,
and which ones is knowledge only the project has.

Both commands write the client entry, because what belongs in it is a property
of the project rather than of the run: a project that required this package
after it was first installed, or that gained a DDEV configuration since, needs
a different entry than the one that is there, and `update` is what moves it.
An entry that starts something other than this server is somebody else's and is
refused instead — the two commands then say so and change nothing.

Naming no client at all is a setup of its own, recorded as `generic`: `install`
then writes the `.mcp.json` entry and publishes the skills to `.agents/skills`,
the two locations a client finds without being configured for it. It is
refreshed by the same `update` as every named client. `--agent=` does not take
`generic`, because it is nobody's name.

Install and update write `typo3-cms-mcp.json` and the package-owned skill
directories into the project's `.gitignore`, between `# BEGIN typo3-cms-mcp` and
`# END typo3-cms-mcp`. Everything between those markers belongs to this package
and is replaced whole on every run, so a client that is gone or a skill that was
renamed leaves no line behind; everything outside them is the project's and is
never touched. Merged agent or MCP configuration such as `.codex/config.toml` or
`.mcp.json` is not ignored, because the project may share it.

It writes the following shape with the actual absolute path:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "stdio",
      "command": "php",
      "args": ["/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp"]
    }
  }
}
```

This is the setup to use when working on the knowledge base itself, since the
`feedback/` tools only exist in a checkout.

## As a dependency

The package is not published on Packagist, so it is required from a local
checkout (or a Git URL) through a `repositories` entry in the consuming
project's `composer.json`:

```json
{
  "repositories": [
    { "type": "path", "url": "/absolute/path/to/typo3-cms-mcp" }
  ]
}
```

```bash
composer require typo3/cms-mcp
```

Composer then exposes the stdio entrypoint as `vendor/bin/typo3-cms-mcp`.
Install it from the consuming project's root:

```bash
vendor/bin/typo3-cms-mcp install
```

Use `vendor/bin/typo3-cms-mcp install --agent=codex` for the corresponding Codex
setup, and `vendor/bin/typo3-cms-mcp update` to refresh it and every other
client installed there.

`vendor/bin/typo3-cms-mcp help` lists both commands and every client they
accept. Passing anything else fails with that same text: without an argument
the entrypoint is the MCP transport itself and waits on stdin, which at a
terminal is indistinguishable from a hang.

The same commands support the agent identifiers `amp`, `junie`, `cursor`,
`claude`, `copilot`, `factory`, `kiro`, `opencode`, `antigravity`, `zed`,
`pi`, and `grok`. Each receives the skill at its native project path and,
where the client supports it, its native MCP configuration. Antigravity and Pi
receive skills only.

### VS Code reads the skills only once it is told to

`--agent=copilot` writes them to `.github/skills`, which is one of the two
locations VS Code searches by default — but only if `chat.useAgentSkills` is
on, and it is not:

```json
"chat.useAgentSkills": true
```

Without it the client assembles no search paths at all, so nothing reports that
six skills are sitting in the repository unread; a session there answers from
the checkout as if none had been installed (measured on VS Code 1.131.0,
2026-07-31). `chat.agentSkillsLocations` is the list itself and needs no change:
it already covers `.github/skills` and `.claude/skills` per workspace.
`github.copilot.chat.skillTool.enabled` is a different, experimental switch and
not the one that makes them visible.

That writes the same `.mcp.json` shape with an absolute path.

### A written entry is not a registered server

The note above is one artefact over from the one every client install writes.
Putting the entry on disk registers the server with nothing: a client that
scopes project servers behind an approval has not been asked yet, and a session
that was already open when the file was written is running against the
configuration it started with. Both end with an entry that is entirely correct,
a published skill naming eleven tools beside it, and no tool in the session —
which is where two sessions in one project went, on 2026-07-29 and 2026-07-31.

`install` and `update` print what follows under the line that reports the entry,
so it is read at the terminal rather than here. What each client needs is the
client's own property, so each line below is that client's own documentation,
read on 2026-08-02, and a client whose documentation does not answer is left
unestablished rather than filled in:

- **Claude Code** (`.mcp.json`) — both. "Claude Code reads `.mcp.json` at
  session start. Exit and restart the session after editing the file", and "the
  first time Claude Code sees a project-scoped server, it asks you to approve
  it". Approve at the prompt or in `/mcp`; a server once refused is reset with
  `claude mcp reset-project-choices`.
  ([quickstart](https://code.claude.com/docs/en/mcp-quickstart),
  [reference](https://code.claude.com/docs/en/mcp))
- **Amp** (`.amp/settings.json`) — an approval. "MCP servers in workspace
  settings (`.amp/settings.json`) require explicit approval before they can
  run", and "in the CLI, you'll be prompted to approve workspace servers when
  they're first detected". `amp mcp approve typo3-cms-mcp` does it without the
  prompt, and `amp mcp doctor` shows one `awaiting approval`.
  ([manual](https://ampcode.com/manual))
- **VS Code** (`.vscode/mcp.json`) — a trust confirmation. "When you add an MCP
  server to your workspace or change its configuration, you need to confirm that
  you trust the server and its capabilities before starting it." The experimental
  `chat.mcp.autoStart` restarts the server when the configuration changes.
  ([MCP servers](https://code.visualstudio.com/docs/copilot/customization/mcp-servers))
- **Codex** (`.codex/config.toml`) — a trusted project. MCP servers can be
  scoped "to a project with `.codex/config.toml` (trusted projects only)", so the
  trust prompt for the directory is what admits them. Whether a running session
  reads the file again is not documented; `codex mcp list` reports what it has.
  ([MCP](https://learn.chatgpt.com/docs/extend/mcp))
- **Kiro** (`.kiro/settings/mcp.json`) — nothing. "Changes to MCP configuration
  apply automatically when you save the file" and "servers will reconnect". A
  tool `autoApprove` does not name is still asked about on the call.
  ([MCP configuration](https://kiro.dev/docs/mcp/configuration/))
- **Droid** (`.factory/mcp.json`) — nothing. "Droid reloads automatically when an
  `mcp.json` file changes, so new servers are available immediately." Each tool
  is approved on first use, and `droid mcp permissions` keeps that approval.
  ([MCP](https://docs.factory.ai/cli/configuration/mcp))
- **Junie** (`.junie/mcp/mcp.json`) — no approval: servers "imported from the
  `mcp.json` file are enabled by default". Whether an IDE that was already open
  reads a new one is not documented; the list is *Settings | Tools | Junie | MCP
  Settings*.
  ([MCP configuration](https://junie.jetbrains.com/docs/junie-cli-mcp-configuration.html))
- **Cursor** (`.cursor/mcp.json`) — unestablished. Servers are listed under
  *Customize*, where one is toggled off, and "Cursor asks for approval before
  using MCP tools by default" — which is the tool call, not the server. Whether
  a window that was already open reads a new file is not documented.
  ([MCP](https://cursor.com/docs/mcp))
- **opencode** (`opencode.json`) — unestablished. `enabled: false` switches a
  server off, which the written entry does not; whether a session that was
  already open reads the file again is not documented.
  ([MCP servers](https://opencode.ai/docs/mcp-servers/))
- **Zed** (`.zed/settings.json`) — unestablished, and further than the others:
  the documentation puts `context_servers` in the settings file opened with
  `zed: open settings file` and says nothing about a project `.zed/settings.json`,
  so whether the written entry is read at all is unconfirmed.
  ([MCP](https://zed.dev/docs/ai/mcp))
- **Grok** (`.grok/config.toml`) — unestablished. A project `.grok/config.toml`
  does contribute `[mcp_servers]`, walking up to the git root; whether a running
  session reads it again, and whether anything gates it, is not documented.
  `grok mcp doctor` reports what it has.
  ([MCP servers](https://docs.x.ai/build/features/mcp-servers))

Antigravity and Pi receive skills only, so there is no entry and nothing to
finish. Where a session has the entry and still offers no `typo3_` tool,
[checking that it came up](#checking-that-it-came-up) separates the two halves:
a server that answers there is not the missing piece.

## In a DDEV project

Run the installer inside DDEV:

```bash
ddev exec vendor/bin/typo3-cms-mcp install --agent=codex
```

The project directory is mounted, so the skills are available to the host at
`.agents/skills`. The generated MCP entry deliberately starts the server with
the project's container PHP, at the `config.bin-dir` the project declares —
`.build/bin/typo3-cms-mcp` in the layout most extension repositories use:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "stdio",
      "command": "ddev",
      "args": ["exec", "php", "vendor/bin/typo3-cms-mcp"]
    }
  }
}
```

Outside DDEV — and in a DDEV project that never required the package, where the
container would not see the checkout the server runs from — the generated
configuration uses the absolute entrypoint:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "stdio",
      "command": "php",
      "args": ["/absolute/path/to/project/vendor/bin/typo3-cms-mcp"]
    }
  }
}
```

The knowledge base ships inside the package, so nothing else needs to be
deployed or configured.

## Which tools a client is offered

Every one of them, wherever the server was started. Some of what it knows is the
core's own contribution process — the review rules, the Gerrit workflow, the
core testing suites — and none of that transfers to a project. What it is worth
is said in the answer, per topic and per path, because whether a task is core
work is a property of the task and not of the directory it is asked from.

The server used to leave those three tools out of a Composer project. That read
the repository where the task was meant, and a core patch written from a site
installation was answered as core work and then sent to a tool the client had
not been given.

- `TYPO3_MCP_EXCLUDE_TOOLS` removes tools by their comma-separated names, and is
  the only thing that shortens the list. `typo3_server_scope` is never one of
  them: it is what explains a shorter list.

`typo3_server_scope` names what was excluded, and nothing routes to a tool that
is not there.

## What comes with it

Clients that expose MCP prompts also list `commit_message`. It turns a summary
into the same checked draft as `typo3_commit_message_guide`; the rules remain in
the guide rather than being duplicated in the prompt.

Task skills are authored once below `skills/`. They contain routing and order,
not a second copy of tool answers; client installation publishes them from that
source.

## Checking that it came up

Two JSON-RPC lines on stdin are enough to see the server start and list its
tools:

```bash
printf '%s\n' \
  '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"t","version":"1"}}}' \
  '{"jsonrpc":"2.0","id":2,"method":"tools/list"}' \
  | php bin/typo3-cms-mcp
```

Where discovery needs help, two environment variables end the guessing:
`TYPO3_MCP_ROOT` names the installation and `TYPO3_MCP_CONSOLE` the command that
reaches its console, for example `ddev exec .build/bin/typo3`.
`typo3_server_scope` then names the installation it is reading, how it got
there, and whether the console is reachable.

`TYPO3_MCP_ROOT` names what is read and not where the work is. Point it at a
site installation from a core checkout and the icons and labels come from the
site while the answers stay the core's; only where nothing can be walked up to
does it decide that too.
