---
date: 2026-07-30T00:01:24+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# Asked directly about route enhancer aspects and the cache hash, nothing in the answer is about th...

## Observation

Asked directly about route enhancer aspects and the cache hash, nothing in the answer is about the cache hash. The three hints that came back are site-sets, frontend-records and caches — the last one is about $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'], i.e. the cache framework, which shares only the word "cache" with the question. The frontend-records hint does say the shippable shape is "a Simple enhancer whose routePath carries the argument, with a PersistedAliasMapper aspect naming the table and the slug field", which is exactly right and was the useful part — but it stops before the question I had: whether an argument resolved through a persisted aspect still needs &cHash= in the generated URL, or whether mapping it into the path is what makes the hash unnecessary. That decides whether the URL a site ships is "/enquiry/PW-1001" or "/enquiry/PW-1001?cHash=20e55f98…", which is the whole point of writing an enhancer, and it is the question anyone reaches this topic with.

## Query

typo3_architecture_lookup task="Route enhancer aspects and the cache hash: when does a mapped route argument still need cHash in the URL", paths=["Configuration/Sets/Printworks/route-enhancers.yaml"]

## Suggestion

Add the cache-hash rule to the routing hints, alongside the Simple-enhancer/PersistedAliasMapper shape that is already there: which aspect types make an argument a routing argument rather than a cache-relevant one, so that no cHash is generated (the persisted and static mappers, whose values are validated against the database or an enumerated list), versus a free argument left in the query string, which keeps needing one. Worth naming the two sides of the consequence: an unmapped argument in the query string of a *cacheable* plugin is precisely why the hash exists — drop it and every value is answered from the first one's cache entry — while a non-cacheable action needs no hash at all, which is the reasoning behind leaving free-text search arguments unmapped. A pointer to the relevant CacheHashCalculator / PageArguments behaviour would let a reader verify it rather than take it on faith.
