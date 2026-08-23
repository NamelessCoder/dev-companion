---
id: R-SCO-007
title: 'Only the caller shortens the tool list'
status: held
restsOn: [D-AUD-004]
heldBy:
  - ExcludedToolsTest
---

# R-SCO-007 — Only the caller shortens the tool list

**Every client is offered every tool, and the only thing that takes one away is
the caller naming it.**

Which repository the server was started in shapes what an answer says, never
whether the tool that says it is there: whether a task is core work is a
property of the task, which is
[`R-AUD-002`](../audience/aud-002-the-audience-is-a-property-of-the-task.md),
and the tool list cannot vary per task. Where the caller does exclude one,
nothing the server hands out points at it, and `typo3_server_scope` — which no
caller can exclude — names what went and which variable took it: a shorter list
a client cannot explain is a broken server as far as it can tell.

## From

The `project` profile withholding `typo3_test_run_guide` while a core-shaped
task asked from a site installation was still answered as core work and routed
to it, twice on a patch and six times on a test task (`E-SITE`, 2026-08-02).
Weighed and removed under
[`D-AUD-004`](../../decisions/audience/aud-004-every-client-is-offered-every-tool.md).

## Held by

- `ExcludedToolsTest` in full — that no kind of repository shortens the list,
  that a core-shaped task from a project is answered and the tool it routes to
  is offered, that the scope both the tool answer and the resource index are
  built from routes to nothing excluded, and that the tool explaining a short
  list cannot itself be excluded
