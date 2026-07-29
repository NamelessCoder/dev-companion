# What is being worked on

This file exists so a session can end anywhere. It holds the **order of the
work** and where the last one stopped — not what must be true (that is
[requirements.md](requirements.md)), not the questions real sessions asked (that
is `feedback/`), not the map of what the audiences need (that is `scenarios/`).
Those three outlive the work; this one is consumed by it.

Rules that keep it from becoming a fourth backlog:

- An item names what it serves — a requirement, a note, a scenario. An item that
  serves nothing is not a task, it is an idea, and ideas go in the note that had
  them.
- An item says what the **next concrete step** is, in enough detail that someone
  who has read nothing else can start. "Continue with the bindings" is not that;
  "bind the statements in `php.json` against `.checkouts/12.4` and `13.4`" is.
- A finished item is deleted, not ticked. What it established is already in
  `requirements.md`, and the commit is the record that it happened.
- The order is the order. When something jumps the queue, it moves up here
  first, so the reason is written down before the work starts.

---

## Every session starts here: read `feedback/` again

This item is never done and is never deleted. Notes arrive while work is
happening — a session somewhere records what it was missing, and the file lands
in `feedback/` without anyone being told. And notes that were open yesterday are
often half answered by what shipped since, without their text saying so.

So, before picking anything up:

1. `typo3_feedback_list` — or read `feedback/` — for what is there now.
2. For each note, **run its own query against the current server**. A note is
   evidence about a version of this server that may no longer exist.
3. Close what is answered, trim what is half answered down to the part that is
   still open, and let a new note that changes the order move the items below.

The notes are the only input that comes from outside this repository. Everything
else here was written by someone who already knew what they meant.

---

## Go through what is marked `binding: "core"` and say what the project side is

Serves the one note left open,
`feedback/2026-07-29-180528-project-work-needs-a-second-axis-the-repository.md`.
The count that item used to ask for has been made: differing answers already
existed in four shapes, and what was missing was the force of a statement, which
is now `binding: "core"` on 22 hints and 4 single statements.

Marking says "this is not yours to follow". It does not say what is, and that is
what a twin hint is for. The next concrete step is one pass over the marked
entries — `grep -l '"binding": "core"' knowledge/architecture-hints/*.json`, then
the four statement-level ones — and per subject one of two answers, written into
the note: marking is enough (the backend CSS rules are wanted unchanged by a
project that builds a backend module, and a twin would say the same thing
twice), or the project side is a real answer that is missing.
`documentation-changelog` is the clearest candidate for the second: a project
writes release notes too, and the hint says nothing about that. Where the answer
is the second, the twin is written the way `project-extension-tests` was — same
subject, its own hint, and each pointing at the other.

Below that, nothing is queued: everything else written down so far is in
`requirements.md`, so the work after it is whatever the notes a session finds
ask for — or, where there are none, a scenario from `scenarios/` still marked
`gap`.
