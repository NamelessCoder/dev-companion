---
id: D-SKL-001
title: 'The order a task starts in is one file'
date: 2026-08-01
status: confirmed
coveredBy:
  - SkillTest::theBaseFixesTheOrderEveryTaskStartsIn
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

The **Wrong if** got a second answer from a strength, and it lands in both
halves: the base was outrun and one of its steps was read past. A session whose
own account is that the order fit perfectly named the workflow minus step 3, and
its tool log puts a glob and fifteen file reads ahead of the first hint lookup.
The installed copy carried the step, which is dated and settles that. Re-run,
the call that step makes answers with the lookups the caller has just read and
names no workflow at all, which is what a second session reported from the other
side — one skipped step 3 and reported no loss, one ran it and reported no gain.
So the question is what step 3 is for once a task skill is loaded, and the two
answers run opposite ways: this file says what the call is worth, or the tool
names the workflow it claims to.

## Confirmed on 2026-08-02

The same run was read again from the call log rather than from the strength
beside it, and the log is the stronger artifact: numbered, and filed while the
account was still being written. It records the base outrun at the one step the
base exists for — steps 1 and 2 in place, step 3 never run, then a glob and
fifteen reads before the first lookup. That is worse than the **Wrong if**
predicted, which expects the order to survive where a checklist reinforces it:
the checklist here carried the rule a second time in its own words.

The evidence is a report rather than a transcript. What reproduces is the
server's half, and step 3 is the one that would not have paid: the call matches
no intent for an audit and hands back a brief for changing a package to a run
told to change nothing.

## Confirmed on 2026-08-02

**Stopping is still right when the server is one process away, and the base does
not learn the way round.** Three things settled it. The base cannot carry the
route: one file is copied into every published skill byte-for-byte, while the
path to the binary is a property of the machine. What the hand-driven route
produced is on record — the same session pasted a live encryption key into this
repository, which is what acting as your own client costs. And stopping reaches
somebody who can act. What is left is not a skill question: the install writes
the entry and never says a callable tool is one step further on (`D-DIS-009`).

## Since then

The question got its other side, from a session that had no skill at all: it
looked nothing up until the user demanded it, and the route through the server
ends before the skill. The entry point was in the text it was sent, and it hands
over nothing — re-run, the guide answers with the two hints that session spent
its evening guessing at and names seven tools, none of them a skill.

So the two answers this question holds open are not the same size. Saying in the
base what the call is worth does nothing for a caller that arrived without a
skill; only the other answer reaches this session. On the ladder that is step 3
— the skill exists, the payload is in the guide's answer, and nothing joins
them.

## Since then

The question was answered on 2026-08-03 by the person who was asked, and it is
the second of the two: `typo3_task_guide` names the workflow step 3 says it
returns (`D-SKL-013`). The base is unchanged by it, which is the half worth
recording — the answer that would have grown this file is the one that was
turned down, and what it cost instead was five intent entries and thirty lines
in `src/`.

## Confirmed on 2026-08-22

Seven readings held the rule and changed nothing in it. Five are the growth this
**Wrong if** watches, and each bought one thing: what a finding rests on, the
deprecation sweep, the precondition above the order, the changelog's own axes in
place of a query set, and the manual stopping being where a PHP identifier is
sent. One is a strength from a model nothing here has measured, and one ran step
3 and reported no gain. Two of the fifteen published skills carry a recorded
forward run, which is what the third **Wrong if** bears on.
