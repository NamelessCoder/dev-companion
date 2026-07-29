---
date: 2026-07-29T09:35:22+00:00
category: bug
status: open
tool: typo3_commit_message_guide
---

# The Gerrit trailers arrive welded to the subject and body rules, which do transfer

## Observation

Half of this note is closed: the draft no longer appends a `Releases:` line the
caller did not ask for while warning in the same answer that it is missing.

What remains is the other half. In this repository (GitHub PRs, TYPO3-style
subject lines, no Gerrit, no Forge issues) the subject and body rules — keyword
prefix, 52/72 character limits, wrapping that preserves fenced blocks and URLs —
were all correct and useful, but they cannot be had without `Resolves:`,
`Releases:`, and a hard `missing-issue` error that no message here can satisfy.

## Query

typo3_commit_message_guide {"message":"[BUGFIX] Suppress PHP 8.4 deprecations from scssphp compile\n\n...\n\nResolves: #1620\n"}

## Suggestion

Offer a way to get the subject and body rules without the Gerrit trailers, e.g.
`releases: []` or a `workflow: "github"` flag. The TYPO3 commit conventions are
used well outside the core; the Gerrit trailers are not.
