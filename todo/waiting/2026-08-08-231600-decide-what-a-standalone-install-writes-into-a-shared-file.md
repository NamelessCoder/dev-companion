# Decide what a standalone install writes into a shared file

**Serves:** feedback/2026-08-08-184226-install-writes-a-machine-specific-absolute.md
**Priority:** normal
**Waiting on:** what `install` may write, and where, when this server is not a
    dependency of the project. One of the options moves what this command
    touches outside the project directory, which is not a reading — it is a
    decision about the posture. Judged 2026-08-08 as `D-DIS-015`.

Where the server is a dependency the entry is nameable relatively and that is a
defect being fixed —
`todo/open/2026-08-08-231500-name-the-installed-entrypoint-relatively-wherever-it-exists.md`.
This card is the rest: a checkout elsewhere, where the absolute host path is the
only path that exists, written into a file the client documents as the shared,
committed, team-level one. That is how this repository installs into `E-CORE`
and both `E-EXT` checkouts.

The reporting session priced three answers, and they are not exclusive:

1. **Say it.** A `REMAINING` line naming the file, that its command is valid on
   this machine only, and that it is therefore a candidate for the project's
   `.gitignore`. `REMAINING` is already where per-client "what is left" is said,
   so this needs no new mechanism and moves no boundary. It is the floor: it is
   right whatever else is decided.
2. **Write it where the client keeps private per-project configuration.** Claude
   Code is the only one of the eleven that documents such a scope — local scope
   in `~/.claude.json` under the project path, what
   `claude mcp add --scope local` writes — and it would leave the repository
   untouched altogether. **This is the one that needs the answer:** it writes
   outside the project this server was pointed at, and everything else `install`
   does stays inside it.
3. **A `--scope` argument**, explicit where a client offers a choice, with the
   default refusing to put an absolute entrypoint into a shared file without
   one. That changes the behaviour of a command people have already run.

What the report established and this card rests on: the eleven targets are each
documented by their own client as the shared location, quoted in the feedback
from readings of 2026-08-08. What it did not establish, and nobody here can, is
whether writing into a user's home directory is something this package is
willing to do.

Nothing is broken while this waits once the dependency case is fixed: what is
left is the setup this repository uses for its own environments, where the entry
is correct on the machine it was written on and the `.gitignore` block the
install already writes covers the other artefacts.
