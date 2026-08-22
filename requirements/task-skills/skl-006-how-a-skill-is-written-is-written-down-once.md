---
id: R-SKL-006
title: 'How a skill is written is written down once'
status: held
---

# R-SKL-006 — How a skill is written is written down once

**The rules a skill is written under are stated in one place, and every rule
there names the test that holds it.**

They are the name it is filed and routed by, that it starts from the base and
states only what it adds, that it keeps no second copy of what a tool owns, that
its references are one hop away and loaded on demand, and that it says what it
owns and where it stops.

The rules that run over the skills directory are the ones a skill written later
is held to without its author ever seeing them, so they are also the ones that
have to be readable before it is written. The written form and that set of
assertions are held to each other in both directions: a rule stated with nothing
behind it, and a directory-wide assertion nobody wrote down, are both failures.

Three of them no test can hold, and they are stated with the rest because they
decide whether any of the others apply and what the file ends up saying: that a
domain earns a skill only where a scenario or a recorded run shows the tools and
skills that exist fail to carry the task; that the practice is researched — the
server's own answers, the official documentation, the tools the task runs
through — before a line is written rather than recalled while writing it; and
that the draft is shown whole to the person who asked for it, with feedback
asked for by name, before it is published. A skill written from recall is shaped
exactly like one written from the documentation, which is why the page has to
carry the step instead of a check.

## From

The feedback of 2026-07-30, trimmed on 2026-08-01 to its authoring half once
`bin/cli scenarios` and `scenarios/runs/` had answered the runner half. The
stable rules were visible by then and spread across seven assertions in
`SkillTest` and five skills restating them in their own words, which is the
arrangement that made them unreadable to the next author — and the next author
was the `typo3-extension-upgrade` skill, queued directly behind this.

## Held by

- `SkillTest::theAuthoringContractIsWrittenDownAndNamesWhatHoldsIt`, which holds
  [documentation/contributing/writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst)
  and the seven directory-wide assertions to each other; that a skill's author
  read the page before writing one is not guarded and cannot be.
