---
id: D-SKL-024
title: A description names the task and leaves the steps to the body
date: 2026-08-08
status: confirmed
coveredBy:
  - SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement
  - SkillTest::aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout
---

# D-SKL-024 — A description names the task and leaves the steps to the body

**A skill's description names the task, the sides it owns and where it stops,
and the ordered steps stay in the body.**

The description is the only part read before the skill is chosen, so a summary
of the workflow arrives where the body has not been loaded yet and reads as the
workflow itself.

## Evidence

- obra/superpowers measured the failure in another project: a description saying
  "code review between tasks" produced one review where the skill's own flow
  specified two, and their reading is that the agent followed the summary
  instead of the body. Nothing here has been measured that way.
- All twelve descriptions were read on 2026-08-08 for that shape and six carry
  it. Four are the core workflows, each opening with an em-dash clause that is
  the body's own order: triage's "find the candidates, read what the report
  claims, establish against the checkout" is three of its sections, patch
  development's "assess the issue, reproduce it, make the change, cover it,
  write the changelog entry, run the checks, push it" is all six of its
  sections, and checkout's "find the change, fetch the patch set, put it on the
  branch it targets, rebase, restore" is its five in the order it states them.
  Patch checkout carried a second one — "It stops rather than improvises", which
  is what its "Stopping is the normal ending" section and
  `references/checklist.md` own. Patch review's clause is the checklist rather
  than the order.
- The other two are one method clause each: the module skill's "where the
  implementation must match the active installation and TYPO3 version", and the
  installation skill's "so the package can actually be run, opened in a browser
  and clicked through".
- The remaining six name their domain and then list nouns a user types — CType
  registration, TCA, Fluid, PHPStan, `Tests/` — or situations they arrive in.
  The upgrade skill's em-dash clause reads like the four but is not one: adding
  a major, dropping a major and replacing what one removed are three shapes of
  the request, which is what `R-SKL-010` asks for.

## Decided

- The six clauses are cut and every side stays. The twelve total 6383 characters
  against 7153 before, measured over `skills/*/SKILL.md` the same way the budget
  card measured it.
- What names a sibling skill stays, because it routes rather than summarises: a
  boundary sentence is read before the choice and is the only thing that can
  send the task elsewhere.
- Where a cut clause held a word a user would type, the word stays as a trigger
  rather than as a step — patch checkout keeps rebasing onto the target branch,
  patch development keeps the changelog entry and the push to Gerrit, and patch
  review keeps its surfaces as nouns.
- The rule goes into
  [documentation/contributing/writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst)
  beside the two that already govern a description, and not into a requirement
  of its own: which clause is a summary is not readable off the file, so a
  requirement would be `not guarded` and would repeat the page it points at.

## Assumed

- That what obra/superpowers measured in another client transfers to the clients
  this server is installed in. No run here has shown a session substituting a
  description for a body.
- That a step named in a description is taken as the workflow rather than as a
  route into it. The opposite is arguable: a caller who reads "reproduce it
  against the branch you are fixing" may open the body to find out how.

## Wrong if

- A forward run against one of the four core skills follows its body no more
  closely than the runs before this change. Then the description was never the
  shortcut, and the 770 characters bought listing budget alone.
- A task that names one of the cut steps stops matching its skill — "reproduce
  this core bug against main", "click through the installed site" — which would
  say the clause was carrying the activation rather than summarising the body.

## Confirmed on 2026-08-09

The **Assumed** was measured for the first time: a session asked to review a
change in a git worktree read the checkout skill's description, took it for a
branch-switching workflow, and did the fetch by hand — with the review skill's
routing line naming it in front of it.

What the sighting adds is the mechanism. A step clause does not only summarise
the body, it **narrows what the description names**: every verb after the fetch
moves the branch the session is standing on, so a worktree review read as
actively not its case, which is a stronger failure than being skimmed. The
clause was cut, traded back in by a budget trim the same day, and undone again —
`SkillTest::aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout` holds both halves
so the next trim cannot make the trade unseen.

## Since then

The second **Wrong if** fired on the clause cut from triage: a session searched
the backlog six times, read four candidates, and never opened the skill whose
description had stopped naming that step. What fired is narrower than the
statement — "find the candidates" was not a step of the task the description
names but a deliverable of its own, which `D-SKL-031` settled the day after the
cut. So a cut that reads a job as a step removes what nothing else says, and
`D-SKL-076` is what that is decided as. The other five skills were read for the
same thing and each runs one task through to one deliverable, so triage is the
case rather than the first of several.
