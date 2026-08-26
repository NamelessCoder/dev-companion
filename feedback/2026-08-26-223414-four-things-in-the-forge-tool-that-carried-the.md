---
date: 2026-08-26T22:34:14+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# four things in the forge tool that carried the session and should survive a refactor

## Observation

Task: "please find 1 old forge issue and fix it", narrowed to Extbase, ending in a shipped patch for #76202. Filing the strengths separately because they are load-bearing and easy to lose in a rewrite.

1. The inline `reviews` field on every row. It is scraped from review.typo3.org URLs in the text, and it is what let me see at a glance that #70921, #72962, #82228 and #52070 all carry ABANDONED changes — prior attempts that died — while #76202 carried none. That distinction is most of what makes an old issue worth or not worth picking up, and I got it without a single Gerrit call. It is also why typo3_gerrit_lookup never had to be opened.

2. open="stale" as distinct from open="oldest". The description's framing — "filed long ago is about the report, untouched for years is about the attention it got" — is not decoration; I used both, they returned materially different sets, and the second is the one that finds a report everybody walked past. An issue filed in 2009 and commented on last month is being worked, and only the stale ordering says so.

3. notes="people" on the sweep. On the 25-row stale enumeration this kept the answer readable; the same call with full notes would have been mostly patch-set pings. The reasoning given for it — that the cost of reading ten issues decides whether they get read at all — matched my behaviour exactly.

4. category matching one word at a time, plus `categoriesUsed` in the answer. I passed "extbase" and got both "Extbase" and "Extbase + l10n" with the tracker's own spelling echoed back. I never had to learn the project's category vocabulary, and when the user redirected me mid-task with "please find something in extbase" I could re-scope in one call.

Also worth recording: the schema's "exactly one of issue / query / open" constraint is stated plainly enough that I never violated it across 11 calls, and no call this session errored or needed re-running with different arguments. Given how much of the session ran through this one tool, that is not nothing.

## Query

typo3_forge_lookup with: open="stale"+updatedBefore="2020-01-01"+notes="people"; open="oldest"+category="extbase"; issue= on eight issues

## Suggestion

Keep all four. If the issue answer ever grows the "which cited symbols still exist" verdict I asked for in a separate feedback, model it on `reviews` — a small extra field on the row that a sweep can read without a second call, rather than a new tool.

The one thing I would add without changing any of the above: `reviews` proves the scrape-URLs-out-of-prose machinery already exists and works well. Pointing it at forge.typo3.org issue URLs is the subject of a separate feedback, and it should reuse this exact shape.
