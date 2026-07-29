---
date: 2026-07-29T09:35:22+00:00
category: bug
status: open
tool: typo3_commit_message_guide
---

# The returned message and the returned checks contradict each other. The draft came back with "Rel...

## Observation

The returned message and the returned checks contradict each other. The draft came back with "Releases: main" appended — a trailer the input did not contain and the caller did not request, added from the releases default — while checks simultaneously reported {"level":"warning","code":"missing-releases","message":"No Releases: line found."}. The checks are evaluated against the input, the message field is described as "ready to commit", so committing the returned text produces a message the tool has already warned about, and silently adds a Gerrit/Forge trailer. In this repository (GitHub PRs, TYPO3-style subject lines, no Gerrit, no Forge issues) "Releases:" is simply wrong, and the false warning trains the caller to ignore the checks.</observation>
<parameter name="suggestion">Run the checks against the message that is actually returned, so draft and verdict cannot disagree — a trailer the tool itself adds must not be reported as missing. Beyond that, offer a way to get the subject/body rules (keyword prefix, 52/72 character limits, wrapping that preserves fenced blocks and URLs — all of which worked correctly) without the Gerrit trailers, e.g. releases: [] or a workflow: "github" flag. The TYPO3 commit conventions are used well outside core; the Gerrit trailers are not.</suggestion>
</invoke>

## Query

typo3_commit_message_guide {"message":"[BUGFIX] Suppress PHP 8.4 deprecations from scssphp compile\n\n...\n\nResolves: #1620\n"}
