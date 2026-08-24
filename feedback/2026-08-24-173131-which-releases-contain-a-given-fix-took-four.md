---
date: 2026-08-24T17:31:31+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# which releases contain a given fix took four git calls per issue and no tool answers it

## Observation

Task: pick another old open issue from forge.typo3.org, branch, and work it off. Mid-task the user asked for the list of already-fixed issues with justification so they could close them on Forge.

That turned the session into a triage deliverable, and the deliverable needed one fact per issue that neither typo3_forge_lookup nor typo3_gerrit_lookup gives: which TYPO3 releases actually contain the fix. I had to establish it in the checkout, four calls per commit:

  git log -S'<marker>' -- <file>      to find the commit
  git show --stat <sha>               for date and message
  git branch -r --contains <sha>      for the branches
  git tag --contains <sha>            for the releases

and for backports two more: git show origin/13.4:<file> to confirm the code is there, then git log origin/13.4 -S to get the backport's own hash, because the main-branch hash is not contained in the release branch.

This mattered for correctness, not just effort. I first told the user #88263 was fixed "in v14.0.0 and backported to 13.4", based on git branch -r --contains. Only when I later read the commit body did I see "Releases: main, 13.4, 12.4" — 12.4 as well. I had to correct the statement in a later turn. The trailer was the authority and I reached it last.

typo3_gerrit_lookup does return the changes sharing a Change-Id, which is the backport relation, and it does return the Forge issues a commit message names. So it holds most of the raw material. What it does not do is start from a commit hash in my checkout, and it does not turn branches into release versions.

## Query

After identifying commits cc880c67777, cf227b18e20, 90c2181b260 as fixes, I ran per commit: git log -S'<marker>' -- <file>; git show --stat <sha>; git branch -r --contains <sha>; git tag --contains <sha>. Also git show origin/13.4:<file> and git log origin/13.4 -S to find the backport hash aaec618cf33. Task text: "ich brauche die liste der bereits behobenen issues mit begruendung damit ich die issues schliessen kann".

## Suggestion

Let typo3_gerrit_lookup accept a commit hash as a handle alongside change number and Change-Id, and have any change it answers carry the release versions it is contained in, not only the target branch. "Contained in v14.0.0, v14.1.0, ... and, via change 90012, in 13.4 from v13.4.x" is the sentence a triage answer needs and the one I hand-assembled four times.

Second, when typo3_forge_lookup answers an issue, surface the Releases: trailer of the changes it lists in reviews[]. The trailer is the authoritative statement of intended branches and it is the thing I got wrong by inferring from git branch --contains instead.
