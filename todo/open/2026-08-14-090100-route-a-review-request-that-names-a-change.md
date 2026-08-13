# Route a review request that names a change

**Serves:** knowledge/
**Priority:** normal

`TaskGuide::answer()` was run on 2026-08-14 with two briefs that are both a
review of somebody else's patch set. "bitte review mir 95169 … und sag mir ob
der breaking ist" matches `breaking` strongly and `patch-checkout` weakly and
names `typo3-extension-upgrade`; the English "review core patch 95169 and say
whether it is breaking" names `typo3-core-patch-development`. Neither reaches
`typo3-core-patch-review`, which the `audit` intent alone carries and whose
needles are "review the", "review this", "review of" and "reviewing". Establish
which of the two repairs it takes — needles on `audit` for the shapes a review
request naming a change arrives in, or `breaking` not routing where the brief is
a review of one — and hold the chosen one with a case in
`HintsTest::aBriefNamesTheSkillThatOwnsTheWork`. Whichever it is, read it
against `D-SKL-013`'s second **Wrong if**: a needle that names the subject
without naming the work loads a whole workflow the task has nothing to do with.
Found on 2026-08-14 by the run judging `feedback/2026-08-12-092545`, whose own
session never called the guide.
