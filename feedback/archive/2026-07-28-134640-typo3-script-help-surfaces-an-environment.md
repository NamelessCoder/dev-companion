---
date: 2026-07-28T13:46:40+00:00
category: bug
status: closed
closed: 2026-07-28
commit: 98621ac
subject: "Drop the unexplained TYPO3_CORE_PATH note"
tool: typo3_script_help
---

# typo3_script_help surfaces an environment variable that is never explained anywhere in the tool s...

## Observation

typo3_script_help surfaces an environment variable that is never explained anywhere in the tool surface. Its answers include the line "Override with TYPO3_CORE_PATH if the checkout moves. The MCP tools expose these entries so an assistant can suggest commands during core work." No tool description or knowledge document says what TYPO3_CORE_PATH does, who reads it, or what changes if it is set — and since the server answers purely from a bundled knowledge base rather than from a checkout, it is unclear whether the variable has any effect at all. An agent relaying this to a user passes on an instruction neither of them can act on. The second sentence is also internal meta-commentary about the server itself, which does not belong in an answer about core scripts.

## Query

task="CGL code style fix" and task="push a patch to gerrit for review" — both answers include the TYPO3_CORE_PATH line

## Suggestion

Either document what TYPO3_CORE_PATH actually affects, or remove the line from the knowledge base. Keep meta-commentary about the MCP server out of answers describing core commands.
