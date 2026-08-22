---
id: D-FBK-038
title: What decides a breaking removal is the caller, not the marker
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::whetherARemovalIsBreakingIsAnsweredWithoutTheMarker
---

# D-FBK-038 — What decides a breaking removal is the caller, not the marker

**Both tool-gap reports from the patch-review session are answered by the corpus
and the corrected boundary, and neither becomes a tool.**

`D-FBK-037` took the API-stability one on as a lookup. The core's own changelogs
say a lookup would answer beside the question.

## Evidence

- `Breaking-110319-RemovedUnusedInternalBootstrapMethods` removes three
  `@internal` methods of `Bootstrap` and is filed **Breaking**. Its **Affected
  installations** section names who called them — non-Composer bootstrap scripts
  — and says the extension scanner reports the usages.
- `Important-108796-InternalShortcutClassesRenamedToBookmark` renames
  `@internal` classes, says outright that "some extensions might have used them
  although they were internal", and is filed **Important**, with no Impact and
  no Affected installations section at all.
- So the marker is an input. A lookup answering `@internal: true` would have
  told the reviewer of 110319 that the removal was not breaking, which is the
  opposite of what the core concluded — a confident wrong answer in place of a
  reading.
- The corpus already answers the sibling question this way. The first statement
  of `deprecated-apis` sends the caller to read the `@deprecated` declaration
  itself rather than offering a lookup for it, and nobody has reported that as a
  gap.
- What was actually missing is reachable now and was not before:
  `bin/cli hints:probe "is removing this internal method breaking"` matched
  nothing, with every hint returned as the index.
- The reviewer was reading the class anyway. A patch review opens the changed
  file to review the diff in it, so the annotation costs no call — which is the
  case `D-FBK-027` names as not qualifying: a fact the caller reads once from
  its own checkout, and a cost that is the model's reading rather than its
  calls.

## Decided

- No API-stability lookup. `deprecated-apis` carries what `@internal` says, that
  it is read on the class and on the member, that it does not settle whether a
  removal is breaking, and what does — whether anything outside the core calls
  it, which is what a Breaking entry's Affected installations section states.
- The `breaking` intent settles that question before it says how to write the
  entry, because a wrong answer to it produces the entry.
- An absent annotation is written as not a statement either way, because that is
  the reading a marker-shaped answer invites.
- No git-reading tool, unchanged from `D-FBK-037` and for its reasons:
  `git show --name-only --format=%B HEAD` is one command the caller already
  runs, and git carries none of the access-path trap that decided the Forge
  case.
- Both feedback are answered and archived. The card `D-FBK-037` queued is
  deleted rather than rewritten — what it was to design does not exist.

## Assumed

- That the two changelogs are the rule rather than two picks. They are the two
  the corpus was checked against, and no sweep of every `@internal` removal
  across the covered majors was run.
- That a reviewer who is told the marker does not settle it will look for the
  call sites. The statement names the extension scanner and the Affected
  installations section so that step has somewhere to go.

## Wrong if

- A session reads the statement and files a removal of an `@internal` member as
  Important where the core would have called it Breaking, which would mean
  naming the caller as the test was not enough to make it happen.
- Somebody sweeps the `@internal` removals of a covered major and finds the
  marker does decide it, with these two as the exceptions. Then the lookup is
  back on the table and this entry is what was measured against.
- A review of a patch that is not checked out asks for the annotation and has no
  file to read, which is the case that would put both declined tools back
  together.
