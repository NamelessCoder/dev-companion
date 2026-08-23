---
id: R-FBK-001
title: 'A feedback is about as many tools as it is about'
status: held
heldBy:
  - FeedbackTest::severalToolsStaySeveralToolsRatherThanOneWord
  - FeedbackTest::theListCanBeRestrictedToOneTool
  - FeedbackTest::theRecorderStillTakesAListTheSchemaNoLongerDeclares
  - StdioServerTest::aListOfToolNamesIsRefusedWithTheTypeItWanted
---

# R-FBK-001 — A feedback is about as many tools as it is about

**A feedback is about as many tools as it is about.**

The names survive recording as names, are listed as a list, and the store can be
filtered by one of them — the obvious question to ask of it is what is open
about one tool.

How they arrive is one string, separated by commas: the declared argument is a
plain `string` since `D-ANS-017`, and a list on the wire is refused. That is
about what a client can compose, not about what a feedback may say, and this
holds either way.

## From

Four tool names recorded as one unsearchable word, because everything that was
not `[a-z0-9_]` was stripped from the field (2026-07-29).
