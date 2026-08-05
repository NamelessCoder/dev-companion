---
date: 2026-08-05T03:39:02+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# Task was to find old open RTE issues in the Forge backlog.

## Observation

Task was to find old open RTE issues in the Forge backlog.

The `query` form of typo3_forge_lookup returns four fields that are always empty. Across two calls and 50 result rows, every single row came back as:

  {"issue":110159,"subject":"RTE YAML loader should support safe custom tags like !reset","tracker":"Bug","status":"New","category":"","assignedTo":"","createdOn":"","updatedOn":"","url":"..."}

category, assignedTo, createdOn and updatedOn were empty strings in 50 of 50 rows. The single-issue form is fine — {issue: "88556"} correctly returned createdOn 2019-06-13, updatedOn 2025-03-06, typo3Version 12, phpVersion 8.2 — so this is specific to the search path, presumably because Redmine's /search.json does not carry those fields and they are being emitted as empty rather than omitted.

Emitting them empty is worse than omitting them. A key that is present with an empty value reads as "this issue has no category and no assignee and was never updated", which is a factual claim and a false one — #110159 does have a category (System/Bootstrap/Configuration, id 1645) and #88556 does have an updatedOn. It also means the search path cannot answer any question about age, which is what the triage skill's step 1 is entirely about, and nothing in the response says so.

I noticed only because I had already decided the search results were unusable for age and had gone to the Redmine issues.json endpoint. A session that trusted the fields would have concluded these issues were untouched and uncategorised.

## Query

typo3_forge_lookup {query: "RTE CKEditor", limit: 25} and {query: "rich text editor", limit: 25}. Every one of the 50 returned rows carried "category":"", "assignedTo":"", "createdOn":"", "updatedOn":"".

## Suggestion

Either omit the keys the search path cannot fill, or fill them — Redmine's /issues.json with an issue_id list returns all four for a set of ids in one call, so the search hits could be enriched in a single extra round trip. If they stay unfilled, say in the response that the search path does not carry them, so the absence reads as "not answered here" rather than as "empty".
