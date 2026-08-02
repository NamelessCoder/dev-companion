---
id: D-SKL-001
date: 2026-08-01
status: confirmed
---

# D-SKL-001 — The order a task starts in is one file, and the reading comes last in it

**`skills/base.md` holds the order every task starts in, and is copied into each
published skill rather than shared with it.**

The order is project scope, extension scope, task guide, conventions, and only
then the checkout.

Four recorded `REVIEW-01` runs of the same prompt in the same checkout took it
from a review this server took no part in to `covered`. The thing that decided
each step was never the wording of an instruction; it was where the reading of
the checkout sat relative to everything else.

## Evidence

- Run 2 activated the skill and followed two of its seven evidence steps. Run 3
  read the skill's checklist in its first twenty seconds, then ran
  `find . -type f` and spent five minutes reading the result before calling
  `typo3_task_guide` or a single conventions lookup — and filed translations
  under "assessed and clean" with `source-language="de"` on screen, because it
  had asked `typo3_translation_domain_lookup` and taken a runtime answer for a
  verdict. Comparing the other four skills then showed the arrangement was
  everywhere: "inspect the checkout" stood at step 2 of 6 in content-element, 3
  of 6 in documentation, 3 of 7 in testing, each with the conventions lookup
  behind it. Run 4, against one base file, walked the order and produced both
  findings three runs had missed.

## Decided

- `skills/base.md` holds the order — project scope, extension scope, task
  guide, conventions per subsystem, and only then the checkout — plus the two
  things no skill should re-derive: a runtime lookup reports what is registered
  and never whether it is right, and a returned rule judges the code that
  already exists as well as the code about to be written. `Installer` copies it
  into each published skill as `references/base.md` rather than sharing one
  file, because a skill lands in someone else's project alone. Each `SKILL.md`
  states only what it adds. Two tool defects found the same way were repaired
  rather than worked around: an identifier reaching `addRecordType()` through a
  variable was dropped silently, and nothing reported what an extension does
  not ship.

## Wrong if

- `REVIEW-02` in an extension repository shows the base being outrun again,
  which would mean the order is followed only where a checklist reinforces it
  and the conformance skill was carrying it rather than the base. Or the base
  grows: it is load-bearing because it is short enough to be read before the
  first call, and every sentence added to it is one the reading can swallow. It
  is also unproven for four of the five skills — their order was corrected on
  the strength of a defect measured in the fifth, and no forward run has
  touched them.

## Confirmed on 2026-07-31

`REVIEW-02` ran in `bootstrap_package` and the base was not outrun. The skill
activated on its own, `references/base.md` and the checklist were read three
seconds later, the three scope calls followed, and the six-surface list was
written out 22 seconds before the first project file was opened. So the order
survives a checkout of another kind — by the same skill. The four whose order
was corrected without a forward run are still unproven, and the base is still
short.

## Since then

The base grew, on 2026-07-31, by one section on what a finding rests on. The
three `REVIEW-02` runs that tested this decision are the same ones that showed
the base saying nothing about the evidence behind an answer, and the section
costs eight lines against a whole class of findings that read as established
when they were derived. It is the first addition since the file was written,
and the budget it spends is real: the next one is measured against a base a
fifth longer than the one this decision called short.

## Since then

The base grew a second time, on 2026-07-31, by a fifth step — the deprecation
sweep of the installed core over what the extension was reported to ship. The
`REVIEW-02` run that earned it is the one this decision's **Wrong if** asks
about, and the answer is the other one: the base was not outrun, it was silent,
and the run swept nothing because nothing told it to. The step is in the base
rather than in the conformance checklist because the upgrade skill that is
queued behind it starts with the same sweep, and a second hand-written copy of
an order is what this file exists to prevent. It pays for itself twice over by
deleting the weaker copy the conformance skill already carried — the sweep
"when an upgrade or a deprecated API is in scope", which is the escape hatch
that run took. The sink half of the same run went the other way and stayed out
of the base: an escaping finding is a claim about a sink, a finding gate for
one surface, and it sits in the checklist beside the gate it qualifies, where
only the skills that judge pay for it.

