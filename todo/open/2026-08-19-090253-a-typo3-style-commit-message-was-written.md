# Reconcile the guide's issue trailers with what it says about them

**Serves:** feedback/2026-08-19-090253-a-typo3-style-commit-message-was-written.md
**Priority:** normal

Judged step 4 on 2026-08-21 — the wording, and it disagrees with the answer.
`typo3_commit_message_guide` writes `Resolves:` and `Related:` from `issue` and
`relatedIssues` in either workflow, while both parameters are described as Forge
issue numbers and the tool's own description says the Forge issue does not apply
outside the core. A session in an extension repository read that and concluded
the guide had no footer for the five pull requests its commit closed, without
calling it. `D-GUI-010` holds the measurement, including the draft that carries
`Resolves: #348` above the line denying it.

First step is the reading, because nothing here establishes it: what a TYPO3
extension repository on GitHub writes to link a commit to the issues and pull
requests it closes, and whether `Resolves: #348` is that form. Then the two
parameter descriptions and the tool's own, and the half of `R-AUD-003` that says
what is written outside the core rather than what is not demanded there.
