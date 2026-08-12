# Get the drawings and the rail back out of the theme

**Serves:** documentation/
**Priority:** normal
**Waiting on:** `typo3/soul-guides-theme`, which is generated from the design
    system's own monorepo and cannot be changed from this checkout. Both items
    below are one change there and would then arrive with a `composer update`
    here.

`D-DOC-024` moved the site onto the packaged theme and named three readings of
`D-DOC-023` it could not carry over. Two of them are the theme's to answer.

## A drawing cannot be told which mode the page is in

A Markdown image is an inline node, and the theme renders `sds-figure` for the
reStructuredText `figure` directive alone. So every drawing on this site is a
plain `<img>` — a document of its own, which reaches none of the page's tokens
and none of its `@font-face` rules. A reader in dark reads a light drawing, and
the lightbox that read a 1200px drawing at the size it was drawn at is gone with
the script that opened it.

What the theme would need is an inline image that comes out as `sds-image`, and
one more thing behind it: `de()` in `soul.js` works a drawing's box out of a
table of three names — `answer-sources`, `installation-fallback` and
`system-overview` — which are this repository's own drawings and three of the
eleven it has. A drawing the table does not name is referenced into an `<svg>`
with no `viewBox`. Reading the box out of the file, or taking it as an
attribute, is what would make the other eight work.

What this repository owes once that lands: the eleven light drawings rewritten
into the artwork form — every colour a `var()` with a hex fallback, the whole
drawing under one `id` — after which the eleven `-dark.svg` twins,
`Site::publishDrawings()`, `Site::DARK` and
`SiteTest::everyDrawingShipsItsDarkTwinAndThePublishedOneCarriesIt` all go. The
map the swap needs is a straight one, measured on `answer-sources.svg` against
the design system's own converted copy of it:

| Here      | Token                            |
| --------- | -------------------------------- |
| `#1C1A17` | `var(--text-primary, #1C1A17)`   |
| `#4A453D` | `var(--text-secondary, #4A453D)` |
| `#8A8378` | `var(--text-muted, #726C63)`     |
| `#E3DFD6` | `var(--border-subtle, #E3DFD6)`  |
| `#C9C3B7` | `var(--border-strong, #C9C3B7)`  |
| `#FBFAF7` | `var(--surface-canvas, #FBFAF7)` |
| `#FFFFFF` | `var(--surface-raised, #FFFFFF)` |
| `#986200` | `var(--status-warn, #986200)`    |
| `#FF8700` | `var(--accent, #FF8700)`         |

The three signets in `documentation/images/` are already written that way and
are what a converted drawing is checked against.

## A rail item is cut off rather than wrapped

`sds-rail` truncates, and the tool pages are named `typo3_backend_module_lookup`
and `typo3_system_extension_lookup` — 27 and 29 characters of mono. `D-DOC-023`
named this as a departure the design system asks to be named rather than
forbidden, and the reason has not changed: a truncated identifier is not the
identifier. It reads `typo3_backend_module_looku` in the rail as it stands.

## What was checked while writing this

- External `<use href="file.svg#art">` resolves in Chrome 140, and a custom
  property declared on the referencing page reaches the referenced art. So the
  mechanism `sds-image` uses is sound and the box is the only thing missing.
- `automatic-menu` is what builds the rail and the trail out of the directories,
  which is the part of the toctree this Markdown corpus can have. That works and
  is not waiting on anything.
