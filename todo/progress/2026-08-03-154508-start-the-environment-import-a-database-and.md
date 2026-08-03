# Answer a brief for booting an installation with hints about booting one

**Serves:** feedback/2026-08-03-154508-start-the-environment-import-a-database-and.md
**Priority:** low
**Branch:** todo/start-the-environment-import-a-database-and
**Claimed:** 2026-08-03

The hint half of that feedback, which `D-GUI-008` assumed `CHANGE_TYPE_TERMS`
would move and which it cannot: the terms of a change type reach
`Domains::detect()` for the domains a brief reports, while `Hints::find()`
detects the domains it selects by from the paths and the task text alone.
Measured on the reported call on 2026-08-03 with `frontend build sitepackage` as
the terms of `operations`: the same four hints came back —
`extension-asset-build`, which was on topic, and `datahandler-basics`,
`fal-basics` and `public-assets`, which were not — and the brief gained
`buildCss`, `lintScss` and `typo3_component_lookup`, which is what writes backend
markup. So what is missing is a statement in the corpus about bringing an
installation up, not a domain signal.

The four cards behind
[`D-SKL-012`](../../decisions/task-skills/skl-012-bringing-a-development-installation-into-existence-earns-a-task-skill.md)
are what would supply one — the Composer root package, the `typo3 setup`
command, the distribution import and DDEV in practice. So the reading starts
with whether they have landed and what they landed as: a hint on that subject is
what this call would then select, and re-running the query is what says whether
it does.

`low`, because the shape of the brief is no longer the complaint: `operations`,
the `installation-operations` intent and its checklist landed on 2026-08-03, and
what is left is which statements a brief for that work carries.
