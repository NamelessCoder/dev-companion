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
