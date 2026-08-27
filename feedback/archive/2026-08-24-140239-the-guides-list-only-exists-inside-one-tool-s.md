---
date: 2026-08-24T14:02:39+00:00
category: tool-gap
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_rule_lookup
directory: /home/benji/projects/ext-usercentrics
---

# The guides list only exists inside one tool's answer, and the page that fitted my task went unread

## Observation

Task: verify an extension's rendering in a real browser and add functional tests, TYPO3 13.4 and 14.3.

Trimmed to what is left of this report: a page that was named to me and still went unread. `project/installation/booting-a-clone` reached me twice — in the `guides` field of the brief and in the `documents` field of the installation-setup hint — and I opened neither. I then spent the larger part of the session building that procedure myself: a DDEV container, a demo extension carrying a site set with page TypoScript and a Fluid template, a site configuration, and three full teardown-and-rebuild cycles to prove a fresh clone comes up.

Why it was not read: a page arrives as an id in a field near the end of a long answer, at a moment when the task has not started yet. By the time the task reaches the subject the page covers, the answer that listed it has scrolled out of working attention, and nothing in the later answers repeats it. The hint answers do carry a documents field — extension-test-frontend-request returned one — but it is one line among many and reads as a citation rather than as "read this before you build it".

The recognition half landed on 2026-08-24 (`D-GUI-014`): the `browser-check` needles reached none of the words I wrote the task in, so the brief named no browser page. It names `any/testing/browser-check` for that task now. That does not touch the half above, where the page was named and read past anyway.

## Query

typo3_project_describe (returned guides: any/testing/browser-check, extension/testing/phpunit, project/installation/booting-a-clone, core/testing/proving-a-rendering and 13 more). typo3_rule_lookup was never called in this session, with a query or a documentId. typo3_task_guide named project/installation/booting-a-clone in its guides field, and the installation-setup hint named it again.

## Suggestion

Have the skills name the document they expect to be read, as a step rather than as a pointer. typo3-development-installation already does this in one place — it names typo3_rule_lookup documentId="project/installation/booting-a-clone" as the thing to read "before the declared steps are run, not after one of them has failed". That is the placement that already existed for the page I skipped, so whatever closes this is something other than one more pointer.
