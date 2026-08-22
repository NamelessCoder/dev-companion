---
id: R-GUI-005
title: 'The commit-message guide is also a prompt'
status: held
---

# R-GUI-005 — The commit-message guide is also a prompt

**The existing commit-message guide is also exposed as an MCP prompt, so a user
can invoke it without first discovering the corresponding tool.**

The prompt delegates to the guide and does not maintain a second set of
commit-message rules.

## From

The SDK prompt primitive being unused while the most naturally user-invoked
guide already existed (2026-07-30).

## Held by

- `StdioServerTest::theCommitMessageGuideIsAvailableAsAPrompt`
