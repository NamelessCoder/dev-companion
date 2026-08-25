---
date: 2026-08-25T11:46:53+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_script_lookup
directory: /home/benji/projects/typo3-cms
---

# a whole session inside Build/Scripts never once tried typo3_script_lookup

## Observation

Task: check whether ./Build/Scripts/runTests.sh -s cglGit runs correctly inside a git worktree of the TYPO3 core checkout, fix it, and write the commit.

This is a report about a tool that exists, was listed, and stayed shut for an entire session that was about nothing but its subject.

Every file I touched lives in Build/Scripts: runTests.sh, cglFixMyCommit.sh, cglFixMyCommitFileHeader.sh. The first user message named the script directly ("cglfixmycommig runtests"). typo3_script_lookup was in my tool list from the first turn. I never called it, not once, in five turns.

What I did instead, entirely with grep and sed against the checkout:
- located the cglGit and cglHeaderGit suite branches in runTests.sh (grep for the script name, then sed a 50-line window)
- worked out how CORE_ROOT is derived (grep CORE_ROOT=, then sed lines 830-845 to see the cd ../../ that sets it)
- worked out what the container mounts (grep CONTAINER_COMMON_PARAMS= with 10 lines of context, for both the docker and podman branches)
- worked out what CGLCHECK_DRY_RUN expands to per suite (grep, four hits, read each)
- established that only these two scripts call git inside the container (grep 'git ' across Build/Scripts/*.sh)

That is five separate grep/sed round trips to reconstruct the mechanics of one dispatcher. Whether typo3_script_lookup would have answered any of it I genuinely cannot say, because I never asked — and that is the finding. I am not reporting missing knowledge; I am reporting that the name did not reach me at the moment it applied.

Why I think it did not: I went to Bash immediately because the question was empirical ("does it run cleanly") and my harness prompt for this session told me to prefer Bash for reading and searching. The tool list was long, the MCP tools read as documentation lookups, and I had a live checkout in front of me. "script" also did not connect for me to "the runTests.sh dispatcher and the suites it defines" — I read it as something nearer to CLI/console scripts or bin/ entry points.

## Query

Never called. Task text: "wir wollen prüfen ob der cglfixmycommig runtests im worktree sauber laufen kann" (check whether cglFixMyCommit / runTests can run cleanly in a worktree)

## Suggestion

If typo3_script_lookup does cover Build/Scripts/runTests.sh and its suites, put those words in the description: name runTests.sh, name the suite mechanism, name a couple of suite ids (cglGit, functional, phpstan). An agent with a checkout open will always be tempted to grep; the description has to say "this answers what the suite does and how the dispatcher is wired" loudly enough to beat five greps. If it does not cover them, that is the gap worth recording: the container mount contract (-v CORE_ROOT:CORE_ROOT and nothing else) and its consequences are exactly the kind of thing that is invisible in the file until it bites, and it took me an experiment in a worktree to see it.
