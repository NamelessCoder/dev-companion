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

### 2026-08-27 — made, and what it cost the neighbouring briefs

The three levers were built and measured over twenty-five briefs, before and
after, in this worktree.

**The route.** `triage` takes `old issue`, `old forge issue` and `forge issues`
as strong needles, and `old issue` leaves `matchWeak`. A new `patch` intent
recognizes the caller's own act of writing the change — `fix it`, `fix the`,
`work it off`, `write the patch`, `implement what` and the rest — and names
`typo3-core-patch-development` inside the core. `TaskGuide` reads that intent as
what ends every shape that changes nothing, so the skeleton is the patch's, and
`TaskIntents::owned()` returns what reads before what writes.

What moved: "please find 1 old forge issue and fix it" and "find an old forge
issue and fix it" go from `skills: []` and no recognized intent to both skills
and sixteen items; "fetch another old issue from Forge, create a branch, work it
off" goes from seven items to twelve and gains the second name; "search forge
issues in the asset renderer area" — the second session's own brief — goes from
nothing to the triage skill and eleven items; "implement what Forge 98765 asks
for" gains `typo3-core-patch-development`.

What did not move: the three filing briefs of `D-SKL-078`'s measurement keep
`reporting` alone, because the plural and the adjective separate taking an issue
from writing one; `D-SKL-039`'s review briefs answer exactly what they did,
`fixes` being no match for a needle that ends in a space. "fix an old issue in
the FormEngine" is the brief this entry declined `old issue` over, and it is a
patch brief with twelve items: the needle and the fork together are what that
bullet asked for.

`typo3_task_guide` still answers no skill for "fix Forge 15984 in the
FormEngine". No intent has ever named the patch workflow for a plain fix that
carries none of the seven change-type words, and nothing here was widened to
reach it.

### 2026-08-27 — a third session, a fourth preposition, and what the needles cannot reach

**"in forge" and "on forge" join the tracker needles, and the shape that
survives them is a task worded as the defect rather than as the work.**
`feedback/2026-08-27-145332` asked *please search for 1 workspace bug in forge
and fix it* and got the patch skill alone: `forge` on its own is `matchWeak`,
and the strong list carried `from forge` and `off forge` and not the two
prepositions a third session used. It triaged seven issues by hand and reports
that it never considered either skill.

That is the second session in one day whose only obstacle was which preposition
it wrote, so the list is the finding rather than the fix. What it cannot reach
is measured beside it: with `changeType="bugfix"` and a core path, *add the
missing language parameter to getMovedRecordsFromPages* and *workspaces service
does not filter moved records by language* both answer `skills: []`, while *fix
a bug in WorkspaceService* names the patch skill. A brief knows it is core work
that changes something and still names no workflow, because only the sentence is
read and the declared `changeType` is not.

`D-SKL-082` decides that half. This entry keeps the route it built; what the
route cannot be reached by is the other entry's.
