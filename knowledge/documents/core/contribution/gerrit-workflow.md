---
description: >-
  How a core patch reaches review: the one-time git setup, fetching a patch set into the checkout, pushing, amending a patch set, and backporting.
whenToUse: >-
  When a change is ready to leave the checkout, when a patch under review has to be read or tried out locally, or when a patch already under review has to be changed.
hints: []
---

# TYPO3 Gerrit Workflow

Source: https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/

TYPO3 core patches are not submitted as pull requests. They are pushed to Gerrit
at https://review.typo3.org, one commit per patch, and improved by amending that
commit.

## One-Time Setup

The push URL points at Gerrit while fetching still uses GitHub:

```bash
git remote set-url --push origin ssh://<username>@review.typo3.org:29418/Packages/TYPO3.CMS.git
```

Gerrit identifies a patch by the `Change-Id:` line in the commit message, which
the `commit-msg` hook adds. Install the hooks once per clone:

```bash
composer gerrit:setup
```

This enables the `commit-msg` hook (adds the `Change-Id`, rejects a commit
message with a line longer than 72 characters, and rejects one without a
`Resolves:` line) and a `pre-commit` hook that runs local checks. The hook file
itself lives in `Build/git-hooks/commit-msg` and can also be copied manually:

```bash
cp Build/git-hooks/commit-msg .git/hooks/commit-msg && chmod +x .git/hooks/commit-msg
```

Without the hook, the first push is rejected because the commit has no
`Change-Id`.

## Where This Checkout Pushes

The command above is the writing side. Where a checkout is already pointed is
four questions of its own, and neither the directory name nor the URL it fetches
from answers them — the sources are hosted on GitLab and mirrored to GitHub,
which is what a clone usually fetches, while Gerrit is coupled to GitLab.

```bash
git remote -v
git config --get remote.origin.pushurl
git config --get remote.origin.push
cat .gitreview
```

- `git remote -v` prints a `(fetch)` and a `(push)` line per remote. The two
  differ only where a push URL is set, so the same URL twice means a push goes
  back where the clone came from.
- `remote.origin.pushurl` is that push URL, and `git remote set-url --push` is
  the command that writes it. Unset, the read prints nothing and exits non-zero,
  and the fetch URL is what a push uses — a clone in that state cannot open a
  change on Gerrit at all, which is what a checkout kept for looking things up
  is normally in.
- `remote.origin.push` is the refspec a bare `git push` uses. Set to
  `+refs/heads/main:refs/for/main`, `git push` on its own goes to Gerrit; unset,
  it addresses the branch, which Gerrit rejects.
- `.gitreview` in the checkout root carries `host`, `port`, `project` and
  `defaultbranch` under a `[gerrit]` heading. It is the `git-review` tool's
  configuration and git itself never looks at it, so it says where the project
  lives on Gerrit even in a clone whose remote points nowhere near it. With an
  account name those values are the push URL:
  `ssh://<username>@<host>:<port>/<project>.git`.

## Fetch a Change Into This Checkout

A patch under review is a ref named after the change's own number, and every
patch set keeps its own:

```bash
git fetch <the Gerrit URL above> refs/changes/<last two digits>/<change>/<patch set>
git switch --detach FETCH_HEAD
```

`refs/changes/02/95102/2` is change 95102, patch set 2, filed under the last two
digits of the number. Patch set 1 stays fetchable after 2 is pushed, so a review
comment about an earlier revision can be read against the revision it was
written on. There is no ref for "the current one": the patch set number is read
off the change rather than defaulted to, and the only other ref a change carries
is `meta`, which is its review history and not a commit anybody builds on.

**The ref is on Gerrit and not on GitHub.** A core clone fetches from the mirror
and pushes to the review server, so `git fetch origin refs/changes/…` reports
that the ref does not exist in a checkout whose push would reach it — the same
asymmetry as above, from the reading side. What to fetch from is
`remote.origin.pushurl`, or a remote of its own pointed at the review server.
Measured on 2026-08-05: `refs/changes/02/95102/2` resolved over the Gerrit URL
and returned nothing at all over the GitHub one.

A fetched patch set is somebody else's commit and is not on a branch. Detaching
onto `FETCH_HEAD` says that in the checkout, which is what keeps a local branch
from quietly acquiring a commit that belongs to a review.

## Push a Patch for Review

```bash
git push origin HEAD:refs/for/main
```

`refs/for/<branch>` is Gerrit's magic ref: it opens a review instead of writing
to the branch. A plain push like this one publishes the change to everyone who
can read the project and puts it in front of reviewers. The two forms that do
not are below, and which of the three a change wants is the author's to say.

## Push a Private or Work in Progress Change

```bash
git push origin HEAD:refs/for/main%private
git push origin HEAD:refs/for/main%wip
```

