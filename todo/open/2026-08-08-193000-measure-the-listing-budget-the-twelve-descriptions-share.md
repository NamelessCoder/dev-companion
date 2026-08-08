# Measure the listing budget the twelve descriptions share, and get under it

**Serves:** R-SKL-010
**Priority:** high

The twelve published descriptions total **7153 characters**, measured on
2026-08-08 over `skills/*/SKILL.md`. The client does not read them one at a
time: Claude Code holds every skill's metadata in one listing budget of about
**1% of the context window, falling back to 8000 characters**, and where the
listing overflows it shortens or drops descriptions outright, least-used first
([anthropics/claude-code#56710](https://github.com/anthropics/claude-code/issues/56710),
[#47627](https://github.com/anthropics/claude-code/issues/47627); the per-skill
cap is a separate 1536 raised from 250 in 2.1.105, and no description here is
near it — the longest is 951).

So this server spends 89% of the fallback budget before the project's own
skills, plugins and slash commands are counted, and on a fresh install every one
of the twelve is least-used. What a dropped description costs is already
recorded:
[`D-AUD-003`](../../decisions/audience/aud-003-the-instructions-carry-the-entry-point.md)
is a review prompt whose every criterion the conformance skill would have met,
which did not activate it, and thirty-five Bash calls instead.

This runs against `R-SKL-010`, which is why it is filed there rather than as a
tidying step. That requirement makes a description name **every side** of what
its skill owns, and
`SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement` holds the pair
it was measured on. Naming every side is what makes descriptions long. Cutting
them is not free and the trade is real in both directions: a description trimmed
under budget that stops naming a side loses the task that arrives from that
side, and no listing warning fires for it.

The step is to **measure before deciding anything**. Establish what the real
budget is for a session that has this server installed — 1% of which context
window, and what the project's own skills add on top — then say for each of the
twelve which characters are triggers a user would type and which are workflow
summary that `R-SKL-010` never asked for. n8n states the rule the trimming
follows ("be explicit about every situation that should activate the skill,
since Claude undertriggers by default") and obra/superpowers measured the
failure of the other kind: a description that summarised a workflow made an
agent do one review where the skill specified two, because the body became
documentation it skipped. Trim the summaries, keep the sides. Where the total
still will not fit, that is a finding about how many skills this server can
publish at once and belongs in a decision rather than in a further trim.
