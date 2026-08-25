# nothing answers whether an old issue's defect still reproduces against the checkout

**Serves:** feedback/2026-08-24-173116-nothing-answers-whether-an-old-issue-s-defect.md
**Priority:** normal
**Branch:** todo/nothing-answers-whether-an-old-issue-s-defect
**Claimed:** 2026-08-25

Judged on 2026-08-25 as `documentation/records/judging.rst` step 3 and written
up under the entry of that date in `D-SKL-038`, which is where the reading, the
measurement and the two suggestions that are not built stand. Give the `open`
form of `typo3_forge_lookup` the tail `GerritLookup::workflow()` carries and
leave the `issue` form as it is: the name `typo3-core-issue-triage`, and under
it the readings a candidate is decided on, which `D-SKL-031` settled and the
skill's own first section states. Hold what it names and what it leaves out with
a test in `ForgeTest`, the way
`GerritTest::aNamedChangeIsHandedTheWorkflowsThatOwnIt` holds the other one, and
say what it cost from `bin/cli tools:measure` rather than from an estimate.
