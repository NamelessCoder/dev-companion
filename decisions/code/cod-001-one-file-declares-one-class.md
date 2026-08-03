---
id: D-COD-001
date: 2026-08-01
status: open
---

# D-COD-001 — One file declares one class

**A PHP file below `src/` declares exactly one class, interface, trait or enum,
and it is named after it.**

PSR-4 finds a file through the name of the class being loaded. A second class in
the same file has no path of its own, so it resolves only where something has
already loaded the first one — the file that declared it works, and the first
caller from anywhere else gets a class-not-found for a class that is plainly
there.

## Evidence

- Written while adding a listing renderer on 2026-08-01. Two small classes went
  into one file because they were two shapes of the same idea, and the
  arrangement would have worked until the second was used from a second place.

## Decided

- One file, one class, held by `StructureTest::everyFileDeclaresOneClass` rather
  than by review. Two exceptions are named in the test and are not classes at
  all: `bootstrap.php`, which locates the autoloader, and `Runtime/probe.php`,
  which runs inside somebody else's installation.

## Assumed

- That a class too small to deserve its own file is a sign about the class
  rather than about the rule. The renderer that prompted this shrank to one
  method and stayed worth a name.

## Wrong if

- Something genuinely needs two declarations in one file — a backed enum used by
  exactly one class, say — and splitting it makes both harder to read. Then the
  rule needs an allowed list rather than a flat ban, and the test is where it
  goes.
