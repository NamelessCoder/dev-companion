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

## 1. What an extension registers, not only that it is installed

**Serves:** `feedback/2026-07-29-094054` · **Next step:** decide where it goes —
a `registers` section per extension in `typo3_project_scope`, or an extension of
its own with a key as its argument — then read it from the files the way the
sites are read: `Configuration/TCA/` and `Configuration/TCA/Overrides/`,
`Configuration/Services.yaml`, `Configuration/Icons.php`, the Fluid roots, the
content elements it adds. Report the Composer patches from `extra` while there.

The scope names an extension and its path today. A maintenance question is
usually about what is inside it, and that is readable without a console.

## 2. Let a target version decide in the catalogs, not only inform

**Serves:** `feedback/2026-07-29-094245`, R-AUD-4 · **Next step:** record per
catalog entry what it was verified against — the same `since`/`until` the hints
use — then accept `targetVersion` on `typo3_component_lookup` and
`typo3_catalog_scope` and leave out what does not hold there.

The hints and `typo3_task_guide` take a target version and filter by it. The
catalogs answer from one pinned revision and say so in a skew sentence, which
names the difference without acting on it: markup taken from one revision either
holds on the stated version or it does not, and the honest answer for "does not"
is to decline it and name what to verify against.
