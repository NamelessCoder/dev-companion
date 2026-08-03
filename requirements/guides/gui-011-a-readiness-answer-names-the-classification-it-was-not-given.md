---
id: R-GUI-011
status: held
restsOn: [D-GUI-001]
---

# R-GUI-011 — A readiness answer names the classification it was not given

**Where `typo3_commit_message_guide` checks a core message whose subject carries
no `[!!!]`, and the caller supplied no `isBreaking`, the checks say that the
classification was assumed rather than checked.** `isDeprecation` is the same
field and owes the same sentence.

The guide cannot derive either one: both are inputs and the tool never sees the
diff. That limit is not the defect. The defect is that the answer does not state
it, so a caller who has not yet classified the change reads a scoped result as a
clearance — and that caller is the one most likely to ask.

A subject that already carries `[!!!]` needs nothing said: the caller has
answered, and the changelog and release-target checks fire on it today. So has a
caller who passed `isBreaking` themselves, whichever value — which is why the
field is carried through as `null` where nobody supplied it rather than as
`false`, and why the input schema no longer declares `false` as its default.

## From

A core patch review of `9f6c6eb9093` (#110359), which passed the whole message
with no `isBreaking` and got `no-issues-found` back, while the patch removed a
protected method from a class that is neither `final` nor `@internal`
(`feedback/2026-08-03-144432`, 2026-08-03). The same clearance beside an unready
message was reported the day before by the session behind `R-GUI-007`
(`feedback/2026-08-02-144315`, 2026-08-02).

## Held by

- `CommitMessageTest::aClassificationNobodyGaveIsNamedInTheChecks`
- `CommitMessageTest::aClassificationTheCallerGaveIsNotAskedAboutAgain`
- `CommitMessageGuideTest::aCheckedMessageSaysTheClassificationWasAssumed`
