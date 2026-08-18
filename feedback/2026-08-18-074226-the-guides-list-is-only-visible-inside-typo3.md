---
date: 2026-08-18T07:42:26+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_rule_lookup, typo3_task_guide
directory: /home/benji/projects/blog
---

# The guides list is only visible inside typo3_project_describe, so a session can finish without re...

## Observation

Task: repair a 404 on a DDEV TYPO3 14.3.6 installation of t3g/blog, then fix a v14 rendering crash and cover it with tests. Full session; my transcript does not begin at a summary.

My client (Claude Code) showed no MCP resource list at any point. The only place I learned the guides exist was the guides key at the end of typo3_project_describe, and I was reading that answer for the version, the sites and the declared commands. I never called typo3_rule_lookup with any documentId. So I finished a session that verified a rendering change in a real browser without reading any/testing/browser-check, and that added functional tests without reading extension/testing/phpunit.

Whether either would have changed what I did I cannot say, which is the point. The list arrived as eleven ids with one label each, inside a large answer about something else, at a moment when I had a 404 to diagnose and no reason to think a matching procedure existed. By the time the session turned into test work — three user turns later, after "zudem hast du keine unit / functional tests dafür angelegt" — that list was far behind me in the transcript and I did not go back for it.

This is a discoverability finding, not a complaint about the guides. The server cannot see it: from its side the session looks like a caller who was told the guides exist and chose not to open any.

## Query

typo3_project_describe (no arguments), whose answer ended with a guides array of 11 ids including any/testing/browser-check, extension/testing/phpunit and project/testing/playwright. No typo3_rule_lookup call was made in the whole session.

## Suggestion

Re-offer the relevant guide at the moment it applies rather than only once at orientation. typo3_task_guide already routes to a skill by name and could name a guide id the same way — changeType test naming extension/testing/phpunit, a task about proving a rendering naming any/testing/browser-check — so the pointer arrives with the work instead of before it.
