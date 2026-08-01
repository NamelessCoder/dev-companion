---
id: D-VER-3
date: 2026-07-30
status: tested
---

# D-VER-3 — The Fluid engine gets no version axis of its own, because the core pins it

**Fluid gets no version axis of its own: each covered branch pins the engine in
its `composer.json`, so `since`/`until` on the TYPO3 major already carries it.**

A note said Fluid has no empty array literal, and writing that down turned out to
need three engines rather than one: Fluid's major is not the TYPO3 major, and
nothing in `knowledge/` had said which is which. It never came up before because
no Fluid statement had been version-bound.

- **Decided:** no second axis. Each covered branch pins the engine in its own
  `composer.json` — 12.4 on `^2.15.0`, 13.4 on `^4.6.1`, 14.3 and main on
  `^5.3.1` — so the Fluid major is a function of the TYPO3 major, and `since` /
  `until` on the TYPO3 major already carries it. A caller asking with a
  `targetVersion` is asking about an engine, whether they know it or not.
- **Decided:** verifying such a statement means fetching the engine, not reading
  the checkouts. `.checkouts/` has no `vendor/`, and `typo3fluid/fluid` is a
  Composer dependency rather than part of the mono repository, so the parser the
  statement is about is not in the tree the rest of the knowledge is checked
  against. What was done here — one throwaway directory per major with the engine
  required into it, and the behaviour rendered through a probe ViewHelper — is
  the procedure, and it is worth the twenty minutes: the note's own diagnosis
  («`{}` is a string») is what a reading of the source would plausibly have
  produced, and the measurement says null.
- **Wrong if:** a branch loosens its constraint to span two engine majors, or a
  Fluid minor changes behaviour inside one — the strict argument processor
  arrived in 5 and is injectable, so 5.x turning lenient by default would make a
  `since: 14` statement wrong without any TYPO3 version moving. Either one and
  the engine needs its own field.
- **Tested on 2026-08-01:** the four constraints are still what this recorded —
  12.4 `^2.15.0`, 13.4 `^4.6.1`, 14.3 and main `^5.3.1` — and each pins one
  engine major, so the TYPO3 major still carries the engine. The first half of
  **Wrong if** is no longer a promise: `bin/cli catalog check` reads
  `typo3fluid/fluid` out of every covered checkout's `composer.json` and fails
  on a branch that admits two majors or none. The second half is not held by
  anything, and cannot be from here — a Fluid minor changing behaviour inside
  one major is visible only to the throwaway-directory procedure above, run
  again.
