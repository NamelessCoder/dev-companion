---
id: R-ANS-027
title: 'An answer that cannot separate two causes says so in the answer'
status: held
restsOn: [D-ANS-062]
heldBy:
  - GerritTest::aReviewNoteOnTheIssueTurnsTheHedgeIntoAnAnswer
  - GerritTest::anEmptyAnswerForACommitSaysWhatItCannotSeparate
  - GerritTest::anEmptyAnswerForANamedChangeSaysWhatItCannotSeparate
  - GerritTest::anEmptyBacklogSaysWhatItCannotSeparate
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

**Measured on 2026-08-07.** Asked of the review server directly, `change:95162`
— the change the report is about — answers `200` with `[]`, the same as a change
number that exists nowhere. The two really are one answer, and the
`source-not-answering` the report saw beside it was that call not being answered
rather than a second shape of the same cause.

**Since 2026-08-07 one of the two is separable**, on the issue side. Gerrit Code
Review posts a note on the tracker for every patch set it receives, so an empty
search plus a review URL there is not two possibilities: the change exists and
this reader may not see it. The answer says that instead of hedging, and the
tracker is asked only on the empty path — 0.12 seconds measured. The side the
report was actually about stays a hedge: searching the tracker for a change
number costs 2.5 seconds and answers two issues, one unrelated, and searching
for a Change-Id answers nothing.
