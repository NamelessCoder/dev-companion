---
date: 2026-08-13T21:48:38+00:00
category: idea
status: closed
closed: 2026-08-13
model: claude-opus-5[1m]
tool: typo3_label_lookup, typo3_rule_lookup, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# what carried this review: the label lookup settled a Playwright selector in one call and the gerr...

## Observation

Task: review Gerrit change 93319, a Playwright e2e diff, after cherry-picking it onto main. This is the half that worked, recorded so it does not get refactored away.

typo3_label_lookup was the single highest-value call in the session. The patch adds a locator `getByRole('button', { name: 'Page', exact: true })` against the page tree toolbar. The core source renders that button as `${backendPagesNewLabels.get('newPage')}`, so whether the spec matches anything depends entirely on what that label's source text is — and "newPage" reads like it would be "New page", which would have made the assertion wrong. One call with query "newPage" and extension "backend" returned six candidates across two resources and the right one was unambiguous: backend.pages_new:newPage = "Page", from EXT:backend/Resources/Private/Language/locallang_pages_new.xlf, distinct from backend.layout:newPage = "Create new page". Two labels, both named newPage, with different text, in the same extension — exactly the case where guessing loses. Returning the resource path per hit is what made it decidable. That verification cost one round trip and would otherwise have cost booting the backend.

typo3_rule_lookup with documentId "core/contribution/gerrit-workflow" carried the fetch end to end, and the paragraph that mattered is the one no general knowledge of Gerrit supplies: the change refs are on the review server, not on the GitHub mirror a core clone fetches from, and what to fetch from is remote.origin.pushurl. My checkout is exactly that asymmetric case — fetch git@github.com:TYPO3/typo3.git, push ssh://review.typo3.org:29418. I would have run `git fetch origin refs/changes/...`, got "couldn't find remote ref", and spent the next few minutes deciding whether the change existed. The skill was right to tell me to read that document whole by id rather than search it; the section I needed was not the one my words would have matched.

typo3_forge_lookup on #109339 was worth the call for one thing the commit message does not carry: the issue enumerates the three scenarios it wants covered — drag & drop, "Create new page" button, "Create another page" — which is what let me check coverage against the request rather than against the author's summary of it. Its noteCount/botNoteCount split (12 and 12) also told me at a glance that there was no human discussion to read, without reading twelve notes.

typo3_project_describe correctly reported a core checkout with no project-owned extensions, which is what let me skip typo3_extension_describe as the base order says rather than call it and get nothing.

One thing that did not cost me anything but is worth noting as design: typo3_task_guide with changeType "audit" returned the review checklist, the suites, and the checkout-discovery list in one answer, and its hints for Build/tests/playwright carried the fixtures/helper/e2e layout and the iframe rule. It also stated its own coverage ("omittedHints": []), which is what let me not repeat it as typo3_hint_lookup — the base order asks for that sentence to be read and the answer actually supplies it.

## Query

typo3_label_lookup(query: "newPage", extension: "backend"); typo3_rule_lookup(documentId: "core/contribution/gerrit-workflow"); typo3_forge_lookup(issue: "109339")

## Suggestion

Keep the resource path on every typo3_label_lookup hit — it is what separates two same-keyed labels and it is the whole reason the call was decisive here.

Keep the gerrit-workflow document readable whole by documentId, and keep the fetch-remote asymmetry stated in it rather than in a searchable fragment; it is the one fact in the workflow that a session cannot supply from general knowledge and cannot discover from a failed command.

Keep typo3_task_guide stating its own hint coverage explicitly. A machine-readable equivalent of that sentence would be better than the prose, but the prose already works.
