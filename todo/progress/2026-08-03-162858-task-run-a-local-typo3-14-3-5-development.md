# Task: run a local TYPO3 14.3.5 development instance under DDEV (v1.25.1) for an extension, later ...

**Serves:** feedback/2026-08-03-162858-task-run-a-local-typo3-14-3-5-development.md
**Priority:** low
**Branch:** todo/task-run-a-local-typo3-14-3-5-development
**Claimed:** 2026-08-03

The settings-management half is answered. `project-configuration-files` names
the four sections DDEV's generator writes, what taking the file over costs, and
the installation it cannot configure — `R-KNW-060`, held by
`HintsTest::theDdevSettingsAnswerNamesEverySectionItGeneratesAndTheDatabaseItAssumes`.

What is left is the two lifecycle findings `D-KNW-049` kept out of that
requirement: `fail_on_hook_fail` defaulting to false, so a post-start hook that
installs TYPO3 can fail behind a green `ddev start`, and a `ddev config` run
rewriting `config.yaml` and dropping the keys that held their default value.
`D-ANS-044` reads the first of them as evidence for the lifecycle a project
answer declares and queues that separately, so what this needs is the judgement
of whether anything is owed beyond it — and where nothing is, this feedback is
archived rather than worked.
