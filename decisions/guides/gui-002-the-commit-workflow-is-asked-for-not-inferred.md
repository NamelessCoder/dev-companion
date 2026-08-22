---
id: D-GUI-002
date: 2026-07-29
status: revoked
revokedBy: D-GUI-010
---

# D-GUI-002 — The commit workflow is asked for, not inferred

**The commit workflow is an argument that defaults to `core`.**

A commit message carries no paths, and its subject text describes the change
rather than the repository it lands in.

`typo3_commit_message_guide` now takes `workflow: "core" | "project"`. Every
other tool that draws this line derives it — `Scope::of()` reads the paths and
the task text. This one does not.

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
produced that run, because the three forward reviews are reviews, `D-EVI-003`
says a review changes nothing, and `EXT-03` is a contract case, which is read
rather than run forward. What that run may be was answered on 2026-08-03. A
session may change code and commit in one of the checkouts, and this server may
do neither itself: it says what is to be done, it validates, and every tool but
`typo3_feedback_record` carries `readOnlyHint`. The run is driven ad hoc and
recorded as a feedback, so neither `D-EVI-001` nor `D-EVI-003` is opened for it,
and `todo/open/2026-08-01-200601` is what carries it. The fourth channel moved
on the same day: the two core skills were published and both name the tool, and
the seven the reading above counted still do not. A session in an extension
checkout reaches for one of those seven, so the hole the run has to be designed
around is where it was.

That run happened on 2026-08-04, in `/home/benji/projects/syntax`, and it
answers the first claim in a way this **Wrong if** did not anticipate. A session
was handed a bug report against that extension and told to reproduce it, fix it
and commit it. It had this server on stdio, all 26 tools in its context and the
nine published skills beside them. It made 37 tool calls, every one of them
Bash, Read, Edit or Write, and called none of the 26. No skill activated. It
reproduced the defect in a headless browser against the DDEV frontend, fixed one
line of TypoScript, ran the two checks that repository declares, and committed
`[BUGFIX] Load Prism toolbar plugin before show-language` over a wrapped body
with no `Releases:`, no Forge trailer and no `Change-Id`.

So the argument was left out, and the hard error the second claim measured never
appeared, because the tool was never called. The route decides this before the
default does. `typo3_task_guide` handing the workflow to the commit guide — the
next step this entry proposes — is reached only by a session that calls
`typo3_task_guide`, and this one did not. Naming the tool where an extension
author already is, rather than in the two core skills alone, is what
`feedback/2026-08-04-012644` asks for from the same run.

Two readings from that day stand beside it. The default is unchanged: called
with `changeType` and `summary` and no `workflow`, the guide still answers with
`Resolves: #ISSUE_NUMBER`, `Releases: RELEASE_TARGET` and the hard
`missing-issue` error, re-measured over stdio against the build the run used.
And the message that landed is what `R-AUD-003` and `R-GUI-002` exist to
produce, reached from the session's own habits, so it is not evidence for this
server. The entry stays open, and what would settle the claim it was written
about is a session that reaches the commit guide in a project repository at all.

That feedback was judged on 2026-08-04 as a delivery failure rather than a wrong
default, and
[`D-SKL-014`](../task-skills/skl-014-the-commit-step-is-named-where-a-skills-workflow-ends-in-a-change.md)
is where the placement it asks for is decided.

## Revoked on 2026-08-04

The first half of the **Wrong if** is what this entry was revoked on, and the
measurement recorded above is what settled it: without the argument the answer
in a project repository is `Resolves: #ISSUE_NUMBER`, `Releases: RELEASE_TARGET`
and a hard `missing-issue` error. The judgement of 2026-08-04 read that as a
delivery failure rather than a wrong default, and the maintainer read it the
other way — three audiences reach this server and one of them has a Forge issue.

What holds instead is
[`D-GUI-010`](gui-010-the-commit-workflow-defaults-to-the-repository-most-callers-are-in.md),
whose **Wrong if** is a different list: what can go wrong now is a core patch
whose missing trailer nobody names, not a project message nobody can commit. The
argument itself survives — the workflow is still asked for and still not
inferred from the subject text.
