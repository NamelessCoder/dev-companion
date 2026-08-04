---
name: typo3-extension-documentation
description: Create, update, or review documentation for a TYPO3 project or extension, including Documentation/ manuals, README content, configuration and integration guides, examples, changelog-facing migration notes, screenshots, and user-facing XLF labels. Use when documenting extension installation, configuration, editors' workflows, APIs, content elements, plugins, backend modules, site sets, TypoScript, upgrades, or ensuring docs match the active TYPO3 version and implementation.
---

# TYPO3 Extension Documentation

Document the implementation that exists and bind TYPO3 claims to the active
version. Keep this skill as routing and documentation workflow; do not copy API
reference material into it.

## Gather authoritative evidence

Work through [references/base.md](references/base.md) first — it fixes the order
every task here starts in and why that order is not interchangeable. Its second
step is the one this workflow leans on hardest: `typo3_extension_scope` reports
whether a manual and a README exist at all, and which XLF files there are with
the source language each declares.

Then, for this workflow:

- `typo3_documentation_lookup` with several short English queries and the target
  version for every external TYPO3 API or workflow claim. Prefer the links it
  returns over hand-built documentation URLs.
- `typo3_label_lookup` before adding or rewording a label, and
  `typo3_translation_domain_lookup` for the extension's own XLF path.
- Read its public configuration, registration files, templates, examples,
  existing manual and tests. Code is the source for what this package
  implements; documentation that outruns it is the defect this workflow exists
  to avoid.

For backend documentation, verify modules through `typo3_backend_module_lookup`,
icons through `typo3_icon_lookup`, and undeclared Fluid prefixes through
`typo3_fluid_namespace_list` when those facts appear.

## Choose the documentation surface

Read [references/checklist.md](references/checklist.md) for audience and surface
selection, secret hygiene, evidence conflicts, and the completion gate.

- Extend existing `Documentation/` structure instead of creating a competing
  manual layout.
- Keep README content concise: purpose, essential setup, and the canonical
  documentation link where one exists.
- Separate administrator/developer setup from editor workflows.
- Derive configuration keys, defaults, types, and examples from the checkout.
- Mark version requirements and migration behavior only when supported by
  Composer constraints, the installed changelog, or official documentation.
- Use runnable, minimal examples consistent with the extension's namespaces and
  file layout.

Do not claim that a command, module, option, label, or rendered result exists
without checking its owner. Do not document internal implementation details as
stable public API.

## Write and verify

- Preserve the repository's existing markup format, heading hierarchy, link
  style, terminology, and line-wrapping conventions.
- Reuse canonical TYPO3 terminology and existing project labels.
- Make prerequisites, commands, expected outcomes, and failure conditions
  explicit.
- Validate internal links, referenced paths, configuration examples, and code
  identifiers against the checkout.
- Run documentation, lint, and test commands only when the project declares them
  or the task establishes them.
- Report the files updated, validation performed, and any behavior that could
  not be verified from the installation or project.

This skill owns documentation and user-facing wording changes. Test
implementation is `typo3-extension-testing`, conformance assessment is
`typo3-extension-conformance`, backend-module code is
`typo3-backend-module-development` and a content element is
`typo3-content-element-development`. Activate the owner rather than making the
change here, and document only the public behaviour it hands back verified.
