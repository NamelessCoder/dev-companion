# Run `REVIEW-03` once a core patch is in progress

**Serves:** REVIEW-03
**Priority:** normal
**Waiting on:** may a patch be made in a core checkout so `REVIEW-03` can run —
    or is there another checkout that carries one? The case reviews the current
    changes in a core checkout, and `/home/benji/projects/typo3-cms` is on
    `main` with a clean tree, so the review has nothing to read. The checkouts
    belong to somebody, and a session here may not put uncommitted work in one
    of them to grade itself against.

`REVIEW-03` is the one forward review of the three that has never run:
`bin/cli repository:check` reports it as `Never run forward` beside two that
have recorded runs. What it needs is not a fix here but a state over there — its
prompt is *review the current changes in this TYPO3 core checkout*, and every
one of its five criteria reads the diff. `E-CORE` is what it wants and
`todo/reference/which-checkout-plays-which-environment.md` is what says which
checkout plays it. Once the answer is in, this is a forward run like the other
two: drive it per `documentation/evidence/forward-runs.md` and record it with
`bin/cli scenario:record`.
