---
id: R-GUI-007
title: 'A body the guide reflowed or left too long says so'
status: held
restsOn: [D-GUI-003]
---

# R-GUI-007 — A body the guide reflowed or left too long says so

**A body line `typo3_commit_message_guide` joins into a paragraph is named in
the checks, and a line it leaves over 72 characters is an `error` under
`workflow="core"`.** Under `workflow="project"` that second one stays a
`warning`, because no hook runs there.

The two rules the guide holds — wrap at 72 characters, leave structure intact —
cannot both hold for a block whose lines are long, and the caller is the only
one who can decide which to give up. So the answer says which one the draft gave
up, in the place the caller already reads.

## From

A core patch session that passed a four-line "Executed commands:" block and got
it back as one running paragraph, with `no-issues-found` beside it, twice in the
same session (`feedback/2026-08-02-144315`, 2026-08-02). The other half was
established on 2026-08-03 from `Build/git-hooks/commit-msg` in the `main`
checkout: `checkForLineLength()` refuses every line of 73 characters or more,
whether it is indented, fenced or a URL.

## Held by

- `CommitMessageTest::aRunOfLinesTheWrappingJoinedIsNamed`
- `CommitMessageTest::eachJoinedRunIsReportedOnItsOwn`
- `CommitMessageTest::aBodyTheWrappingLeftAloneReportsNoReflow`
- `CommitMessageTest::aLineOverTheWidthIsAnErrorForTheCoreAndAWarningOutsideIt`
- `CommitMessageGuideTest::aCheckedMessageStillSaysWhatTheWrappingJoined`
