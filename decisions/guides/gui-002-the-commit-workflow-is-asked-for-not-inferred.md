---
id: D-GUI-002
date: 2026-07-29
status: open
---

# D-GUI-002 — The commit workflow is asked for, not inferred

**The commit workflow is an argument that defaults to `core`.**

A commit message carries no paths, and its subject text describes the change
rather than the repository it lands in.

`typo3_commit_message_guide` now takes `workflow: "core" | "project"`. Every
other tool that draws this line derives it — `Scope::isOutsideCore` reads the
paths and the task text. This one does not.

## Decided

- An argument, defaulting to `core`. A commit message carries no paths, and the
  one thing it does carry — the subject text — describes the change, not the
  repository it lands in. Inferring from it would be guessing from prose, which
  is exactly what R-SCO-001 exists to stop.

## Assumed

- The caller knows which repository they are committing in, and the pointer at
  the end of every answer is enough for them to find the other mode. The default
  is `core` because dropping rules must be something the caller asked for, never
  something a typo achieves.
- `[SECURITY]` stays refused for core work. The keyword exists in the core's
  history — the Security Team writes those commits — so its absence from the
  enum was a gap, but accepting it for a contributor would be a wrong answer
  with worse consequences than a missing one.

## Wrong if

- Agents commit in a project repository and never pass the argument, so the hard
  `missing-issue` error stays the normal answer there. The next step would then
  be for `typo3_task_guide`, which does compute `outsideCore`, to hand the
  workflow to the commit guide by naming it in the follow-up tool call it
  suggests.

## Since then

The **Wrong if** is two claims and only the second of them was settled on
2026-08-02. Driven over stdio from `/home/benji/projects/syntax`, one call to
`typo3_commit_message_guide` carrying `changeType` and `summary` and no
`workflow` answers as this entry says. The draft comes back with
`Resolves: #ISSUE_NUMBER` and `Releases: RELEASE_TARGET` in it, and the first
check is `ERROR: A Forge issue is required`. So where the argument is left out
in an extension repository, the hard error is the answer and the draft is not
one anybody can commit there.

Whether agents leave it out is the other claim, and it was not settled. It is
about behaviour, and no session outside this repository has been watched
committing. What could tell them was read instead. The tool description names
`workflow="project"`, and so does the `workflow` enum in the input schema; a
client that defers cannot call the tool without fetching that schema, so this
pointer arrives at the moment of use rather than at the start of the session.
That is the opposite of what `D-AUD-003` found for descriptions nobody has to
fetch. `knowledge/server-scope.json` names the argument in the covered topic,
and its routing entry — "Writing or amending the commit message" — does not.
`typo3_task_guide` outside the core named it in the checklist and left it out of
the follow-up calls printed underneath, which is one answer disagreeing with
itself about one step; the commit that writes this line closes that half and
`ScopeTest::theBriefPointsAtTheGuideForTheStepItEndsWith` holds both. The fourth
channel is the worst of them: none of the seven published skills names
`typo3_commit_message_guide` at all, so a session that reaches this server
through a skill has no route to the step at all, let alone to the argument.

The option this entry never weighed is the checkout rather than the message.
`Instance::describe()` already reports `kind` as a core checkout or a Composer
project, read from `composer.json` and not from prose, so the default could be
taken from the repository the server was started in. That is not the inference
`R-SCO-001` refuses. It is the weak evidence `D-SCO-005` is about, and it is
written down here as an option and not as a decision.

What would settle the first claim is one run: a session in `E-EXT` asked to fix
something and commit it, graded on whether it passed the argument. Nothing
produces that run today. The three forward reviews are reviews, `D-EVI-003` says
a review changes nothing, and so none of them ever reaches a commit. `EXT-03` is
the case that names this task shape, and a contract case is read rather than run
forward. The entry stays open until that run exists.
