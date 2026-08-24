---
date: 2026-08-24T14:02:39+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_rule_lookup
directory: /home/benji/projects/ext-usercentrics
---

# The guides list only exists inside one tool's answer, and the page that fitted my task went unread

## Observation

Task: verify an extension's rendering in a real browser and add functional tests, TYPO3 13.4 and 14.3.

My client (Claude Code) showed me no MCP resource list at any point. The only way I learned this server carries whole procedures was that typo3_project_describe's answer ended with 17 documentIds. I read that list once and read none of the documents.

One of them is any/testing/browser-check, "Looking at a Change in a Real Browser". The user's request that turn was, verbatim: "wir brauchen die tests und auch die frontend verifikation". I then spent the larger part of the session building that myself: a DDEV container, a demo extension carrying a site set with page TypoScript and a Fluid template, a site configuration, and three full teardown-and-rebuild cycles to prove a fresh clone comes up. If that page carries the procedure, it would have shortened the session more than anything else available.

Others I passed over that were named to me and would have applied:
- extension/testing/phpunit, "Setting Up PHPUnit in a TYPO3 Extension" — I assembled Build/FunctionalTests.xml from the testing-framework's own template plus hints instead.
- project/installation/booting-a-clone — named twice, by typo3_task_guide's guides field and inside the installation-setup hint's documents field.
- core/testing/proving-a-rendering — attached to the extension-test-frontend-request hint answer.
- extension/compatibility/running-a-package-on-a-declared-major-that-is-not-installed — directly relevant while I was proving v13 behaviour with v14 installed.

Why none was read: they arrive as ids in a field near the end of a long answer, at a moment when the task has not started yet. By the time the task reaches the subject a page covers, the answer that listed it has scrolled out of working attention, and nothing in the later answers repeats it. The hint answers do carry a documents field — extension-test-frontend-request returned one — but it is one line among many and reads as a citation rather than as "read this before you build it".

## Query

typo3_project_describe (returned guides: any/testing/browser-check, extension/testing/phpunit, project/installation/booting-a-clone, core/testing/proving-a-rendering and 13 more). typo3_rule_lookup was never called in this session, with a query or a documentId. Task text at the moment browser-check would have applied: "wir brauchen die tests und auch die frontend verifikation".

## Suggestion

Have the skills name the document they expect to be read, as a step rather than as a pointer. typo3-development-installation already does this well in one place — it names typo3_rule_lookup documentId="project/installation/booting-a-clone" as the thing to read "before the declared steps are run, not after one of them has failed" — and that is the phrasing that would have worked for browser-check too, in typo3-extension-testing or wherever a browser verification is routed. Alternatively let typo3_task_guide return the guides that match the task it was just handed, not the repository: my call had changeType=operations and paths including Tests/Functional, and it returned only project/installation/booting-a-clone, not any/testing/browser-check.
