# Decide whether a skill change buys a baseline run

**Serves:** R-SKL-006
**Priority:** normal
**Waiting on:** Whether a skill change may cost a handful of paid model runs
    before it is published — and if so, whether that is every change or only a
    new skill. Nothing in this checkout decides it: it is a standing cost
    against a benefit no assertion here can measure, so it is the maintainer's
    call rather than a reading.

Three of the authoring steps are the author's word and nothing reads them off a
file: that a domain earned a skill at all, that the practice was researched
before it was written, and that the draft was shown and asked about.
[writing-a-skill.md](../../documentation/contributing/writing-a-skill.rst) says
they are written down because that is all that can be done for them.

The reading of 2026-08-08 found that is not quite all. obra/superpowers holds
the first of the three with a rule they state as an Iron Law — no skill without
a failing test first, extended to **edits** as well as new skills — and the test
is literally to run the task without the skill and watch what the session does.
Two other projects reached the same place independently:
`czlonkowski/n8n-skills` calls it evaluation-driven development and writes the
scenarios before the SKILL.md, and Anthropic's own `skill-creator` spawns the
with-skill and baseline runs in the same turn. What makes it more than a slogan
is that superpowers ran it against their own file and got a number: deleting one
section degraded test-first behaviour from 8/10 to 5/10, corroborated on two
models, which is why the section came back in a different form rather than on
somebody's judgement.

This repository is closer to that than it looks. `scenarios/contracts/` already
holds the prompts, `scenarios/runs/` already records a graded run, and
`documentation/feedback/` already has the debrief. The missing half is the run
**without** the skill installed, which is what turns "the skill helped" into a
measurement, and `.environments/` already makes an uninstalled checkout cheap to
produce.

What it costs is the question. Superpowers price their micro-tests at roughly
$0.15–0.30 a sample and run five or more per variant against a no-guidance
control, so one skill change is a few dollars and a session's attention, not one
call. That is small in isolation and is a standing charge on every edit if the
rule is taken whole.

Nothing is broken while this waits. The three steps stay written down and
unheld, which is what they are today, and the two skills the other cards touch
can be changed without it. What would move this is an answer to the question
above, or a session that changed a skill, published it, and found out afterwards
that the change made the workflow worse — which is the evidence this card is
really asking whether to buy in advance.
