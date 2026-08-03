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
message without a `Resolves:` line) and a `pre-commit` hook that runs local
checks. The hook file itself lives in `Build/git-hooks/commit-msg` and can also
be copied manually:

```bash
cp Build/git-hooks/commit-msg .git/hooks/commit-msg && chmod +x .git/hooks/commit-msg
```

Without the hook, the first push is rejected because the commit has no
`Change-Id`.

## Push a Patch for Review

```bash
git push origin HEAD:refs/for/main
```

`refs/for/<branch>` is Gerrit's magic ref: it opens a review instead of writing
to the branch. Push a work in progress with `%wip`:

```bash
git push origin HEAD:refs/for/main%wip
```

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
- Before amending, fetch the current patch set from Gerrit (the "Download" menu
  of the change offers the `git fetch … && git cherry-pick FETCH_HEAD` command)
  when it is not already the local commit.

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
