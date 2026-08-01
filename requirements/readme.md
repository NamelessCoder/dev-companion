# What this server has to do

A feedback is a question, and it leaves the backlog once it is answered.
The requirement the feedback established is not a question and does not go away
with it: it has to keep holding while everything around it changes. This
directory is where it survives the feedback.

Every entry states one thing that must be true, names where the demand came
from, and says what holds it to that — a test where there is one, `not guarded`
where there is none. An entry marked **open** is a requirement that has been
accepted and not yet met; that is the backlog, and it is deliberately the same
list, because a requirement nobody has implemented yet and one that could
silently regress are the same kind of thing.

Rules for keeping it usable: an entry is added when a feedback is worked off,
not when it arrives — a feedback nobody has judged yet is a feedback, not a
requirement. An entry is never deleted because it was implemented; it is
deleted only when the requirement itself is withdrawn, and then the reason goes
in [decisions/](../decisions/readme.md). Assumptions and evidence live there
too; these files hold only what must be true.

## Where an entry lives

One requirement is one file, named after its id, in the group its id names. The
group is not a filing preference: it is what the requirement is about, and the
prefix carries it, so a file's id decides its path and two entries cannot
quietly share a number.

| Group                                     | What it is about                                    |
| ----------------------------------------- | --------------------------------------------------- |
| [audience/](audience/readme.md)           | Who the answer has to be right for                  |
| [discovery/](discovery/readme.md)         | Which installation is read, and how                 |
| [answers/](answers/readme.md)             | What a caller may conclude from one                 |
| [documentation/](documentation/readme.md) | What the live manuals answer                        |
| [task-skills/](task-skills/readme.md)     | What an installed workflow owes the task            |
| [project/](project/readme.md)             | The repository the caller is standing in            |
| [scope/](scope/readme.md)                 | Core conventions where they apply, and nowhere else |
| [guides/](guides/readme.md)               | What a returned draft is worth                      |
| [feedback/](feedback/readme.md)           | What the backlog has to stay usable for             |
| [knowledge/](knowledge/readme.md)         | What the knowledge base has to cover                |
| [code/](code/readme.md)                   | What must hold of the source itself                 |

Each group's `readme.md` says what that group is about, and the listing at the
foot of it is generated from the files below it by `bin/cli requirements index` —
a listing kept by hand is a second copy of the directory that only says what
was true once. `bin/cli requirements check` holds the files to the shape described
below, and `composer test` runs the same check through `RequirementsTest`.

That check cannot fail on an entry being **open** or `not guarded` — both are
legitimate, and the second is the only honest answer for a requirement no test
can hold. `bin/cli backlog list` reads them out instead, together with whether
an item in [todo.md](../todo.md) names the id. Nothing here reaches the order of
the work on its own; that listing is the whole of the coupling.

An id is never reused: a withdrawn requirement takes its number with it, so a
number that appears in an old commit, feedback or scenario still means the one
thing it always meant.

## What an entry looks like

```markdown
---
id: R-DIS-9
status: held
---

# R-DIS-9 — A negative is never remembered

**Nothing that says "there is no installation" is remembered.**

A successful resolution is memoized for the process; a failure is retried on
every call, because the caller who reads that answer is the one likely to
install, migrate or start something and ask again in the same session.

**From:** a session lost to a cached negative — the agent ran `composer
install`, started DDEV, verified `bin/typo3` answered, and every tool kept
reporting no installation until the client was restarted (2026-07-29).

**Held by:** `InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound`
```

- The **bold first sentence** is the requirement. Everything under it is why it
  is one, and a reader who stops after the bold line has read the whole demand.
- **From** is the session, review or feedback the demand came out of, with its date.
  It is evidence, not decoration: it is what tells the next person whether the
  requirement still describes a real failure.
- **Held by** names the tests that hold it, or says in as many words that
  something is not guarded. A test named there has to exist — a requirement
  claiming a test that was renamed away is a claim nobody answers for.
- `status` is `held` or `open`. `open` means accepted and not met yet, and it is
  the only mark a backlog needs.
