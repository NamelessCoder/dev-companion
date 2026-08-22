---
id: D-ANS-064
title: An issue answer holds what a triage needs and does not make it legible
date: 2026-08-08
status: open
---

# D-ANS-064 — An issue answer holds what a triage needs and does not make it legible

**Everything the reporting session went to the checkout for was already in the
`typo3_forge_lookup` payload.** What it was not was findable: a number without a
subject, a change reference inside prose, and a journal too large to read across
candidates.

## Evidence

- Relations come back as pairs and nothing else. Re-run on 2026-08-08, issue
  15984 answers
  `[{22860, relates}, {26484, relates}, {78825, relates}, {32756, precedes}]`.
  `feedback/2026-08-07-231225` skipped all four rather than spend four issue
  reads, and `#32756` is "Massive Memory Leak in 4.5.8+ / 4.6", the issue the
  2012 revert was filed under — the single record that answers "what would this
  cost". The session found it in a git commit message instead.
- Filling them is one call rather than four.
  `issues.json?issue_id=22860,26484,78825,32756&status_id=*` answers `total 4`
  with subject and status for each, measured against forge.typo3.org the same
  day.
- Gerrit change references are in the payload and only as prose.
  `feedback/2026-08-07-231146` never called `typo3_gerrit_lookup` and never
  loaded its schema. The references sit in journal notes — "Patch set 3 … It is
  available at http://review.typo3.org/38419" — where they read as history
  already told rather than as a handle. The session answered the question from
  `git log --all --grep` and could only report the 2021 attempt as "abandoned",
  which is all the Forge comment said.
- The journal has no bound. `feedback/2026-08-07-231213` reads two issues and
  says a triage across ten would not have been affordable. Measured: issue 14858
  answers 4090 characters of which 2573 are the journal, and 8 of its 15 notes
  are Gerrit Code Review patch-set pings. On 15984, 3 of 15.
- The same session filed the journal as what saved it.
  `feedback/2026-08-07-231137` credits it three times over: Benni Mack's note
  calling 14858 a feature rather than a bug, which stopped a session verifying a
  misfiled feature request; Susanne Moog's 2012 revert reason, which became the
  design constraint it reported; and two reproductions establishing the bug
  survived three majors.

## Decided

- The answer is made legible rather than smaller. Both halves come from one
  session and neither is wrong: the journal is the most valuable thing in the
  payload and it is the reason a second issue cannot be afforded. That is the
  shape [judging.md](../../documentation/records/judging.rst) names for step 5,
  arriving here from one reader rather than two.
- So bounding it is a parameter and never a default. A caller reading one issue
  keeps what it has; a caller sweeping candidates asks for less. Dropping the
  bot notes is the bound worth having first — it takes half the volume off 14858
  and removes nothing a reader was going to use.
- A relation carries the fields a search hit already carries. The cost is one
  bulk read per issue answer and it is paid once rather than per relation, which
  is what makes this a fix and not a trade.
- A change reference becomes a field of its own, naming `typo3_gerrit_lookup`.
  The information is already parsed into the notes; what is missing is that it
  is a handle. This is the same failure `D-ANS-061` named for a document uri, on
  a different payload.

## Assumed

- One session. It is one reader over two issues, and the affordability claim is
  its estimate of a sweep it did not run.
- The bots are recognisable by author name. "Gerrit Code Review" and "Mr.
  Hudson" are the two seen; an older or renamed one would pass the filter.
- Bulk-reading relations stays one call. Redmine answers `issue_id` as a list
  today, and a relation set larger than a URL can carry would split.

## Wrong if

- A session reports missing something because the bot notes were filtered, which
  would say the patch-set pings carry more than a URL.
- Relations come back filled and are still skipped, which would say the cost was
  never the reason.
- A structured change reference is reported as noise on issues where the patch
  is ancient and irrelevant, which would say it belongs behind a parameter too.

## Since then

### 2026-08-12 — a filled relation was followed and reframed a review

The second **Wrong if** watched for relations coming back filled and being
skipped anyway. `feedback/2026-08-11-055157` is the other outcome. Reviewing
change 94686, the session read issue 110123, followed the one relation the
answer carried — #108072, *Module action is not kept in breadcrumb*, Closed — to
the change that fixed it and to that commit in its own checkout, and the review
changed shape: the patch under review partially reverts that earlier fix, which
decided the severity of two findings and produced a `Related:` recommendation.
The session states that neither the diff nor the commit message says so.

The subject is what made the hop worth taking, and it is the field this entry
added. `feedback/2026-08-12-092654` reports the same field deciding a different
review, where an issue titled about table captions was fixed in a figcaption and
the mismatch was the review point.

Re-run on 2026-08-12: issue 110123 answers the relation with subject, tracker
and status filled, so the fill this entry decided is what those sessions read.
Neither report confirms the entry — `D-FBK-018` — and what they carry is that
the cost was the reason, which is what the **Wrong if** was written to find out.
