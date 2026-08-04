---
description: >-
  Where the upstream contribution documentation lives, as links to it.
whenToUse: >-
  When a question goes past what the bundled documents answer and the official guide has to be read.
hints: []
---

# TYPO3 Contribution Sources

Official upstream documentation behind this knowledge base. The guide itself is
not bundled — link to these URLs when a question goes past what the local
documents cover.

## Core Contribution Guide

Entry point:
https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/

- Git and Gerrit setup:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Setup/Git/Index.html
- Commit hooks:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitHook.html
- Create a patch:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/BugfixingAZ/Index.html
- Upload a new patch set:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/HandlingAPatch/ChangeAPatch.html
- Cherry-pick a patch from Gerrit:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/HandlingAPatch/CherryPick.html
- Backport a change:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/CoreMergers/Backport.html
- Commit message rules:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html
- Git cheat sheet:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/CheatSheets/Git.html

Review system: https://review.typo3.org — Forge issues:
https://forge.typo3.org/projects/typo3cms-core

## Local Policy

- Prefer official TYPO3 documentation for workflow rules.
- Prefer the local TYPO3 core checkout for available scripts, current branch
  state, and changed files.
- Keep derived rules short and link back to the source when a recommendation
  depends on official process.
- Re-check upstream documentation when a workflow rule is likely to have
  changed.
