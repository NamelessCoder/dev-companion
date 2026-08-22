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

**Since then** a third field of the same kind was found, and the mechanism above
does not reach it. `isBreaking` is something the guide cannot know either: it is
an input, and the tool never sees the diff. A core patch review passed the whole
message of `9f6c6eb9093` with no `isBreaking` argument. The answer was
`no-issues-found` and nothing else. That patch removes a protected method from a
class that is neither `final` nor `@internal`, so whether the subject owed
`[!!!]` was the open question of the review — and a caller who had not yet
classified the change is the one most likely to ask
(`feedback/2026-08-03-144432`). A placeholder cannot carry this field. The
answer's place in the subject is the *absence* of a marker, so there is nothing
to write `RELEASE_TARGET` into, and the statement has to move out of the draft
and into the checks. Re-run on 2026-08-03 against the code as it stands: that
same message now returns five `body-lines-reflowed` infos and no
`no-issues-found` at all, and a short-bodied core message still returns
`no-issues-found` alone — so the silence about the classification survives both
answers and is not what the clearance wording is doing. `R-GUI-011` is what must
hold; the judgement queued it rather than making it, because the change is in
`src/` and adds a check code.

**Since then** that check exists as `breaking-not-assessed`, and building it
took one thing the placeholder mechanism never needed: a third value. `false`
and "not supplied" had been the same answer everywhere — `create()` read
`$input['isBreaking'] ?? false` and `parse()` wrote the key from the subject —
so the field is now `?bool` and `null` all the way through, which is the only
form in which the checks can tell a caller who classified the change from one
who never did. `default: false` came off the input schema in the same move: a
client that materialises a declared default would have sent the assumption back
as an answer and silenced the check for exactly the caller it is written for.
`isDeprecation` shares the one check rather than getting a second of its own —
nothing in a subject answers it either way, so a separate check would have fired
on almost every core call and said what this one already says (2026-08-03).

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

Re-read on 2026-08-22 and nothing has moved. `CommitMessage` still drops both
placeholders out of the parsed input before the checks run, so a draft handed
back for checking reports its fields missing rather than clean. **Wrong if**
needs a placeholder seen in a pushed commit, and only a forward run against a
real contribution would produce one.
