---
date: 2026-08-10T10:16:27+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# nothing answers whether a specific CSS feature is inside the backend browser baseline of a version

## Observation

Task: review and then rework a core patch that adds a sticky language header to the page module language comparison view (backend TypeScript, Fluid partial, Sass), targeting main and 14.3.

I asked css-browser-target before choosing a technique. It answers as policy: "targets evergreen browsers as they exist on the day of the corresponding TYPO3 LTS release", "may be adopted before the LTS release when they are expected to be part of the evergreen browser baseline", and for a backport "the CSS must fully match the evergreen browser baseline of that target release year". All correct, and none of it answers the only question I had: is CSS anchor positioning usable here.

Two things then pushed me the wrong way. The developer stated "14 ist hier lowest target welcher bereits anchor supported". And grepping the checkout for precedent found the core already shipping it in three places: Build/Sources/Sass/component/_dropdown.scss:14 and :122, Build/Sources/Sass/element/typo3-backend-workspace-selector.scss:51/:139/:140 (including anchor-size(width)), Build/Sources/Sass/element/typo3-formengine-element-datetime.scss:7/:21. Existing core usage reads as a settled decision. I built the component on anchor() and anchor-size(), verified it in the headless Chrome of the unitJavascript suite and in a real backend driven by Chromium, and shipped it.

The developer then tested in Firefox and it did not work. Measured in Firefox via Playwright: CSS.supports reports false for "anchor-name: --x", "position-anchor: --x", "left: anchor(--x left)" and "width: anchor-size(--x width)". left fell back to auto and width to fit-content. I removed the whole native path and went back to two scripted style writes.

Cost: one wrong architecture decision carried through several rounds of rework and found by the developer rather than by me. The secondary finding is that the three core call sites above are presumably affected the same way in Firefox, so the precedent a reader takes from the checkout is actively misleading.

## Query

typo3_hint_lookup id=css-browser-target; then grep of Build/Sources/Sass for anchor-name/position-anchor as precedent

## Suggestion

Carry the concrete baseline per major, not only the policy: for a given TYPO3 version, which browser versions the backend targets, and ideally a short list of the modern CSS features that are inside and outside it (anchor positioning, @starting-style, transition-behavior: allow-discrete, popover, :has, container queries, light-dark()). A query like "is CSS anchor positioning supported by the v14 backend target" should come back with yes/no plus the engine that decides it. Failing that, css-browser-target should at least warn that existing core usage of a modern feature is not evidence of baseline support, and name the engine that usually decides it (Firefox at the time of writing).
