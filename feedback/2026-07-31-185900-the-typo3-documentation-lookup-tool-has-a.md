---
date: 2026-07-31T18:59:00+00:00
category: bug
status: open
model: unknown
tool: typo3_documentation_lookup
directory: /home/benji/projects/site-new
---

# The typo3_documentation_lookup tool has a schema discrepancy: the inputSchema marks only targetVe...

## Observation

Trimmed on 2026-08-02 to the part that is left. The search call is not
impossible and was not on the day this was written: re-run against the server as
it is now, `queries` with `targetVersion` and no `page` answers with six results
from docs.typo3.org. The schema reading is right — only `targetVersion` is
required — and `page` is genuinely optional. `D-ANS-012` has the readings.

What is left is the message that said otherwise. `queries` and `page` are
alternatives, declared in a root `oneOf` that the tool reference does not render
and the validator reports one branch at a time: a call carrying `targetVersion`
alone is refused with "Missing required properties: queries." and "Missing
required properties: page." in one line, and the half a caller acts on is the
last one. Passing `page: ""` after that is refused for its length, which is
correct — a call that carries `queries` needs no `page` at all.

## Query

Called typo3_documentation_lookup with queries=['encryption key environment variable TYPO3_ENCRYPTION_KEY'], targetVersion='14.3', omitting page. Server returned: Invalid parameters: Missing required properties: page. When page was added as '', server returned: Minimum string length is 1, found 0.

## Suggestion

Say that the tool takes queries or page, never both, where a caller composing
the call reads it — the argument descriptions and the tool reference — or refuse
the call in one sentence that names the rule instead of two that each name a
different property.
