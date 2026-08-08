---
id: D-SKL-024
date: 2026-08-08
status: confirmed
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
  [documentation/clients/writing-a-skill.md](../../documentation/clients/writing-a-skill.md)
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

A session asked to review change 95179 in a git worktree read patch checkout's
description, took it for a branch-switching workflow, and did the fetch and the
worktree by hand — with the review skill's own routing line naming the checkout
skill in front of it
(`feedback/2026-08-08-224413-typo3-core-patch-checkout-stayed-shut-on-a.md`).
That is the **Assumed** above, measured here for the first time: the steps were
read as the workflow rather than as a route into it.

What the sighting adds is the mechanism. A step clause does not only summarise
the body, it **narrows what the description names**, because a list of steps
describes one way of doing the job and reads as a refusal of every other. Every
verb after the fetch in this one moves the branch the session is standing on,
and a worktree review is the opposite operation — so the description read as
actively not its case, which is a stronger failure than being skimmed.

The clause is still there. `a1b09af` cut it and `4b186b3`, the budget trim of
the same day, wrote it back in exchange for the requests the description used to
list. That trade is `D-SKL-026`'s third **Wrong if** rather than this entry's,
and the repair is queued.
