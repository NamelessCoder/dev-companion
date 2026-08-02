---
id: R-SCO-7
status: open
---

# R-SCO-7 — A client is offered only the half it can use

**A client is offered only the half of the server it can use.**

In a Composer project the core contribution surface is left out of the tool
list — the review rules, the Gerrit workflow, the `runTests.sh` suites —
and `TYPO3_MCP_PROFILE` decides it outright. Whatever the profile, nothing the
server hands out points at a tool it does not offer, and `typo3_server_scope`
is in every profile and names the active one, what it left out, and how to be
offered it anyway: a shorter list a client cannot explain is a broken server as
far as it can tell.

**From:** marking a topic core-only telling a site developer what an answer is
worth without keeping the tool that gives it out of the list (2026-07-29).

**Held by:** `ProfileTest` in full — the derivation, the override, the
misconfiguration, and that the scope both the tool answer and the resource
index are built from routes to no omitted tool

**Open on:** the sentence about what the server hands out, which holds for the
scope and not for `typo3_task_guide`. Under the `project` profile a core-shaped
task is still answered as core work, and that answer routes to
`typo3_test_run_guide` — twice on a patch, six times on a test task. `TaskGuide`
reads `Scope::read()` and filters by the audience of the task, where
`Scope::offered()` filters by the profile as well. Read in `E-SITE` on
2026-08-02 and recorded under
[`D-AUD-2`](../../decisions/audience/aud-2-two-profiles-because-a-third-would-have-been-the-same-set.md).
