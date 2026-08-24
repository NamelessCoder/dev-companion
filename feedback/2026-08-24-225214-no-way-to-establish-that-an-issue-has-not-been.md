---
date: 2026-08-24T22:52:14+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# No way to establish that an issue has not been filed yet, before filing one

## Observation

Task: a reviewer on change 93177 had asked the author "could you file it?" about a separate defect. Before reporting the thread as open, I had to establish whether the issue existed.

I made three calls, all empty:
  typo3_forge_lookup(query="LocalizationController getContent permission check") -> total 0
  typo3_forge_lookup(query="localization wizard endpoints missing permission") -> total 0
  typo3_forge_lookup(query="wizard localization getContent access check editor") -> total 0

Three round trips bought me a negative I could not rely on. The tool's own description is candid about why — full-text, unranked, every word must appear, "one wording does not settle it" — so this is not a surprise, and the answers were not wrong. But the question I actually had is one this server routes here and cannot settle: has somebody already filed an issue for this, in words I have not thought of?

The asymmetry is what makes it costly. A hit is conclusive; an empty is not, and no number of rewordings makes it conclusive. I ended up writing "three Forge searches with different wordings, all empty" into my review, which is an honest but weak statement, and the user later simply created the issue (#110533) — at which point typo3_forge_lookup(issue="110533") answered perfectly and confirmed subject, tracker and version.

Worth contrasting with the tool that does answer this shape well: typo3_gerrit_lookup(path=...) answers "which changes touch this file", which is a structural question rather than a lexical one, and an empty there means something. Forge has no equivalent axis.

## Query

typo3_forge_lookup(query="LocalizationController getContent permission check", limit=10); typo3_forge_lookup(query="localization wizard endpoints missing permission", limit=15); typo3_forge_lookup(query="wizard localization getContent access check editor", limit=15) — all total 0. Then typo3_forge_lookup(issue="110533") once the user had filed it, which answered fully.

## Suggestion

Two things, either of which would help:

1. A structural narrowing like Gerrit's `path`. The core files issues under categories; `category` already exists and is resolved from the project's own list. Combining `category` with `open` and a recency window gives an enumeration a session can actually read — "these are the 18 open issues under Backend User Interface filed in the last year" — which answers "has this been reported" by reading subjects rather than by guessing at wording. The tool supports this today; what is missing is the routing that says so. The description sells `query` for "has this already been reported" and mentions the enumeration route only in the `category` parameter text.

2. Say in the answer what an empty result establishes and what it does not, once, so a session does not spend three calls learning it: "no issue carries all of these words; this does not establish that none exists. Enumerate the area with category + open to read subjects instead."

The second is a one-line change and would have saved two of my three calls.
