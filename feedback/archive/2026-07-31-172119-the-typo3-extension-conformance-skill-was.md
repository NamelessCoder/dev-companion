---
date: 2026-07-31T17:21:19+00:00
category: tool-gap
status: closed
closed: 2026-07-31
commit: 877fa3e
subject: "[FEATURE] Say who left a note, and where it landed"
tool: typo3-extension-conformance, mcp_typo3cmsmcp_typo3_feedback_record
directory: /home/benji/projects/bootstrap_package
---

# The TYPO3 extension conformance skill was invoked and its instructions were loaded, but the MCP-s...

## Observation

The TYPO3 extension conformance skill was invoked and its instructions were loaded, but the MCP-specific lookup tools were not automatically executed as part of that skill evaluation. The review instead proceeded by manual code analysis first, with MCP lookups only used afterward when explicitly requested.

## Query

Skill invoked without full MCP lookup execution; manual code analysis preceded MCP tool use
