# Take Boost's marker fence for the copy that goes stale

**Serves:** R-DIS-025
**Priority:** normal

`R-DIS-025` holds that a publication which went stale says so before the first
call, and the digest is what makes that readable. What the digest cannot do is
fix it: the session is told the copy is behind, and somebody has to run the
installer. Laravel Boost — an MCP server that also writes guidance into the
user's project, which is this repository's shape in another ecosystem — solved
the second half, and the mechanism is worth reading before this repository
invents one.

Three parts. Boost wraps what it writes in a `<laravel-boost-guidelines>` marker
fence and rewrites only what is inside it, so the file it shares with the user
is **co-owned** rather than owned: the user's own lines survive an update and
Boost does not have to choose between clobbering them and never writing again.
It wires its update command into Composer's `post-update-cmd`, so the copy is
refreshed by the thing that already moved the package rather than by the user
remembering. And it keeps a tracked list of the names it wrote, which is what
lets it remove a skill it no longer publishes instead of leaving it behind.

The third is the one this repository most clearly lacks. `Installer::digest()`
covers what was published and reports drift; a skill that was published under a
name this package no longer carries stays in the project with nothing saying so,
and the record already holds the names, so the gap is in what is done with them
rather than in what is known.

The step is to establish which of the three this package can take.
`post-update-cmd` is the concrete one and the one to price first: this is a
Composer library with a `bin`, installed as a dev dependency, so the hook is
available and the question is only whether a publisher may write into the
project during an update without being asked — which is a decision, not a
reading, and `R-DIS-011` and `R-DIS-021` are what it has to agree with. Read
Boost's implementation of the fence and of the stale-name sweep before pricing
either, and note that the fence matters here only where this installer writes
into a file the user also owns; where it writes a whole directory the problem
does not arise and the mechanism buys nothing.
