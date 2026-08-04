---
date: 2026-08-04T18:01:54+00:00
category: idea
status: closed
closed: 2026-08-04
model: claude-opus-5
tool: typo3-extension-testing, typo3_hint_lookup
directory: /home/benji/projects/site-new
---

# Task: establish a check layer, then — after the maintainer asked whether better alternatives exis...

## Observation

Task: establish a check layer, then — after the maintainer asked whether better alternatives existed — re-evaluate the frontend linters.

static-quality.md names eslint and stylelint, and I installed both because the page names them. The maintainer pushed back ("is eslint the best choice?", "are there not more better alternatives"), so I measured on this repository's own files. The results split cleanly and neither half was what the page implies:

- CSS: against the stylesheets as they stood before any fix, stylelint reported 58 findings and Biome reported 0. Every one of the 58 was a modernisation rule — colour function notation, alpha value notation, media feature range notation, a duplicate selector. Biome's CSS rules are correctness rules and would have passed all of them silently. Stylelint is the right answer and the page is right.
- JavaScript/TypeScript: eslint with its recommended preset found 0 across one script and eleven specs. Biome found 10, oxlint found 0. One of Biome's ten was a genuine gap rather than taste — a spec reading .y off boundingBox() with no null guard while the two tests either side of it guarded, which would have died with a bare TypeError. So on this package eslint was carrying nothing.
- Cost: eslint plus @eslint/js plus typescript-eslint was ~78 npm packages of the 208 installed; Biome is 2, and the lint runs in 8ms.

I am not proposing that the page prescribe Biome. The measurement is on one small package and would likely come out differently on a repository with real frontend source. What the page currently lacks is any instruction to measure at all — it reads as a list of defaults to install, and I installed them without asking what they would find here.

Also worth noting for anyone repeating this: Biome's suggested fix for the non-null assertions was to rewrite `filter!.y` as `filter?.y`, which it marks unsafe and which turns a crash into a silent comparison against undefined. The right fix was a helper that asserts and narrows.

## Query

typo3-extension-testing references/static-quality.md, "Shipped frontend assets": eslint with @typescript-eslint for scripts, stylelint with stylelint-scss/stylelint-order for stylesheets — applied to a sitepackage shipping 1 JS file, 3 CSS files and 11 Playwright TypeScript files

## Suggestion

Add one line to "Shipped frontend assets" telling a session to check what the named tool actually finds on the package in hand before declaring the entry covered — a linter reporting nothing on the only file it guards is a gap dressed as coverage, which is the same standard the page already applies to a version matrix whose cells run only version-independent steps. Optionally name Biome and oxlint as current alternatives for the JavaScript half, with the caveat that Biome's CSS coverage is not a substitute for stylelint's.
