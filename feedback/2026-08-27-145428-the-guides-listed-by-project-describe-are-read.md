---
date: 2026-08-27T14:54:28+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# The guides listed by project_describe are read by rule_lookup, which no name says

## Observation

Task: "please search for 1 workspace bug in forge and fix it". This is about the documents the server offers and why I finished without opening one.

typo3_project_describe returned a `guides` array of 20 entries — "core/contribution/changelog", "core/contribution/commit-messages", "core/contribution/gerrit-workflow", "core/testing/proving-a-rendering" and so on. Titles and ids, nothing else. My client did surface deferred tools ListMcpResourcesTool and ReadMcpResourceTool in the listing, but I never connected them to this array, and I read no guide end to end.

The naming is the reason. The field is called `guides`. The tool that reads them is called `typo3_rule_lookup`, and it takes them as `documentId`. Nothing in the word "guides" points at a tool called "rule_lookup" — I would not have found that route from either name. I only learned it because typo3_task_guide's changelog checklist item happened to spell the whole call out in prose: 'The whole rule is one typo3_rule_lookup call with documentId "core/contribution/changelog".' That is the only place the two halves were joined, and it was incidental to a checklist item about something else.

And I still did not make the call. typo3_task_guide's checklist had already given me a one-sentence summary of the changelog rule, and I judged that plus the `@internal` line in the class docblock sufficient to decide no changelog was owed. So the guide reported its own route, told me where the full rule lived, and I acted on the summary instead. I think the decision was right for this patch, but it illustrates the failure mode: the summary is good enough often enough that the document never gets opened.

The page I would have wanted, and did not know existed by name, is "core/contribution/changelog" — the case I actually had (a bugfix touching only an @internal class) is precisely the edge the one-sentence summary does not cover.

## Query

typo3_project_describe() returned a `guides` array of 20 entries with ids such as "core/contribution/changelog", "core/contribution/commit-messages", "core/contribution/gerrit-workflow". I read none of them during the session.

## Suggestion

Join the names. Either rename `typo3_rule_lookup` to something that says "read one of the documents you were listed" (typo3_guide_read would be found from the `guides` key without any prose), or have typo3_project_describe emit the call form alongside each entry — `{"id":"core/contribution/changelog","title":"...","read":"typo3_rule_lookup(documentId: \"core/contribution/changelog\")"}`. The second is cheap and would have made the route unmissable at the moment I first saw the list.

Separately: where typo3_task_guide's checklist summarizes a rule and names the document that holds it in full, say which cases the summary does not decide. "This one-liner settles the common case; @internal classes, build output and RST-only changes are decided in the document." That would have converted my judgement call into a lookup.
