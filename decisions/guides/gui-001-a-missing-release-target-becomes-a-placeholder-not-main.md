---
id: D-GUI-001
date: 2026-07-29
status: open
---

# D-GUI-001 — A missing release target becomes a placeholder, not `main`

**A release target the caller did not name is drafted as `RELEASE_TARGET` and
keeps its warning, rather than being filled in with a plausible default.**

`typo3_commit_message_guide` appended `Releases: main` when the caller named no
release, and warned `missing-releases` in the same answer. Two ways out: stop
warning, or stop filling in. It now fills in `RELEASE_TARGET` and keeps the
warning, the way a missing issue has always produced `Resolves: #ISSUE_NUMBER`.


**Since then** the half of that this repository can see was read: what the draft
itself does. The markers are unmistakable — neither `RELEASE_TARGET` nor
`#ISSUE_NUMBER` has the shape of a release target or a Forge issue, which is
what `main` failed at and the reason the placeholder was chosen. The other path
was not. A message carrying both, handed back to `typo3_commit_message_guide`
for checking, was answered with "No commit message readiness issues found":
`parse()` read each placeholder as the answer it stands in for, and the checks
then had nothing to report. That is the last moment before a push at which
anything here can speak, and it was the moment the guide called the message
clean — the **Assumed** above held for the caller who never checks and failed
for the one who did. `CommitMessage` now drops both placeholders before the
checks run, so the field reports missing again and the corrected draft still
carries the marker. The **Wrong if** itself stays unguarded: no pushed commit
has been seen either way, and only a forward run against a real contribution
would produce one (2026-08-02).

## Decided

- The draft states what it does not know, in the place where the answer belongs.
  A placeholder is visible in a `git commit` editor and in a diff; a plausible
  default is not, and `main` is the wrong answer for every backport.

## Assumed

- The checks are read. A caller that copies the draft without reading them now
  commits `Releases: RELEASE_TARGET`, which is a worse commit message than
  `Releases: main` would have been — but a visibly broken one rather than a
  quietly wrong one, and Gerrit rejects it.

## Wrong if

- The placeholder shows up in a pushed commit. Then the guide would have to
  refuse the draft outright instead of marking it.
