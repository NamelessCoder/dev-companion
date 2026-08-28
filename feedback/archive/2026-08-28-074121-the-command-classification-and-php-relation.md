---
date: 2026-08-28T07:41:21+00:00
category: idea
status: closed
closed: 2026-08-28
model: claude-opus-5[1m]
tool: typo3_project_describe
directory: /home/benji/projects/bootstrap_package
---

# the command classification and PHP relation block are what made a check-only review runnable, kee...

## Observation

Task: review pull request #1613 against bootstrap_package under instructions not to change files, then fix it and cover it with a test. This feedback is about what worked, so it is not broken later.

Three things in the single typo3_project_describe answer carried the session:

1. `commands[]` with each entry marked `runs: check | change | unknown`. The review was told to change nothing. Without the marking I would have either run nothing and reported the pipeline's word for it, or run `composer cgl` and rewritten the author's tree. I ran exactly the three checks — `test:php:lint`, `phpstan`, `cgl:ci` — and named `test:php:unit`, `test:php:functional` and `npm run build` in the report as available-but-not-run. The marking is doing real work: it converts "do not change files" from a reason to skip evidence into a filter over which evidence to gather.

2. The `phpRelation` block, with `installedPhpBound: 8.4.1` and `environment.php: 8.5` beside the caller's shell. My first attempt to render a Fluid template died in Composer's platform check: "Your Composer dependencies require a PHP version >= 8.4.1. You are running 8.3.23." I had that answer already in the project_describe output and went straight to `ddev exec`, which the same answer named as the environment the repository configures. One failed call, not a debugging session. The tool description's claim that this is "what marking a command a check never said" is accurate, and it was load-bearing here.

3. `installedAgainstLock: {state: "matches"}`. It let me trust the vendor tree as evidence for API questions, including reading Fluid 5's own TemplateView source when its API turned out to have moved. Without it I would have had to establish that separately before any reading was worth anything.

Also worth keeping: `sites: []` was correct and mattered later — the functional test had to write its own site configuration, and knowing the repository configures none framed that as expected rather than as something I had broken.

Fourth, from a different tool but the same session: typo3_task_guide's checklist entry "Enumerate what the diff removes or renames before judging what it adds" directly shaped a section of the review that I would not otherwise have written. The diff removed nothing, and saying so explicitly is what distinguished a surface that was checked from one that was silent.

## Query

typo3_project_describe (no arguments), called once at the start of the session, in a composer-project checkout of bootstrap_package with TYPO3 14.3.6 installed and a ddev environment declared.

typo3_task_guide task="Review an incoming pull request that changes an f:if condition in a Fluid partial of the table content element", changeType="audit", paths=["Resources/Private/Partials/ContentElements/Table/Columns.html"]

## Suggestion

Nothing to change. Recorded so that a later reshaping of typo3_project_describe's answer — it is a large one and an obvious candidate for trimming — knows that `commands[].runs`, `phpRelation`, `installedAgainstLock` and `environment` were each used, not skimmed, in a single session, and that dropping any of them turns a decision into a debugging step.

If the answer does get trimmed, these four are the ones that earn their bytes. The 21-entry `guides` array in the same answer is the part I did not use, and that is filed separately as a routing problem rather than as an argument for removing it.
