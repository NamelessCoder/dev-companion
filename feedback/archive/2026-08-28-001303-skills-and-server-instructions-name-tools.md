---
date: 2026-08-28T00:13:03+00:00
category: bug
status: closed
closed: 2026-08-28
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3-extension-patch-review, typo3-extension-health, typo3-extension-testing
directory: /home/benji/projects/bootstrap_package
---

# Skills and server instructions name tools without the prefix a deferred-tool client needs

## Observation

Task: review pull request #1621 against bk2k/bootstrap-package, then add regression tests and backport to a release branch.

My client (Claude Code) defers MCP tool schemas: a tool is named in a reminder but must be fetched through a ToolSearch call before it can be invoked, and the callable name carries a client-side prefix — mcp__typo3-dev-companion__typo3_project_describe.

Every place that named a tool to me spelled the bare name:
- the server's initialize-time instructions ("Start every task with typo3_project_describe")
- the bundled skills' reference files (base.md: "typo3_hint_lookup with the subsystem's concrete paths"; "typo3_rule_lookup with documentId=...")
- every answer's own nextTools array, e.g. {"tool":"typo3_changelog_lookup"}

My first fetch was select:typo3_project_describe,typo3_extension_describe,typo3_task_guide,typo3_hint_lookup,typo3_changelog_lookup. It returned "No matching deferred tools found" — an empty result, not an error naming the right form. I guessed the prefix on the retry and all five schemas came back.

One wasted round trip is small, but the place it lands is not. base.md makes the first call the check for whether this server is present at all: "No typo3_ tool in this session, or a first call that errors: stop, say this workflow needs the server and it is not there." An empty tool-search result on the bare name is indistinguishable from an absent server, and the prescribed response to that is to abandon the workflow. A less stubborn session stops there and reports the server missing while it is in fact connected.

The bare names are correct for a client that loads every tool up front, so this only bites deferred-tool clients — which is a growing class, and the one where the first call is most expensive.

## Query

ToolSearch query "select:typo3_project_describe,typo3_extension_describe,typo3_task_guide,typo3_hint_lookup,typo3_changelog_lookup" → "No matching deferred tools found". Retried as "select:mcp__typo3-dev-companion__typo3_project_describe,mcp__typo3-dev-companion__typo3_extension_describe,mcp__typo3-dev-companion__typo3_task_guide,mcp__typo3-dev-companion__typo3_hint_lookup,mcp__typo3-dev-companion__typo3_changelog_lookup" → all five schemas returned.

## Suggestion

Two cheap places to fix it:

1. The initialize-time instructions gain one line: "Clients may expose these tools under a qualified name such as mcp__typo3-dev-companion__typo3_project_describe; both refer to the same tool."

2. base.md's presence check gains the qualifier it is missing: an empty tool-search result is not evidence of absence until the qualified form has been tried. That file is where the empty result gets converted into "the server is not there", so it is the load-bearing one.

Leaving nextTools and the skills spelling the bare name is fine once either of those exists.
