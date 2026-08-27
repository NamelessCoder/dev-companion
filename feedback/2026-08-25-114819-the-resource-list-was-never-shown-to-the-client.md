---
date: 2026-08-25T11:48:19+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_documentation_lookup, typo3_reference_list, typo3_server_scope
directory: /home/benji/projects/typo3-cms
---

# the resource list was never shown to the client, so no document was read whole

## Observation

Task: check whether ./Build/Scripts/runTests.sh -s cglGit runs correctly inside a git worktree of the TYPO3 core checkout, fix it, and write the commit.

Trimmed to what is left of this report: the tool I did call answers on the
subject of a page this server ships and never named it. The two halves that are
answered are the resource surface and the orientation call, both settled on
2026-08-07 and re-read against this session under the entry of 2026-08-27 in
[`D-ANS-061`](../decisions/answers/ans-061-an-answer-that-names-a-document-hands-it-over.md).

The page I wanted, and would have read whole had I known it existed: something called "Commit message rules for core patches" or "Anatomy of a core commit" — the 52-character subject preference, the 72-character body limit, the trailer order, what the commit-msg hook does and does not enforce, and the policy on which release lines a bugfix goes to. In this session I assembled precisely that page myself out of AGENTS.md, .git/hooks/commit-msg, two typo3_commit_message_guide calls and three separate git-log statistics runs (subject lengths across 400 commits, Signed-off-by frequency across 200, Releases lines across the last 40 [BUGFIX] commits touching Build/).

## Query

typo3_commit_message_guide, twice, with workflow="core". No resource was listed
or read, and no answer named a document.

## Suggestion

Name the important documents inside the tool answers that touch their subject — typo3_commit_message_guide's response would be the natural place to say "the full commit message rules are in <document name>". A one-line pointer riding along inside answers the agent already wanted reaches that agent, an orientation tool does not.
