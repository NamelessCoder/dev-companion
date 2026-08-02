---
id: D-KNW-001
date: 2026-07-29
status: revoked
---

# D-KNW-001 — Sitepackage work is answered from the General category

**Sitepackage work is answered from the always-selected General category,
because the task text that asks for it names none of the technical domains the
categories mirror.**

The hint categories mirror the technical domains: PHP, TypoScript, Fluid,
Backend CSS, Backend TypeScript, General. Building a section of a website
crosses all of them at once — a TCA file, a data processor in TypoScript, a
YAML route enhancer, a Fluid template — and the task text that asks for it
names none of them, so a domain-scoped hint is unreachable exactly when it is
needed.

## Decided

- `sitepackage-layout` and `frontend-records` go into `general.json`, whose
  category is always selected. A new category file would never match:
  `Domains::hintCategories()` returns a fixed list, and a category outside it
  is loaded and then filtered away.
- "sitepackage" and "content element" also became domain keywords for Fluid and
  TypoScript, and frontend markers besides — so such a task pulls the page
  rendering and site set hints too, and the backend's own CSS and TypeScript
  conventions are withheld from it.

## Assumed

- The noise this adds is smaller than the miss it removes. Two entries in the
  always-on category is the cost, paid by every query.

## Wrong if

- A backend-only task comes back with the sitepackage layout because it
  mentioned a content element, or the General category keeps growing until it
  is what every answer is made of. The fix in that case is a category for the
  audience rather than for the domain, which is a larger change than this one
  earns today.

## Revoked on 2026-08-02

Both halves fired. Five backend-only task texts naming a content element went
through `typo3_task_guide`, and two came back with `sitepackage-layout`: «Add a
TCA field to the content element in the backend» and «The backend preview of
the content element is broken in the page module». `bin/cli hints:probe` says
how it got there — `text only(150)`, not one `appliesTo` pattern matched. The
hint runs to 670 words against a corpus mean of 268, and it describes the
package from the backend that administers it, so a backend query reads well
against its body. On the size, General holds 18 of 65 hints and supplied 35 of
54 matched over the scenario prompts, with 16 of 29 answers made of it alone.
`bin/cli hints:coverage` prints that from here on, and fails on none of it,
because the number to watch is the growth.

## Revoked on 2026-08-02

The fix named above is not the one that answered it. An audience category would
have left the hit standing. The second **Decided** bullet made "content
element" a keyword of Fluid and TypoScript, so both are candidate categories
for such a task however the corpus is filed, and moving `sitepackage-layout`
out of General only moves it into one of them. What answered it was the
exclusion already in `ArchitectureHints::find()`, reached until now by the
words "backend module" alone — though nothing about it was ever about modules.
It reads `Domains::namesOnlyTheBackend()` now: backend markers present, no
frontend marker beside them. That is not `namesTheFrontend()` negated, where
the backend markers win, because a task naming both halves is asking for both —
`SITE-05` is one, and it keeps the layout. `SITE-08` holds the shape that
failed.

## Since then

The second **Decided** bullet stood uncorrected while the first was answered:
such a brief still opened with `Domains: fluid, typoscript`, so the hint
exclusion was what kept that work out of the answer rather than the scoping.
`D-KNW-006` closes it on 2026-08-02, and by the gate this entry's second
revocation wrote rather than by deleting the keyword — which was measured and
costs `SKILL-04` both domains and a rendering question its one Fluid hint.
