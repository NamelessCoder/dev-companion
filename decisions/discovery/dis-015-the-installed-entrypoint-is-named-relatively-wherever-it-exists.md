---
id: D-DIS-015
date: 2026-08-08
status: revoked
revokedBy: D-DIS-016
---

# D-DIS-015 — The installed entrypoint is named relatively wherever it exists

**Where the project has this server as a dependency, the client entry names it
relative to the project root, whether or not a DDEV configuration is there. Only
a standalone checkout has no such path, and what to write for it is a question
nobody here can answer.**

This is the judgement of
`feedback/2026-08-08-184226-install-writes-a-machine-specific-absolute.md`,
which reported that `install` writes a host path into files eleven clients
document as shared and committed.

## Evidence

- The report is right about the outcome and wrong about the condition. It read
  `installedEntrypoint()` as returning a relative path whenever the server is a
  dependency; `jsonServer()` uses that return **only** where `.ddev/config.yaml`
  exists, and writes `$this->entrypoint` — the absolute host path — on every
  other route.
- Measured 2026-08-08 in a fixture project declaring `typo3/dev-companion`, with
  `vendor/bin/typo3-dev-companion` present and no `.ddev`:
  `install --agent=claude` wrote
  `/home/benji/projects/typo3-cms-mcp/bin/typo3-dev-companion` into `.mcp.json`.
  So the shareable case the feedback assumed already worked does not.
- The three checkouts this repository installs into were repaired the same day
  and all three carry the absolute path, which is consistent with the reading:
  in those the server genuinely is not a dependency.
- The report's client reading was not re-verified here and does not need to be
  for this half: whether those files are meant to be committed decides what the
  standalone case should write, and the dependency case is wrong under either
  answer — the relative path is both shareable and correct, and it is the one
  the DDEV branch already writes.
- Nothing in `decisions/` settles this. `D-DIS-009` is about what an install
  says is *left* to do, not about what it writes; `D-DIS-002` is about honouring
  the declared `bin-dir`, which is the very path being discarded here.

## Decided

- Ladder step: **repair to something that exists**, not a gap. The command, the
  targets and the relative path are all here, and one branch does not use them.
- The two halves are judged apart, because only one of them is ours. The
  dependency case is a defect and is queued. The standalone case is a boundary:
  the alternatives include writing into the client's private per-project
  configuration outside the project, and what this command may touch is the
  maintainer's to decide, so it waits.
- Fixing the dependency case first is not a partial answer. It is what removes
  the reported harm from every project that installed this server the ordinary
  way, and it narrows the open question to the checkout-elsewhere setup.
- The feedback is not archived by this commit. Two todos serve it and it stays
  open until the change lands.

## Assumed

- That a client spawns the server with the project root as its working
  directory, which is what makes a relative `args` entry resolve. The DDEV
  branch already rests on it, so this decision does not add the assumption — but
  it extends it to every client rather than to DDEV projects alone, and it is
  the first thing the todo has to establish.

## Wrong if

- A client resolves the command against something other than the project root,
  and a relative entry starts a server for nobody. That is worse than a
  host-specific path, because it fails on the machine that ran the install too.
- The absolute path turns out to be what somebody wanted in the dependency case
  — a project deliberately pointing at a checkout it is developing against. Then
  the entry needs a choice rather than a rule, which is the `--scope` argument
  the feedback proposes.

## Revoked on 2026-08-08

The second half of the first **Wrong if** fired before anything was built, out
of the clients' own documentation rather than out of a session. A relative
`args` entry resolves against the working directory the client spawns the
process in, and that is not a property this decision may assume: the MCP
specification defines the stdio transport as "the client launches the MCP server
as a subprocess" and says nothing whatever about a working directory.

Four of the eleven targets were read on 2026-08-08 and they give three different
answers. VS Code documents a `cwd` field that "defaults to the workspace folder
when run in a workspace", so a relative path is safe there and stated. opencode
has a `cwd` option whose relative paths "resolve from the workspace". Cursor
does not document the working directory at all but resolves `${workspaceFolder}`
in `command` and `args`. **Claude Code documents no working directory and warns
against the assumption outright** — it sets `CLAUDE_PROJECT_DIR` in the spawned
server's environment "so your server can resolve project-relative paths without
depending on the working directory", and `${CLAUDE_PROJECT_DIR}` in a
project-scoped `.mcp.json` needs a `:-.` default, which is the working directory
again.

That also explains why the DDEV branch this decision wanted to generalise is
correct: `ddev exec` runs in the container's project root, so DDEV supplies the
working directory the client does not. Taking the relative path out of that
branch removes the very thing that made it right.

What replaces this is `D-DIS-016`, which states the question as the per-client
one it is. What does not change is the finding underneath: a project that has
this server as a dependency and no DDEV gets a host path with `vendor/bin`
sitting there, and that is still wrong.
