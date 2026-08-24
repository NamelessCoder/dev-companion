# Say which state the repository is in where no installation is reached

**Serves:** feedback/2026-08-24-140259-changelog-lookup-was-unusable-before-composer.md
**Priority:** normal
**Branch:** todo/say-which-state-the-repository-is-in-where-no-installation-is-reached
**Claimed:** 2026-08-24

Carry the state `typo3_project_describe` already reports into
`Result\Unsupported`, so a tool that cannot reach an installation says whether
the repository declares TYPO3 with nothing installed yet or declares nothing at
all. `Instance::project()` computes it and `Installation\Project` is its only
caller today; the state joins `cause` in `Schema::unsupported()`, and
`D-ANS-105` says what the boundary is, what may not move with it and what is
still open — which field carries the state, and whether the remedy is worded
beside it.
