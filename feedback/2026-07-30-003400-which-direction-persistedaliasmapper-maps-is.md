---
date: 2026-07-30T00:34:00+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# Which direction PersistedAliasMapper maps is not stated anywhere in the answers, and getting it w...

## Observation

Which direction PersistedAliasMapper maps is not stated anywhere in the answers, and getting it wrong is a redesign rather than a typo. The frontend-records hint recommends "a Simple enhancer whose routePath carries the argument, with a PersistedAliasMapper aspect naming the table and the slug field", which reads as though the argument value and the path segment are the same thing. They are not: the mapper maps the record *uid* to the value of routeFieldName and back, so the query argument the link has to pass is the uid, and the pretty segment is the field. I built the whole chain — a button, a query parameter, an event listener validating the parameter with a regex, unit tests over that regex — around passing the field value itself, and only reading the class showed that a Simple enhancer over that parameter can never drop the cache hash, because an unmapped free-text argument stays cache-relevant. The redesign was: pass the uid, let the mapper produce the segment, and read the displayed value from the record instead of trusting the request. Two consequences are worth having stated up front, because they are the reasons to do it this way: the URL loses its &cHash= entirely, and a segment that matches no record is a 404 before anything renders, which replaces input validation in application code with the mapper's own database lookup.

## Query

typo3_architecture_lookup task="Route enhancer aspects and the cache hash", paths=["Configuration/Sets/Printworks/route-enhancers.yaml"] — follow-up to the cHash note: what PersistedAliasMapper actually maps

## Suggestion

Where the Simple-enhancer/PersistedAliasMapper shape is recommended, say what the aspect maps: uid in the argument, routeFieldName value in the path. A two-line example showing both sides — the link building `?arg={uid}` and the resulting `/path/PW-1001` — would prevent the whole wrong-way-round design. Add that the field has to be unique per site for the mapping to be reversible, and that the record lookup doubles as validation, so an enhanced route needs no pattern check on the argument in application code. This pairs with the cache-hash rule already reported: the reason the hash disappears is exactly that the argument is resolved against the database.
