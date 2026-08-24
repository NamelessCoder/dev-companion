# Resolve the demo path on every covered major

**Serves:** D-CAT-002
**Priority:** normal

Make `demoPath` answer on 13.4, where none of the 22 entries currently resolves.
The styleguide renamed its templates between the two majors — `Form.html` on
13.4 and `Form.fluid.html` on 14.3 — and every entry records the newer spelling,
so the path is silently 14-and-newer for the whole catalog.

## What it costs today

`bin/cli catalog:check` digests a demo only where the file is there, so a
rewrite on 13.4 moves under every entry without anything reporting it. Nothing
fails, and that is the problem: the check reads as covering four majors and
covers two.

An answer that names a demo path is also handing a caller a path that is not
there on their major, which is the shape of miss this catalog is otherwise
careful about.

## What has to be decided

- **Whether the path is derived rather than recorded.** The name is the same
  either way and only the extension moves, so a reader could try both. That is a
  rule in code against a second field in the data, and the second field is what
  `AGENTS.md` prefers for something read as data.
- Whether 12.4 gets one at all. The styleguide is not in the core there —
  `D-CAT-009` — so a path could only point into a package that may not be
  installed.

## Where it came from

Read on 2026-08-24 while the `form` and `select` entries were being written:
their digests came back for 14 and 15 only, and the same is true of the twenty
that were already there.
