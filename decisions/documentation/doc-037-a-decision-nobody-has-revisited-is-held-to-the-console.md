---
id: D-DOC-037
title: A decision nobody has revisited is held to the console
date: 2026-08-18
status: open
coveredBy:
  - DecisionsTest::anUnvisitedDecisionNamesNoCommandTheConsoleLost
---

# D-DOC-037 — A decision nobody has revisited is held to the console

**The head of a decision that carries no dated section may name no `bin/cli`
command the console does not have.**

Three entries were presenting a deleted command as the way to do the thing, and
the only thing that had ever checked a command name was a todo's `**Run:**`
line.

## Evidence

- `Cli::knows()` exists and is called from one place, `TodoCheck`, for the
  `**Run:**` line of a todo. A command named in a decision, in `documentation/`
  or in `AGENTS.md` was held by nothing: `LinksTest` holds paths and
  `ToolNamingTest` holds MCP tool names, and a CLI name falls between them.
- Swept on 2026-08-18, six command names written in the corpus were not
  registered. Three sat in revoked entries, which is the record working. Three
  sat at the head of a live one: `bin/cli feedback:next` in `D-FBK-012`, deleted
  by `D-FBK-016` on 2026-08-02; `bin/cli todo:sync` in `D-FBK-016`, deleted by
  `D-FBK-045` on 2026-08-14; `bin/cli documentation:build` in `D-DOC-017`, which
  `D-DOC-020` folded away and `D-DOC-028` replaced with
  `documentation:prepare`.
- Two of the three had been revisited and the revisit did not reach the head.
  `D-FBK-016` says in its **Since then** that the sync went with `D-FBK-045`;
  `D-DOC-017` said the three commands became `documentation:render`, which was
  itself deleted four days later. A dated section is appended at the foot, and
  the head is what a reader reads first.

## Decided

- The head only — the statement and the paragraphs above the first section.
  Below it an entry is an account of what was decided and what was rejected, and
  the entry that removes a command has to name it there: `D-FBK-045` says
  `bin/cli todo:sync` is deleted, which is that sentence doing its job. Held on
  the whole file it would fail on the entries that are working.
- Only where no dated section stands. One of those is somebody having been back
  and written what changed, which is the mechanism this repository already has.
  It is also the looser half of this entry, and the reason is below.
- No statement is rewritten. A decision is what was settled on its date, and
  `D-FBK-012` and `D-DOC-017` were given a **Since then** carrying the reading
  to today instead — the shape `D-FBK-017` already used.
- A test rather than a command. The answer is binary and belongs where a rename
  that misses a reference fails the suite, which is what `bin/cli links:check`
  and `LinksTest` do for a path. Rejected: a `cli:check` beside them, which is a
  subject for one assertion.

## Assumed

- That a dated section means the head was read. It does not: two of the three
  found here carry one. What it means is that somebody was in the file, which is
  the closest thing to a signal there is, and holding the head under a dated
  section too would demand that statements be rewritten.
- That the corpus outside `decisions/` can go unheld. `documentation/` and
  `AGENTS.md` have no head-and-evidence shape to separate what is prescribed
  from what is recounted, and `documentation/records/readme.rst` names
  `bin/cli todo:sync` deliberately, saying what it did until 2026-08-14.

## Wrong if

- An entry is given a dated section for some other reason and its head then goes
  stale unchecked. That is the first assumption failing, and what would show it
  is a sweep finding a head that names a lost command under a **Confirmed on**
  written about something else.
- A page under `documentation/` sends a reader to a command that does not exist.
  Nothing here would catch it, and the reader is somebody following a procedure
  rather than reading a record.
