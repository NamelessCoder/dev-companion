---
date: 2026-07-29T16:04:01+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 3a812c0
subject: "[TASK] Name the two traps that make a sitepackage render the wrong page"
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# TWO TRAPS WHERE A SITEPACKAGE MEETS fluid_styled_content AND THE MENU, both of which produce a wr...

## Observation

TWO TRAPS WHERE A SITEPACKAGE MEETS fluid_styled_content AND THE MENU, both of which produce a wrong page rather than an error:

1. Directory collision between PAGEVIEW and fluid_styled_content. PAGEVIEW turns one "paths" entry into Pages/ + Partials/ + Layouts/. fluid_styled_content ships its own Layouts/Default.fluid.html — the content element frame with the frame-type div and the header partials. A sitepackage that renders pages through PAGEVIEW almost always has a Layouts/Default.fluid.html of its own too — the page frame with header and footer. The moment styles.templates.layoutRootPath (or lib.contentElement.layoutRootPaths.N) is pointed at Resources/Private/, every content element starts rendering the page frame inside itself: header, navigation and footer repeated per element. I avoided it by keeping content element templates in a separate Resources/Private/ContentElements/ tree and never overriding the FSC layout root path.

2. Menu excludeDoktypes REPLACES the default list, it does not extend it. AbstractMenuContentObject defaults to [DOKTYPE_BE_USER_SECTION, DOKTYPE_SYSFOLDER]; as soon as conf.excludeDoktypes is set, that default is thrown away and only the configured list applies. The sitepackage here had "excludeDoktypes = 6,199,255" — perfectly reasonable-looking, and it silently puts every sysfolder into the main navigation. It only became visible when I added a storage folder for the product records. The correct value has to repeat 254.

## Query

typo3_architecture_lookup task="frontend rendering with PAGEVIEW, custom content elements and DataProcessors in a sitepackage" → hints "frontend-page-rendering" and "site-sets"

## Suggestion

Add to "frontend-page-rendering": that PAGEVIEW's Layouts/ and fluid_styled_content's Layouts/Default.fluid.html collide by name, and that content element templates therefore belong in their own directory tree. Add to the same hint or to a menu-related hint: excludeDoktypes replaces AbstractMenuContentObject's default [6, 254] rather than extending it, so 254 has to be repeated or sysfolders appear in the menu.
