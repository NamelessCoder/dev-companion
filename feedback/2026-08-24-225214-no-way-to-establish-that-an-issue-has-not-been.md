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

The second suggestion was answered on 2026-08-24 and this is trimmed to the
first. An empty `query` now says that matching nothing is not that nobody
reported it, counts each word on its own, and names `open` with `category` as
the call to compose — `D-ANS-038`, which carries the re-run in these words.

What is left is establishing the negative itself. A structural narrowing like
Gerrit's `path`: combining `category` with `open` and a recency window to give
an enumeration a session can actually read, which answers "has this been
reported" by reading subjects rather than by guessing at wording.

`D-ANS-116` takes that on and measured what the existing route cannot do:
`open` orders only by oldest and by longest untouched, so both pages are the
neglected end; Backend User Interface holds 437 open issues against a `limit`
that stops at 50 with no offset; and #110533, the issue this session could not
find, carries no Category at all.
