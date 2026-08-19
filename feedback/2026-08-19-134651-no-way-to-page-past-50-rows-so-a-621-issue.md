---
date: 2026-08-19T13:46:51+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# no way to page past 50 rows, so a 621-issue person history is only ever a count

## Observation

Task: the user asked me to find all Forge issues by Frank Nägler ("finde mir bitte alle forge issues von Frank Nägler").

typo3_forge_lookup with open=oldest, reportedBy="Frank Nägler", status="all", limit=50 answered total=621 and 50 rows. limit caps at 50 and there is no offset, page or cursor parameter, so rows 51-621 are unreachable through this server — the count is the only thing I can say about them.

The tool description defends this: "a set that has to be paged through is answered by other words rather than by more of these". For a topic question that holds — narrow by category, tracker, date. For a person question it does not, because there are no other words. "What has Frank Nägler reported" is one well-defined set of 621 rows; every narrowing I could add (tracker=Bug, category=..., createdBefore=...) answers a different, smaller question than the one that was asked. I ended up reporting the count, listing the 4 open ones, and handing the user a forge.typo3.org URL to read the rest — that is, the answer to the literal request came from the web UI and not from this server.

What I actually needed was not 621 rows in my context. It was the shape of the set: how many per status, per tracker, per category, per year. That answers "what has this person worked on" in one call, at a fraction of what 621 rows would cost.

## Query

typo3_forge_lookup {"open":"oldest","reportedBy":"Frank Nägler","status":"all","limit":50} → total=621, 50 rows, no parameter reaches rows 51-621. Task text: "finde mir bitte alle forge issues von Frank Nägler".

## Suggestion

Two options, and the second is the better one:

1. An offset/page parameter, or a cursor echoed in the answer, so a caller who genuinely wants the whole set can walk it. Cheap to add, but 621 rows through a context is the wrong trade for most callers.

2. An aggregate mode on `open`: instead of rows, return counts of the matched set broken down by status, tracker, category and year, alongside the `total` that already comes back. For a person question that IS the answer — "621 filed, 617 closed/rejected, 4 open, concentrated 2014-2016, mostly Backend User Interface and tracker Task" says more about Frank Nägler's history than the 50 oldest rows do. It composes with every existing filter, and it costs one call and a few hundred tokens where paging costs a dozen calls and tens of thousands.

If only one gets built, build the aggregate. Keep `total` beside the page either way — it is what let me say "621" honestly instead of implying the page was the set.
