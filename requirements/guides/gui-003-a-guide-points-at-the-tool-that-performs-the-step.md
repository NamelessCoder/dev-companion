---
id: R-GUI-003
status: held
---

# R-GUI-003 — A guide points at the tool that performs the step

**A guide that names a step points at the tool that performs it, in the answer
where the step appears.**

The routing table is read once, at the start of a session; the step is taken
hours later, out of whatever the last answer listed.

## From

Four commit messages written in one session without
`typo3_commit_message_guide` ever being called — its brief ended with
"Summarize changed behavior", and its next lookups never named the tool that
does exactly that (2026-07-29).

## Held by

- `ScopeTest::theBriefPointsAtTheGuideForTheStepItEndsWith`
