---
id: D-KNW-1
date: 2026-07-29
status: standing
---

# D-KNW-1 — Sitepackage work is answered from the General category

**Sitepackage work is answered from the always-selected General category,
because the task text that asks for it names none of the technical domains the
categories mirror.**

The hint categories mirror the technical domains: PHP, TypoScript, Fluid,
Backend CSS, Backend TypeScript, General. Building a section of a website
crosses all of them at once — a TCA file, a data processor in TypoScript, a
YAML route enhancer, a Fluid template — and the task text that asks for it
names none of them, so a domain-scoped hint is unreachable exactly when it is
needed.

- **Decided:** `sitepackage-layout` and `frontend-records` go into
  `general.json`, whose category is always selected. A new category file would
  never match: `Domains::hintCategories()` returns a fixed list, and a category
  outside it is loaded and then filtered away.
- **Decided:** "sitepackage" and "content element" also became domain keywords
  for Fluid and TypoScript, and frontend markers besides — so such a task pulls
  the page rendering and site set hints too, and the backend's own CSS and
  TypeScript conventions are withheld from it.
- **Assumed:** the noise this adds is smaller than the miss it removes. Two
  entries in the always-on category is the cost, paid by every query.
- **Wrong if:** a backend-only task comes back with the sitepackage layout
  because it mentioned a content element, or the General category keeps growing
  until it is what every answer is made of. The fix in that case is a category
  for the audience rather than for the domain, which is a larger change than
  this one earns today.
