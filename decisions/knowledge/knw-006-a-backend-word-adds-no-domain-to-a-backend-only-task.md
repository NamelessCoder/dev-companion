---
id: D-KNW-006
title: 'A backend word adds no domain to a backend-only task'
date: 2026-08-02
status: confirmed
coveredBy:
  - HintsTest::aBackendTaskIsNotCalledFluidAndTypoScriptWork
---

# D-KNW-006 — A backend word adds no domain to a backend-only task

**"content element", "sitepackage" and "site package" stay keywords of Fluid and
TypoScript, and stop adding those two domains where the task names only the
backend.**

`D-KNW-001` made "content element" a keyword of both. Its hint half was answered
on 2026-08-02 by the exclusion in `ArchitectureHints::find()`; the domain half
was not, so a task about one TCA field still had its brief opened with
`Domains: fluid, typoscript` and was answered out of both categories.

## Evidence

- Deleting the keyword from `FLUID` and `TYPOSCRIPT` was measured over seven
  task texts. It answers `SITE-08` — its three backend-only prompts drop to
  `php`, and «Add a TCA field to the content element in the backend» gains
  `tca-formengine`, which the task actually named.
- It costs the other two. `SKILL-04`'s prompt names no half at all and loses
  both domains, and «The content element renders nothing on the page» stops
  reaching `frontend-page-rendering`, the one Fluid hint either of them gets.
  `SITE-05` is unaffected either way, because "site package" carries the two
  domains on its own.
- The gate that separates them exists: `namesOnlyTheBackend()`, written for the
  hint half of `D-KNW-001`. Under it the three backend-only prompts drop to
  `php` and `SITE-05`, `SKILL-04` and the rendering question are unchanged.

## Decided

- The suppression is a list of its own, `ADMINISTERED_FROM_THE_BACKEND`, and it
  reads `namesBackendModule() || namesOnlyTheBackend()`. The first is what
  "sitepackage" was already gated on and stays reachable: a backend module in a
  site package names the website half in its own owner, so backend-only is false
  for it.
- The keyword stays in both lists. The word tells the matcher which thing the
  task is about, and a task that names neither half is the case it was added
  for.

## Assumed

- A task that names the backend and never the website is asking about the
  backend. A backend preview template is the case where that is thin — it is
  Fluid — but no Fluid hint reached that query before this change either.

## Wrong if

- A task about building or rendering a content element comes back as `php`
  because it happened to say "backend form" as well, or the two domains stop
  being reachable for a content element at all outside a text that says
  "sitepackage" or "frontend".

## Confirmed on 2026-08-23

Neither half of the **Wrong if** holds, and the second is measured rather than
argued: four phrasings of a content element task all select fluid and
typoscript, so the two domains are reachable without a text that says
sitepackage or frontend.

The first half is covered from a direction that did not exist when this was
written: a backend preview selects `php` alone, as this entry decided, and the
first hint it returns is a Fluid one, because a curated phrase crosses the
domain gate under `D-ANS-084`.
