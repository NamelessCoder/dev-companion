---
date: 2026-07-31T18:59:00+00:00
category: tool-gap
status: open
model: unknown
tool: typo3extensionconformance, typo3_project_scope, typo3_architecture_lookup, typo3_configuration_lookup, typo3_documentation_lookup, typo3_changelog_lookup
directory: /home/benji/projects/site-new
---

# During an audit of the Printworks 3D site package, the typo3-extension-conformance skill was acti...

## Observation

During an audit of the Printworks 3D site package, the typo3-extension-conformance skill was activated. Its base.md instructs the agent to call typo3_project_scope, typo3_architecture_lookup, typo3_configuration_lookup, typo3_documentation_lookup, and typo3_changelog_lookup before reading any code. None of these tools were available as callable functions in the agent environment. The audit was completed entirely by reading the checkout and from the model own knowledge, with no runtime or documentation verification. Only after completing the audit did I discover the tools are available by invoking the typo3-cms-mcp binary directly via the MCP stdio protocol, which I was not initially aware of.

## Query

Activated the typo3-extension-conformance skill. It instructed: 1) typo3_project_scope for installation metadata. 2) typo3_extension_scope per extension. 3) typo3_task_guide for the workflow. 4) typo3_architecture_lookup per subsystem. 5) typo3_changelog_lookup for deprecations. None of these were callable.

## Suggestion

Make the typo3-cms-mcp server tools discoverable and callable in the agent environment without requiring the agent to discover the binary manually. The .mcp.json file is present but the tools were not exposed in the function list.
