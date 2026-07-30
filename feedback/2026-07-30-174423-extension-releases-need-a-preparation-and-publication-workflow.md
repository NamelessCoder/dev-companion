---
date: 2026-07-30T17:44:23+02:00
category: tool-gap
status: open
tool: typo3_project_scope, typo3_extension_scope, typo3_task_guide, typo3_architecture_lookup, typo3_documentation_lookup
---

# Extension releases need a preparation and publication workflow

## Observation

The server can assess an extension, test it, document it and draft a project
commit, but it does not carry an extension maintainer from a release candidate
to a verified release artifact. Release readiness spans owners that no current
task skill coordinates: supported TYPO3 and PHP constraints, extension metadata,
version consistency, changelog and migration documentation, licensing,
dependencies, generated assets, archive exclusions, quality commands, tags, and
the requirements of the intended package registries.

A generic conformance audit does not establish that the exact archive to be
published contains the intended files, omits development and secret material,
installs from its declared dependencies, and passes the same checks as the
checkout. Preparation and external publication also have different safety
boundaries: building and inspecting an artifact is local and reversible;
creating tags or publishing to a registry changes shared state and requires an
explicit request and confirmed target.

## Query

Prepare this TYPO3 extension for its next release to its package registries.
Build and verify the exact artifact, tell me what is still blocking the release,
and do not tag or publish anything until I explicitly ask.

## Suggestion

Add a `typo3-extension-release` skill that starts with project and extension
scope, selects the intended registries and version, and composes the existing
conformance, testing and documentation workflows into one release gate. Derive
registry requirements from current official documentation instead of retaining
them in the skill. Build the artifact through the repository's own release
command where one exists, inspect its file list and metadata, install or validate
it in a clean environment, and run the declared quality commands against the
release candidate.

End preparation with the artifact path, checksum, included and excluded
material, verification results, unresolved blockers and the exact publication
steps not taken. Treat tag creation, pushing and registry publication as a
separate explicitly authorized phase with confirmed repository, version and
credentials. Add a forward scenario that fails on an artifact containing
development files or secrets even when the checkout itself is green.
