---
date: 2026-08-25T11:46:05+00:00
category: idea
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# commit_message_guide caught a 52-char subject rule the core's own commit-msg hook misses

## Observation

Task: check whether ./Build/Scripts/runTests.sh -s cglGit runs correctly inside a git worktree of the TYPO3 core checkout, fix what is broken, and write the commit.

This is the thing that worked and must not be broken.

I drafted the commit message myself and validated it only against the checkout: I read .git/hooks/commit-msg, saw it enforces line length with grep -q -E '^[^#].{72}', measured every line of my message at 68 characters or less, and told the user the message was ready. The subject was 56 characters. The user pushed back with "bitte schau nochmal genau hin" pointing at the subject line, and only then did I call typo3_commit_message_guide with workflow="core".

It returned summary-length-preferred: "The subject line is 56 characters long: a 47-character summary plus 9 for the keyword prefix. Under 52 characters is preferred in total, which leaves the summary 43."

That rule is the one thing in this whole session I could not have derived from the checkout. The commit-msg hook does not enforce it. AGENTS.md does not state it — it says only "No line of the message may reach 72 characters". So the repository's own two sources of truth both told me the message was fine, and the server was the only thing that knew otherwise.

The same single call also produced older-release-line, warning that 13.4 on my Releases: trailer claims a severity a build-tooling fix does not earn. I verified that against the log afterwards and it held: of the last 40 [BUGFIX] commits touching Build/, essentially all use "Releases: main, 14.3". I dropped 13.4.

Both corrections were right, both changed the delivered artifact, and both arrived in one call. The failure in this session was mine — I called the tool when challenged instead of when drafting.

## Query

typo3_commit_message_guide(message="[BUGFIX] Make the git based CGL suites work in worktrees ...", workflow="core", releases=["main","14.3","13.4"], issue="110534")

## Suggestion

Keep this exactly as it is. Two accurate, actionable, artifact-changing corrections out of a single call on a message a careful reading of the repository had already passed. If anything, the value would rise if the tool were easier to reach at draft time rather than at review time — see the separate feedback on the mandated opening calls never being made.
