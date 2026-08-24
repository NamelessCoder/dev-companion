---
id: D-ANS-104
title: The maintained release lines are placed where a task names a branch
date: 2026-08-24
status: open
coveredBy: []
---

# D-ANS-104 — The maintained release lines are placed where a task names a branch

**The lines that take a patch today reach a session through the call its task
actually makes, and not only through the message it hands
`typo3_commit_message_guide`.**

So `feedback/2026-08-24-122348` is step 2 of the ladder and is queued. The
answer was here nineteen days before the session that went to `git branch -r`
for it.

## Evidence

- The feedback re-run on 2026-08-24 through `CommitMessageGuide::answer()`, on
  the change it was written about. With `Releases: main, 14.3, 13.4` and
  `workflow="core"` the answer is "13.4 is maintained, and a BUGFIX is released
  on main, 14.3. An older line takes a priority bug fix and a grave or
  security-relevant defect, so naming it claims the severity earns it". With the
  trailer left out it names "main, 14.3, 13.4" as the lines that can take a
  patch at all and "main, 14.3" as where a bug fix goes.
- That is the fact the session asked for, stated better than the fact it built:
  it separates what can take a patch from where this one goes, which counting
  remote branches cannot.
- Both halves predate the feedback. `knowledge/release-lines.json` landed on
  2026-08-05 with `D-ANS-058`, and `ReleaseLines::ordinary()`, which is what
  narrows a bug fix to two lines, on 2026-08-10. The feedback is timestamped
  2026-08-24T12:23:48.
- It is routed for this exact task shape. The `routing` block of
  `knowledge/server-scope.json` ends the review entry with
  `typo3_commit_message_guide with workflow="core"`,
  `knowledge/task-intents.json` names it for a core patch before the push, and
  `skills/typo3-core-patch-review/SKILL.md` gives it the section *Commit shape
  and target branch*.
- None of it fired. `feedback/2026-08-24-122413`, the same session twenty-five
  seconds later, records no skill activation in the whole session and no
  `typo3_task_guide` call: three server calls — `typo3_gerrit_lookup`,
  `typo3_project_describe`, `typo3_forge_lookup` — and Bash after that.
- What the session did instead is in its **Query**: `git branch -r`, a listing
  of `Documentation/Changelog/`, and `Typo3Version.php`. It reached the right
  three lines, the same way the session behind `D-ANS-058` reached them by
  counting trailers on forty commits.
- `bin/cli hints:probe` with the feedback's own subject reaches
  `extension-ter-release` and nothing else. The fact is not in the hint corpus
  at all: it is a JSON file and a class behind one tool's checks.
- Nothing about TYPO3 was established here. The branch facts are the ones
  `knowledge/release-lines.json` already carries, and its windows are what the
  re-run printed.

## Decided

- Step 2, delivery. Not 1a, because `release-lines.json` holds the windows and
  the guide prints them. Not 1b, because no verb is missing — the session had a
  commit message in hand and the tool that owns the trailer would have answered
  it. Not 3, because the routing entry exists and names that tool for this task
  shape; it sits behind a tool the task was not sent to, which is what step 2
  describes.
- Queued rather than closed on the spot. Any carrier adds a field to an answer,
  which changes `src/` and a declared output schema, and
  [judging.rst](../../documentation/records/judging.rst) puts both beyond a run
  that has read only this repository.
- Which call carries it is the todo's first step, and the session named both
  candidates itself: `typo3_project_describe`, the one call it did make and the
  one the `instructions` tell every session to start with, which already reports
  the installed version; and `typo3_gerrit_lookup`, which already returns the
  change's target branch.
- The set stays out of it.
  [`D-ANS-073`](ans-073-what-can-take-a-patch-and-where-this-one-goes-are-two-readings.md)
  is the boundary a placed answer is held to: it states the lines and their
  windows, never which of them a change belongs on.
- No requirement and no test yet, which is what `coveredBy: []` says here. The
  demand is carrier-shaped —
  [`R-PRJ-008`](../../requirements/project/prj-008-the-project-answer-says-what-runs-it-not-only-what-it-declares.md)
  and `R-ANS-018` are each written on the answer that carries them — so it is
  written with the placement rather than before it.
- The feedback is trimmed rather than archived. Its first claim, that nothing
  names the maintained branches, is answered; the placement it asks for in the
  same breath is not.
- Nothing on another branch was touched. The changelog half it names —
  `feedback/2026-08-24-122249`, the `.x` folder for the oldest backported branch
  — is in hand in another worktree, and the skill-activation half
  `2026-08-24-122413` has its own card in the queue. Neither `Serves:` line
  gained this feedback.

## Assumed

- That the session would have called `typo3_commit_message_guide` had a skill
  opened. Nothing records why a tool is not called, and its sibling says the
  three lookups answered so completely that the work stopped looking like a
  workflow.
- That a session naming a release branch has a commit message in hand. This one
  did, because it was rewriting somebody else's trailer. A session deciding a
  changelog `.x` folder does not, and would have to invent a message to reach
  the fact.

## Wrong if

- A session holding an answer that names the lines goes to `git branch -r` all
  the same. The fact would then be delivered and not taken, which is step 4 and
  a rewrite rather than a placement.
- Skill activation is fixed and this observation does not recur, which would say
  the placement was never the lever and `2026-08-24-122413` owned the whole of
  it.
- The carrier turns out to serve more than the trailer. A site developer told
  that 13.4 is in regular support until 2027-12-31 is answered a question this
  judgement priced only for a core patch, and the boundary would then be wider
  than the feedback that moved it.
