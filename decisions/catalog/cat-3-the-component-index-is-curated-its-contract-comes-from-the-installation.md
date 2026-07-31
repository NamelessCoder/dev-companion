---
id: D-CAT-3
date: 2026-07-30
status: standing
---

# D-CAT-3 — The component index is curated; its contract comes from the installation

**Where the caller targets the active installation and its backend CSS is
present, the installed files decide the component contract, and the curated
index stays the searchable subset and the complete fallback.**

The component catalog was deliberately pinned to one core revision and guarded
by `bin/verify-catalog`. In the first `EXT-04` run that safeguard was not enough:
the installation was TYPO3 14.3.5, the snapshot described main, and the session
made about twenty-five shell reads into the installed core before it trusted the
markup and classes it had already been given.

- **Evidence:** Composer installations ship
  `EXT:backend/Resources/Public/Css/backend.css`; they do not ship the
  repository-level `Build/Sources/Sass/` tree. The compiled artifact is also the
  contract the browser actually receives and includes classes generated from
  Sass maps that a source-text search misses.
- **Decided:** when `targetVersion` is the active installation and its
  backend CSS is present, installed package files win. The compiled CSS decides
  component presence, classes, and custom properties; installed JavaScript
  decides custom-element presence; and the first matching installed styleguide
  example replaces bundled markup. Every answer names the files and exact TYPO3
  version it read.
- **Decided:** derivation does not replace curation. Names, summaries,
  keywords, the component boundary, and the association with a styleguide page
  are judgments rather than an inventory a stylesheet contains. The bundled
  catalog remains that searchable subset and remains the complete fallback when
  no installation is readable, its package evidence is incomplete, or the
  caller targets another major.
- **Assumed:** an `sg:example` containing the root class or custom element is a
  copyable example of that component. On a dedicated component page whose
  examples render through a ViewHelper instead of spelling the class, its first
  example is the installed usage contract. Where the installed template has no
  example at all, the answer keeps the bundled markup and labels it as fallback
  instead of pretending it was derived.
- **Wrong if:** component state is generated only at runtime and appears in
  neither the compiled CSS nor installed JavaScript, or a styleguide template's
  first matching example is page scaffolding rather than component markup. The
  former needs another installed source; the latter needs an explicit selector
  in the curated index rather than a more permissive extractor.