## Since then

The base grew a third time, on 2026-07-31, by the precondition that now stands
above the order: no `typo3_` tool in the session means stop and say so, and
never answer from what the model knows instead (`1960e50`, `R-SKL-008`). It is
the growth this decision's **Wrong if** watches. The arithmetic is the worst so
far: 496 words when the file was written, 960 after the sweep, 1099 now. It
costs no call, because the first step of the order is the check, and it buys the
one failure the order cannot see — an order followed perfectly against nothing.

`feedback/2026-07-31-185553` is that failure from the other side, and judging it
is what brought this entry the numbers above. A session in `site-new` activated
`typo3-extension-conformance`, read `references/base.md`, and found none of the
eleven tools it names callable. It audited the site package from its own
knowledge anyway, and filed at 18:55:53 UTC — eleven minutes before `1960e50`
landed. So it is archived as answered rather than turned into work, and what the
re-run showed is this: the server starts and serves 24 tools over stdio, every
one of the eleven among them, so nothing was ever missing here. The gap was the
client's connection. That is why the feedback's own suggestion — expose the
tools in the agent environment — is not a change this server makes at runtime.
In `site-new` itself the installer has since written the precondition into all
six published skills under both `.claude/skills` and `.agents/skills`, beside
an `.mcp.json` that names this server.

One half of that session is still open and lands elsewhere.
`feedback/2026-07-31-185900-during-an-audit-of-the-printworks-3d-site.md` is the
same report four minutes later, and it carries what this one does not: the
session reached the tools afterwards by driving the stdio binary by hand. The
precondition answers what to do when the tools are absent and says nothing about
that. So whoever judges the duplicate is judging a different question: not
whether to stop, but whether stopping is still right when the server is one
process away.

## Since then

The **Wrong if** got an answer from outside the recorded runs. A session on
`opencode/ling-3.0-flash-free` left a feedback on 2026-07-31 at 18:36, in a
checkout this repository has never seen. It names "the base to scope to lookup
to reading order" as what "prevented premature conclusions" — the property this
decision claims, from a run nobody here wrote the prompt for. It is weaker
evidence than a `REVIEW-02`. The checkout, the prompt and the finding list are
unreadable from here, and what came back is a session's account of its own work
rather than a transcript. It is also the first evidence about the
order from a model this repository has never measured, and it says the same
thing the four runs said. The base was not outrun and it has not grown since.
The four skills whose order was corrected without a forward run are still
unproven. Judged in [`D-SKL-002`](skl-002-a-focused-audit-narrows-what-is-assessed.md),
which is what the other half of the same feedback asked for.

## Since then

The **Wrong if** got its other answer, from the same project and one day later.
A strength this time, and it lands in both halves: the base was outrun, and one
of its steps was read past.

`feedback/2026-07-31-192945` names the conformance skill's workflow as
"project_scope → extension_scope → architecture_lookup → changelog_lookup →
read checkout → report", and says there is nothing to drop. That is the order
of this file minus step 3. The same session's tool log, filed twenty seconds
later as `feedback/2026-07-31-193005`, lists thirteen numbered round trips with
no `typo3_task_guide` among them. It also puts a `glob` of the extension and
fifteen file reads at positions 5 and 6, ahead of the first
`typo3_architecture_lookup` at 7. So a session whose own account is that the
order fit perfectly followed neither the step nor the placement.

The copy does not explain it. `references/base.md` under both `.claude/skills`
and `.agents/skills` in that project carries step 3 and the sweep. It differs
from this file only by the empty-sweep paragraph of 2026-08-02. The two are
dated: step 3 landed at 02:15 CEST on 2026-07-31 and the sweep at 16:24. A base
recited with the sweep in it is a base that carried step 3.

