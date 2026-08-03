---
id: D-SKL-008
date: 2026-08-03
status: open
---

# D-SKL-008 — A review reads the review the patch is already in

**The core patch review asks `typo3_forge_lookup` for the issue the message
resolves and `typo3_gerrit_lookup` for the change it is, before it reads the
code a second time.**

Both tools existed and no skill routed to either. What they answer is not in the
checkout and cannot be derived from it.

## Evidence

- The third recorded `REVIEW-03` run called neither, and its own report closed
  by saying Forge #110359 had not been fetched and the match against the issue's
  scope was therefore unverified. It named the gap and had no instruction that
  would close it.
- Both calls were made while judging that run and both answered at once. Forge
  gives the issue as "Avoid calling ImageService methods - part 2", status
  `Under Review`, target 15.0, **description empty**, one note and that note is
  the Gerrit bot. Gerrit gives change 95070, `NEW`, branch `main`, patch set 1.
- The patch under review is therefore part of a series whose part 1,
  `0ba69a4a841 [TASK] Use FAL API directly in image ViewHelpers`, is already in
  `origin/main`. The run judged it as a change standing alone. Its finding (i) —
  that `ImageService` is now referenced nowhere in core and a reviewer will ask
  whether `getImage()` gets deprecated — is the question the next part of the
  series answers, and the run had no way to know a next part was intended.
- The empty description is a review-readiness finding the run could not make.
  The checklist's own surface asks whether the patch can be understood from the
  issue and the message alone, and nothing said to open the issue.
- `grep` over `skills/` on 2026-08-03: `typo3-core-patch-development` names
  Gerrit throughout and routes to `typo3_rule_lookup` for the workflow, never to
  either lookup. `typo3-core-patch-review` carried the word Gerrit in its
  description and nowhere in its body.
- **The two identifiers are different numbers and swapping them does not fail.**
  Measured the same day against this patch, whose Forge issue is 110359 and
  whose Gerrit change is 95070. `typo3_gerrit_lookup` given 95070 as an issue
  answers `answered` with change 70860, "[TASK] Stabilize acceptance tests",
  MERGED on `master` in 2021 — it searches commit messages for the string and
  something from 2021 contains it. `typo3_forge_lookup` given 95070 answers
  `answered` with issue 95070, "Stabilize flaky acceptance tests", closed
  against 11.4. Neither payload carries anything that says the number belonged
  to the other tool.

## Decided

- The two calls go into the review skill as their own step, placed where the one
  reading of the patch has just produced their arguments.
- The checklist gains a surface for it, so a review that did not ask reports the
  surface as unassessed rather than leaving it absent.
- The skill states which number belongs to which lookup, that a swap answers
  rather than fails, and the check that catches one: the change that comes back
  carries the subject of the commit under review or the number was wrong. It
  also states the crosswalk, because each identifier names the other — the
  `Resolves:` line is the Forge number and the issue's notes carry the review
  URL that ends in the change number.
- An answer of nothing is a result the review states. A patch not yet pushed has
  no change, and a silent surface there is indistinguishable from one nobody
  asked about.
- Where the commit in the checkout and the change on the server differ, the
  review names which it read. Reviewing a superseded patch set is what this step
  is against, and the checkout cannot report it.
- Voting, commenting and uploading are named as staying with the person. Both
  tools are read-only and hold no credential; the skill says so rather than
  leaving a reader to infer it.

## Assumed

- That a core patch under review usually has an issue worth reading. The corpus
  says the message names one — `Resolves:` is required — but not that the issue
  carries more than its subject, and #110359's description is empty.
- That reading an unanswered reviewer comment is worth a round trip on most
  patches, where the run this came from found a change nobody had commented on.

## Wrong if

- Both lookups answer nothing on most reviewed patches, because a review of
  one's own change usually happens before the push. Two calls that report
  `empty` every time buy the caller nothing and are what `D-FBK-027` refuses.
- The issue turns out to be thinner than the commit message on most patches, so
  the step adds a round trip and no finding. #110359's empty description is the
  first data point and it points that way.
- A review starts deferring to what a Gerrit reviewer already said instead of
  reading the code, which would turn the step into a way of borrowing a verdict.
- A recorded review reports on a change whose subject is not the commit it read.
  That is the swap happening despite the paragraph written against it, and it
  would say the guard belongs in the tools rather than in a skill: neither
  lookup can currently tell a caller that the number it was given looks like the
  other one's.

## Covered by

- `SkillTest::aReviewReadsTheReviewThePatchIsAlreadyIn`
