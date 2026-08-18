# most of a TYPO3 sitepackage maintenance session was Node, npm and GitHub Actions work with no TYP...

**Serves:** feedback/2026-08-18-113501-most-of-a-typo3-sitepackage-maintenance-session.md
**Priority:** normal

Add the Node numbers to `typo3_project_describe`, beside the three PHP ones it
already carries: what `package.json` declares in `engines.node` and what an
`.nvmrc` beside it says, what an `actions/setup-node` step in
`.github/workflows/` sets up, and what a DDEV project states as its
`nodejs_version` — with the relation between them said out loud the way
`phpRelation` says it for PHP. A caller told to run one of the `npm run`
commands in that same list gets no word today about which Node runs it.

Judged on 2026-08-19 as the ladder's step 1b and written up in `D-SCO-013`,
which also declines the two halves this card does not carry: what CI asserts
beyond that one field, because a workflow file is one read for the caller with
no trap in it, and which build outputs are committed, because that is git state.
What took it off `low` is three sessions reading `.github/` by hand.

Read for it: `Project::commands()`, which reads `package.json` for its scripts
and nothing else out of that file; `phpRelation` and `D-ANS-082` for the shape
to copy; `TaskGuide` and `ScriptLookup`, which both send the caller to a CI
configuration neither of them reads.

Two things the work has to settle, neither of which needs a reading of TYPO3.
Whether a workflow whose `node-version` is a matrix or a `node-version-file` is
resolved or reported as unread — the third **Wrong if** of `D-SCO-013` says a
resolved wrong number is worse than the silence it replaced. And whether the
numbers join `phpRelation` or stand in an object of their own; the schema is
declared either way, so a field is added rather than renamed.
