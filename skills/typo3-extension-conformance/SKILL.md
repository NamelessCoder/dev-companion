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
3. Call `typo3_task_guide` with a short English task, affected area, target
   version, and change type to establish task-shaped checks.
4. Read [references/checklist.md](references/checklist.md) for the audit
   surfaces, the finding gate, and the severity rubric. An assessment walks the
   surfaces the checkout supports; it does not report whichever of them the
   files happened to show first.

## Ask before judging, on every surface in scope

Scope says which surfaces are in play, and reading says what is there. Neither
says whether it is right. That comes from the owner of the convention, and it is
asked for **before** a view of the subsystem is formed rather than to confirm
one that already exists:

- `typo3_architecture_lookup` with the subsystem's concrete paths and a short
  English description. One query per surface in scope; a single broad query is
  not subsystem evidence.
- The lookup that owns that surface's runtime facts, where one exists:
  `typo3_backend_module_lookup` for registered modules and routes,
  `typo3_icon_lookup` for icon identifiers, `typo3_label_lookup` for labels and
  overrides, `typo3_fluid_namespace_list` for globally available Fluid
  prefixes, `typo3_configuration_lookup` for effective runtime configuration.
- `typo3_documentation_lookup` with several short English queries and the
  target version where an official API or configuration detail decides the
  finding.
- `typo3_changelog_lookup` for the installed core when an upgrade or a
  deprecated API is in scope, and verify each identifier it names in the
  checkout.

Read the checkout for what none of those can know: the files themselves, the
registrations, the tests, the documentation, and the conventions the project has
settled into.

Then read every returned rule in both directions. It says what new code should
do, and it says what this checkout is already doing wrong. A file that has
settled into the opposite of a rule is a finding, not a local style to preserve
— the project's own habits are part of what is being assessed, so consistency
with them establishes nothing.

Do not report the absence of an optional subsystem as a defect. But a surface
that is present and was never asked about is **unassessed**, and unassessed is
not clean: say so in the result. A defect nobody looked for and a defect that is
not there read identically in a report that does not separate them. Distinguish
a verified violation from a recommendation and from missing evidence.

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

Close on coverage rather than on a summary: every surface the checkout supports,
each marked assessed or unassessed, clean ones briefly. That list is what makes
an audit readable as one. Without it a thorough report and a narrow report look
alike, and the cheapest way to look thorough is to examine less.

This skill owns assessment and prioritization, and it owns saying who takes each
finding onward. Name the workflow the follow-up belongs to — the testing,
documentation, backend-module or content-element skill — in the result itself,
whether or not fixes were requested: a reader deciding what to do next needs
that as much as a session that was told to do it. When fixes are requested, hand
over to that skill for the changes in its area and keep conformance responsible
for re-checking the resulting finding.
