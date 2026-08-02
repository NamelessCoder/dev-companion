---
id: D-SCO-1
date: 2026-07-29
status: revoked
---

# D-SCO-1 — Outside the core the core test guide declines rather than adapts

**Outside the core `typo3_test_run_guide` returns no suite at all, while
`typo3_architecture_lookup` keeps its hints and drops only their check
commands.**

`typo3_test_run_guide` recognises work outside the core, and the question was
whether it should adapt its answer to the checkout it finds itself in.

## Decided

- The difference between the two is what the payload is made of. A hint is a
  convention and travels; a suite is a command against a script that lives in
  the core repository and does not.

## Wrong if

- A project-suite source turns up whose answer has the same targeted invocation
  shape as `typo3_test_run_guide`. Until then, project commands come from the
  checkout and the task skill, not from a core suite adapted by analogy.

## Revoked on 2026-07-29

It was assumed that nothing in `knowledge/` described how an extension runs its
tests, so anything the guide offered instead would have been invented.
`project-extension-tests` now carries that harness, verified against the
matching `typo3/testing-framework` tags. The `typo3_test_run_guide` still
declines because its answer shape is a core suite invocation; the
`typo3-extension-testing` skill takes the other branch, verifies the checkout's
harness, and routes setup or repair through the extension-test hint and
versioned documentation before adding coverage.
