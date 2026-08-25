---
date: 2026-08-25T11:49:10+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_script_lookup, typo3_test_run_guide, typo3_server_scope
directory: /home/benji/projects/typo3-cms
---

# the whole diagnosis was mechanism, not convention, and the server holds only convention

## Observation

Task: check whether ./Build/Scripts/runTests.sh -s cglGit runs correctly inside a git worktree of the TYPO3 core checkout, fix it, and write the commit.

This is the wish, and the honest boundary question that goes with it. The server earned its keep on conventions (the 52-character subject, the Releases policy). Every load-bearing fact in the actual bug was mechanism, and I got all of it from experiments.

What I had to establish myself, in full:
- runTests.sh mounts only -v CORE_ROOT:CORE_ROOT, and CORE_ROOT is derived by cd-ing two levels up from the script (lines 836-839). Read from the file.
- In a git worktree, .git is a file containing "gitdir: <path>" pointing outside CORE_ROOT, so git inside the container has no repository. Own knowledge, confirmed by cat.
- The consequence, which is the actual defect and was visible nowhere: cglGit reports SUCCESS having checked zero files. Found only by running it in a worktree and comparing with the main checkout ("Found 0 of 5 files" vs "fatal: not a git repository: (null)" followed by "all is well").
- Why it stays silent: cglFixMyCommit.sh line 118 pipes git into grep, and a pipeline's exit status is grep's, so a failed git call is structurally invisible. Read from the file, but the significance only after the run.
- That mounting the common gitdir at its absolute path fixes it. Proven with a hand-written docker run before touching runTests.sh.
- That `cd ""` in bash succeeds and leaves you in the current directory — which made my own first patch fall back to mounting CORE_ROOT twice. Own knowledge, verified with a one-line bash test.
- That docker tolerates a duplicated mount point (exit 0, tested). Podman, the dispatcher's default, is not installed here, so that stayed unverified and I said so.
- That only these two scripts call git inside the container. grep over Build/Scripts.
- Forge issue existence, the development major (Typo3Version::BRANCH = 15.0), the maintained release lines, and what the commit-msg hook actually validates. Checkout and curl.

Two of those are worth the server knowing about even if it decides the rest is out of scope, because they are invariants of the build tooling rather than facts about my machine: the container mount contract, and the fact that the git-based CGL suites fail open rather than closed. An agent that knows the first predicts the whole bug from the file. An agent that knows the second never trusts a green cglGit run without checking the file count.

I am aware this may sit outside where the maintainers draw the line — the server describes itself as covering TYPO3 knowledge, and runTests.sh is build tooling. Recording the wish anyway, as instructed, because a wish dropped as out of scope is the one that never gets heard.

## Query

Nothing was asked of the server about this. Task: "wir wollen prüfen ob der cglfixmycommig runtests im worktree sauber laufen kann". Established instead by: grep/sed over Build/Scripts/runTests.sh, running ./Build/Scripts/runTests.sh -s cglGit -n in a worktree and in the main checkout, and four hand-built docker run experiments.

## Suggestion

The single answer that would have shortened this session most: "runTests.sh mounts only the core root into the container, so anything the suite needs from outside that path — the gitdir of a worktree above all — is not there; the git-based suites (cglGit, cglHeaderGit) consequently find no files and exit 0." That is two sentences and it is the entire diagnosis. Reachable from a query containing "worktree", or "runTests", or "cglGit". I searched Forge for "worktree" and got zero results, so this is not written down anywhere else either. Second best, and cheaper: have whatever tool covers test running state the mount contract once, since it explains a whole class of "the suite passes but did nothing" confusion, not just this one.
