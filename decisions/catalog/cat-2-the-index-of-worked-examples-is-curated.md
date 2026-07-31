---
id: D-CAT-2
date: 2026-07-29
status: standing
---

# D-CAT-2 — The index of worked examples is curated, and existence is all that is checked

**The path of a worked example is machine-checked against every covered
checkout; the sentence about what it is a reference for is not.**

An index of directories inside the core is the kind of answer that rots
silently: a moved path leaves a caller reading a miss as "I looked in the wrong
place". `bin/verify-catalog` therefore re-derives each entry's range from
whether the path is there in every covered checkout, the way the component and
system-extension catalogs are derived.

- **Decided:** the path is machine-checked, the sentence about what it is a
  reference for is not. Existence is what a script can know; whether
  `theme_camino` still demonstrates the layout is a judgement, and a wrong
  judgement here costs one read, while a wrong path costs the caller their
  trust in the tool.
- **Assumed:** the list stays short enough to reread by hand when a major lands
  — seven entries, each a subject a session actually needed. The value is one
  line per directory, not coverage.
- **Wrong if:** an entry's path still exists while what is inside moved —
  `Build/tests/playwright/e2e` surviving a rewrite that puts the fixtures
  somewhere else. Then the check has to descend into the entry, and the honest
  form is naming the two or three files that carry the shape rather than the
  directory.
