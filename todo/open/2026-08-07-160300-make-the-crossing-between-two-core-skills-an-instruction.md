# Make the crossing between two core skills an instruction

**Serves:** feedback/2026-08-07-065244-the-triage-skill-names-its-successor-in-prose.md, feedback/2026-08-07-132559-the-review-skill-has-no-marker-for-the-point.md, feedback/2026-08-07-130022-patch-checkout-covers-rebasing-a-fetched-patch.md
**Priority:** high

Give `typo3-core-issue-triage` and `typo3-core-patch-review` an explicit step
that invokes `typo3-core-patch-development` by name at the point the crossing
happens — the verdict is "still happens" and the user asks for a fix, or the
reader accepts the findings and asks for the change — rather than the closing
paragraph each has now, which two sessions read and did not act on. Keep the
closing paragraph: it is what tells a reader where the boundary is. Then settle
`typo3-core-patch-checkout` separately, because it is a description problem and
not a handoff: its body covers rebasing, but every noun in its description is
about a change fetched from review.typo3.org, so a session asked to rebase its
own committed patch read it as somebody else's work and correctly did not open
it. Two answers were offered and neither is decided — widen that description, or
give `typo3-core-patch-development` a rebase-before-push step. Whichever carries
it states the two non-obvious parts the session worked out itself: a running
`runTests.sh` suite reads the mounted working tree, so a rebase under it
invalidates the run and its containers have to be cleared first, and the
Change-Id is confirmed after the rebase, because losing it turns a new patch set
into a second Gerrit change. A skill lands in somebody else's project, so this
is the expensive kind — `D-SKL-022` is the judgement.
