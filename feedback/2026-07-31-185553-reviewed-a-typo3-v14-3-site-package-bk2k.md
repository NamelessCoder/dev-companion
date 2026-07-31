---
date: 2026-07-31T18:55:53+00:00
category: tool-gap
status: open
model: unknown
tool: typo3extensionconformance
directory: /home/benji/projects/site-new
---

# Reviewed a TYPO3 v14.3 site package (bk2k/printworks-sitepackage) for concrete problems, risks, a...

## Observation

Reviewed a TYPO3 v14.3 site package (bk2k/printworks-sitepackage) for concrete problems, risks, and missing safeguards. The typo3-extension-conformance skill was activated and its methodology (base.md, checklist.md) was useful for guiding the audit. However, the skill references many lookup tools (typo3_project_scope, typo3_extension_scope, typo3_architecture_lookup, typo3_backend_module_lookup, typo3_icon_lookup, typo3_label_lookup, typo3_fluid_namespace_list, typo3_configuration_lookup, typoyo3_documentation_lookup, typo3_changelog_lookup, typo3_task_guide) that were NOT available as callable tools in this environment. The audit was completed entirely by reading the checkout and from the model own knowledge, with no way to verify findings (e.g. whether TYPO3 core reads TYPO3_ENCRYPTION_KEY from env, whether additional.php reaches production) against the knowledge server. The skill methodology worked well on its own but could not execute its intended two-phase verification (runtime lookup + convention lookup) for any surface.

## Query

Activated typo3-extension-conformance skill; it instructed to call typo3_project_scope, typo3_architecture_lookup, typo3_configuration_lookup, typo3_documentation_lookup, and typo3_changelog_lookup before judging each surface, but these tools were not callable.

## Suggestion

Expose the MCP server tools (typo3_project_scope, typo3_configuration_lookup, etc.) as callable functions in the agent environment. The skill depends on them for its two-phase verification (runtime facts + convention rules). Without them, the skill degrades to a checklist that can only be partially executed.
