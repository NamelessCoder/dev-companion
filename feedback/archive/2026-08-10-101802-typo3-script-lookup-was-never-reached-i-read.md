---
date: 2026-08-10T10:18:02+00:00
category: idea
status: closed
closed: 2026-08-10
model: claude-opus-5[1m]
tool: typo3_script_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_script_lookup was never reached; I read runTests.sh by hand instead

## Observation

Task: review and rework a core patch, then build a browser-driven verification loop around runTests.sh.

typo3_script_lookup was in my tool list the whole session and I never called it, although both skills I activated name it. From the name and its one-line description I read it as "look up a script", which I mapped to "find the right runTests.sh suite" — and typo3_test_run_guide had already given me that, so it looked answered. The questions I actually had did not sound like script lookups to me: how -s e2e provisions its instance, whether extra arguments reach Playwright, which container image and network flags it uses, whether an existing instance can be reused.

So I read Build/Scripts/runTests.sh by hand: grep for runPlaywright and 40 lines after it, grep for unitJavascript, grep for IMAGE_PLAYWRIGHT and CONTAINER_COMMON_PARAMS, then a second pass to find PLAYWRIGHT_USE_EXISTING_INSTANCE. Roughly six shell round trips for facts a lookup could have answered in one, if I had thought to ask it.

I cannot say whether it would have answered, because I never made the call. That is the finding: the server sees no call here and would otherwise conclude the need did not arise. It did arise, five times, and the name did not reach it.

## Query

not called; the questions were "how does runTests.sh -s e2e provision its instance", "does it pass arguments through to playwright", "which container image and network parameters does it use"

## Suggestion

Widen what the description advertises so it matches the questions people bring: not only which script to run, but what a given suite does internally — what it provisions, which containers and images it starts, which environment variables change its behaviour (PLAYWRIGHT_USE_EXISTING_INSTANCE, CI, CHUNKS), and what it does and does not pass through to the underlying tool. If it already answers those, say so in the description; if it does not, that is the gap, since the alternative is every session grepping the same 1400-line script.
