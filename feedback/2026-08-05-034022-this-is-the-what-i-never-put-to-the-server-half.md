---
date: 2026-08-05T03:40:22+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_rule_lookup, typo3_script_lookup, typo3_documentation_lookup, typo3_extension_describe
directory: /home/benji/projects/typo3-cms
---

# This is the "what I never put to the server" half of the report, for a session that triaged Forge...

## Observation

This is the "what I never put to the server" half of the report, for a session that triaged Forge #88556 and then wrote the core patch for it.

Resources. I never saw a resource list. My client listed ListMcpResourcesTool and ReadMcpResourceTool among the deferred tools available, and I read past both without calling either — so I finished the whole session without ever learning what typo3://guides holds. The only contact I had with those documents was indirect: typo3_rule_lookup returned matches carrying documentId and uri (typo3://guides/core/contribution/commit-messages, typo3://guides/core/testing/scripts, typo3://guides/core/contribution/sources), each as a single heading plus body. Those excerpts were good, but they are sections, and I never read a page end to end.

The page I actually wanted and never got: the Gerrit push procedure. typo3-core-patch-development sends you to "typo3_rule_lookup for the Gerrit workflow: what the push actually is, how a change is amended into a new patch set [...] and both forms of the refspec, visible and unlisted". I stopped before pushing, so I did not need it yet — but had the session continued one more step I would have had to search for it by guessing words rather than opening a page I knew existed. A resource list surfaced at the point of use, or a rule_lookup answer that says "this is a section of a longer procedure at <uri>, read it whole before pushing", would have closed that.

Tools I read and passed over, with the assumption I made:
- typo3_server_scope: skipped deliberately. The skills gave me an explicit order of calls, so I never needed orientation. The assumption held, but it means the tool that exists to say "which lookup is the right one" is invisible to any session that arrives through a skill — which, given the skills, is most of them.
- typo3_documentation_lookup: never called. I assumed RteHtmlParser's DB-to-RTE transformation internals have no manual page to be titled after, on the strength of base.md's own statement that "a PHP identifier has no page to be titled after". I did not test the assumption. If the RTE processing configuration (Processing.yaml, allowTags, allowTagsOutside, HTMLparser_rte) does have a documented page, a session fixing this class would benefit from it and mine never looked.
- typo3_script_lookup: schema loaded, never invoked. typo3_test_run_guide had already returned every command I needed with its full invocation notes, so the call would have restated it. Worth knowing that in a paths-driven session the two overlap and only one is reached.
- typo3_extension_describe: base.md step 2 says to call it "for each extension in scope". typo3_project_describe had already answered "extensions": [] for a bare core checkout, so the step had no argument. That is correct behaviour, but the base order does not say what to do when step 1 empties step 2 — a session following it literally either calls it with nothing or silently drops a prescribed step, and mine did the latter.

One more thing not asked about: the language instruction worked. The server tells you to query in English whatever language you speak with the user. The user wrote in German throughout ("ich würde mir gerne alte rte issue anschauen", "ok kannst du mir dafür einen patch bauen?"), I translated every query into English and translated the answers back, and nothing was lost. Worth noting because it is the kind of instruction that is easy to state and easy to ignore, and stating it in the server instructions rather than in each skill is what made it stick across two skills.

## Query

Whole session, triage plus patch for Forge #88556. Tools never called: typo3_server_scope, typo3_documentation_lookup, typo3_extension_describe, typo3_script_lookup (schema loaded, never invoked). Resources: no typo3://guides resource was ever read whole; my client (Claude Code, VS Code extension) showed no MCP resource list, and I never called ListMcpResourcesTool or ReadMcpResourceTool although both were offered as deferred tools.

## Suggestion

Assume the client will not show the resource list — mine did not. Where a rule_lookup answer is one section of a procedure that exists whole as a typo3://guides resource, say so in the answer and name the uri, so a session that needs the rest can ask for it instead of searching for the next section by guessing words. Consider having the skills name the resource they expect to be read whole (typo3-core-patch-development's push step is the clearest case). For base.md, say what step 2 becomes when step 1 reports no project-own extensions, so the step is visibly satisfied rather than silently skipped.
