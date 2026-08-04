---
date: 2026-08-04T17:59:16+00:00
category: tool-gap
status: closed
closed: 2026-08-04
model: claude-opus-5
tool: typo3_component_lookup, typo3_hint_lookup, typo3-extension-testing
directory: /home/benji/projects/site-new
---

# Task: add Playwright coverage proving the backend previews of five custom content elements render...

## Observation

Task: add Playwright coverage proving the backend previews of five custom content elements render in the page module — the audit had found that no test in the repository reached the page module at all.

Everything I needed about the page module's DOM I established by writing a throwaway probe spec against the running backend and dumping attributes and innerText, then deleting it. Three extra Playwright runs. What I had to discover:
- A backend module renders inside an iframe, id="typo3-contentIframe", name="list_frame". This is the one that matters: without it every locator silently finds nothing, and my first four specs failed for that reason alone.
- A content element tile is .t3-page-ce.t3js-page-ce-sortable, carrying id="element-tt_content-<uid>", data-table and data-uid.
- The preview body is .t3-page-ce-body, and the header the page module draws itself is inside it — which is what made "does the header appear once or twice" expressible as an assertion.
- The module menu, used to prove a login succeeded, is addressable as role=navigation with accessible name "Module Menu". My first guess, the custom element <typo3-backend-module-menu>, does not exist in 14.3.

I never called typo3_component_lookup. I read its description — "backend UI components ... markup, classes, custom properties" — and assumed a page-module grid was not a curated component. I do not know whether that assumption held, and that is the finding: the tool may have had half of this and I did not ask.

The content-element-preview hint does say a preview is asserted in a browser test rather than a functional one, which correctly routed me to this layer. It stops exactly where the selectors begin.

## Query

Never asked. I loaded typo3_component_lookup's schema and passed it over; typo3_hint_lookup id=browser-tests and id=content-element-preview were read but neither names a page-module selector.

## Suggestion

Add the page-module structural selectors somewhere a browser-test task will find them — either inside typo3_component_lookup, or as a hint (browser-tests already exists and would be the natural home). The single highest-value sentence is "a backend module renders inside #typo3-contentIframe, so every locator in a backend spec goes through a frame"; a session that does not know it writes specs that fail without saying why. Tile and preview-body selectors and the module-menu accessible name are the rest.
