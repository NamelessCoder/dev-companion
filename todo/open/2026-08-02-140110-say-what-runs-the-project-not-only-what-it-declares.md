# Say what runs the project, not only what it declares

**Serves:** feedback/2026-07-31-193611-task-typo3-extension-conformance-audit-my.md, R-PRJ-008
**Priority:** normal

Give `typo3_project_scope` the environment the repository configures for itself:
read `.ddev/config.yaml` in `Project::describe()` — never `ddev describe`, so
`R-PRJ-001` and `R-DIS-006` both stand — and put its `php_version` next to the
declared constraint in the opening line, with the command list saying that what
it lists runs inside that environment rather than in the caller's shell. Settle
first, against DDEV's own documentation and the v1.25.1 installed here, whether
a `.ddev/config.*.yaml` can override `php_version` and how the file spells a
version that is not a bare `8.4`; then decide from `Typo3Cli::viaOverride()` and
`viaDdev()` what the answer says for a project that configures something DDEV is
not, because a field that is silent there reads as "no environment" rather than
as "not known". The schema in `ProjectScope::outputSchema()`,
`documentation/clients/tool-answers/typo3_project_scope.md` and the assertion
`R-PRJ-008` names in `ProjectTest` move with it.
