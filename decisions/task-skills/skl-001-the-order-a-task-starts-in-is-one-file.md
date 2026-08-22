---
id: D-SKL-001
title: 'The order a task starts in is one file'
date: 2026-08-01
status: confirmed
---

# D-SKL-001 — The order a task starts in is one file

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

- `skills/base.md` holds the order — project scope, extension scope, task guide,
  conventions per subsystem, and only then the checkout — plus the two things no
  skill should re-derive: a runtime lookup reports what is registered and never
  whether it is right, and a returned rule judges the code that already exists
  as well as the code about to be written. `Installer` copies it into each
  published skill as `references/base.md` rather than sharing one file, because
  a skill lands in someone else's project alone. Each `SKILL.md` states only
  what it adds. Two tool defects found the same way were repaired rather than
  worked around: an identifier reaching `addRecordType()` through a variable was
  dropped silently, and nothing reported what an extension does not ship.

## Wrong if

- ~~`REVIEW-02` in an extension repository shows the base being outrun again,
  which would mean the order is followed only where a checklist reinforces it
  and the conformance skill was carrying it rather than the base.~~ Fired on
  2026-08-02 in a self-reported call log rather than in a recorded run, and the
  checklist is what did not help. The `REVIEW-02` that would settle it was
  declined.
- ~~Or the base grows: it is load-bearing because it is short enough to be read
  before the first call, and every sentence added to it is one the reading can
  swallow.~~ Fired six times, from 496 words to 1531, and what each growth
  bought is the ledger below.
- It is also unproven for four of the five skills — their order was corrected on
  the strength of a defect measured in the fifth, and no forward run has touched
  them.

## Confirmed on 2026-07-31

`REVIEW-02` ran in `bootstrap_package` and the base was not outrun. The skill
activated on its own, `references/base.md` and the checklist were read three
seconds later, the three scope calls followed, and the six-surface list was
written out 22 seconds before the first project file was opened. So the order
survives a checkout of another kind — by the same skill. The four whose order
was corrected without a forward run are still unproven, and the base is still
short.

## Since then

The **Wrong if** got a second answer, from the same project and one day later. A
strength this time, and it lands in both halves: the base was outrun, and one of
its steps was read past.

`feedback/2026-07-31-192945` names the conformance skill's workflow as
"project_scope → extension_scope → architecture_lookup → changelog_lookup → read
checkout → report", and says there is nothing to drop. That is the order of this
file minus step 3. The same session's tool log, filed twenty seconds later as
`feedback/2026-07-31-193005`, lists thirteen numbered round trips with no
`typo3_task_guide` among them. It also puts a `glob` of the extension and
fifteen file reads at positions 5 and 6, ahead of the first `typo3_hint_lookup`
at 7. So a session whose own account is that the order fit perfectly followed
neither the step nor the placement.

The copy does not explain it. `references/base.md` under both `.claude/skills`
and `.agents/skills` in that project carries step 3 and the sweep. It differs
from this file only by the empty-sweep paragraph of 2026-08-02. The two are
dated: step 3 landed at 02:15 CEST on 2026-07-31 and the sweep at 16:24. A base
recited with the sweep in it is a base that carried step 3.

What the step is worth was re-run on 2026-08-02, from `site-new` through
`bin/typo3-dev-companion`. `typo3_task_guide` with the audit task, area
`extension`, version 14 and change type `unknown` answers in 1,937 words. Under
"Next lookups" it names `typo3_hint_lookup` and `typo3_changelog_lookup`, which
are steps 4 and 5 the caller has just read. Its checklist is six items about the
target branch, the issue context and keeping a patch focused. And it names no
workflow: `src/Tool/TaskGuide.php` has no skill in it, while step 3 here says
the call returns "the workflow this task belongs to".

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
this file says what the call is worth to a caller that already has its workflow,
which is a change to a contract installed in somebody else's project. Or
`typo3_task_guide` names the workflow it says it names, which is `src/`. The
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
`typo3_project_describe`, then `typo3_extension_describe` on the site package.
Step 3 never ran at all. Then a glob listed 46 files of the extension and
fifteen `read()` calls went through the ones it picked — and only behind that
reading came the two `typo3_hint_lookup` calls, the deprecation sweep, the two
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
`bin/typo3-dev-companion`, `typo3_project_describe` answers 14.3.5 with the five
declared commands, and `typo3_changelog_lookup` with
`type=deprecation, version=14, limit=30` returns the first 30 of 75 entries —
the 30 the session reported. Step 3 is the one that would not have paid.
`typo3_task_guide` called with an audit task, the area and the extension's path
matches no intent — `intents: []` — and hands back the generic change checklist:
keep the patch focused, add the narrowest useful test coverage, write the commit
message. That is a brief for changing a package, given to a run the skill tells
to change nothing. So the step this session skipped is also the step with no
answer for the task shape it was in, and `knowledge/task-intents.json` has no
entry for an audit.

