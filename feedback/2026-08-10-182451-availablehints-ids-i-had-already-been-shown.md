---
date: 2026-08-10T18:24:51+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# availableHints ids I had already been shown went unfetched while I read the repository for the sa...

## Observation

Task: review and then rework TYPO3 core Gerrit change 95163; the rework added a TypeScript module and, briefly, a JavaScript unit test.

typo3_hint_lookup answers carry an `availableHints` array of every id the query did not return. Two of those ids were exactly what I needed later, and I went to the filesystem for both instead of asking for them by id.

`javascript-unit-tests` ("JavaScript Unit Tests in the Core") appeared in the availableHints of my very first hint call. When I later needed to know where a JS unit test goes and how it is run, I instead: listed Build/tests/, grepped Build/package.json for a test script, catted Build/web-test-runner.config.mjs looking for a files pattern, failed to find it in a .js file that is actually .mjs, grepped again for `files`, then listed Build/Sources/TypeScript/*/tests to confirm the convention, then read popover-test.ts for the idiom. Six calls. One `typo3_hint_lookup id=javascript-unit-tests` would very likely have replaced most of them.

`css-tokens-specificity` ("CSS Tokens and Specificity") was in the same list. The middle of this session was almost entirely about CSS custom property design — whether a fade distance deserves its own token, whether it may borrow `--typo3-spacing`, whether to derive `--module-docheader-sticky-height` from `--module-docheader-scroll-offset` or the reverse. The user corrected me twice on exactly that (once for borrowing a foreign global token, once for a hardcoded value where a derivation existed). I never fetched the hint whose title names the subject.

I did use the id form successfully twice — `css-z-index-layering` and `css-browser-target` — so I knew it worked. The pattern is that I mined availableHints when I already suspected a specific subject, and never when I was mid-task solving something the list would have covered. The list is long, uniform, and arrives attached to an answer about something else.

## Query

typo3_hint_lookup paths=[Build/Sources/Sass/component/_module.scss, typo3/sysext/backend/Resources/Private/Partials/DocHeader.fluid.html, typo3/sysext/backend/Resources/Public/Css/backend.css, Build/Sources/TypeScript/viewpage/main.ts] task="backend docheader sticky CSS, Sass build artifacts, Fluid partial markup" targetVersion=15.0 — availableHints included javascript-unit-tests and css-tokens-specificity, neither fetched.

## Suggestion

Consider whether availableHints can be ranked or split rather than returned flat: the ids whose category matches a domain the query already established are a different kind of thing from the rest of the catalogue. In this session Backend CSS and Backend TypeScript were both established domains, and css-tokens-specificity and javascript-unit-tests were both withheld — surfacing "these matched your domains but not your words" would have been actionable where an alphabetical catalogue was not.
