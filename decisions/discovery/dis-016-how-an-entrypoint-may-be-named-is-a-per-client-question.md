---
id: D-DIS-016
date: 2026-08-08
status: open
---

# D-DIS-016 — How an entrypoint may be named is a per-client question

**Whether an entry may name this server by a path relative to the project is
decided per client, out of that client's own documentation.** A client that does
not answer is recorded as unestablished rather than assumed.

`D-DIS-015` wanted one rule for all eleven targets. The clients give at least
three answers, and the one this repository is used with most warns against the
rule it proposed.

## Evidence

- The MCP specification defines the stdio transport as "the client launches the
  MCP server as a subprocess" and says nothing about a working directory. So
  nothing at the protocol level makes a relative `args` entry resolvable.
- Read 2026-08-08, four of the eleven:
  - **VS Code** — a `cwd` field, "Working directory for the server command.
    Defaults to the workspace folder when run in a workspace." Relative is safe
    and documented, and `${workspaceFolder}` is available besides.
  - **opencode** — a `cwd` option, "Working directory for the MCP server
    process. Relative paths resolve from the workspace." No variables
    documented.
  - **Cursor** — the working directory is not documented. Variables are: "Cursor
    resolves variables in these fields: `command`, `args`, `env`, `url`, and
    `headers`", `${workspaceFolder}` among them.
  - **Claude Code** — no working directory documented, and the opposite advice
    given: `CLAUDE_PROJECT_DIR` is set in the spawned server's environment "so
    your server can resolve project-relative paths without depending on the
    working directory". `${VAR}` expands in `command` and `args`, but that
    variable "is set in the server's environment, not in Claude Code's own", so
    a `.mcp.json` reference "requires a default such as
    `${CLAUDE_PROJECT_DIR:-.}`" — and the default is the working directory
    again.
- The seven not read are unestablished, not assumed either way.
- This is why the DDEV branch is correct where a general one would not be:
  `ddev exec` runs in the container's project root, so DDEV supplies the working
  directory the client does not promise.

## Decided

- No blanket rule. The shape of a shareable entry is a property of each client,
  the way `D-DIS-009` found the restart-and-approval answer to be, and
  `documentation/clients/installing.md` is where a per-client reading lands.
- Three shapes are available and which one applies is what the reading decides:
  a plain relative path where the client documents the workspace as the default
  working directory; a variable the client resolves to the project root; or the
  absolute path with the install saying that it is machine-specific.
- The absolute path stays until a client's own documentation says otherwise. It
  is wrong on another machine and right on this one; a relative path against an
  undocumented working directory would be wrong on both.
- Saying it is the floor and is right under every answer. That is the same
  sentence the standalone card carries as its first option, so the two halves of
  the reported defect converge on one mechanism rather than two.

## Assumed

- That the four readings describe the clients as shipped. Each is the client's
  current documentation and none was driven — this repository can install into a
  client but cannot watch one spawn a process.

## Wrong if

- A client documents a workspace working directory and still spawns elsewhere,
  which would make the documented half of this as unreliable as the undocumented
  half.
- The per-client shapes turn out to be one shape after the remaining seven are
  read, and the split cost more than a single rule would have.
- Naming the machine-specific path stays the answer everywhere, and the reading
  bought a sentence that `install` could have printed without it.
