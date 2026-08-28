---
id: D-SKL-041
title: A patch carried onto current code is carried on a named branch
date: 2026-08-14
status: open
coveredBy:
  - KnowledgeTest::theCarryOntoCurrentCodeNamesTheBranchItLandsOnAndTheUndo
---

# D-SKL-041 — A patch carried onto current code is carried on a named branch

**`typo3-core-patch-checkout` gains a way in whose result is a local branch
named for the change, and an undo that deletes it.**

The two ways in it has are both about reading the patch as its author wrote it,
so every local result is disposable and the skill says so in as many words. A
request to cherry-pick a change onto current `main` and review it there makes
that local result the thing the work is about, and the skill argues against
giving it a name.

## Evidence

- `feedback/2026-08-13-214754`: the session was asked to review change 93319 and
  cherry-pick it onto `main`. Neither way in fitted, so it detached onto `main`
  and cherry-picked there, leaving a new commit on no branch. The maintainer
  corrected it mid-session — a checkout that cherry-picks onto `main` always
  gets a review branch — and the rest of the review ran from `review/93319`.
- `knowledge/task-intents.json` already matches "cherry-pick" and "cherry pick"
  onto `patch-checkout`. The routing fired and the skill it fired into has no
  form for the request, so this is not a routing failure.
- `bin/cli hints:probe "cherry-pick a gerrit patch onto main review branch"`
  matched nothing, and the one cherry-pick the corpus documents is a merger
  backporting a merged commit onto a release branch
  (`knowledge/documents/core/contribution/gerrit-workflow.md`), which is
  authoring rather than reading. Nothing here covers a patch set carried
  locally.
- The detach rationale stands twice — in the skill's "Fetch and apply" and in
  that document — and both state it without its scope. It is about the fetched
  object, and a commit this session made is not one.
- The gap reaches the rebase path the skill already has. "Put the checkout back"
  step 2 says leaving the detached commit "loses nothing that is not still on
  the review server", which stopped being true the moment a rebase or a
  cherry-pick produced a commit that exists only locally.
- One session behind it: the five feedback of 2026-08-13 are one review of one
  change. What carries the shape is the maintainer's correction, not a count.

## Decided

- Ladder step 1b, the shape is missing, and queued rather than closed on the
  spot: a published skill is a contract installed into somebody else's project,
  where no release here corrects it — `D-SKL-021`.
- The result of carrying a patch onto current code is named. That is the
  boundary: where the local commit is what the rest of the work reads, it stops
  being disposable, so it has a branch, the undo deletes that branch explicitly,
  and the answer says the branch is gone rather than that the checkout is
  attached again.
- The findings say which commit they are about. A cherry-pick's hash is not the
  patch set's, and a review quoting the local one without saying so is
  unattributable.
- Where the two halves land is fixed by rules already here: the command form and
  the scope of the detach rationale go to
  `knowledge/documents/core/contribution/gerrit-workflow.md`, because a skill
  carries no command the checkout has not been asked for, and the way in, the
  naming and the undo go to the skill.
- Priority `normal`. The intents file promises the path, so a session that asks
  for it by name is routed into a skill that steers it away from the answer.
- Left to the reading: what the branch is called, and whether the existing
  rebase path is folded into the new way in or stays beside it. Both are
  readable — the contribution guide's cherry-pick page and the skill itself —
  and neither is settled by this judgement.

## Assumed

- The correction is the practice rather than one review's convenience. It came
  from the person who maintains this repository, which is the strongest source a
  judgement here has and still one session.
- Naming the local result costs less than it saves. A branch that outlives the
  work is a state the next session cannot tell from its own, which is the
  failure the detach was preventing, moved one step along.

## Wrong if

- A recorded run takes the new way in and leaves the branch behind, so the state
  the detach rule guarded against arrives under a name.
- A later session is corrected the other way — that the local carry wanted no
  branch — which would say the correction was about that one review.
- Reviews start quoting the local hash because there is now a local name for it,
  which is the attributability this decision claims to improve, lost the way it
  was gained.
- Sessions ask for the cherry-pick and the skill's rebase path already answers
  them, which would say this was a wording fix to one paragraph and not a way
  in.

## Since then

Both questions were read and answered. The contribution guide's cherry-pick page
names no branch at all — it cleans the checkout onto `main` and runs the line
Gerrit's Download menu copies, so the commit lands there. That is worse than
what the skill prescribed rather than a source for the name, so the branch name
is the maintainer's correction written down. The rebase path is folded into the
new way in rather than left beside it: every core patch is one commit, so
rebasing it and cherry-picking it onto current code produce the same thing, and
keeping them apart would state the boundary twice — the stale copy was the
rebase one.
