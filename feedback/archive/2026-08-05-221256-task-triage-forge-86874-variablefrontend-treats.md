---
date: 2026-08-05T22:12:56+00:00
category: idea
status: closed
closed: 2026-08-06
model: claude-opus-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms
---

# This is a report on something that worked and must not be broken.

## Observation

This is a report on something that worked and must not be broken.

Task: triage Forge #86874, "VariableFrontend treats all Backends like TaggableBackendInterface-Backends", filed against TYPO3 8 in 2018, against a 15.0.0-dev checkout. The reporter's symptom was that getByTag() throws when the frontend sits on a SimpleFileBackend.

A grep over typo3/sysext/ for getByTag returned nothing, which establishes absence and no more. The triage skill's checklist is explicit that this is not enough: "fixed by accident is as final as fixed on purpose, but only where the mechanism is named", and "it works for me now" is not a mechanism. So an empty grep was about to leave me with a weaker verdict than the issue deserved.

One call closed it. typo3_changelog_lookup with the bare identifier getByTag returned both Breaking-81460 and Deprecation-81460, "Deprecate getByTag() on cache frontends", 9.0, tagged PHP-API and FullyScanned. That is the mechanism: deprecated in 9.0 and removed since, which turns "I cannot find it" into "it was taken away, here is the entry". The answer also carried matchedIn "name" and the full list of versions the installed core covers, so I could see what the search had actually ranged over rather than inferring it.

The behaviour worth naming is the one the tool's description promises and delivered: an identifier found in code is a query in its own right, and it reaches the entries that write it whether or not the change was titled after it. In a stale-issue triage that is the highest-value single call available, because the whole question is "what happened to this since it was filed", and a removed API is the most common answer. I used the same tool on other candidates and it correctly returned nothing where the area had genuinely not been touched — which the description also warns about, and which I carried into the write-ups rather than reading emptiness as "unchanged".

## Query

typo3_changelog_lookup with query "getByTag" — a bare PHP method name taken from an eight-year-old issue report, with no version or type filter.

## Suggestion

Nothing to fix. What to protect: identifier-as-query reaching entry text and not only entry titles — that is what made a bare method name from a 2018 report land on the right 9.0 entry. Keep matchedIn in the answer, since it says whether the hit came from the file name or from the body and therefore how much weight it carries. Keep the versions list, which shows the search's range rather than leaving it assumed. And keep the removalRule note explaining that an empty removal field is what the entry states rather than a promise — I did not need it here, but it is the kind of line that stops a wrong inference somewhere else.
