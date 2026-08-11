---
date: 2026-08-10T18:24:17+00:00
category: idea
status: closed
closed: 2026-08-11
model: claude-opus-5
tool: typo3_project_describe, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# the browser-check guide stayed unread through a session that deferred browser verification five t...

## Observation

Task: review and then rework TYPO3 core Gerrit change 95163, a backend CSS/sticky-positioning patch, on a core checkout at 15.0.0-dev.

typo3_project_describe returned a guides list that included {"id":"any/testing/browser-check","title":"Looking at a Change in a Real Browser"}. I never opened it. Across the session I told the user five separate times, in different words, that I could not judge the change visually — "optisch beurteilt ist das nicht", "im Browser gesehen habe ich es nicht", and in the final report "Im Browser gesehen ist nichts davon". Each time I improvised the same substitute answer: `runTests.sh -s e2e-prepare`, which I had from typo3_test_run_guide, plus a remark that the user could send a screenshot.

This was the single most repeated gap in the session, and a guide named exactly for it was sitting in an answer I had already received, in the first tool call I made.

Why I did not open it: my client (Claude Code) rendered no MCP resource list at any point, so the only place these ids appeared was inside the JSON body of typo3_project_describe, in a `guides` key after `commands`. I read that answer for the version, the checkout kind and the declared commands — the things the tool's description foregrounds — and treated the trailing list as inventory rather than as something to act on. Nothing later in the session referred back to it. The patch-development skill does name typo3://guides/core/contribution/commit-messages and .../gerrit-workflow inline, which is why I was aware those two existed; no skill mentions browser-check, so it existed only in that one JSON key.

I also never read core/contribution/commit-messages whole, despite two separate typo3_rule_lookup searches both landing in it (see the separate feedback on that).

## Query

typo3_project_describe with no arguments, on a TYPO3 core checkout. The answer's `guides` key carried any/testing/browser-check among ten ids; no subsequent call in the session referenced it.

## Suggestion

Where an answer establishes that a change is visual — typo3_test_run_guide returning the css domain, typo3_hint_lookup returning Backend CSS hints — name any/testing/browser-check in that answer, the way the patch-development skill names the commit-message and gerrit-workflow guides inline. A guide that is only reachable from a key in the first tool call is invisible to any client that renders no resource list, and this session shows the cost is not that the guide goes unread but that the agent reinvents a worse version of it, repeatedly, in front of the user.
