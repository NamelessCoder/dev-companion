---
date: 2026-07-29T17:16:25+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: a49a464
subject: "[FEATURE] Cover the test kind that needs a browser"
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# EVIDENCE FOR THE PLAYWRIGHT GAP: the first axe run failed, and it was right.

## Observation

EVIDENCE FOR THE PLAYWRIGHT GAP: the first axe run failed, and it was right.

I built the end-to-end suite that the previous note said is missing from the knowledge base — Playwright against the served DDEV site, browsers on the host, twenty-three specs across catalogue, teaser, navigation, accessibility and one narrow-viewport project. Twenty-three pass now. Four failed on the first run, all of them accessibility, all the same rule, and all of them real:

- The brand orange #ff6b2c as a button background with white text: 2.84:1. Required is 4.5:1. That is every button on the site, including the skip link.
- The darker brand orange #d9501a as text on white: 4.11:1, and on the grey surface 3.79:1. That is every eyebrow, every "Details ansehen", the price on the detail page, and the default link colour.
- After fixing those, one left: the disabled "Zurück" of the pagination at 1.27:1, because it used the border grey as a text colour.

Two of those three colours were in the sitepackage before I touched it; the third I introduced by using them for text. Neither the functional rendering tests nor anything else in this session would ever have seen them — the markup is correct, the HTML assertions pass, and the defect only exists once a browser has applied the stylesheet.

The fix is worth recording too, because it is the shape the answer usually takes: split the palette rather than darken the brand. --pw-accent stays #ff6b2c for the dark surfaces, borders and marks where it is decorative; --pw-accent-text is the same hue darkened to #cd3d00, which clears 4.5:1 as text on white (4.93), as text on the grey surface (4.55) and as a background for white text (4.93). One variable, one rule: anything a reader has to read uses the text one.

This is the argument for the hint the previous note asks for. A theme extension without an axe pass ships contrast failures and nobody notices, because every other kind of test says the page is fine.

## Query

follow-up to the Playwright note — the suite is now built and run, and the first run produced a concrete result worth attaching to that gap

## Suggestion

When the Playwright hint gets written, lead with accessibility rather than with clicking: @axe-core/playwright over the site's own page types is the check that finds something on day one, and it is the one thing no PHP test can substitute for. Worth naming the two traps that produce it — a brand colour used both decoratively and as text, and a disabled control styled with a border colour — together with the palette split that fixes the first.