Both are Gerrit push options appended to the magic ref. Git carries the string
and does not read it, so the form is the same from any client and any checkout.

- `%private` decides **who can see the change**: its owner, the reviewers added
  to it, and accounts holding the View Private Changes capability, and nobody
  else. It is not a quieter way to ask for review — somebody who was not added
  by name cannot reach the change at all.
- `%wip` decides **who is asked to act**. A work in progress change is visible
  to anyone, but it notifies no reviewer, stays out of reviewers' dashboards,
  and sends nothing on most later operations. It says to the review server what
  `[WIP]` in the subject says to a reader.

Coming back out is not symmetrical:

- `%private` sticks. A further patch set pushed without the option does not
  publish the change; `%remove-private` is what removes the flag.
- `%wip` comes off with `%ready` on a push, or with the change's own "Start
  Review" button. Only the change owner, project owners and the administrators
  of the review server may use `%wip` and `%ready` on a push.

What `%private` does not do:

- It does not survive the merge. A private change that is merged becomes visible
  to everyone who can read the target branch and loses the flag, so it is no way
  to land a security fix quietly.
- It does not hide from a follow-up. A non-private change pushed on top of it
  makes the private parent visible through the parent relationship.
- It does not hide the commit from whoever knows its id. Anyone who could
  otherwise see the change can fetch the commit by its hash.

A Gerrit account can also be set to open every new change as work in progress,
so what state a push produced is read off the change rather than off the command
that made it.

## Pushing From a Git Worktree

Nothing about the push changes in a `git worktree`: same remote, same magic ref,
same options.

```bash
git push origin HEAD:refs/for/main%private
```

`HEAD` resolves to that worktree's own commit, while the remotes, the
configuration and the hooks live in the one git directory every worktree of a
clone shares. So `git config --get remote.origin.pushurl` answers the same in
all of them, and the `commit-msg` hook installed once for the clone runs for a
commit made in a worktree and writes its `Change-Id` there;
`git rev-parse --git-path hooks` names the directory it is taken from.

What decides the push is the branch point rather than the worktree: what goes up
is `HEAD` and every ancestor the target branch does not already have. A worktree
branched off an up-to-date `origin/main` sends one commit, one branched off a
local `main` that is behind sends what lies between as well, and
`git log --oneline origin/main..HEAD` is what says which of the two this is.
Where a worktree does change the answer is the checks: a suite that takes its
file list from git can report success having inspected nothing in one.

## Update an Existing Patch

Every patch is exactly one commit. Improvements never add a second commit — the
existing one is amended and pushed again, which creates a new patch set:

```bash
git commit --amend -a
git push origin HEAD:refs/for/main
```

- Keep the `Change-Id:` line untouched. It is what links the new patch set to
  the existing review; a changed or removed `Change-Id` opens an unrelated
  second change.
- Earlier patch sets are never overwritten. Reviewers can diff between them, so
  amending is safe.
- Before amending, fetch the current patch set when it is not already the local
  commit. Which ref that is and where it is fetched from is above.

## The Forge Issue a Change Hangs Off

Every core change names its issue in the commit message with
`Resolves: #<issue number>`, and the `commit-msg` hook checks that such a line
is there — a number, not what state the issue is in. Gerrit does not ask Forge
either, so a push against a closed issue is refused by nothing and opens a
change that looks like any other.

Forge is the side that notices: an issue gets a comment from Gerrit Code Review
linking the change when its first patch set arrives, and that is what moves a
report to "Under Review". Whether that also happens on a report that is closed
is a property of the tracker's own workflow and is not established here.

Closed is what Forge uses for a report that is outdated, no longer reproducible,
outside the project's goals, left without feedback, or attached to an abandoned
patch. A change hanging off one asks whoever looks at it to reverse a decision
the issue already records, without saying so anywhere in the change itself. So
the closure is settled before the push rather than after it: reopened where the
reason no longer holds, which is the tracker's step and may need whoever closed
it, or answered on the change with why it stands anyway. No rule requires the
reopening — anyone may contribute a patch to any issue — and what skipping it
costs is the first question anybody reading the change will ask.

## Release Branches and Backports

```bash
git push origin HEAD:refs/for/13.4
```

Push to a release branch only when the bug exists there and not on `main`. In
the normal case the patch targets `main` and the merging core team member takes
care of the backport. The `Releases:` line in the commit message names the
branches the change is meant for.

A backport is a cherry-pick of the merged commit onto the release branch,
usually started from Gerrit's "Cherry pick" action. The `Change-Id` of the
original change is kept unchanged — that is what lets Gerrit link the backport
to it. Everything below the `Change-Id` line (`Reviewed-by:`, `Tested-by:`, and
the blank lines) is removed, and the code is adjusted to the older branch where
it no longer applies cleanly.
