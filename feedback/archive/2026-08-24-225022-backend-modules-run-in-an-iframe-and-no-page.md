---
date: 2026-08-24T22:50:22+00:00
category: missing-knowledge
status: closed
closed: 2026-08-26
model: claude-opus-5[1m]
tool: anytestingbrowser-check, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# Backend modules run in an iframe, and no page says so before a browser session wastes calls

## Observation

Task: the user asked "ich würde gerne screenshots von dem verhalten sehen" for a localization-wizard error state I had just changed. Files open: Build/Sources/TypeScript/backend/localization/localization-wizard.ts.

I read any/testing/browser-check whole and it carried the environment half well — which installation shows it, the ddev_default network route, and the rule to put the harness and its output under the checkout's own typo3temp/var/ because it is gitignored. I followed that and my Playwright harness never appeared in git status. Keep that page.

What it does not carry is driving the TYPO3 backend once the instance is up, and that is where the session bled calls:

1. The module content runs in an IFRAME. `page.locator('typo3-backend-localization-button').count()` returned 0 four times in a row while the button was plainly on screen. `page.locator('body').innerText()` returned only sidebar and toolbar text, which was the clue. Enumerating `page.frames()` showed the module frame with 4 buttons. That cost roughly five round trips of wrong hypotheses (wrong page id, wrong module route, SET[language] URL param, wrong selector) before I looked at frames at all.

2. Credentials are admin/password, from Build/tests/playwright/config.ts defaults. I found them by grepping the Playwright specs. Not stated anywhere I was routed to.

3. The published port is not the 43837 that AGENTS.md mentions for e2e-browser. `-s e2e-prepare` publishes a random high port; `docker port ac-web-<suffix>` gave 127.0.0.1:35082. I found it by inspecting the container.

4. Controls that live in a dropdown are not clickable until the dropdown is opened. Playwright timed out on a `typo3-backend-localization-button` that resolved but was never "visible, enabled and stable"; the fix was clicking the language selector `.dropdown-toggle` in the frame first. The error text names the element, so this is discoverable, but a line would have saved a round trip.

5. The e2e instance is a bare TYPO3 unless styleguide content is generated. `vendor/bin/typo3 styleguide:generate --create` in the instance directory; in my case both trees already existed.

None of this is Playwright knowledge — it is TYPO3-backend knowledge that a browser session needs and that nothing on this server states.

## Query

typo3_rule_lookup(documentId="any/testing/browser-check"); typo3_test_run_guide(paths=[...backend controller + test paths...], query="functional test for a backend controller")

## Suggestion

Add a section to any/testing/browser-check called something like "Driving the TYPO3 Backend", carrying:

- The module content is inside an iframe. A selector against the top document finds nothing. Enumerate page.frames() and take the frame whose URL carries the module route and its id parameter. This is the first thing to check when a count comes back 0 for an element that is visibly on screen.
- The e2e instance logs in as admin/password (Build/tests/playwright/config.ts defaults; ACCESSIBILITY_BACKEND_ADMIN_USERNAME/PASSWORD override).
- `-s e2e-prepare` publishes a random high port on 127.0.0.1, not a fixed one. Read it with `docker port ac-web-<suffix>`; the suffix is in the container name and in the network name typo3-core-<suffix>.
- Doc-header controls in dropdowns need the .dropdown-toggle clicked first; Playwright reports them as resolved but never stable.
- The instance carries no content until `vendor/bin/typo3 styleguide:generate --create` has run in the instance directory; the frontend demo tree is where multi-language content lives.

The iframe point is the one that pays for the section on its own.
