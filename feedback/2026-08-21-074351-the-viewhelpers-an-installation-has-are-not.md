---
date: 2026-08-21T07:43:51+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_fluid_namespace_list
---

# the ViewHelpers an installation has are not answerable, only the namespaces they live in

## Observation

typo3_fluid_namespace_list answers which Fluid namespaces are globally registered, so a template knows which prefixes it may use undeclared. What it does not answer is what is in them: which ViewHelpers this installation actually has, what arguments each takes, which are required, and what the class says it is for. typo3_documentation_lookup reaches the official ViewHelper Reference, but that is the documented core set for a version rather than what is installed — a third-party ViewHelper is in neither answer.

This is the same shape as typo3_icon_lookup and typo3_label_lookup, both of which exist because the identifier fails only at runtime and no bundled answer could be right for one installation. A ViewHelper argument name is exactly that: wrong, it throws when the template renders.

The other implementation lists them by class and by namespace, resolves which registration wins where several PHP namespaces share one Fluid namespace, searches identifier, class and PHPdoc, and returns prepared argument definitions per ViewHelper.

## Query

Task: compare this server against another TYPO3 MCP server. No failing call — typo3_fluid_namespace_list answers the prefixes and stops there.

## Suggestion

Answer the installed ViewHelpers from the running installation: identifier, class, what it is for, and the argument definitions with names, types and which are required. Search by identifier, class or description, and validate a batch of identifiers the way typo3_icon_lookup validates icons — that is the call a session makes when it has just read a template. Where the console cannot be reached, the registered namespaces of the installed packages are the fallback, and the answer says what that leaves out.
