---
date: 2026-08-07T23:35:12+00:00
category: idea
status: closed
closed: 2026-08-08
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# the session never learned typo3_server_scope or the typo3:// guides exist, because no step in the...

## Observation

Task: find the 30 oldest unresolved core issues, take the first genuine bug report, verify it against a 15.0.0-dev checkout.

My client showed me no MCP resource list at any point in the session. The only documents I read whole were references/base.md and references/checklist.md, which are files belonging to the typo3-core-issue-triage skill inside the repository — not resources this server serves. I read no typo3:// guide, and did not know one existed until I re-read the server instructions while writing this debrief.

The server's own instructions predict this precisely: "typo3_server_scope says what is covered, at which depth, by which tool, and which installation is read — including the whole procedures served as typo3://guides resources, which your client may not list at all." The prediction held. The mechanism is that nothing made the call: references/base.md fixes "the order every task here starts in" as five steps beginning with typo3_project_describe, and typo3_server_scope is not among them, nor is any step that would surface the guides. A session that follows the prescribed order faithfully — which I did — never learns what else is on offer.

Every tool I called was chosen from one of three places: the deferred-tool name list my client showed, the MCP server instructions block, or a skill naming the tool outright. Not one came from browsing what the server offers.

The page I wanted and assembled by hand is the subject of a separate feedback (re-attempting a merged-then-reverted core fix). Had a guide list been visible, that is where I would have looked for it before doing the work myself. I would also have checked whether a guide covered "reproducing a frontend access-restriction defect", which I likewise assembled from typo3_test_run_guide plus reading the existing test fixture.

## Query

Whole session: triage the 30 oldest unresolved core bugs and verify the first genuine one against the checkout. typo3_server_scope was never called; no MCP resource list was ever shown to me.

## Suggestion

Put typo3_server_scope into references/base.md as the step before typo3_project_describe for a session that has not yet called it — the order is followed literally, so a step is the reliable way in. Alternatively, name the guides that exist by title in the server instructions rather than only naming the tool that lists them: a procedure served as a resource is reachable only by a client that lists resources, whereas a title in the instructions is reachable by every client.
