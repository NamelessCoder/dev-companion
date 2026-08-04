---
date: 2026-08-04T17:58:19+00:00
category: bug
status: open
model: claude-opus-5
tool: typo3_documentation_lookup
directory: /home/benji/projects/site-new
---

# Task: repo-wide cleanup of a TYPO3 14.3.5 project; the call happened while writing the extension ...

## Observation

Task: repo-wide cleanup of a TYPO3 14.3.5 project; the call happened while writing the extension manual.

My first call to typo3_documentation_lookup passed `query` (singular) and failed with: MCP error -32602: Invalid parameters for tool 'typo3_documentation_lookup': Missing required properties: `queries`.; Missing required properties: `page`.

I had guessed the parameter name from every other search tool on this server, all of which spell it `query`: typo3_changelog_lookup, typo3_icon_lookup, typo3_label_lookup, typo3_component_lookup, typo3_backend_module_lookup. typo3_hint_lookup uses `task`. typo3_documentation_lookup is the only one using the plural `queries`.

Cost: one failed call plus a ToolSearch round trip to fetch the schema. That cost is higher on this server than it would be elsewhere, because in this client the typo3_* tools arrive schema-deferred — a name has to be guessed or a schema fetched before any call, so an inconsistent parameter name is paid for twice.

The error message itself is decent: it names both alternatives (`queries` or `page`) and the exactly-one-of constraint.

## Query

typo3_documentation_lookup with query="extension documentation guides.xml" and targetVersion="14.3" — singular `query`

## Suggestion

Either accept `query` as a singular alias that is folded into `queries`, or have the validation error say "did you mean `queries`?" when an unknown `query` property was supplied. The constraint text already explains the queries/page split; one extra line naming the near-miss spelling would remove the round trip entirely.
