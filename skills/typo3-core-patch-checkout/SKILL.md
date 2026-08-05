---
status: draft
name: typo3-core-patch-checkout
description: Get a patch that is under review on review.typo3.org into a core checkout and onto the branch it targets — find the change, fetch the patch set, put it on current code, and rebase it where the branch has moved under it. Use for trying out somebody's patch, for reading a change against the code it applies to, for checking whether a patch still applies at all, and for picking up a change to carry on with. It stops rather than improvises: a conflict it cannot resolve from the change itself, a checkout that is not clean, or a patch set that is not the current one ends the work with what it found.
---

# TYPO3 Core Patch Checkout

Put one change under review into the checkout, in the state it is actually in.
Keep this skill as routing and stopping rules; the refs, the remotes and the
commands are lookups, and a copy of them here goes stale in somebody else's
checkout with nothing to report it.

The reason this is a workflow of its own is what it must not do. A patch that no
longer applies is a finding — the branch moved under a change nobody rebased —
and a session that quietly resolves its way to something that compiles has
destroyed that finding and produced a patch nobody wrote. So every step below
has an end, and reaching one is a result.

## Establish the change before touching the checkout

1. Work through [references/base.md](references/base.md), which fixes the order
   every task here starts in.
2. `typo3_gerrit_lookup` with the change number or the Change-Id, or with the
   issue number where the change was reached through its issue. Four things it
   answers decide everything below: the **branch the change targets**, which is
   what it has to be applied onto and is regularly not the one you are standing
   on; the **patch set that is current on the server**, because an older one is
   still fetchable and applying it silently reviews a revision nobody is looking
   at; the **status**, since MERGED and ABANDONED are both answers that end the
   work; and the **commit**, which is what says afterwards whether the checkout
   holds the revision under review.
3. `typo3_rule_lookup` for the Gerrit workflow. It carries the ref a patch set
   is fetched by, and the one thing about fetching a core change that is not
   guessable: which remote the ref is on, which is not the one the checkout
   fetches from.

## Before the checkout is changed

Establish these three, in this order, and stop at the first that fails.

- **The working tree is clean.** Uncommitted work and a fetched patch set on top
  of each other cannot be told apart afterwards, and what a rebase does to the
  mixture is not recoverable from the checkout alone. Stop and say what is
  uncommitted; do not stash it as a convenience.
- **The target branch is there and current.** A change targeting a release
  branch rebased onto the wrong one produces conflicts that are an artefact of
  the mistake, and they look exactly like a stale patch.
- **Where you are is where you will be able to get back to.** Write down the
  commit the checkout is on before anything moves it.

## Fetch and apply

Fetch the patch set the server says is current, and put the checkout on it. It
is somebody else's commit and belongs on no local branch of yours until you have
decided to carry it on.

Then establish what you are holding before judging anything about it: the
checkout's commit is the change's current revision, or it is not, and only the
second needs explaining.

## Rebase only where the branch moved

Rebase when the change does not sit on current code and the work needs it to —
running the suites, reading it against code that has since changed. Not as a
matter of course: a patch read against the code it was written on is the patch
its author wrote, and moving it is a step that can go wrong.

Where it rebases clean, say so. That is itself an answer about the change: it
still applies.

Where it conflicts, [references/checklist.md](references/checklist.md) is what
decides whether to resolve or to stop, one conflict at a time. Read it at the
first conflict rather than after resolving a few — the rule it carries is about
what you are allowed to know, and it cannot be applied backwards.

## Stopping is the normal ending

When a rule above ends the work, undo what was started rather than leaving the
checkout half-way: abort the rebase, return to the commit written down at the
start, and say what state it is in now. A checkout left mid-rebase is a trap for
whoever opens it next, including you.

Report what was found and not what was attempted. The change, its patch set, its
target branch, how far it got, and the specific thing that stopped it — the
files that conflicted, the hunks, and why the change alone did not decide them.
That report is the useful outcome, and it is what somebody rebasing the patch
properly starts from.

## Once it is in and applies

`typo3_test_run_guide` with the paths the change touches names the suites that
can fail on it and their targeted invocations. Run them through the checkout's
own runner: a suite run through an installed binary is a result nobody can
reproduce, and a check that inspected no files is not a green.

Say which branch and which patch set every result is about. A green reported
without them is unattributable the moment a new patch set is pushed.

This skill owns getting a change under review into a checkout and onto code it
applies to: finding it, fetching the patch set, rebasing it where that is what
the work needs, resolving what the change itself decides, and stopping where it
does not. It owns the undo as much as the do. It does not own judging the patch
— where the request is to say what is wrong with it, `typo3-core-patch-review`
owns that, and it starts from the checkout this leaves behind. It does not own
changing the patch either: amending a change into a new patch set and pushing it
belongs to `typo3-core-patch-development`, and carry over the change number, the
patch set that was fetched and whether it needed a rebase to apply.
