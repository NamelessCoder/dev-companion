---
date: 2026-07-29T16:11:47+00:00
category: idea
status: closed
closed: 2026-07-29
commit: 54bfa60
subject: "[TASK] Say what sendCacheHeaders does to a development installation"
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# AVOIDABLE MISTAKE, AND A BEST PRACTICE THAT IS MISSING: config.sendCacheHeaders = 1 in a developm...

## Observation

AVOIDABLE MISTAKE, AND A BEST PRACTICE THAT IS MISSING: config.sendCacheHeaders = 1 in a development installation.

The sitepackage had sendCacheHeaders = 1 unconditionally. TYPO3 then answers a cacheable page with "cache-control: max-age=31535807" and an Expires header one year out. During development that means a page that rendered wrong once stays wrong in the browser until someone hard-reloads — server-side cache:flush changes nothing, because the browser never asks again.

That is exactly what happened here. After I fixed a Fluid bug and verified all six detail URLs with curl, the user reported "die detailseiten funktionieren noch nicht". Nothing was broken; their browser was serving a year-old-by-header copy of the pre-fix response. Diagnosing it meant looking at response headers, which is not where anyone looks first when a template change appears not to take effect.

The fix belongs in the sitepackage, not in a habit:

    [applicationContext == "Development"]
        config.sendCacheHeaders = 0
    [END]

The general shape of the rule: any TypoScript that hands caching control to a client should be conditional on the application context. sendCacheHeaders is the one that bites; there may be others worth naming in the same breath.

## Query

no lookup covers this — surfaced while debugging "die detailseiten funktionieren noch nicht" after the template was already fixed

## Suggestion

Add it wherever frontend page rendering conventions live: sendCacheHeaders sets a far-future max-age, so it should be switched off in the Development context, and "the change is not showing up in the browser but curl shows it correctly" should be named as the symptom. It is a cheap hint that saves a debugging session that looks like a code bug and is not one.
