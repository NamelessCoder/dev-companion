---
id: D-SKL-081
title: A brief spanning triage and the patch it leads to carries both
date: 2026-08-27
status: open
coveredBy:
  - SkillTest::aBriefThatTriagesAndThenFixesCarriesBothWorkflows
---

# D-SKL-081 — A brief spanning triage and the patch it leads to carries both

**Where a task finds an issue on the tracker and then fixes it,
`typo3_task_guide` names both workflows in order and keeps the steps the patch
owes.**

Two sessions asked for exactly that and reached neither skill. The brief that
does match the triage intent strongly loses six of the patch's own checklist
items, the commit message among them.

## Evidence

- **The session.**
  [`feedback/2026-08-26-223325`](../../feedback/archive/2026-08-26-223325-the-stated-entry-point-and-both-fitting-skills.md),
  `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]`, sent to "please find
  1 old forge issue and fix it" and then narrowed to Extbase.
  `typo3-core-issue-triage` and `typo3-core-patch-development` were both listed
  and neither was invoked; `typo3_task_guide`'s schema was loaded in the first
  `ToolSearch` call and the tool was called in none of the roughly eighty-five
  that followed.
- **The second session** is
  [`feedback/2026-08-24-163220`](../../feedback/archive/2026-08-24-163220-both-skills-matching-this-task-stayed-shut-for.md),
  two days earlier in the same checkout: "bitte suche forge issues im asset
  renderer bereich", then one that is easy to fix and can be proved with tests.
  It found the issue, wrote the patch, the tests and the changelog entry, and
  opened no skill. `D-SKL-076` was written from it and read the backlog half
  alone.
- **The route does not reach the skill.** Measured in this worktree on
  2026-08-27: `typo3_task_guide` with
  `task="please find 1 old forge issue and fix it"` matches `reporting` and
  `triage` weakly and answers `skills: []`. "find an old forge issue and fix it"
  and "search forge issues in the asset renderer area" answer the same. What
  `triage` holds for those words is `forge` and `old issue` in its `matchWeak`,
  and a weak intent names no skill (`D-SKL-023`).
- **The needle alone is the wrong lever, measured on a brief that already
  matches.** `fetch another old issue from Forge, create a branch, work it off`
  is `D-SKL-078`'s own worked example, matches `triage` strongly and ends in a
  change. Its brief carries 7 checklist items against the 12 of "fix Forge 15984
  in the FormEngine", and what is gone is the patch half: the target branch, the
  deprecation sweep, keeping the patch focused, the narrowest useful test
  coverage, the targeted test run, and writing the message with
  `typo3_commit_message_guide`. `triage` carries `changesNothing`, so a strong
  match makes the whole brief one that writes nothing (`D-SKL-039`).
- **What the session reconstructed by hand is that list.** It reports
  test-first, `cgl`, `phpstan` and a commit message obeying the 52 and 72 column
  rules, all derived from the core checkout's own `AGENTS.md`, and it names the
  two things that file did not carry: the branches a `Releases:` trailer takes,
  and whether the change owes a changelog entry. Both sit in the patch half of
  the brief it never got.

## Decided

- The judgement is
  [`documentation/records/judging.rst`](../../documentation/records/judging.rst)
  step 3, routing, and the lever is the `changesNothing` fork rather than a
  needle. A brief may triage and then change something, and the fork has one
  slot for it.
- **Both skills are named, in order.** Triage is the front half and the patch
  workflow is what the crossing hands over to
  ([`D-SKL-022`](skl-022-a-handoff-between-skills-is-an-instruction-rather-than-a-closing-sentence.md)),
  so the answer states the order rather than leaving the caller to pick one of
  two names.
- **The patch skeleton stays.** A brief whose words name a change keeps the
  items that change owes, whatever else it recognized. That is the third **Wrong
  if** of `D-SKL-039` read from the other side: it watches for an author's brief
  losing its route to a review, and this is an author's brief losing its
  checklist to a triage.
- Against a third skill. `D-SKL-005` settled that core contribution earns two,
  and what is missing is the order across them rather than a workflow neither
  owns.
- Against promoting `old issue` to a strong needle on its own, which is the
  shape the feedback's own suggestion has. Measured above, it would make every
  "fix an old issue in X" a brief that writes nothing.
- **Queued rather than made here.** The route is in `src/`, and the two
  descriptions the same card carries are a tool's contract and a skill's.
- The priority is `normal`: two sessions, and this is how core contribution work
  is asked for.
- The two description halves are recorded where they belong — the triage
  description's closing clause at `D-SKL-076`, `typo3_task_guide`'s opening at
  [`D-AUD-014`](../audience/aud-014-a-description-opens-with-what-the-callers-own-route-cannot-do.md).

## Assumed

- That the session would have called `typo3_task_guide` had its description
  earned the call. It called it at no point, so what the route answers is
  measured against a call nobody made, which is why the description half rides
  on the same card rather than behind this one.
- That two names in one answer are read as an order. `D-SKL-013` put one name
  there and nothing has measured what a second does.

## Wrong if

- A session gets both names and enters the second one only. Then the crossing is
  the handoff `D-SKL-022` watches and not the route.
- A feedback reports the triage half as noise: a caller already holding the
  issue, sent to establish what it still claims anyway.
- A brief of this shape opens nothing once both names arrive. Then no answer is
  what the choice is made on, and it is `D-SKL-033`'s count.
- A filing brief reaches the triage skill through one of the three needles added
  below. `write a new forge issue` is the shape that comes closest and does not,
  measured on the corpus in the section under this one.
- A review is answered with the patch skeleton because the request quoted the
  change it is about. That would say `patch`'s needles read the words of
  somebody else's work, which is the failure `D-SKL-039` names from the other
  side.

## Since then

The three levers were built and measured over twenty-five briefs before and
after. Four briefs that answered no skill now answer both, and the ones that did
not move are the ones the entry predicted: the filing briefs keep their own
intent, because the plural and the adjective separate taking an issue from
writing one, and the review briefs answer exactly what they did.

A third session then lost on a preposition — two of the four a session might
write were in the strong list and two were not — so the list is the finding
rather than the fix. What the needles cannot reach is measured beside it: a
brief that knows it is core work and declares a change type still names no
workflow, because only the sentence is read. `D-SKL-082` decides that half.
