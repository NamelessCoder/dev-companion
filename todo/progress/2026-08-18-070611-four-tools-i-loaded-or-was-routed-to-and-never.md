# Split the setup step between the installed console and the manual

**Serves:** feedback/2026-08-18-070611-four-tools-i-loaded-or-was-routed-to-and-never.md
**Priority:** normal
**Branch:** todo/four-tools-i-loaded-or-was-routed-to-and-never
**Claimed:** 2026-08-18

Judged on 2026-08-18 as the ladder's rung 3, routing, and queued because the
change is a sentence in a published skill. `D-SKL-057` carries the reading and
the boundary: which options the setup command offers, and which of them this
installation's packages have disabled, is the console's; what a value does to
the settings and what the command refuses is the manual's.

The step is 3 of *Create one where none is declared* in
`skills/typo3-development-installation/SKILL.md`, which today routes the whole
question to `typo3_documentation_lookup`. What it has to gain is the console as
the source of the option set, without taking the two checks it already names off
the manual. What the sentence says is settled in `D-SKL-057`: it names
`--distribution` rather than stating a rule, because the sweep found no second
option of its kind, and it says the property starts at 14, because
`.checkouts/13.4` composes no option description that way and `.checkouts/14.3`
and `.checkouts/main` both do. What is left is writing it inside the retention
rule the skill is held to.

`ROUTING_SKILLS` in `SkillTest` records which tools this skill routes through
and in what order, so a routing that changes has to be right there too —
`D-SKL-055` is why a mention that disowns a call is written as a discharge
instead. The priority is `normal` because the option this misroutes is the
seeding one, which step 4 of the same workflow reaches for.
