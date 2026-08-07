---
id: R-ANS-027
status: open
restsOn: [D-ANS-062]
---

# R-ANS-027 — An answer that cannot separate two causes says so in the answer

**Where a lookup names one record and finds nothing, and an absent record looks
the same as an unreadable one, the answer says which two it cannot separate.**

The tool description is read when a client is installed; the answer is read when
the verdict is written. A status word that overstates what was established is
acted on as an established fact, and nothing downstream can tell it from one.

## From

`feedback/2026-08-07-132416`, 2026-08-07. `typo3_gerrit_lookup` answered `empty`
for a Change-Id taken out of the commit under review and `unavailable` with
`source-not-answering` for the same change read by number; both were an
anonymous read of a private change. The review made "this was never pushed" its
first finding and recommended coordinating with an author who did not exist as a
separate party.

## Held by

- `not guarded` — no test reaches the review server, and what would hold it is a
  recorded answer for a change no anonymous reader may see.
