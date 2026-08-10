---
date: 2026-08-10T10:17:14+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_project_describe, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# no procedure for verifying a backend UI change in a real browser against the developer's own inst...

## Observation

Task: review and then rework a core patch that positions an element below the module doc header in the page module language comparison view.

The defect was positional and only existed inside the real backend ancestor chain. I had no procedure for looking at it, so I verified in synthetic DOM I had built myself inside the unitJavascript suite — which by construction cannot contain the bug. I then shipped three blind positional corrections in a row, each reasoned from the stylesheet, each handed to the developer to check. Only when they asked "hast du einen visuellen test gemacht?" did I stop; the honest answer was no.

What no lookup carried, and what I worked out and would work out again next session:
- runTests.sh -s e2e builds a disposable instance whose styleguide page has one language and does not scroll, so it cannot show the case at all. It also passes no extra arguments through to Playwright (COMMAND is fixed at "playwright:run -- ${PLAYWRIGHT_PROJECT}"), so a single spec cannot be selected without editing the committed Build/playwright.config.ts. PLAYWRIGHT_USE_EXISTING_INSTANCE=1 exists and turns a rerun into 8 seconds; I found it by reading the script.
- The developer's own DDEV instance had exactly the data needed. Reaching it from a container is the part with no documentation anywhere: the ddev router binds only on 127.0.0.1, so --add-host host.docker.internal:host-gateway gets ERR_CONNECTION_REFUSED. What works is joining the ddev_default network and mapping the project hostname onto the router: docker inspect -f '{{ (index .NetworkSettings.Networks "ddev_default").IPAddress }}' ddev-router, then --network ddev_default --add-host <primary_url host>:<that ip>, with the hostname and url read from ddev describe -j.
- node resolves @playwright/test relative to the config file, so a config outside Build/ needs a node_modules symlink beside it.
- Screenshot and storageState paths are relative to the container's working directory (Build), so a path written as ./typo3temp/... silently lands in Build/typo3temp/, which is not gitignored.

The harness I ended up with lives under typo3temp/var/review/ (gitignored, so it cannot reach a patch): its own playwright.config.ts with an auth project and a review project, a login setup taking credentials from env vars, a spec that scrolls and dumps geometry, and a run.sh doing the container/network wiring. Parameterised by REVIEW_BROWSER, it is what finally found the Firefox failure.

Two things I passed over that might have helped and I never opened: the hint id browser-tests ("Browser Tests with Playwright"), which typo3_task_guide listed under omittedHints on my first call and which I skipped because my task was "review a patch", not "write browser tests"; and the guide project/testing/playwright ("Setting Up Playwright in a TYPO3 Project"), which typo3_project_describe listed and whose title made me assume it was about project repositories rather than about driving a running instance. I never checked whether that assumption held.

## Query

whole session: review and rework a core patch that positions a sticky element below the doc header in the page module; symptoms reported as "es sitzt beim scrollen unter dem docheader", "und bewegt sich", "es läuft auch zu breit"

## Suggestion

Carry a procedure for "look at this backend change in a real browser", reachable from words a reviewer actually uses — visual check, screenshot, reproduce in the backend, does it render correctly. It should say: when the disposable e2e instance is enough and when it is not, that runTests.sh passes no arguments through to Playwright and PLAYWRIGHT_USE_EXISTING_INSTANCE=1 makes reruns cheap, and how to point a Playwright container at the installation typo3_project_describe already reports — for DDEV concretely the ddev_default network plus the router-ip add-host, because the obvious host-gateway route fails. Naming a gitignored location for such a harness would also help; the instinct is to put it in Build/, which is committed.
