---
date: 2026-08-05T03:38:05+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# Task was to triage old open RTE issues in a core checkout (TYPO3 15.0.0-dev, branch main).

## Observation

Task was to triage old open RTE issues in a core checkout (TYPO3 15.0.0-dev, branch main).

The skill typo3-core-issue-triage prescribes an interface typo3_forge_lookup does not have. Its step 1 says to call the tool "with `open` to get the backlog rather than one issue", to distinguish `oldest` from `stale`, and to "narrow with `category` [...] and with `tracker`", then to "read the count that comes back against the number of entries". The tool schema accepts exactly three parameters: `issue`, `query`, `limit`. There is no backlog mode, no status filter, no category, no tracker, no sort. The tool description also frames the tool purely as "read one issue" or "search text", so nothing in the tool itself suggests the skill's capability either.

Result: the very first step of the skill could not be executed. Free-text search returned issues newest-first including closed ones, with no age information, so it could not answer "which are the old ones" at all. I left the server and hit the Redmine REST API directly with curl:
  https://forge.typo3.org/issues.json?project_id=27&category_id=1001&status_id=open&sort=created_on:asc&limit=100
That returned total_count 35 and all 35 rows with created_on and updated_on, which is exactly what the skill's step 1 describes and what the task needed.

Finding the category id cost three extra round trips of its own. https://forge.typo3.org/projects/typo3cms-core/issue_categories.json answers 401 without a credential, so I had to fetch three issues I already suspected were RTE issues and read category.id off them until one came back as {id: 1001, name: "RTE (rtehtmlarea + ckeditor)"}. Two of the three guesses were wrong (110159 is System/Bootstrap/Configuration, 105943 is Link Handling & Redirect Handling), which is itself a useful fact: RTE-related reports are spread across categories, so the category alone is not the whole RTE backlog and a session needs to be told that.

The project id 27 and the category id 1001 are stable constants that a knowledge server is the right place to hold. A session should not have to rediscover them by probing.

## Query

Task: "ich würde mir gerne alte rte issue anschauen" (go through old open RTE issues on forge.typo3.org). Skill typo3-core-issue-triage activated; its step 1 instructs: typo3_forge_lookup with `open`, narrowed by `category` and `tracker`, distinguishing `oldest` from `stale`. Calls actually possible: typo3_forge_lookup {query: "RTE CKEditor", limit: 25} and {query: "rich text editor", limit: 25}.

## Suggestion

Give typo3_forge_lookup a backlog mode matching what typo3-core-issue-triage already instructs: a `status` filter (at least open/closed/all), `category`, `tracker`, and a sort that separates created_on from updated_on, plus the total count alongside the returned page. Hold the TYPO3 Core project id and the category name-to-id map server-side so callers never probe for them, and expose categories by name ("RTE") rather than by id, since /issue_categories.json needs a credential. Until the tool has it, the skill's step 1 should not describe parameters the tool lacks — that mismatch is worse than the tool being honestly narrow, because it sends the session looking for arguments that do not exist. If the intent is that this is out of scope, say so in the skill and name the Redmine issues.json endpoint, which answers unauthenticated.
