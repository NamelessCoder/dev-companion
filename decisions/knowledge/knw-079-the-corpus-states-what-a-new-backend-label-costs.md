---
id: D-KNW-079
date: 2026-08-14
status: open
---

# D-KNW-079 — The corpus states what a new backend label costs

**Both halves of the repair are stated: the `l10n` cache keys on nothing about
the file, and the module's URL does not move when a label changes.**

`javascript-labels` carries it in `labels.json`, beside the hints about
authoring, referencing and retiring a label, because importing one into a module
is the fourth thing done to a label rather than a second subject.

## Evidence

- The reading is
  [`D-KNW-076`](knw-076-what-a-new-backend-label-costs-before-it-resolves-is-a-gap-this-server-owns.md)'s
  **Confirmed on**, taken on `.checkouts/14.3` and `main` on the same day.
  Neither of its two **Wrong if** fired, so the report's two steps are both real
  and the statement is a procedure rather than a correction.
- The feedback's own query reaches the new hint first —
  `bin/cli hints:probe "JavaScript labels module cache flush after adding XLF trans-unit"`
  on 2026-08-14, at `appliesTo(30)` above the three hints the report was given.
- The failure phrasings reach it too, probed the same day:
  `Label is not defined at runtime after adding a new label` and
  `my new label does not show up in the backend JavaScript module` both lead
  with it.
- The neighbours keep their questions. `authoring a new XLF label file for my
  extension` still returns `language-files` alone, and
  `clear the caches after a TCA change` still leads with `page-cache-flushing`.

## Decided

- One hint rather than a sentence in `page-cache-flushing`. That hint is which
  cache holds a rendered page's old output, asked from `fluid`, `typoscript` and
  `php`; reaching this caller would mean tagging a long hint with two more
  domains, and the answer here is a browser cache as much as a server one.
- `labels.json` rather than `backend-ui.json`. The subject is what happens to a
  label, and the hint a caller needs next — `translation-domain`, for the name
  the module is imported under — is in the same file.
- `xliff` first of the two domains, so the answer files it under Labels. The
  third domain also keeps it out of the frontend withholding rule, which drops a
  hint whose domains are the two backend UI ones (`D-KNW-033`).
- Every statement bound `since: 14`, so the hint does not exist on 12 or 13.
  There is no hint-level `since`, and a hint the matcher offers on an LTS that
  has no `~labels/` at all would describe a mechanism the caller cannot reach.
- The green build is one statement and `scope: "core"`. `grunt scripts` and
  `Build/types/labels/` are the core's own build; an extension author gets the
  runtime throw with no stub in front of it, which is why the throw is stated in
  the first statement instead.
- Two pointers, and they are the two hints the reporting session was given.
  `language-files` says a unit reaches a module later than it reaches PHP;
  `backend-typescript` says the bundle is not one of the generated files it is
  about, which is the wrong conclusion that hint makes available.
- `appliesTo` claims the artefacts and the failure phrasings and not "use
  labels". That phrase is what somebody asking how to use a label in a Lit
  component types, and it also matches the same question about a Fluid template,
  where this hint answers nothing.

## Assumed

- That a caller arrives with the failure rather than with the feature. The
  statements are ordered for somebody holding a throw, and the import is stated
  as where the module comes from rather than as how to write one.
- That the hard reload is the whole of the browser half. Nothing here drove a
  browser: what was read is the `max-age` and the identifier the URL carries, and
  a reload that a service worker or a proxy answers instead was not tested.
- That the `cacheBustInfix` is stated at the right altitude. It is named as the
  version, the project path and the package list, which is what
  `PackageDependentCacheIdentifier` composes, rather than as which of the two
  package caches supplies it.

## Wrong if

- A covered branch starts feeding the label files into the identifier, on either
  side. The `l10n` entry would then clear itself, or the URL would move, and the
  statement would name a step nobody owes.
- The two steps turn out to be one in practice, because the flush already
  happened for another reason and the reload is all that is left. The hint would
  then read as twice the work it is.
- A session reads it and flushes `all` rather than `system`. The group is stated
  to be precise, and `page-cache-flushing` is where the groups are explained;
  the lever would be the neighbour rather than the group name.
- The same question is reported again from an extension. Nothing bound to the
  core here except the build, and an extension's own type stubs would make that
  statement wrong rather than core-only.

## Covered by

- `HintsTest::aNewBackendLabelIsToldWhatItCostsBeforeItResolves`
- `HintsTest::theLabelModuleIsWithheldFromTheMajorsThatHaveNone`
