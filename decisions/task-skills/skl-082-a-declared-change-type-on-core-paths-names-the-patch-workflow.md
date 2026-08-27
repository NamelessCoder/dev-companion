---
id: D-SKL-082
title: A declared change type on core paths names the patch workflow
date: 2026-08-27
status: open
---

# D-SKL-082 — A declared change type on core paths names the patch workflow

**A brief for a change to core files names `typo3-core-patch-development`,
whether or not the task sentence happens to carry a word the `patch` intent
matches.**

`skills` is read off confirmed intents alone, so a caller who describes the
defect instead of the work is told no workflow owns it.

## Evidence

- Measured on 2026-08-27 with `changeType="bugfix"` and
  `paths: ["typo3/sysext/workspaces/Classes/Service/WorkspaceService.php"]`.
  *add the missing language parameter to getMovedRecordsFromPages* answers
  `skills: []`, and so does *workspaces service does not filter moved records by
  language*. *fix a bug in WorkspaceService* answers
  `["typo3-core-patch-development"]`. The three describe one task.
- `feedback/2026-08-27-145332` names this as the concrete bug: "That field
  existing and being empty for the most obviously skill-covered task in the
  repertoire". The session had both skills in its listing and activated neither.
- `changeType` is not a guess. The caller declares it, `TaskGuide` already reads
  it for the checklist and the checks, and every value but `audit` and `triage`
  says a file is going to change.
- Which repository is being worked in is already established the same way, from
  the paths, and `TaskIntents::scoped()` uses it to drop or demote a core-only
  intent. The evidence is in hand at the moment `skills` is computed.

## Decided

- Core work that changes something names the patch workflow, from the declared
  `changeType` and the paths rather than from the sentence. What the intents
  name comes first and this is what is left when they name nothing.
- The intent's own confidence is untouched. Confirming `patch` from a
  `changeType` would pull its checklist and its checks into briefs that never
  described that work, which is a wider claim than naming the workflow that owns
  the file being changed.
- Nothing equivalent outside the core yet. Which workflow owns a change to a
  package depends on what the package is, and no single skill answers for it the
  way `typo3-core-patch-development` answers for `typo3/sysext/`.

## Assumed

- One session, and its account of what it would have done with the name. It
  reports activating no skill and doing the triage by hand; that the name alone
  would have changed it is its estimate.
- A caller passing `changeType` means it. The value is documented and the schema
  enumerates it, and a caller guessing one is the failure `D-GUI-011` measured
  from the other side.

## Wrong if

- A brief for a change to a core file names the patch workflow and a session
  reports it as noise — a one-line label fix told to carry a patch to review.
  Then what is missing is a size, which is `D-GUI-022`'s question.
- The name arrives and the skill is still not activated, which would say the
  `skills` field is not what a client reads and the lever is the instruction
  block instead — the half of this feedback that waits on the instruction-length
  measurement.
- The same emptiness turns up outside the core, which would say a skill is owed
  for a shape this entry declined to answer for.
