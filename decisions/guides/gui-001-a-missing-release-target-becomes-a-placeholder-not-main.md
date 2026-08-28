---
id: D-GUI-001
title: A missing release target becomes a placeholder, not `main`
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

## Since then

The markers are unmistakable, and the other path was not: a message carrying
both, handed back for checking, was answered "no readiness issues found" because
`parse()` read each placeholder as the answer it stands in for. That is the last
moment before a push at which anything here can speak, and the guide called the
message clean — the **Assumed** held for the caller who never checks and failed
for the one who did. `CommitMessage` drops both placeholders before the checks
run now. The **Wrong if** stays unguarded.

## Since then

A third field of the same kind does not fit the mechanism. `isBreaking` is an
input the tool never sees the diff for, so a review that passed a whole message
without it was answered with nothing at all — and the caller who has not yet
classified the change is the one most likely to ask. A placeholder cannot carry
it, because the answer's place in the subject is the absence of a marker, so the
statement has to move into the checks. `R-GUI-011` is what must hold, queued
rather than made because it adds a check code in `src/`.

## Since then

The check exists as `breaking-not-assessed`, and building it took one thing the
placeholder mechanism never needed: a third value. `false` and "not supplied"
had been the same answer everywhere, so `isBreaking` is `?bool` and `null`
throughout, and the declared default came off the input schema — a client
materialising it would have sent the assumption back as an answer and silenced
the check for the caller it is written for. Re-read on 2026-08-22, the
placeholders are still dropped before the checks; the **Wrong if** needs one
seen in a pushed commit.
