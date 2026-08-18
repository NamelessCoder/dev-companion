---
date: 2026-08-17T21:23:00+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# availableHints is 78 percent of everything hint_lookup transferred, and a caller asking by id has...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. I have named this inside the suggestion field of two other feedbacks and it needs to stand alone, because a clause in somebody else's finding gets worked off as part of neither — which is the same mistake I filed against the workflows earlier in this session.

Measured over 21 hint_lookup calls, every one of them made with an explicit id: 197,738 characters transferred, 43,564 characters of actual hint payload. The availableHints array — around ninety ids with titles, reprinted in full on every answer — was 154,174 characters, 78 percent of everything the tool sent me.

In tokens the whole knowledge base's useful output for a complete sitepackage-and-distribution build was 12,101. Delivering it cost roughly 55,000.

The cost is not one-off. A tool result is paid once at cache write and again at a tenth of the input rate on every subsequent request it is still in context for, and this session ran 148 requests. Amortised at Opus 5 list rates the availableHints arrays alone came to about $2.57 of a $29.59 session — 8.7 percent of the total cost of building the whole project, for a navigation list I never requested.

The ratio is worst exactly where the value is highest. extension-schema-sql delivered 522 characters of payload — one sentence, and it saved me a redundant ext_tables.sql that would have drifted from the TCA — inside a 9,128-character answer. content-element-shape delivered 1,149 characters that decided the entire data model, inside 12,038.

I want to be careful not to argue against the list itself, because it earned its place elsewhere in this session: all 21 of my calls used a known id rather than a text query, and several of those ids came from availableHints arrays in earlier answers. As discovery it works. What it does not need to be is unconditional and identical on every one of 21 answers — after the second or third, the caller has the list.

I am reporting it as measured rather than as an impression because the server cannot see the amortisation: it sees one response of a certain size, not the hundred and forty-eight requests that response is subsequently re-read across.

## Query

Call typo3_hint_lookup 21 times with id= over one build session and measure the answer bodies: total characters against characters excluding the availableHints array.

## Suggestion

Suppress availableHints when the call carries an explicit id, or make it opt-in with a parameter — a caller who names an id has already chosen and is not browsing. That single change recovers roughly three quarters of everything this tool transfers, at no loss to the discovery path, which runs through task, paths and the first answer of a session. If dropping it outright is too blunt, returning it only on calls that used task or paths, or only on the first answer per session, would keep the routing benefit and remove the repetition. Worth applying the same test to the other always-attached blocks across the server: what a caller who asked a specific question needs back is usually a fraction of what is currently sent.
