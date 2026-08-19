---
date: 2026-08-19T09:02:53+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/blog
---

# A TYPO3-style commit message was written without commit_message_guide, which claims that case

## Observation

Task: update the JavaScript dependencies of EXT:blog and close the Dependabot PRs. Full transcript, no summary prefix.

The server's opening instruction claims this case in as many words: "the commit message, in your own repository as much as in the core: typo3_commit_message_guide". EXT:blog is the user's own repository and follows core commit conventions — the five commits visible in git log at session start are [BUGFIX], [BUGFIX], [BUGFIX], [TASK], [BUGFIX].

I wrote a fourteen-line commit message with a [TASK] prefix, a subject line, and three body paragraphs, entirely from my own knowledge of TYPO3 conventions. I did not call typo3_commit_message_guide. Subject line: "[TASK] Update the frontend build to current dependencies". It is now commit 909b288 on TYPO3GmbH/blog master, pushed.

Why it lost is worth recording, because it is not the same reason as in the CSS-class feedback. There I never reframed the task as TYPO3 work. Here I did know I was writing a TYPO3-style commit message — I chose the [TASK] prefix deliberately, having read the existing log. The tool lost because I was confident I already knew the convention, and confidence is exactly the state in which a lookup feels redundant. Nothing in the moment made the cost of checking look worth it.

What I would actually have wanted checking, and did not:
- Is [TASK] right for a dependency update that also carries an API migration and changes committed build artefacts, or does that want a different prefix?
- Does this repository's convention want a "Resolves: #..." or "Related: #..." footer? I added none. The Dependabot PR numbers (348, 347, 346, 344, 294) were available and I referenced them nowhere in the commit — only in the PR close comments, in the other direction. A reader of the git history cannot get from this commit to the five reports it resolved.
- Line-length and imperative-mood rules for the body, which I followed by habit rather than by rule.

The second point is the one I would change if I could: the commit and the five closed PRs are linked one way only, and I did not notice until re-reading the transcript for this debrief.

## Query

Not asked. The call I should have made: typo3_commit_message_guide, before `git commit`, for a dependency-update commit in an extension repository (not the core) that also carries a third-party API migration and regenerated build artefacts, and that resolves five GitHub Dependabot pull requests. Actual message written without it: subject "[TASK] Update the frontend build to current dependencies", three body paragraphs, no footer.

## Suggestion

Where the guide covers an extension's own repository rather than the core, it would help if the answer said so in its first line — an answer that reads as core-only leaves me deciding whether it transfers, which is the same coin-flip as not calling it. Two specifics worth carrying: which prefix fits a dependency update that also migrates an API and regenerates committed artefacts, and what footer links a commit back to the GitHub issues or pull requests it resolves in an extension repository, since the core's Forge conventions do not map onto Dependabot PR numbers. A worked example of a dependency-bump commit would have been enough to settle all of it in one call.
