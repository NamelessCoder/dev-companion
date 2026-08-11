---
date: 2026-08-10T18:25:23+00:00
category: idea
status: closed
closed: 2026-08-11
model: claude-opus-5
tool: typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# two searches landed in the same document and neither answer suggested reading it whole

## Observation

Task: review TYPO3 core Gerrit change 95163 and judge its commit message, changelog obligation and Releases: trailer.

I made two typo3_rule_lookup calls, minutes apart:

1. query "release branches taking patches changelog entry required for TASK" → matchCount 1, the "Changelog Files" section of core/contribution/commit-messages.
2. query "Releases trailer which branches take a patch today maintained versions" → matchCount 1, the "Release Targets" section of the same document, core/contribution/commit-messages.

Both answers carried documentId "core/contribution/commit-messages". The tool description says to pass that documentId back to read the whole page, and typo3-core-patch-development says explicitly that reading it once is cheaper than learning it from checks one call at a time. I did neither — I searched twice into one document and never opened it, in a session where commit-message shape, the changelog obligation and the release targets were all live questions and the message was rewritten three times.

Both answers were correct and directly useful: "a BUGFIX owes none, a TASK owes none" settled the changelog question and, notably, told me that demanding an entry of a TASK would itself be a review defect — which stopped a finding I was inclined to file. "Release Targets" confirmed main + one line back and warned that `git branch -r` does not answer it, which is exactly the shortcut I had taken minutes earlier by listing remote branches.

The gap is not in the content, it is that nothing in the first answer said "the rest of this page probably answers your next question too", and the second search cost a round trip to reach a neighbouring heading in a page I already held the id for.

## Query

typo3_rule_lookup query="release branches taking patches changelog entry required for TASK" targetVersion=15.0, then typo3_rule_lookup query="Releases trailer which branches take a patch today maintained versions" — both matched core/contribution/commit-messages, different headings.

## Suggestion

When a search returns matches from a single document, say how many other sections that document has and name them, with the documentId to fetch it whole. "This is 1 of 6 sections in core/contribution/commit-messages: Subject, Body, Release Targets, Changelog Files, Trailers, Examples" would have made the second search obviously redundant before I made it. The affordance already exists; what is missing is the answer telling the caller it is standing next to it.
