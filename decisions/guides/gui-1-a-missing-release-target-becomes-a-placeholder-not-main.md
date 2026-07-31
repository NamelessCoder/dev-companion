---
id: D-GUI-1
date: 2026-07-29
status: standing
---

# D-GUI-1 — A missing release target becomes a placeholder, not `main`

**A release target the caller did not name is drafted as `RELEASE_TARGET` and
keeps its warning, rather than being filled in with a plausible default.**

`typo3_commit_message_guide` appended `Releases: main` when the caller named no
release, and warned `missing-releases` in the same answer. Two ways out: stop
warning, or stop filling in. It now fills in `RELEASE_TARGET` and keeps the
warning, the way a missing issue has always produced `Resolves: #ISSUE_NUMBER`.

- **Decided:** the draft states what it does not know, in the place where the
  answer belongs. A placeholder is visible in a `git commit` editor and in a
  diff; a plausible default is not, and `main` is the wrong answer for every
  backport.
- **Assumed:** the checks are read. A caller that copies the draft without
  reading them now commits `Releases: RELEASE_TARGET`, which is a worse commit
  message than `Releases: main` would have been — but a visibly broken one
  rather than a quietly wrong one, and Gerrit rejects it.
- **Wrong if:** the placeholder shows up in a pushed commit. Then the guide
  would have to refuse the draft outright instead of marking it.