What the step is worth was re-run on 2026-08-02, from `site-new` through
`bin/typo3-cms-mcp`. `typo3_task_guide` with the audit task, area `extension`,
version 14 and change type `unknown` answers in 1,937 words. Under "Next
lookups" it names `typo3_architecture_lookup` and `typo3_changelog_lookup`,
which are steps 4 and 5 the caller has just read. Its checklist is six items
about the target branch, the issue context and keeping a patch focused. And it
names no workflow: `src/Tool/TaskGuide.php` has no skill in it, while step 3
here says the call returns "the workflow this task belongs to".

`feedback/2026-07-31-194826` is that same call from another model in the same
project. It reports that the guide restated the skill's own checklist and the
lookups the skill had already named, and that it would not call it again after
that skill. So the two feedback are one property from both sides: one session
skipped step 3 and reported no loss, another ran it and reported no gain. Both
were inside a task skill, which is the only place this file is read at all.

The arithmetic this entry keeps: 496 words when it was written, 960 after the
sweep, 1099 in the copy that session read, 1188 today.

The question is what step 3 is for once a task skill is loaded, and it is not
one to settle quietly. Two answers are open and they run opposite ways. Either
this file says what the call is worth to a caller that already has its
workflow, which is a change to a contract installed in somebody else's project.
Or `typo3_task_guide` names the workflow it says it names, which is `src/`. The
feedback stays open behind that answer.

## Confirmed on 2026-08-02

The same run was read a second time, from the call log rather than from the
strength beside it, in a session that could not see the one above. What the
strength says it did is one artifact and what the log says it did is another,
and the log is the stronger of the two: it is numbered, and it was filed while
the account above was still being written.

`feedback/2026-07-31-193005` is a session's own log of its tool calls, filed in
`site-new` on `nemotron-3-ultra-free`, and what it records is the base outrun at
the one step the base exists for. The first two steps of the order ran in place:
`typo3_project_scope`, then `typo3_extension_scope` on the site package. Step 3
never ran at all. Then a glob listed 46 files of the extension and fifteen
`read()` calls went through the ones it picked — and only behind that reading
came the two `typo3_architecture_lookup` calls, the deprecation sweep, the two
runtime lookups and another two or three round trips of files. That is Run 3's
shape exactly, from a session that had the base and read it in its second step.

What happened is worse than the **Wrong if** predicted. It expects the order to
survive only where a checklist reinforces it, and the checklist here was
`typo3-extension-conformance`'s own — the one skill that carries the rule a
second time in its own words, "Listing the files first inverts that". Both files
said it and the reading still came first, so this is not a sentence that was
missing from one of them.

The evidence is a report rather than a transcript, and it is weaker than a
`REVIEW-02` in the same two ways the entry above already names. The session
numbered its own calls afterwards, and which `references/base.md` it read cannot
be recovered: both installed copies in `site-new` were rewritten at 21:20 that
evening, 110 minutes after it filed. That the copy carried the order is settled
another way — the session's step 2 names `base.md`, the file was created by
`66813e3` at 02:15 that morning, and it has carried "**Then** read the checkout.
Not before" since that commit. There is no version of it that lacked the rule.

The server's half of the run reproduces. From `site-new` on 2026-08-02 through
`bin/typo3-cms-mcp`, `typo3_project_scope` answers 14.3.5 with the five declared
commands, and `typo3_changelog_lookup` with `type=deprecation, version=14,
limit=30` returns the first 30 of 75 entries — the 30 the session reported. Step
3 is the one that would not have paid. `typo3_task_guide` called with an audit
task, the area and the extension's path matches no intent — `intents: []` — and
hands back the generic change checklist: keep the patch focused, add the
narrowest useful test coverage, write the commit message. That is a brief for
changing a package, given to a run the skill tells to change nothing. So the
step this session skipped is also the step with no answer for the task shape it
was in, and `knowledge/task-intents.json` has no entry for an audit.

Nothing was changed here. Whether a self-reported call log is the event this
**Wrong if** describes, and what moves if it is, is the question its card
carries in `todo/waiting/`.
