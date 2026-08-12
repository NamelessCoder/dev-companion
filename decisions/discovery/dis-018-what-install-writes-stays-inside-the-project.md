---
id: D-DIS-018
date: 2026-08-12
status: open
---

# D-DIS-018 — What `install` writes stays inside the project

**Every file `install` touches is inside the project it was pointed at, in the
standalone case as much as in the dependency case.**

A checkout elsewhere has no path but the absolute host one, and the client files
that receive it are the shared, committed ones. Saying so is the answer; writing
somewhere else is not.

## Evidence

- The floor is in place since 2026-08-09 and is right under every answer: the
  entry says its command is valid on this machine only and that the file is a
  candidate for the project's `.gitignore`, wherever it names this checkout.
  `D-DIS-016` is the per-client reading that settled the dependency half.
- One client of the eleven documents a private per-project scope at all — Claude
  Code's local scope in `~/.claude.json` under the project path, what
  `claude mcp add --scope local` writes. The other ten offer the shared file and
  nothing else.
- This is how this repository installs into `E-CORE` and both `E-EXT` checkouts,
  where the entry is correct on the machine it was written on.

## Decided

- The project directory is the boundary of what this command touches, and it
  stays a property of the command rather than of an argument.
- Rejected: writing into the client's private per-project configuration. It moves
  what `install` touches outside the project this server was pointed at, for one
  client of eleven, and everything else the command does stays inside.
- Rejected: a `--scope` argument. It changes the behaviour of a command people
  have already run, to offer a choice one client documents.

## Assumed

- That a machine-specific entry in a shared file is caught by the sentence
  `install` prints beside it. Nothing enforces the `.gitignore` it names.

## Wrong if

- A machine-specific entry is committed anyway and a teammate gets a client that
  cannot start the server, which is the harm the report opened with.
- Several clients document a private per-project scope, so "one of eleven" stops
  being the argument against writing there.
- Somebody wants both — a shared entry for the team and a private one for their
  own checkout — which is the choice the rejected argument would have carried.
