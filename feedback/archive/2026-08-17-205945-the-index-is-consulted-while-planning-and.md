---
date: 2026-08-17T20:59:45+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5
tool: typo3_hint_lookup, typo3_rule_lookup, typo3_project_describe
directory: /home/benji/projects/site-demo
---

# the index is consulted while planning and abandoned the moment a symptom appears, so four listed ...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. This is about my own behaviour across the whole session, which the server cannot observe: it sees the calls I made and not the ones I had reason to make.

Every typo3_hint_lookup answer reprints availableHints — around ninety ids with titles — so the index was in my context continuously, dozens of times over. I still did not use it once debugging started. Four cases, all of them answers that existed and stayed shut:

1. datahandler-placement, "Which Page May Hold a New Record, and Where It Lands on It". My content elements came out in reverse order. I read DataHandler.php:728-760 to learn that a new record lands at the top of its page and that a negative pid accepts a NEW placeholder resolved earlier in the same run. The hint title says exactly that. Five calls.
2. fluid-layouts-sections, "What a Fluid Layout Renders, and What Is Never Executed". My accordion stylesheet was never emitted. I found it by diffing the stylesheets on two rendered pages, then reasoned out that f:asset.css above the first f:section is parsed and never executed when a layout is declared. Three calls.
3. preview-record-variable, "What a Fluid Preview Template Is Handed". I wrote six backend previews and took what I needed from content-element-preview plus theme_camino's shipped templates.
4. any/testing/browser-check and project/testing/playwright, both listed in typo3_project_describe's guides array. I never called typo3_rule_lookup at any point in the session. At the end I wanted to verify the backend previews, tried a scripted backend login, found the page module loads its body asynchronously, and shipped the previews as unverified rather than opening the guide written for that exact problem. The user asked me afterwards why I had not browser-checked them, and the honest answer was that I stopped rather than that it was impossible.

The pattern is consistent and worth more than any single instance. I consulted the index while planning — when typo3_task_guide named ids and I fetched them — and abandoned it the moment I had a symptom. With a stack trace or a wrong database row in front of me I reached for the installed source and the exception text, which are concrete and local, and did not ask whether a hint titled after my symptom already existed. Three of the four cases had a title that would have matched what I was staring at.

Cases 1 and 2 were each preceded by a hint_lookup answer whose availableHints array listed the id I needed, in the same context window.

## Query

A long build session on TYPO3 14.3.6: 19 typo3_hint_lookup calls all by id, zero typo3_rule_lookup calls, and the guides array from typo3_project_describe left unopened. Reproduce by watching which lookups a session makes after the first exception rather than before the first file.

## Suggestion

Treat the debugging moment as a routing problem, not a reading problem. Two things would have caught it. First, in the workflows that own build work, say plainly that a symptom is a lookup trigger and not only a task is — the content-element and installation skills both route lookups by subject at planning time and neither says what to do when something breaks. Second, typo3_project_describe's guides array is the only place the procedures are named to a client that renders no resource list, and it is returned at the very start of a session, hundreds of calls before the one guide that matters becomes relevant; naming the applicable guide again at the point of need — browser-check from the content-element workflow where it already says an editor's view is asserted in a browser — would have put it in front of me when I could act on it. Worth knowing generally: a session can end without ever calling typo3_rule_lookup, and mine did.
