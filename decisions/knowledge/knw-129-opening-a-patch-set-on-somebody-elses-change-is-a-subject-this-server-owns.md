---
id: D-KNW-129
title: Opening a patch set on somebody else's change is a subject this server owns
date: 2026-08-27
status: open
coveredBy:
  - KnowledgeTest::aPatchSetOnSomebodyElsesChangeSaysWhatItOwesThatAuthor
  - KnowledgeTest::aSearchWhoseMatchesAreAllInOnePageAnswersWithThePage
  - SkillTest::extendingSomebodyElsesChangeIsAWayIntoTheCheckout
---

# D-KNW-129 — Opening a patch set on somebody else's change is a subject this server owns

**The corpus states what a patch set opened on somebody else's change owes: what
the amend does to authorship, and what the upload says to its author.**

A session was asked twice to put local work on top of an open Gerrit change and
push it back, found the one sentence in reach ruling the case out, and worked
the mechanics and the etiquette out on its own.

## Evidence

- The probe misses. `bin/cli hints:probe` with the feedback's own task —
  extending somebody else's open Gerrit change with local work and pushing a new
  patch set — reaches `site-sets` and nothing else, admitted by the curated
  vocabulary rather than by what it says.
- The page that carries the procedure stops at the author's own change.
  `knowledge/documents/core/contribution/gerrit-workflow.md` has **Update an
  Existing Patch**: amend rather than add a commit, keep the `Change-Id`, fetch
  the current patch set first. Nothing in it says what changes when the commit
  belongs to somebody else.
- The two published skills disagree about whether the case is allowed at all.
  `typo3-core-patch-development` routes it — *"Somebody else's change picked up
  to be finished arrives that way"* — while
  `skills/typo3-core-patch-checkout/references/checklist.md` says a push of the
  carried state *"would be opening a patch set in somebody else's name — that
  belongs to the workflow that owns amending a change, and only where the change
  is yours to amend"*. The session read the second.
- No intent names the task. `amend` occurs once in
  `knowledge/task-intents.json`, inside the `submission` checklist, and on no
  intent's `match` or `matchWeak`.
- The intent a session does reach declares the task changes nothing.
  `patch-checkout` carries `changesNothing: true`, so a request landing on it by
  `patch set` or `cherry-pick` is answered with a brief routing only the
  workflows that change nothing —
  [`D-SKL-039`](../task-skills/skl-039-a-brief-that-changes-nothing-routes-only-the-workflows-that-change-nothing.md).
- The three ways in the checkout skill offers all end in the patch being read or
  tried: onto the branch it targets, into a worktree beside it, or onto current
  code as a commit of this session's own. Local work layered onto the patch set
  is none of them.
- What the session did instead is the whole of the report: it stashed against
  the skill's rule because the user asked for it, transcribed the files whose
  two bases it had verified identical, hand-resolved the rest, established that
  `--amend` keeps the author and moves the committer, and ended on
  `review/95369` with an amended commit — an ending the skill's mandatory undo
  does not allow for.

## Decided

- **Step 1a, taken on.** The answer is not here in any form, and the sentence
  nearest to it forbids the task. What the section says is a reading of TYPO3's
  own process rather than of this repository, so the reading is the card's first
  step —
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  does not reach it, because this run made no such lookup.
- The mechanics land in
  `knowledge/documents/core/contribution/gerrit-workflow.md`, beside **Update an
  Existing Patch**, and not as a fourth way in carrying its own commands. The
  checkout skill says why in its own second paragraph: the refs, the remotes and
  the commands are lookups, and a copy of them in a skill goes stale in somebody
  else's project with nothing to report it.
- The skills carry the routing and the stopping rule. The checklist's clause is
  rewritten to route rather than to forbid, since it is the sentence that
  stopped the session and it disagrees with the other skill in the same install.
- One case rather than three. The clean working tree the checkout skill demands,
  the ending it does not allow for, and the missing mechanics are the same task
  seen from three places: local work is this way in's material rather than its
  obstacle, and the work continuing on the review branch is how it ends.
- `normal`, not `low`. One session reported it, which is not what raises a card;
  what raises this one is that two published files contradict each other about
  it, and that a session followed the one that was wrong.
- `coveredBy: []`, because what would hold this is an assertion over the
  document and the skills, and it is written by the commit that writes them.

## Assumed

- That a patch set uploaded onto another author's open change is accepted TYPO3
  practice. The feedback states it and this run has not read it anywhere; the
  route rests on it entirely, which is why it is the first thing the card
  establishes.
- That the case is a section of the page rather than a document of its own. The
  page already carries the procedure end to end, and a second document would
  split a reader who was told to read this one whole.

## Wrong if

- The contribution guide answers a foreign change with a comment rather than
  with a patch set. Then what the corpus owes is the etiquette and a stopping
  rule, and the checklist's clause was right for the wrong reason.
- A session reads the new section and opens a second change anyway. Then the
  `Change-Id` half was delivered and did not take, and the lever is wording
  rather than the corpus.
- The route arrives and the clean working tree rule stops the next session all
  the same. Then the obstacle was the skill's precondition and the section
  behind it changed nothing.
- Everything the case needs turns out to be one sentence in the section that is
  already there. Then this made a section out of a sentence, and the cost falls
  on a reader who was told to read the page whole.

## Since then

The assumption was read on 2026-08-27 and holds. The contribution guide answers
a foreign change with a patch set — "You can even commit and contribute on other
people's patches - always make sure to ask first, before you do that" — and asks
a reviewer to push a coding guidelines fix rather than vote -1. Gerrit grants
`Add Patch Set` to registered users on `refs/for/*` by default, so nothing on
the review server separates the owner's push from anybody else's. A scratch
clone carrying the core's own `commit-msg` hook settled the rest: the amend
keeps the author and moves the committer, the `Change-Id` line survives it
single and unchanged, one commit stays one commit, and a cherry-pick answers the
same way. So the first **Wrong if** is answered and the other three are not —
each of those needs a session in the field. The empty `coveredBy` the entry
predicted is filled by the same commit, in the two assertions it names.

One bullet of the evidence was a misreading, and it stands rather than being
edited, because it is what the judging session read. `changesNothing: true` on
an intent does not declare that the task changes nothing: `TaskIntents::owned()`
reads it as leave to route in a brief that already changes nothing, and what
makes a brief one of those is the stated change type or an audit, triage,
operations or diagnosis intent. Landing on `patch-checkout` withholds no route
on its own.

The review `writing-a-skill.rst` asks for before publication was made on
2026-08-27, on the branch and before the merge. It returned one correction, the
author line, which is `D-KNW-131`; the fourth way in, the ask before the upload,
the file by file rule and the ending on the review branch were read and passed.
That is the first skill here reviewed by the maintainer before it shipped rather
than after.
