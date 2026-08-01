---
date: 2026-07-29T17:05:44+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: a26cd1e
subject: "[TASK] Put the menu trap where a site question can see it"
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# RE-REPORT: excludeDoktypes replaces the menu default instead of extending it.

## Observation

RE-REPORT: excludeDoktypes replaces the menu default instead of extending it.

Reported in the previous round together with the fluid_styled_content layout collision and the sendCacheHeaders trap. Both of those are now in frontend-page-rendering, verbatim and better worded than I put it. This one is not, in that hint or in any other I could find, so I am recording it again rather than assuming it was dropped on purpose.

AbstractMenuContentObject defaults $excludedDoktypes to [DOKTYPE_BE_USER_SECTION, DOKTYPE_SYSFOLDER] — 6 and 254. As soon as conf.excludeDoktypes is set, that default is thrown away and only the configured list applies. So a perfectly reasonable-looking

    dataProcessing.10 = menu
    dataProcessing.10.excludeDoktypes = 6,199,255

silently puts every sysfolder into the main navigation, because 254 is no longer in the list. The sitepackage in this project had exactly that line, and it only became visible when a storage folder for records was added below the root page — until then there was no sysfolder to leak. Correct is 6,199,254,255.

It fits the same shape as the two that were incorporated: a configuration that looks additive but replaces, no error, a wrong page.</observation>
<parameter name="suggestion">One sentence in frontend-page-rendering next to the data processor line: excludeDoktypes on a menu replaces AbstractMenuContentObject's default of [6, 254] rather than extending it, so a list that does not repeat 254 puts sysfolders into the menu.

## Query

typo3_architecture_lookup id=frontend-page-rendering, targetVersion=14.3 — re-report, this one did not survive the last round
