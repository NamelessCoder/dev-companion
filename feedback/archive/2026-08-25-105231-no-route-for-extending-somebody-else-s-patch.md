---
date: 2026-08-25T10:52:31+00:00
category: idea
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3-core-patch-checkout, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# No route for extending somebody else's patch set, which is what was asked twice

## Observation

Task: "wir sollten das abgleichen und ggf durch unsere erweitern" and then "wir wollen elias patch ammenden" — take another author's open Gerrit change (95369, owner Elias Häußler), put our own uncommitted work on top of it, and push it back as a new patch set.

`typo3-core-patch-checkout` activated and fitted the first half well: it named the three ways in, and its base order and its pointer into the gerrit-workflow document are what got the patch set into the checkout correctly. What it does not have is the way in the user actually asked for — take the fetched patch set as the base and layer local work onto it. Its three ways all assume the patch is read or tried, not extended.

That gap shows in three places I had to decide alone:

- The skill says the working tree must be clean and "do not stash it as a convenience". The user had explicitly asked to stash. I stashed, and then never popped it — I took our four non-overlapping files out of the stash with `git checkout stash@{0} -- <paths>` after verifying with `git diff <our base> <patch base> -- <paths>` that those files were identical on both bases, which made it transcription rather than a merge. The conflicting two files I resolved by hand-picking. The skill's checklist is written for a cherry-pick conflict and does not describe this shape at all.
- `core/contribution/gerrit-workflow` covers amending your own change and says pushing carried state "would be opening a patch set in somebody else's name — that belongs to the workflow that owns amending a change, and only where the change is yours to amend". That is precisely the case I was in, and it stops one step short. Extending another author's change is normal TYPO3 practice. I worked out the mechanics and etiquette myself: `--amend` preserves the author and moves only the committer, the `Change-Id` must stay byte-identical, `origin/main..HEAD` must still be exactly one commit, and the changed anchors need a Gerrit comment so reviewers are not left guessing.
- The skill's "Put the checkout back" section is written as mandatory. This session legitimately ends with the checkout on `review/95369` carrying an amended commit, because the work continues there. Nothing in the skill allows for that ending, so I ignored the section and said so instead.

One more mismatch worth recording: the skill's undo assumes it runs. In this session the user amended the commit between two of my turns, which rebased the branch onto the local `main` tip. I only noticed because a `git diff --cached --stat` came back smaller than it should have, and had to go read `git log` to find out what had happened.

## Query

Session task across three turns: compare Gerrit change 95369 against local uncommitted changes, merge ours into it, amend and push as a new patch set. Skill typo3-core-patch-checkout activated; typo3_rule_lookup(documentId="core/contribution/gerrit-workflow") read whole.

## Suggestion

Add a fourth way in to `typo3-core-patch-checkout`, or a section to the gerrit-workflow document, for extending somebody else's open change. What it would have to carry:

- The base is the fetched patch set, on a branch of its own, and local work goes on top of it — not the reverse.
- Before moving a local file onto the patch, check whether that file is identical between your base and the patch's base; where it is, taking your version wholesale is transcription. Where it is not, it is a resolution and the checklist's rules apply.
- `git commit --amend` keeps the original author and makes you the committer; that is the intended shape for a foreign patch set, not something to correct.
- The `Change-Id` stays byte-identical, and `git log --oneline origin/main..HEAD` must still show one commit before pushing.
- Anything you changed about the author's own decisions — a different anchor, a reworded body, a different `Releases:` line — belongs in a Gerrit comment on the upload, because the diff between patch sets does not say why.
- When not to do it at all: comment instead where the change is still under active discussion by its author.

Also worth softening: "Put the checkout back" should allow the ending where the work continues on the review branch, and say what to report instead.
