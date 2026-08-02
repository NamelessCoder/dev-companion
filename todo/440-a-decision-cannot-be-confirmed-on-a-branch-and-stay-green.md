# A decision cannot be confirmed on a branch and stay green

**Serves:** decisions/, requirements/

Confirming a decision is a `## Confirmed on <date>` section plus `status:
confirmed`, which `DecisionsTest::aStatusNamesTheLastDatedLineInTheFile`
requires together, and the status is what `Decisions::listing()` renders as `·
confirmed` in the generated block at the foot of `decisions/readme.md` and of
the group readme. A session working one of several todos is told to leave that
block alone, by hand and by command, so on a branch the entry can be confirmed
or `composer ci` can be green, never both — measured on 2026-08-02, where
`status: confirmed` on `D-SCO-002` failed
`DecisionsTest::everyGroupListsWhatIsInIt` on both readmes and on nothing else.
Adding a decision or a requirement has the same shape, the listing being short
by one until the merge. Settle where that check belongs, given that the merge
already runs `bin/cli decisions:index` and `bin/cli requirements:index` once
across every branch: it moves to `repository:check` rather than the suite every
branch runs, or the suite learns which of the two runs it is in, or a branch
may regenerate after all and the conflict is accepted as a merge cost. Read
`documentation/feedback/working-todos-in-parallel.md` and
`tests/Unit/DecisionsTest.php` together, because the rule and the check were
written for different runs and neither names the other. Whichever way it goes,
`D-SCO-002` is owed its `Confirmed on 2026-08-02` from the reading recorded in
`CORE-07`.
