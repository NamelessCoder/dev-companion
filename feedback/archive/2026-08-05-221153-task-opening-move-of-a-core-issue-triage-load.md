---
date: 2026-08-05T22:11:53+00:00
category: idea
status: closed
closed: 2026-08-06
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# Task: opening move of a core issue triage — load the tool schemas named in the skill so I could c...

## Observation

Task: opening move of a core issue triage — load the tool schemas named in the skill so I could call them.

The tools arrived in this session as deferred: names listed, schemas not loaded, fetched by name through the client's ToolSearch. My first call selected them by the names the server's own instructions and the skills use throughout — typo3_project_describe, typo3_forge_lookup, typo3_gerrit_lookup, typo3_changelog_lookup, typo3_test_run_guide — and got "No matching deferred tools found", because the callable identifiers carry a client-side prefix: mcp__typo3-cms-mcp__typo3_project_describe. One wasted round trip, then a keyword search ("typo3 project describe forge lookup") landed and returned ten schemas at once, which was more than I needed but got me moving.

I want to be accurate about whose problem this is: the prefix is the client's, the server cannot know it, and the bare names are the right thing for prose to use. So this is not a defect. It is a note that the naming convention in the instructions and skills is not the identifier a caller types, and that the first call of a session is where that bites, before anything has established the mapping. Every later tool I reached, I reached from the keyword search or from the system reminder's list — never by typing a name out of the skill text again.

Related and worth recording in the same breath: typo3_rule_lookup is the tool I would least have found from its name. "Rule" reads like coding standards or lint rules; it actually holds the contribution procedures — commit message conventions, release targets, changelog obligations, the Gerrit refspecs for private and work-in-progress pushes. I only used it because typo3-core-patch-development names it explicitly at each point of use. Had I been working without that skill I would have gone to the web for the Gerrit push options.

## Query

ToolSearch with query "select:typo3_project_describe,typo3_forge_lookup,typo3_gerrit_lookup,typo3_changelog_lookup,typo3_test_run_guide" — answered "No matching deferred tools found". The callable identifiers are mcp__typo3-cms-mcp__typo3_project_describe and so on.

## Suggestion

One line in the server instructions saying that tool names appear there bare and that clients commonly expose them with a prefix, so a by-name lookup should be tried against the client's own list rather than against the prose. Cheap, and it costs one round trip per session where it is missing.

Separately, consider widening how typo3_rule_lookup describes itself: "the local TYPO3 core contribution rules and script notes" undersells it. Naming the surfaces it actually carries — commit message conventions, release targets and which branches take a patch, changelog obligations by change type, the Gerrit push and amend workflow — would make it findable from a keyword search by somebody who does not already know it exists. That is the tool most likely to be replaced by a web search purely because its name does not say what is in it.
