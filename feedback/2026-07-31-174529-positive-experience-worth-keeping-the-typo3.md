---
date: 2026-07-31T17:45:29+00:00
category: idea
status: open
model: big-pickle
tool: typo3_changelog_lookup, typo3_project_scope, typo3_extension_scope
directory: /home/benji/projects/bootstrap_package
---

# Positive experience worth keeping: the typo3-extension-conformance skill workflow together with t...

## Observation

Positive experience worth keeping: the typo3-extension-conformance skill workflow together with the MCP lookups carried the whole review. typo3_project_scope correctly classified every repo command as check or change (and flagged phplint/cgl as unrunnable on the host PHP, which matched the platform_check reality). typo3_changelog_lookup precisely confirmed the ext_tables.php v14.3 deprecation (#109438), the Install->Core UpgradeWizard attribute/interface move (#106947) and the addPiFlexFormValue deprecation (#107047) — that changelog evidence is what made the priority-1 finding (v15 blockers hidden behind the phpstan baseline) provable rather than guessed. typo3_extension_scope surfaced the tables, content elements, icon registration and class structure cheaply.

## Query

review of bk2k/bootstrap-package for maintainability

## Suggestion

Keep the changelog-first review approach and the project/extension scope tools; close the two gaps recorded separately (per-version behavior questions and dead-registration detection).
