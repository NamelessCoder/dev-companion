---
date: 2026-08-10T10:17:14+00:00
category: tool-gap
status: closed
closed: 2026-08-10
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_project_describe, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# no way to point a browser at the developer's own installation

## Observation

Task: review and then rework a core patch that positions an element below the
module doc header in the page module language comparison view.

The defect was positional and only existed inside the real backend ancestor
chain. Looking at it at all is now `-s e2e-prepare`, which this server lists.
What that instance cannot show is this bug: its styleguide page has one language
and does not scroll. The developer's own DDEV instance had exactly the data
needed.

Reaching it from a container is the part with no documentation anywhere: the
ddev router binds only on 127.0.0.1, so `--add-host
host.docker.internal:host-gateway` gets ERR_CONNECTION_REFUSED. What works is
joining the ddev_default network and mapping the project hostname onto the
router: `docker inspect -f '{{ (index .NetworkSettings.Networks "ddev_default").IPAddress }}' ddev-router`,
then `--network ddev_default --add-host <primary_url host>:<that ip>`, with the
hostname and url read from `ddev describe -j`.

Two more that cost time: node resolves `@playwright/test` relative to the config
file, so a config outside `Build/` needs a `node_modules` symlink beside it; and
screenshot and storageState paths are relative to the container's working
directory (`Build`), so a path written as `./typo3temp/...` silently lands in
`Build/typo3temp/`, which is not gitignored.

The harness I ended up with lives under `typo3temp/var/review/` (gitignored, so
it cannot reach a patch): its own playwright.config.ts with an auth project and
a review project, a login setup taking credentials from env vars, a spec that
scrolls and dumps geometry, and a run.sh doing the container/network wiring.
Parameterised by REVIEW_BROWSER, it is what finally found the Firefox failure.

## Query

whole session: review and rework a core patch that positions a sticky element
below the doc header in the page module; symptoms reported as "es sitzt beim
scrollen unter dem docheader", "und bewegt sich", "es läuft auch zu breit"

## Suggestion

Say how to point a Playwright container at the installation
`typo3_project_describe` already reports — for DDEV concretely the ddev_default
network plus the router-ip add-host, because the obvious host-gateway route
fails. Naming a gitignored location for such a harness would also help; the
instinct is to put it in `Build/`, which is committed.