Nothing was changed here. Whether a self-reported call log is the event this
**Wrong if** describes, and what moves if it is, is the question its card
carries in `todo/waiting/`.

## Confirmed on 2026-08-02

**Stopping is still right when the server is one process away, and the base does
not learn the way round.** That is the question the duplicate of
`feedback/2026-07-31-185553` left behind — not whether to stop, but whether
stopping holds when the binary can be driven by hand. It was judged against
`feedback/2026-07-31-185900-during-an-audit-of-the-printworks-3d-site.md` on
2026-08-02. The base is unchanged by it, which is the point: the growth this
decision's **Wrong if** watches did not happen, and the answer cost it nothing.

Three things settled it. The base cannot carry the route even if it wanted to —
`Installer::BASE` copies one `skills/base.md` into every published skill
byte-for-byte, while the path to the binary is a property of the machine that
the installer writes into `.mcp.json`. So the fallback the base could teach is
"go and find a binary", which is precisely what that session did, and it found
it after the audit rather than before. Then what the hand-driven route produced
is on record: the same session, at the same minute, filed the feedback behind
[`D-FBK-019`](../feedback/fbk-019-a-secret-pasted-into-a-feedback-is-taken-out-on-the-way-in.md)
and pasted the live encryption key of the site it had just audited into this
repository. A session acting as its own client sits outside every affordance the
client layer provides, and that is the shape of what it costs. Last, stopping
reaches somebody who can act: the entry was correct all along, so the fix was a
person approving a server or restarting a session, and only the message the
precondition prints was ever going to get it asked for.

What that leaves is not a skill question at all, and it is judged in
[`D-DIS-009`](../discovery/dis-009-installed-is-one-step-short-of-callable.md):
the install writes the entry, reports nine successes, and never says that a
callable tool is one step further on. The precondition is what a session does
when the tools are absent. Keeping them from being absent is the install's, and
that half stays open.

## Since then

The question got its other side, from a session that had no skill at all.

