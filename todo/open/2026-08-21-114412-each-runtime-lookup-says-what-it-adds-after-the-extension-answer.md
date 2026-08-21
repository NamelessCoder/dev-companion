# Each runtime lookup says what it adds after the extension answer

**Serves:** feedback/2026-08-19-094432-the-skill-s-ask-both-lookups-per-surface-rule.md, R-SKL-026, D-SKL-069
**Priority:** normal

Judged as wording rather than a gap: the base's *Two kinds of lookup* section
pairs runtime against conventions, and its own step 2 has already answered the
runtime half for four of the five surfaces, so a session that did the order is
right to skip them. `D-SKL-069` has the evidence and what each of the five
actually adds. Rewrite that section in `skills/base.md` so each lookup carries
what it answers after `typo3_extension_describe` — the resolved value, the
identifier whatever package registered it, the overridden label, the inherited
navigation component — and cut the restatement out of
`skills/typo3-extension-health/SKILL.md`, whose *Ask before judging* section says
it a second time in the same words. Discharge none of the five: `D-SKL-055`'s
construct is for a call the session already holds an answer to, and step 2
answers none of these whole. Then put the `SkillTest` assertion that reads it
into `R-SKL-026`'s **Held by**, which names none today.
