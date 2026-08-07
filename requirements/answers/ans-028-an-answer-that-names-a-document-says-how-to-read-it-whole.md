---
id: R-ANS-028
status: open
restsOn: [D-ANS-061]
---

# R-ANS-028 — An answer that names a document says how to read it whole

**A lookup that returns a section cut from a document carries a way to read that
document whole, and that way does not depend on the client rendering MCP
resources.**

A `uri` is delivery to a client that lists resources. A client that lists none
leaves the caller a string it has no prior reason to read as an action, and the
rest of the document — which regularly holds the section the query was actually
looking for — is never reached.

## From

Three sessions in one core checkout on 2026-08-07. `feedback/2026-08-07-132535`
held `typo3://guides/core/contribution/commit-messages` from two
`typo3_rule_lookup` calls and never fetched it, then finished a full patch
review having read no document end to end; the page it says it wanted is a
section of that document. `feedback/2026-08-07-130058` found
`typo3_script_lookup` returns a guide inline and still never saw one while
working, because `typo3_test_run_guide` answered first.
`feedback/2026-08-07-065313` is the same session earlier, reporting that no
resource list was rendered at any point.

## Held by

- `not guarded` — what would hold it is an answer-shape assertion over the tools
  that return a document section, and which of the offered shapes is built is
  the todo's decision.
