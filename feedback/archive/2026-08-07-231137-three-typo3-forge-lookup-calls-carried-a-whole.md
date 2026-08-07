---
date: 2026-08-07T23:11:37+00:00
category: idea
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_project_describe
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# three typo3_forge_lookup calls carried a whole backlog-triage session with no retries

## Observation

Task: list the thirty oldest unresolved core issues, pick the first that is a real bug, and establish whether it still reproduces in a 15.0.0-dev core checkout. My transcript window begins at that request; the system prompt carries an "Exited Plan Mode" marker, so a planning exchange may precede what I can see and I do not speak for it.

Three forge_lookup calls, zero retries, zero guessed arguments, and they carried the entire tracker half of the task.

- open='oldest', limit=30 answered the request literally. total=2471 came back with the page, so I could tell the user the thirty were a slice and not the whole set. The description promises exactly this, and it is what made the answer honest rather than merely complete.
- issue='14858' was the decisive call. It is the oldest Bug-tracker entry in that list, and its comment journal carried Benni Mack's 2026-01-23 note: "If this should be configurable, then we need a new option, making this not a bug, but actually a feature." Without the comments I would have spent the session verifying a misfiled feature request and handed the user the wrong recommendation. The description's claim — "the comments, where a maintainer who closed or reassigned it said why, which the description never says" — held exactly.
- issue='15984' supplied three things no amount of code reading gives. Susanne Moog's 2012 revert reason ("massive performance impact ... We need to find another solution that does not check the whole root line for each and every page again"), which became the central design constraint I reported. Riccardo De Contardi's 2017 and 2020 reproductions, establishing the bug survived 7.6, 9.5 and 10.4-dev. And a 2026-04-15 comment about PHP timeouts that I flagged to the user as a separate symptom not to conflate with the fix.

The enum on `open` shaped how I read the result rather than just filtering it: knowing oldest-filed and longest-untouched are different questions let me read #15984's 2026-04-15 update date as evidence the issue is alive rather than abandoned.

One inefficiency, mine and not the server's: my first Bash call grepped Typo3Version.php for the core version in the same round trip typo3_project_describe returned typo3Version 15.0.0-dev. The mandated opening call already held it. Reaching for the checkout first is still the reflex.

## Query

Session task: "Give me the thirty oldest issues in our tracker that nobody has resolved, and then take the first one that looks like a real bug and tell me whether it is still a thing." Calls made: typo3_project_describe(); typo3_forge_lookup(open='oldest', limit=30); typo3_forge_lookup(issue='14858'); typo3_forge_lookup(issue='15984').

## Suggestion

Keep all of it. Specifically: keep `total` alongside limited pages, keep the comment journal in the default issue payload, and keep the oldest/stale distinction documented in the parameter itself rather than in a guide — it was read at the point of use and changed how the result was interpreted, which it would not have done from a page nobody opened.
