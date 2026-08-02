# Bound the sweep by the changelog's own axes, in the step that asks for it

**Serves:** feedback/2026-07-31-194459-the-conformance-skill-workflow-was-the-right.md
**Priority:** normal
**Branch:** todo/bound-the-sweep-by-the-changelogs-own-axes
**Claimed:** 2026-08-02

Ladder step 4, wording, on the evidence in
[`D-SKL-003`](../../decisions/task-skills/skl-003-a-sweep-is-bounded-by-the-changelogs-own-axes.md):
step 5 of `skills/base.md` fixes the sweep's query set to the extension's own
surface, and those words are matched all at once against titles the core wrote,
so the sweep returns nothing.

Rewrite that sentence in `skills/base.md` so the sweep is bounded by `type`,
`version` and `tag` — the query omitted rather than derived, the extension's
surface used to pick the tags and to verify what comes back — and carry the same
change through `R-SKL-005` and through
`SkillTest::theDeprecationSweepRunsFromTheExtensionsSurfaceAndIsReportedWhenItFindsNothing`,
which asserts the old wording verbatim. Read `typo3-extension-upgrade` in the
same pass: it sweeps `type: breaking` "as the base sweeps it" and inherits
whatever the base says. What the two bounds return here is in the decision, so
the step does not need establishing again; what does is which tags a sitepackage
sweep should name, since a tag names the system extension a change is in.
