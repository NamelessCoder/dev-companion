---
date: 2026-07-29T16:34:58+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# FIVE THINGS THAT BROKE WHILE BUILDING AN EXTBASE PLUGIN WITH SEARCH AND PAGINATION. Every one pro...

## Observation

FIVE THINGS THAT BROKE WHILE BUILDING AN EXTBASE PLUGIN WITH SEARCH AND PAGINATION. Every one produced a wrong page or an error page, none was findable through this server, and four of them are on the shortest path anyone takes.

1. A GET search form on a cacheable action is a 404. The cHash check lives in a middleware and runs long before Extbase knows an action is non-cacheable, so putting listAction into configurePlugin's fourth argument does NOT by itself make the search work. The plugin's arguments have to be excluded from the cache hash as well:
   $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^tx_myext_myplugin[demand]';
   The '^' prefix means startsWith — that is documented only in a docblock in CacheHashConfiguration, and without it every field needs its own entry. Fluid's own <f:form> hidden fields (__referrer, __trustedProperties) need excluding too, which nothing warns about.

2. An object argument maps nothing until it is allowed. A demand DTO as a controller argument fails with "It is not allowed to map property \"search\". You need to use $propertyMappingConfiguration->allowProperties(...)" — correct and secure by default, but the fix (initializeListAction with allowProperties) is knowledge you either have or spend twenty minutes acquiring.

3. A DTO handed to f:link.action is silently dropped. The URI builder can serialise a persisted entity by uid; a demand object has no uid, so it vanishes without warning and page two of a search becomes page two of everything. Links need the demand flattened to an array.

4. Extbase route enhancer, two ordering rules, both silent when wrong:
   - The list needs its own route with no page variable. Declaring only '/seite-{page}' with defaults.page = 1 makes every generated list link come out as "/produkte/seite" — segment present, number absent. The search form action pointed there and the whole form was broken.
   - '/seite-{page}' has to be declared before '/{product_slug}', or the slug mapper claims the paged URL.

5. The paginator clamps out-of-range page numbers. /seite-99 answers 200 with the contents of page one, so a catalogue serves the same list under an unbounded number of URLs unless the controller compares currentPage against getNumberOfPages() and returns ErrorController::pageNotFoundAction().

Two more that cost time without breaking anything: Extbase orderings resolve through property names, so ordering by the backend sort order needs a $sorting property on the model that is not a domain concept; and MySQL fulltext has both a minimum token length (3) and a stopword list that contains German words ("und"), so a search that looks broken may be behaving exactly as designed.

## Query

follow-up to the "no Extbase hint" note — building the plugin surfaced five failure modes, each found by breaking the site rather than by any lookup

## Suggestion

These belong in the Extbase hint asked for in the previous note, ideally as a "what breaks" section rather than prose — each entry a symptom and its cause, because that is how they are met: 404 on first search, "not allowed to map property", filter lost on page change, "/plugin/seite" without a number, 200 on a page that does not exist. Add the CacheHashConfiguration prefix indicators ('^' startsWith, '~' contains) wherever cache hash comes up; they are documented in a docblock and nowhere else.
