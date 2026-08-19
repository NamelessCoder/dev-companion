---
date: 2026-08-19T13:47:31+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# the day-old reportedBy filter resolved a name on the first call; the people block is why

## Observation

Task: find all Forge issues by Frank Nägler.

Recording this as a strength because the feature is one day old: feedback/archive/2026-08-19-131336 ("typo3_forge_lookup cannot list issues by the person who reported them") was closed this morning by [FEATURE] Filter the Forge backlog by person, and this session is the first use of it I am aware of. It worked, first call, no disambiguation round trip.

Three specific things carried the answer:

1. The `people` block. Passing reportedBy="Frank Nägler" came back with {filter: "reportedBy", asked: "Frank Nägler", name: "Frank Nägler", id: 52, candidates: []}. That told me the name resolved, who it resolved to, and that nobody else was in the running — without my guessing, and without my holding a Redmine user id. The empty `candidates` is a positive signal, not an absence: it is what made me confident enough to state the count as fact instead of hedging it. The umlaut round-tripped correctly, which is not a given.

2. `total` beside a capped page. 621 against 50 returned rows let me report the size of the set honestly rather than implying the page was all of it.

3. The sentence in the `status` description saying a person question needs "all" — "an enumeration that hides those answers 4 where the number is 621". That is exactly the number I got, and without that sentence I would have run the default and believed the 4. It changed what I passed.

The one thing I did not need was the 50 rows themselves; that is the separate feedback about aggregating a person's history.

## Query

typo3_forge_lookup {"open":"oldest","reportedBy":"Frank Nägler","status":"all","limit":50} → people: [{filter:"reportedBy", asked:"Frank Nägler", name:"Frank Nägler", id:52, candidates:[]}], total=621. Task text: "finde mir bitte alle forge issues von Frank Nägler".

## Suggestion

Keep the `people` block on every answer that resolves a name, `candidates` included when empty — dropping an empty array to save payload would cost the caller the grounds to state the result plainly, which is the whole value of the block.

Keep `total` alongside the page. Keep the "answers 4 where the number is 621" sentence in the `status` description verbatim; a shorter, more abstract wording of the same rule would not have made me change the argument.
