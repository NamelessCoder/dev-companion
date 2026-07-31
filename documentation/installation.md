# Installing the server

Requirements: **PHP 8.2+** and Composer. The package works both ways — as a
standalone checkout and as a Composer dependency of another project. The
[readme](../readme.md) has the short version; this page has the cases it leaves
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

Naming no client at all stays a setup of its own: `install` then writes the
`.mcp.json` entry every client reads and publishes no skills, because a skill
has to land somewhere and only a named client says where. A later `update`
confirms that entry and reports that there is nothing else there.

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