`feedback/2026-08-01-003356` is the process half of the testimonials series,
filed in `site-new` on `opencode/deepseek-v4-flash-free`. It reports that
nothing was looked up until the user demanded it, that no skill was activated,
and that `typo3_task_guide` was never called. The knowledge halves of that
session are the siblings
[`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)
lists, and this one reports only the order they were reached in. The half about
the skill not activating already has a change against it: the description
rewrite of 2026-08-02 in
[`D-AUD-003`](../audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md),
made for this same task shape. What is left is the route through the server, and
it ends before the skill.

The entry point was in the text that session was sent. `site-new` runs
`bin/typo3-dev-companion` out of the main checkout, and `18a371a` put the
sentence into the `instructions` at 18:33 CEST on 2026-07-31, six hours before
the session filed. That sentence says `typo3_task_guide` "gives the workflow the
task belongs to, and hands the parts that have their own workflow to the skill
that owns them".

It hands over nothing. Re-run on 2026-08-02 from `site-new`: `typo3_task_guide`
with
`task="build a testimonials content element with a custom backend preview"`,
area `sitepackage`, version 14 and change type `feature` matches the
content-element and test intents. It answers with the two hints that session
spent its evening guessing at. A v14 preview template is handed one `record`,
and a sitepackage derives a content element's template from its CType. Its "Next
lookups" then name seven tools. None of them is a skill, and
`typo3_documentation_lookup` is not among them either.

So the two answers this question holds open are not the same size, and that is
what this feedback adds. Saying in `skills/base.md` what the call is worth to a
caller that arrived through a skill does nothing for one that arrived without a
skill. Only the other answer reaches this session: the tool naming the workflow
is the one route from the channel that arrives to the skill that owns the task.
On the ladder that is step 3 rather than step 1 — the skill exists, the payload
is in the guide's own answer, and nothing joins them.

What is unmeasured is whether that client passes `instructions` to the model at
all, so the sentence may never have been read. That does not gate the finding: a
caller that reads it and makes the call still reaches no skill. Nothing was
changed here, and the feedback waits behind the same question on a card of its
own.

## Since then

The question this entry opened on 2026-08-02 — what step 3 is for once a task
skill is loaded — was answered on 2026-08-03 by the person who was asked, and it
is the second of the two: `typo3_task_guide` names the workflow step 3 says it
returns
([`D-SKL-013`](skl-013-the-guide-names-the-skill-that-owns-the-task.md)).

The base is unchanged by it, which is the half worth recording here. Step 3
keeps the words it has carried since `66813e3` and the arithmetic stays at 1531,
so the answer that would have corrected or grown this file is the one that was
turned down: it reaches a caller that arrived through a skill and leaves the one
that arrived without a skill where `feedback/2026-08-01-003356` was. What it
cost instead was five entries in `knowledge/task-intents.json` and thirty lines
in `src/`. Both cards that carried the question are closed and both feedback are
archived. `feedback/2026-07-31-193005` asked the neighbouring question about a
self-reported call log, and it is archived too. Measuring that log needs a
forward `REVIEW-02` on the model that filed it, and that run was declined.

## Confirmed on 2026-08-22

Seven readings held the rule above and changed nothing in it, so each is a line
here rather than a section of its own. Five are the growth this decision's
**Wrong if** watches, and what each one bought is what its line says.

- 2026-07-31, the first addition since the file was written: a section on what a
  finding rests on, against a whole class of findings that read as established
  when they were derived.
- 2026-07-31, to 960 words: the fifth step, a deprecation sweep of the installed
  core over what the extension is reported to ship. It stands in the base rather
  than in a checklist because the upgrade skill starts from the same sweep, and
  it deleted the weaker copy the conformance skill carried.
- 2026-07-31, to 1099 words: the precondition above the order — no `typo3_` tool
  in the session means stop and say so, and never answer from what the model
  knows instead (`1960e50`, `R-SKL-008`). It costs no call and buys the one
  failure the order cannot see, an order followed perfectly against nothing.
  `feedback/2026-07-31-185553` was judged with it and archived as answered: all
  eleven tools that session could not reach were being served over stdio, and
  the gap was the client's connection.
- 2026-08-03, from 1367 words to 1452: `0fac7c6` put the changelog's own axes in
  place of the sweep's query set, which two models had swept a sitepackage with
  and got nothing back from, and `D-SKL-004` named the installed source as the
  step after the lookups.
- 2026-08-03, to 1531 words: step 5 stopped sending a PHP identifier at the
  manual, which matches page titles and section paths and never the text of a
  page — `D-ANS-010`.
- `feedback/2026-07-31` at 18:36, from `opencode/ling-3.0-flash-free` in a
  checkout this repository has never seen. It names the order as what prevented
  premature conclusions, which is the property claimed above from a model
  nothing here has measured. Judged in `D-SKL-002`.
- `feedback/2026-07-31-194826` on 2026-08-02, which ran step 3 and reported no
  gain. What stands without the question above is the other half: nothing a
  caller can pass says that a task changes nothing, and `R-GUI-006` is what must
  hold.

On 2026-08-22 two of the fifteen published skills carry a recorded forward run —
`typo3-extension-conformance` in `REVIEW-01` and `REVIEW-02`, and
`typo3-core-patch-review` in `REVIEW-03`. The third **Wrong if** above is what
that bears on, and it stands.
