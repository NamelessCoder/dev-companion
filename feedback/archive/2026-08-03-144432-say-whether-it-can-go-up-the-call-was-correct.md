---
date: 2026-08-03T14:44:32+00:00
category: idea
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Task: review core commit 9f6c6eb9093 (#110359) and say whether it can go up.

## Observation

Task: review core commit 9f6c6eb9093 (#110359) and say whether it can go up.

The call was correct and cheap — one round trip, the trailers were validated, the 52/72 limits checked, the unknown Change-Id trailer preserved as documented, and the returned draft rewrapped the body at 72 without mangling it. No complaint about any of that.

The problem is what "no-issues-found" means to a reviewer holding a diff. The single biggest open question about this message is whether the subject should read "[!!!][TASK]" rather than "[TASK]": the patch removes a protected method (ImageService::getImageFromSourceString) and a protected promoted property from a class that is neither final nor @internal and carries #[Autoconfigure(public: true)], and it removes a backend compatibility path. If any of that is breaking, then the message is wrong, a Breaking changelog is owed, and the patch cannot be submitted as it stands.

typo3_commit_message_guide cannot know that — isBreaking is an INPUT to the tool, not something it derives, and it never sees the diff. That is inherent and fine. But the answer does not say so. "No commit message readiness issues found by the local checks" reads, to anything consuming it, as a clearance. The qualifier "by the local checks" is doing all the work and does not name which check was skipped for lack of information.

I caught this because the skill instructed me to "read its answer against the diff rather than on its own" and because I had enumerated the diff's deletions first. A review that called this tool earlier, or that trusted the answer, would have reported the commit shape as clean. That is the failure mode worth closing: the tool is most likely to be consulted by exactly the caller who has not yet classified the change.

Verified detail, so this is not hypothetical: I passed no isBreaking argument, the default false applied silently, and nothing in the answer said that a breaking-change classification had been assumed rather than checked.

## Query

typo3_commit_message_guide(message = the full commit message of 9f6c6eb9093 including subject, six body paragraphs, "Resolves: #110359", "Releases: main" and "Change-Id: Id53f1068d3866128320487df9ec59530d94a4aa1"; workflow "core"). Returned checks: [{"level":"info","code":"no-issues-found","message":"No commit message readiness issues found by the local checks."}] plus a rewrapped draft.

## Suggestion

When a message is checked (the `message` parameter form) and isBreaking / isDeprecation were not supplied, say so in the checks array rather than defaulting silently — e.g. a level "info" check with code "breaking-not-assessed": "The subject carries no [!!!]. Whether one is owed is a property of the diff, which this tool does not see: a removed or narrowed public or protected member, a removed behaviour, or a changed signature makes it breaking, and then the subject needs [!!!], a Breaking changelog entry and an extension scanner matcher. Confirm against the diff or re-call with isBreaking: true." That turns "no-issues-found" from a clearance into a scoped result. The same applies to isDeprecation. Cost is one extra array element and it closes the only way this tool can mislead.
