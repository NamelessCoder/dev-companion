# Say what the environment runs by itself, beside the interpreter it runs on

**Serves:** R-PRJ-009, feedback/2026-08-03-154501-task-boot-an-existing-typo3-composer-project.md
**Priority:** normal

[`D-ANS-044`](../../decisions/answers/ans-044-the-environment-answer-carries-the-lifecycle-it-declares.md)
is the judgement and the evidence; what is left is the reading DDEV owes and the
field it decides. Start with DDEV's own documentation at the version in play —
`composer.lock` names none, so it is the DDEV on the machine the answer is
measured against — and settle four things: how `hooks` merge across
`.ddev/config.yaml` and the `.ddev/config.*.yaml` beside it, what
`override_config: true` does to that merge, whether the pull providers under
`.ddev/providers/` are reported at all and how a project's own is told from a
`#ddev-generated` one, and whether `.ddev/commands/` belongs beside them. Then
carry the answer in the `environment` object of `Project::describe()` and in the
declared `outputSchema` of `typo3_project_scope`, each hook as the stage it fires
at and the command it runs, and say in the prose beside the commands list that
those are what a caller may run while these are what runs without being asked.
`Project::runs()` is the existing rule for what a body does to the sources; the
hooks in the reporting project run `bin/typo3`, which it already answers
`unknown`, so whether it is applied to a hook at all is part of the same
reading.
