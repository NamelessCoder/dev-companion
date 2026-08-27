---
date: 2026-08-27T14:53:49+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# open="stale" plus category and the comments it carries killed three candidates without opening th...

## Observation

Task: "please search for 1 workspace bug in forge and fix it". This is the part that worked and must not be broken.

One call — open:"stale", tracker:"Bug", category:"workspaces", limit:25 — turned an open-ended "find a bug" into a ranked shortlist of 25 with relations, prior Gerrit changes and attachments already attached to each row. AGENTS.md in the core repo describes doing this by hand against the Redmine JSON API with a specific curl user agent to dodge the Anubis bot check; that would have been several calls and a failure mode I would have had to diagnose. `category:"workspaces"` in my own lowercase resolved to the project's "Workspaces" and reported `categoriesUsed`, so I never had to guess the tracker's spelling.

The `reviews` field on each row did real work at the shortlist stage: seeing ABANDONED changes on 70921, 52070, 81838, 82221, 87984 and 85888 told me which issues had already defeated somebody, before I read any of them.

Then the per-issue reads with notes:"people" killed three candidates outright, each on a comment rather than on the description:

- 92377 — Riccardo De Contardi: "no more reproducible on 11.5.13 or 12.0.0-dev". Dropped without touching the checkout. The description alone would have had me building a three-language workspace scenario.
- 42109 — Susanne Moog: "The correct fix for this issue would probably be to introduce context as a concept for backend routing." That is a maintainer saying the issue is architectural, not a patch. Dropped instantly; nothing in the description said this.
- 93286 — the `relations` carried #106936 "Streamline Workspace Stage Change Notification" as Closed, which made me go check whether the notification code had already been reworked rather than assume the 2021 report still stood.

Three candidates eliminated in three calls, none of which required reading a line of core. That is the whole value proposition of the tool and it delivered it. notes:"people" was the right default for candidate-scanning — I used it on all seven per-issue reads and never once wanted the bot pings.

The issue I did take, 97614, was reported against v11 in 2022 and reproduced verbatim on 15.0.0-dev: getMovedRecordsFromPages() had no $language parameter while both sibling collectors did. The tracker data was accurate about a defect three majors old.

## Query

typo3_forge_lookup(open: "stale", tracker: "Bug", category: "workspaces", limit: 25), then typo3_forge_lookup(issue: N, notes: "people") for issues 93286, 42109, 85888, 92377, 102889, 97614, 102768.

## Suggestion

Keep all of it. Specifically: keep `reviews` on enumeration rows (not just on single-issue reads) — it is the cheapest signal for "someone tried and failed"; keep notes:"people" as the recommended mode for scanning candidates; keep the lowercase-category resolution plus `categoriesUsed` echo.

If anything is added, add a hint to the tool description that open:"stale" + tracker + category is the canonical "find me something to work on" call. I arrived at that combination by reading the parameter prose carefully, which was well written but long; a one-line worked example in the description would have got me there in one step instead of a paragraph of reading.
