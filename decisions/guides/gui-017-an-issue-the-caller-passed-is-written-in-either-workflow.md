---
id: D-GUI-017
title: An issue the caller passed is written in either workflow
date: 2026-08-21
status: open
coveredBy:
  - CommitMessageTest::outsideTheCoreATrailerTheCallerWroteIsStillKept
  - CommitMessageTest::outsideTheCoreNoTrailerIsAddedAndNoneIsDemanded
---

# D-GUI-017 — An issue the caller passed is written in either workflow

**`Resolves:` and `Related:` carry the issues a call passed in either workflow,
and the wording says so.**

`D-GUI-010` measured the disagreement: a `project` call passing `issue` returns
a draft carrying `Resolves: #348` above a closing line saying the Forge issue
does not apply. A session in an extension repository read the parameters,
concluded the guide had no footer for the five pull requests its commit closed,
and committed without one.

## Evidence

- The trailer names are not the core's. `typo3/testing-framework` is developed
  on GitHub with its issues there, and its history carries 74 `Resolves:` and 24
  `Related:` lines — `Resolves: #732` in the newest of them — beside 9 `Fixes:`
  and 4 `Closes:`. Counted on 2026-08-21 in `.checkouts/testing-framework/main`.
- `Resolves: #348` is GitHub's own closing form. GitHub documents `close`, `fix`
  and `resolve` with their inflections as closing keywords, states that a colon
  may follow the keyword, and closes the issue when a commit carrying one lands
  on the default branch. Read on 2026-08-21 from GitHub's "Linking a pull
  request to an issue".
- The behaviour was deliberate and already guarded.
  `CommitMessageTest::outsideTheCoreATrailerTheCallerWroteIsStillKept` has held
  it since the project workflow existed, so what was wrong is what the tool says
  about itself and not what it writes.

## Decided

- Nothing changes in the draft. The repair is the tool description, the two
  parameter descriptions, the output schema and the closing line of the answer,
  each of which said the Forge issue does not apply where it meant that none is
  demanded.
- `R-AUD-003` states the half it left unstated, because a requirement saying
  what a workflow does not demand is what the wording was read off.
- The trailer names stay fixed. A repository whose tracker wants another word
  writes the message and passes it as `message`, whose trailers are all kept —
  which is one path rather than a second vocabulary to configure.
- The parameters keep their names and stay Forge-shaped in the core. `issue` is
  a number in whichever tracker the workflow belongs to, so nothing is renamed
  for the audience that is not the core's.

## Assumed

- A TYPO3 extension repository tracks its issues where its code is.
  `typo3/testing-framework` is the witness read here, and a repository on GitHub
  whose issues are on Forge would want the Forge number under the same trailer
  anyway.

## Wrong if

- A caller reads `Resolves: #348` in a project draft as a claim that a Forge
  issue exists behind it.
- Repositories outside the core are found writing something other than
  `Resolves:` and `Related:` for the issues their commits close, often enough
  that one form cannot serve both audiences.
- A `project` draft comes back carrying a trailer the call never passed.
