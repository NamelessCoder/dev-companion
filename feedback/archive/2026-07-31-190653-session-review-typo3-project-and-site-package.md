---
date: 2026-07-31T19:06:53+00:00
category: missing-knowledge
status: closed
closed: 2026-08-02
model: unknown
tool: typo3-extension-conformance, skill
directory: /home/benji/projects/site-new
---

# Session: Review TYPO3 project and site package for critical problems and risks

## Observation

Session: Review TYPO3 project and site package for critical problems and risks

Skills activated: typo3-extension-conformance

The skill fitted the task perfectly — it provides the exact workflow for a scoped conformance audit against a TYPO3 extension. The skill's references/base.md establishes the correct order of operations, and references/checklist.md provides the surfaces to assess.

In it I would keep: the assessment framework, finding gate, and severity rubric. I would drop: nothing from the skill is problematic.

Tool calls made (in order):
1. typo3_project_scope - essential starting point, confirmed installation, extensions, commands
2. typo3_extension_scope with "printworks_sitepackage" - revealed what extension registers and ships
3. typo3_architecture_lookup - none attempted, surfaces not concrete enough without paths
4. typo3_changelog_lookup with deprecation type - none attempted, scope didn't need deprecation sweep
5. typo3_backend_module_lookup - none attempted, not needed for this assessment
6. typo3_icon_lookup - none attempted, not needed for this assessment
7. typo3_label_lookup - none attempted, not needed for this assessment
8. typo3_fluid_namespace_list - none attempted, not needed for this assessment
9. typo3_configuration_lookup - none attempted, not needed for this assessment
10. typo3_documentation_lookup - none attempted, no documentation queries needed
11-16. bash commands - necessary exploration of extension files, structure, and test coverage

Redundant/unnecessary calls to repeat: grep for "license" (ran twice with different patterns), read of checklist and base.md (separate files but same purpose)

Something went wrong: The extension had no manual/README files despite shipping XLF files and being a project extension. This was surprising given the TYPO3 convention that project extensions should have documentation.

The server saved me from: Assuming the PHP version constraint (^8.3 in composer.json) was correct — the project_scope shows PHP ^8.4, so the constraint was outdated. Without this check, I would have reported a PHP version mismatch as a critical finding incorrectly.

What I had to establish elsewhere: I had to read the actual composer.json file to find the version mismatch. The extension_scope and project_scope tools don't validate manifest contents — they only report what's declared.

model: unknown
tool: typo3-extension-conformance skill
query: Review TYPO3 project and its site package. Identify the most important concrete problems, risks, or missing safeguards, prioritize them, and tell me what should happen next. Do not change files.
category: missing-knowledge
suggestion: The skill provides the exact framework needed for a conformance audit, including the correct order of operations and assessment surfaces. It includes references for the assessment process and severity rubric.

Notes filed:
1. skill-fit: typo3-extension-conformance provided the exact workflow for this task and includes references for the assessment process and severity rubric
2. tool-usage: All surface lookups were appropriately targeted, and bash exploration was necessary for finding concrete issues
3. version-mismatch: The server saved me from an incorrect PHP version finding by showing the actual project scope
4. documentation-gap: The extension had no manual or README despite being a project extension and shipping XLF files
5. skill-assessment: The typo3-extension-conformance skill fits the task perfectly and should be used for similar conformance audits

The main finding: PHP version mismatch between composer.json (^8.3) and project scope (^8.4), missing documentation, and PHP files without license headers.

## Query

Review TYPO3 project and its site package. Identify the most important concrete problems, risks, or missing safeguards, prioritize them, and tell me what should happen next. Do not change files.

## Suggestion

The skill provides the exact framework needed for a conformance audit, including the correct order of operations and assessment surfaces. It includes references for the assessment process and severity rubric.
