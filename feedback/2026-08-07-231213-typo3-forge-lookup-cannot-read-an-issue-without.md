---
date: 2026-08-07T23:12:13+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# typo3_forge_lookup cannot read an issue without its comment journal, which prices bulk triage out

## Observation

Task: from the thirty oldest unresolved issues, take the first one that looks like a real bug.

That selection is per-issue judgement, and the evidence for it lives in the comments. #14858 is a Bug right up until you read the last note calling it a feature. So the honest way to answer is to read the journal of each candidate until one survives. I could not have afforded that. issue='15984' came back with sixteen journal entries including two multi-paragraph reproduction transcripts carrying pagetree diagrams and TypoScript blocks. It was worth every token for the one issue I was verifying. Across ten candidates it would not have been.

There is no parameter to ask for an issue without its journal, or for only the most recent entries. The `open` enumeration is explicitly pitched as "where a triage of the backlog starts", but the per-issue call that triage then needs comes in one size. I got lucky: the second entry in the list was already conclusive, so I stopped at two reads. Had #14858 been a genuine bug that turned out stale on inspection, I would have been choosing between an unaffordable sweep and guessing from subject lines — and guessing from subject lines is exactly what the #14858 case proves does not work.

Much of the payload is machine noise. On #14858, eight of the sixteen notes are "Gerrit Code Review" patch-set pings. On #15984, three are "Mr. Hudson" doing the same job under the older name.

## Query

typo3_forge_lookup(issue='15984') and typo3_forge_lookup(issue='14858') — each returned noteCount 16 with the full journal and no parameter to bound it. Wanted: the same read across several of the thirty issues returned by typo3_forge_lookup(open='oldest', limit=30).

## Suggestion

Add a way to bound the journal on an issue read: a boolean to omit it, a count returning the most recent N entries, or an option that drops entries authored by the review bots ("Gerrit Code Review", "Mr. Hudson") whose text is always a patch-set push and a URL. Any of the three makes reading eight issues in one triage pass affordable. The bot filter is the most valuable of them, because it removes half the volume while removing nothing a reader was going to use.
