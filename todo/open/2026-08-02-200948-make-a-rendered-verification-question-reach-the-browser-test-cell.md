# Make a rendered-verification question reach the browser-test cell

**Serves:** feedback/2026-08-01-003533-typo3-extension-testing-skill-was-never.md
**Priority:** low

Judged as
[`D-KNW-017`](../../decisions/knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies.md),
step 3 of the ladder: `browser-tests` is reachable only by words that name the
answer, so a question about whether something renders correctly stops at
`content-elements`. Measure the three candidate crossings against the four probe
queries that entry lists — terms on `browser-tests.appliesTo`, a statement in
`content-elements` that names the cell, and the `content-element` checklist in
`knowledge/task-intents.json` that currently sends rendered coverage to the
functional layer — write the one that reaches without pulling the hint into
neighbouring answers, and give `browser-tests` a scenario prompt, since
`bin/cli hints:coverage` reports that no prompt reaches it today.
