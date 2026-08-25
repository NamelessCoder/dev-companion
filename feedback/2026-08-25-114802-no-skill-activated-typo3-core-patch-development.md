---
date: 2026-08-25T11:48:02+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-patch-development, typo3-extension-testing, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# no skill activated; typo3-core-patch-development fitted from the commit turn onward

## Observation

Task: check whether ./Build/Scripts/runTests.sh -s cglGit runs correctly inside a git worktree of the TYPO3 core checkout, fix it, and write the commit.

No skill activated at any point. I never called the Skill tool. Reporting that as a result, with the moment one should have fired.

The moment: turn 3, the user's message "ich denke wir sollten beides zusammen nehmen" (let us take both together). At that instant I had three modified files in the working tree of the core checkout — Build/Scripts/runTests.sh, cglFixMyCommit.sh, cglFixMyCommitFileHeader.sh — a verified fix, and a decision to make about squashing two concerns into one commit against AGENTS.md's rule that every commit needs its own Forge issue. What I then did by hand, in this order: checked Typo3Version::BRANCH to establish the development major (15.0), listed origin's release branches to work out the maintained lines, searched Forge for a pre-existing issue, read .git/hooks/commit-msg to learn what it validates, drafted the message, measured its lines with awk. That is the typo3-core-patch-development workflow, executed from first principles.

typo3-core-patch-development's description is a good description of what I was doing: "Write a TYPO3 core patch and carry it to review: the changelog entry, the project's checks, the push to Gerrit." It still did not fire. My reading of why: the session never announced itself as patch work. Turn 1 was a diagnostic question about a test script. Turns 2 and 3 were six and seven words long. There was no sentence anywhere in the session resembling "write a core patch" — the patch emerged from the diagnosis. A skill matched on its description alone has nothing to match against when the user's words are "let us take both together".

Two others were near and also stayed shut, correctly or not:
- typo3-extension-testing ("repair or run tests and static quality checks ... and a failing check") — turn 1 was literally a failing check in the containerised suite, but the description reads as being about a project's or extension's own test setup, not about the core's Build/Scripts dispatcher. I would not have expected it to cover runTests.sh and did not try it.
- typo3-core-patch-review ("your own before you push it") — this fitted turn 5 exactly, where the user asked me to look hard at my own commit's subject line. I handled that with typo3_commit_message_guide plus git-log archaeology instead. This is the one I most regret, because a review skill would plausibly have checked the commit message, the Releases line and the issue reference as a set, rather than me discovering the 52-character rule only because I happened to reach for the message tool.

What I would work out again next session, unchanged: the whole worktree diagnosis (mount contract, .git-as-a-file, the experiment that proves it), and the Releases-line decision.

## Query

No skill was invoked in the session. Turn texts, in order: "wir wollen prüfen ob der cglfixmycommig runtests im worktree sauber laufen kann" / "was ist mit dem shell script?" / "ich denke wir sollten beides zusammen nehmen" / "hier ist die forge nummer 110534" / "bitte schau nochmal genau hin [BUGFIX] Make the git based CGL suites work in worktrees"

## Suggestion

Add the trigger words that describe patch work as it actually arrives, not as it would be announced. For typo3-core-patch-development: "also when a fix is already in the working tree and the next step is the commit message, the Forge issue or the Releases: trailer". For typo3-core-patch-review: "also when asked to look again at a commit you just made — its subject, its trailers, its issue reference". Both of those are the shape the request took here, and neither is reachable from the current descriptions. Also consider whether the core's own Build/Scripts tooling belongs to typo3-extension-testing or somewhere else — a failing runTests.sh suite in the core monorepo currently matches nothing, and that is where this session started.
