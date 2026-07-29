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

## Decide what the project-shaped twin is: a mechanism or a rule for authors

Serves the one note left open,
`feedback/2026-07-29-180528-project-work-needs-a-second-axis-the-repository.md`,
trimmed to this half. `core-tests` and `project-extension-tests` are one subject
answered twice, and that pair exists because a note asked for it rather than
because anything makes it the rule.

The next concrete step is to count the evidence before building anything: go
through `knowledge/architecture-hints/` and list the hints whose text would read
differently for a project — the ones that name `Build/Scripts/runTests.sh`, a
path below `typo3/sysext/`, or a harness only the mono repository has. If that
list is two or three, the answer is a rule in `AGENTS.md` for whoever writes the
next hint. If it is a dozen, it is a field on the hint plus selection by the
`outsideCore` flag `Scope::isOutsideCore()` already computes, and the note's
suggestion holds. Write the count into the note or into `decisions.md` either
way — it is the evidence nobody can reconstruct later.

Below that, nothing is queued: everything else written down so far is in
`requirements.md`, so the work after it is whatever the notes a session finds
ask for — or, where there are none, a scenario from `scenarios/` still marked
`gap`.
