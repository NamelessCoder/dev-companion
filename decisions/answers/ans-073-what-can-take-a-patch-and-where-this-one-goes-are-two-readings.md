---
id: D-ANS-073
title: What can take a patch and where this one goes are two readings
date: 2026-08-10
status: open
coveredBy:
  - CommitMessageTest::aBranchTheListDoesNotCarryIsAWarningSayingWhenItWasRead
  - CommitMessageTest::aMaintainedLineFurtherBackSaysWhatItClaims
  - GerritTest::thePlacementNamesTheToolThatReadsATrailerAgainstThoseLines
  - GerritTest::theTextHalfTellsTheTrailerApartFromWhatWasPushed
---

# D-ANS-073 — What can take a patch and where this one goes are two readings

**A bug fix and a task reach the development line and the one release line back
from it, and no further without severity.**

The server said a bug fix goes to every maintained line that carries the defect,
and `ReleaseLines::releasable()` answered three lines. A caller reading the two
together writes all three into the trailer and believes it was told to.

## Evidence

- `feedback/2026-08-10-114833` is the maintainer of this repository stating the
  rule directly, which is the only source it has: no session established it, and
  the correction is recorded here rather than reconstructible from a file.
- The core's published documentation carries the shape and not the sentence. The
  contribution guide says "we **always** fix things on **main** first and then
  backport a change if it goes along with our support rules for older versions",
  and its own review checklist asks whether "the `Releases:` scope of a patch
  [is] spanning the proper TYPO3 versions (depending on the state of current LTS
  and priority-bugfix-only releases)" — so a line's state, not the defect's
  presence, is what the published rule turns on too. TYPO3 Explained says
  support is "provided for the current as well as the preceding LTS release",
  which is the same reach counted from the other end.
- The over-broad half is what costs somebody else work. A trailer naming the
  third line asks a merger to cherry-pick onto a line the change was never meant
  for, and a reviewer refusing it had this server's own sentence against them.
- Two of the three places the feedback names carry the statement:
  `core/contribution/commit-messages.md` and
  `core/contribution/gerrit-workflow.md`. The review skill carries neither the
  word `Releases` nor `trailer` — it never repeated the rule, so there was
  nothing to correct there.

## Decided

- The trailer is two readings and the corpus says so: where the defect is, on
  each line, and whether the severity earns an older one. The second is a
  judgement the author states rather than a consequence of the first.
- `ReleaseLines::ordinary()` is the reach — `releasable()`'s first two, since
  the list is newest first — and it is a second method rather than a narrowing
  of the first. The two answer different questions, and the missing-trailer
  check now names both: what can take a patch at all, and what this change goes
  to.
- A trailer naming a maintained line beyond that reach is a warning rather than
  an error, on a `[BUGFIX]` and a `[TASK]` alone. It is legitimate exactly where
  the severity earns it, so the check says what the trailer is claiming and
  leaves the claim to the author. A `[FEATURE]` is the release managers' call
  and never gets it.

## Assumed

- That the reach is always two lines. It is what "one line back from main" means
  while one release line is current; nothing here derives it from the release
  calendar, and a period with two current lines would need the rule restated
  rather than the arithmetic changed.

## Wrong if

- A merger reports a trailer that should have named an older line and did not.
  The warning is what would have discouraged it, and it would then be the
  wording that is wrong rather than the rule.
- The published contribution documentation is changed to state the reach in
  branches. Then the statement has a source the next session can re-read, and
  this entry stops being the only evidence for it.
