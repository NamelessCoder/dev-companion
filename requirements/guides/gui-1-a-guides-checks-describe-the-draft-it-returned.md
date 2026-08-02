---
id: R-GUI-1
status: held
---

# R-GUI-1 — A guide's checks describe the draft it returned

**The checks a guide returns describe the draft it returns.**

A trailer the tool adds itself is never reported as missing, and what the draft
cannot know it carries as a placeholder rather than as a default. A placeholder
handed back in a message to check is that same unanswered field and is reported
again — the draft returned still carries it, so the checks still name it.

## From

`Releases: main` being appended and `missing-releases` warned in the same
answer (2026-07-29).

## Held by

- `CommitMessageTest::theDraftNeverCarriesAReleaseTheCallerDidNotName`
- `CommitMessageTest::aTrailerTheDraftCarriesIsNotAlsoReportedAsMissing`
- `CommitMessageTest::neitherPlaceholderCouldBeReadAsAnAnswer`
- `CommitMessageTest::aPlaceholderHandedBackIsStillAnUnansweredField`
