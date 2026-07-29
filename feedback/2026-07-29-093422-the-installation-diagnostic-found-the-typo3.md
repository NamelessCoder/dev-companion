---
date: 2026-07-29T09:34:22+00:00
category: bug
status: open
tool: typo3_server_scope
---

# The installation diagnostic ("Found the TYPO3 installation at ..." / "Its console cannot be run r...

## Observation

The installation diagnostic ("Found the TYPO3 installation at ..." / "Its console cannot be run right now ... <reason>") is only in the text content of ToolResult::create(); the structured payload of typo3_server_scope has no installation/console key at all. My MCP client renders structuredContent and drops the text block, so I never saw the diagnostic. All five installation-backed tools then answered {"matchCount":0,"icons":[],"answeredBy":"nothing"} — indistinguishable from a genuine "this identifier does not exist". I concluded twice that bootstrap_package registers no icons and no labels, which is false: Configuration/Icons.php registers ~40 identifiers. Only reading src/Typo3Cli.php revealed the console was never resolved.</observation>
<parameter name="suggestion">Put the diagnostic in the structured content, not only in the text: typo3_server_scope should carry installation: {found, root, kind, startedFrom, packageCount, console: {reachable, via, php, reason}}. And every installation-backed tool (icon, label, backend module, fluid namespace, configuration lookup) should return an explicit unavailable/reason field when answeredBy === "nothing" — e.g. {"answeredBy":"nothing","unavailable":{"reason":"<root> has no bin/typo3 or vendor/bin/typo3"}} — so "not reachable" is never returned in the same shape as "not found".</suggestion>
</invoke>

## Query

typo3_server_scope {} — followed by typo3_icon_lookup {"query":"actions-open"}
