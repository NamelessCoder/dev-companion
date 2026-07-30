---
name: typo3-extension-conformance
description: Review, audit, or improve a TYPO3 project, sitepackage, or extension against its checkout and active installation, and report what is wrong with it in priority order. Use for any open-ended request to look over a repository and say what matters — reviews, readiness, quality and security assessments, modernization, compatibility with the active TYPO3 version, suspicious registration or configuration, TCA, services, backend modules, content elements, site sets, TypoScript, Fluid, labels, icons, security boundaries, deprecated APIs, and before or after a TYPO3 upgrade.
---

# TYPO3 Extension Conformance

Produce an evidence-backed audit against the active installation and the
checkout, not a generic checklist. Keep this skill as routing and assessment
method; do not embed versioned TYPO3 facts.

## Establish scope and evidence

1. Call `typo3_project_scope` to identify the project type, TYPO3 and PHP
   constraints, own extensions, sites, site sets, and declared commands.
2. Call `typo3_extension_scope` for every extension in scope.
3. Inspect its Composer metadata, registration/configuration files, Classes/,
   Resources/, Tests/, documentation, and nearby project conventions.
4. Call `typo3_task_guide` with a short English task, affected area, target
   version, and change type to establish task-shaped checks.
5. Call `typo3_architecture_lookup` with concrete paths and short English task
   descriptions for each affected subsystem. Do not use one broad query as a
   substitute for subsystem evidence.
6. Call `typo3_documentation_lookup` with several short English queries and the
   target version where official API or configuration details matter.
7. When an upgrade or deprecated API is in scope, call
   `typo3_changelog_lookup` for the installed core and verify any referenced
   identifier in the checkout.

Use installation-backed lookups for facts an abstract checklist cannot know:

- `typo3_backend_module_lookup` for registered modules and routes.
- `typo3_icon_lookup` for backend icon identifiers.
- `typo3_label_lookup` for labels and overrides.
- `typo3_fluid_namespace_list` for globally available Fluid prefixes.
- `typo3_configuration_lookup` for effective runtime configuration.

## Assess by subsystem

Read [references/checklist.md](references/checklist.md) for the relevant audit
surfaces, finding gate, and severity rubric.

Review only categories supported by files or behavior in scope:

- package metadata, autoloading, PHP/TYPO3 constraints, and extension identity;
- services, dependency injection, events, middleware, and security boundaries;
- TCA, database schema, DataHandler behavior, content elements, and plugins;
- site sets, TypoScript, TSconfig, Fluid roots, namespaces, and translations;
- backend modules, routes, components, labels, icons, and access control;
- tests, declared quality commands, documentation, and upgrade readiness.

Do not report absence of an optional subsystem as a defect. Distinguish a
verified violation from a recommendation and from missing evidence.

## Report and improve

Order findings by severity and include:

1. the concrete file or runtime registration;
2. the observed behavior or configuration;
3. the applicable MCP or official-documentation evidence;
4. the consequence;
5. a scoped remediation and relevant project check.

For a requested audit, stop after findings unless fixes were also requested.
For requested improvements, make the smallest coherent changes, preserve local
project conventions, and run the commands declared by `typo3_project_scope`.
Report clean categories briefly and list unverified categories explicitly.

This skill owns assessment and prioritization. When fixes are requested, use
the testing, documentation, or backend-module skill for changes in those areas;
keep conformance responsible for re-checking the resulting finding.
