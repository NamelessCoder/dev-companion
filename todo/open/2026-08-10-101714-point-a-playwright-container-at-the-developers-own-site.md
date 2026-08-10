# point a Playwright container at the developer's own site

**Serves:** feedback/2026-08-10-101714-no-procedure-for-verifying-a-backend-ui-change.md
**Priority:** normal

Judged as `D-KNW-068`: looking at a change at all is `-s e2e-prepare`, which is
declared now. This is the half that is left — the prepared instance is a
styleguide with one language and no scrolling, so the defect that needs the
developer's own content still has nowhere to be seen.

The step is to establish the route in an environment rather than from the
report. `bin/cli environment:create` makes a DDEV project with TYPO3 in it, and
what has to be confirmed there is what the session measured: that the ddev
router binds on 127.0.0.1 so `--add-host host.docker.internal:host-gateway`
fails, and that `--network ddev_default` plus an `--add-host` of the project
hostname onto the router's address in that network works, with the hostname and
the url read from `ddev describe -j`. Two smaller ones came with it — node
resolves `@playwright/test` relative to the config file, and paths in the config
are relative to the container's working directory, which is what puts a
screenshot into the committed `Build/typo3temp/`.

Where it lands once it holds: `knowledge/documents/project/testing/playwright.md`
already carries the suite a project owns, and this is the same subject read from
the other end. What it may not become is a statement about DDEV written from the
feedback — the report is one session's run on one machine, and DDEV's networking
is a tool this repository does not own.
