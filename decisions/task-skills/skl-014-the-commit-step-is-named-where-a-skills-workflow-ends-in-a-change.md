---
id: D-SKL-014
date: 2026-08-04
status: open
---

# D-SKL-014 — The commit step is named where a skill's workflow ends in a change

**A published skill whose workflow ends in a change to the repository names
`typo3_commit_message_guide` with `workflow="project"`, and the routing entry
for the commit message names that argument.**

Three of the four channels that could carry the step already do, and the fourth
is the one an extension author arrives through. A session fixing a bug in its
own extension reaches the commit from its own habits, and a conforming message
that came from those habits is not something this server may be credited with.

## Evidence

- **The run.** `feedback/2026-08-04-012644`, `/home/benji/projects/syntax`,
  `bk2k/syntax` 5.0.0 on TYPO3 14.3.0 under DDEV, this server on stdio with all
  26 tools and the nine published skills in the session's context. Told to
  reproduce a frontend defect, fix it and commit it, it made 37 calls — every
  one of them Bash, Read, Edit or Write — called none of the tools and activated
  no skill. The message that landed conforms; it came from the session's own
  habits, so it is evidence about the model and not about this server.
- **The fourth channel, read in this checkout on 2026-08-04.** Only
  `skills/typo3-core-patch-development` and `skills/typo3-core-patch-review`
  name `typo3_commit_message_guide`, and both open with the core's Gerrit
  workflow. None of the seven extension-facing skills carries a commit step at
  all: the single line about committing anywhere in them,
  `skills/typo3-extension-testing/references/static-quality.md`, says to keep a
  formatting pass in its own commit and routes nowhere.
- **The other three carry it.** Driven over stdio the same day,
  `typo3_task_guide` with this feedback's own task text and
  `Configuration/Sets/Base/setup.typoscript` names the guide twice — in the
  checklist and again under "Next lookups for this task", both times with
  `workflow="project"` and with the reason the default is wrong there. The
  default itself is unchanged: `changeType` and `summary` without `workflow`
  answer `Resolves: #ISSUE_NUMBER`, `Releases: RELEASE_TARGET` and
  `ERROR: A Forge issue is required`, and close with the sentence naming
  `workflow="project"`. `knowledge/server-scope.json` names the argument in the
  covered topic "Commit messages" and not in its routing entry "Writing or
  amending the commit message".
- **The ladder stops at step 2.** `bin/cli hints:probe` on the feedback's own
  query reaches `backend-typescript`, `backend-ui` and `language-files` — the
  domains its words happen to spell, since the ordering of two Prism plugins is
  not a TYPO3 subject. Nothing about the commit step is missing from
  `knowledge/`; it is in a tool the session never called, which is delivery.
- **One report, and no sibling.** `bin/cli feedback:list` on 2026-08-04: 10 open
  across three directories, eight of them from `ext-guidedtour` and one from
  `typo3-cms`. This is the only card from `/home/benji/projects/syntax` and the
  only one written by `claude-opus-5`.
- **`D-GUI-002` has been waiting for exactly this run.** Its **Wrong if** —
  agents commit in a project repository and never pass the argument — was
  written on 2026-07-29 and its first claim was unsettled until this session. It
  names the seven skills as the worst of the four channels.

## Decided

- **Placement, not a new capability.** Step 2 of the ladder in
  [judging.md](../../documentation/records/judging.md): the answer exists and
  the route to it does not pass where this task passed. No tool and no
  `knowledge/` entry is built.
- **In the skill body, not in `skills/base.md`.** That file is the order every
  task *starts* in and is copied into all nine skills, so a commit line there
  would state in the two core skills what their own "Commit and push" and
  "Commit shape and target branch" sections already state. `D-SKL-013` settled
  the same fork the same way: the side that reaches a caller who arrived without
  a skill is the tool's answer, and that side already carries the step.
- **The routing entry gains the argument**, so the one place outside a skill
  that names the step says which workflow the caller is in.
- **Which of the seven get it is read, not decided here.** A workflow that ends
  in a review changes nothing and commits nothing, and each skill's own body is
  what says which it is.

## Assumed

- That a session which loads one of those skills reads the closing step. Nothing
  measures how far into a skill a session gets, and `D-AUD-003`'s **Confirmed
  on** is the one reading there is: a run that activated the skill and followed
  it to step 2 of five.
- That naming a tool at the end of a body is routing rather than a second copy
  of what the tool owns, which is what the two core skills already do.

## Wrong if

- A second session fixes something in an extension and commits, with the step in
  the skills, and activates no skill again. Then the skill channel is not the
  route this task takes, and what is left to suspect is the descriptions each
  skill is chosen on: no published skill describes reproducing and fixing a
  reported defect in an extension, and the `typo3_task_guide` brief for that
  task text names no skill either.
- A skill that only reviews gains the step. A review changes nothing —
  `R-GUI-006` — and a commit line in it is the patch checklist that entry exists
  to keep out of a review's answer.
- The routing entry changes nothing because nobody reads it. It sits behind
  `typo3_server_scope`, and `D-AUD-003` measured what that costs: a tool has to
  be called to learn that tools should be called.

## Covered by

- `SkillTest::theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange`

## Since then

Implemented on 2026-08-04. Each body was read for where its own workflow ends,
and the ones that end in files that are written take the step unconditionally:
`typo3-backend-module-development` once the module opens,
`typo3-content-element-development` after the element is verified,
`typo3-extension-documentation` as the last thing it writes,
`typo3-extension-testing` beside the commit split `references/static-quality.md`
already prescribes, and `typo3-extension-upgrade` for the crossing its work list
justified.

`typo3-development-installation` is not published yet and is the draft
`D-SKL-012` queued for review. Its step 5 decides what the install wrote into
the repository and says the ignore rules are written "before the first commit",
so its workflow ends in a change by its own account. It carries the step into
that review rather than being published without it.

The two core skills were left alone. Both already name
`typo3_commit_message_guide`, both commit in the core, and `workflow="project"`
in either of them would drop the rules a core patch is held to.
`typo3-core-patch-review` is the review that the second **Wrong if** protects,
and the assertion above holds it to naming the guide without the argument.

The second **Wrong if** fired the same day: a skill that only reviews had gained
the step. `typo3-extension-conformance` was read as more than a review because
its own body carried an improvement branch, and the maintainer's answer is that
the branch should not have been there — conformance is pure analysis, and that
is the intent even where the text said otherwise. It said otherwise in three
places, and the first is the one no body could have corrected: the `description`
opened "Review, audit, or improve", which is the line a client selects on, so
the skill was loaded for change requests whatever the body said. All three are
gone and the file agrees with `R-GUI-006` rather than carrying an exception to
it. What that opens — a task worded as a change reaching no skill at all — is
answered by
[`D-SKL-016`](skl-016-acting-on-a-conformance-report-earns-a-task-skill-of-its-own.md)
rather than here: a task skill of its own, which starts from this skill's report
and takes the changes it is forbidden from making. It was published as
`typo3-extension-cleanup` on 2026-08-04, with a `cleanup` intent carrying the
change words that reached nothing.

`R-SKL-017` is what holds the placement from now on, the routing entry in
`knowledge/server-scope.json` names the argument, and `skills/base.md` no longer
does — `D-SKL-015`. The entry stays open on the first **Wrong if**: what it was
written against is behaviour, and nothing has watched a session commit in an
extension with these bodies in front of it. That run is what would answer it.
