---
id: D-SKL-068
date: 2026-08-21
status: open
---

# D-SKL-068 — An audit's list is established against the work already in flight

**`typo3-extension-health` establishes per item, before the list is shown, what
the repository already carries unmerged, and the surface it asks is wider than
the open pull requests.**

The skill owns the gate at which a list is agreed. A list proposing work that is
already done on a branch nobody merged is one a maintainer cannot agree to, and
nothing between writing the list and showing it asks the question.

## Evidence

- The feedback of 2026-08-19 09:43, from a full audit of a blog extension before
  its v14 release. The run mapped 23 open pull requests against its 17 items and
  reported item 2 as untouched by any of them. The maintainer then named a
  branch it had not looked at: `git branch -a` turned up 13 pushed branches with
  no pull request at all, one of which carried item 2 already fixed, with the
  same diagnosis the run had reached independently and with the test the run had
  found missing. That branch also carried two v14 defects the audit had not
  found.
- What the skill says today. Steps 5 to 7 write the list, show it and keep it in
  the session; steps 8 to 12 work it off. `pull request` appears in step 11,
  which asks the maintainer where the commits land, and in the closing boundary
  paragraph, which sends one proposed change to `typo3-extension-patch-review`.
  Neither is the question, and no step between 5 and 6 asks it.
- The audit brief is silent too. The `audit` intent in
  `knowledge/task-intents.json` carries six checklist items — what a change
  removes, what a core removal owes, the two `runTests.sh` checks, the finding
  gate, the declared checks, and handing each finding over with its consequence.
  None of them names existing work.
- **The same failure was decided once already, one audience over.** `D-SKL-008`
  and `R-SKL-014` put the issue and the review server into the core patch review
  because the third recorded `REVIEW-03` run judged a patch as a change standing
  alone while it was part 2 of a series whose part 1 was already in
  `origin/main`. The change in hand read as the whole of the work is the same
  mistake in a different workflow.
- A third arrival, the feedback of 2026-08-21 07:40: `typo3_gerrit_lookup`
  answers one change and not its relation chain, so 91563 read alone says a
  feature exists and read as a stack of fifteen says what it consists of, which
  parts are merged and which are abandoned. Its lever is a tool rather than a
  skill, so that card stays its own and is not taken over here.

## Decided

- The gap is real and the answer is *queued* rather than closed on the spot:
  what changes is a skill's contract, which lands in somebody else's project and
  is reviewed rather than improvised.
- The step sits between writing the list and showing it. The list is what gets
  agreed, so the state belongs on the item and not in a paragraph beside it.
- **The surface is stated in the skill, because the obvious reading is too
  narrow.** Open pull requests, branches pushed without one, and the maintained
  release lines. The branch with no pull request is the one that was missed, and
  it is where a maintainer's own unfinished work sits.
- The method is not decided here. Whether a git command belongs in a published
  file at all, and which one, is the todo's first step: the run reported that
  `git cherry` compares patch-ids and calls a squash-merged branch outstanding,
  that a two-dot diff against the base is dominated by how far behind the branch
  is, and that what settled it was a two-dot diff restricted to the files the
  branch touches — reached in four attempts. None of that is verified here, and
  a command written into a skill is not corrected by the next release of this
  server.
- Priority `normal`, above the unjudged cards and below the decided work. What
  sets it is three arrivals at one shape across two audiences, one of which
  already cost a wrong statement to a maintainer; what keeps it off `high` is
  that a single measurement is one repository.

## Assumed

- That the repository half stays the session's own to establish.
  `skills/base.md` already says this server does not read the working tree and
  that the branch is the caller's, so the step instructs rather than routes to a
  tool this server would have to grow.
- That an extension audit usually runs where something is unmerged. The one
  measurement is a repository with 23 open pull requests and 13 pushed branches,
  which is not evidence about the ordinary case.

## Wrong if

- Most audits run on repositories with nothing unmerged. Then the step costs a
  reading per item and returns nothing, and what it should have been is one
  question about the repository rather than a state on every item.
- A session drops a finding because a branch claims to fix it. An unmerged
  branch is a claim and not a fix, and the checklist's *What a dropped candidate
  owes* is the bar it would have to clear.
- A recorded run follows the method the skill ends up stating and reports a
  landed branch as outstanding, or an outstanding one as landed. That would say
  the method belongs where a release can correct it rather than in a published
  file.
- The same omission is reported next from `typo3-core-patch-review` or
  `typo3-extension-patch-review`. Then the rule belongs in `skills/base.md`,
  where every workflow reads it, rather than in the one skill that was asked.
