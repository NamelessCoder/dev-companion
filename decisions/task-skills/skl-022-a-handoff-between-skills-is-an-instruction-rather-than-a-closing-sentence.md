---
id: D-SKL-022
date: 2026-08-07
status: open
---

# D-SKL-022 — A handoff between skills is an instruction rather than a closing sentence

**Three sessions crossed from one core workflow into the next and none of them
opened the skill that owns the second, although two had read the sentence naming
it.**

## Evidence

- `feedback/2026-08-07-065244`. `typo3-core-issue-triage` activated and carried
  the verification. Its closing paragraph names `typo3-core-patch-development`,
  says what crosses over, and the session states that it read it. The user then
  asked for the patch. The session wrote it over roughly forty more turns and
  never invoked the skill — deciding for itself the changelog obligation, the
  suites and databases, the trailers, the release branches and the Gerrit hook.
- `feedback/2026-08-07-132559`. `typo3-core-patch-review` activated and fitted.
  Its closing paragraph names `typo3-core-patch-development` and says a review
  does not change the patch. The user accepted the findings and asked for the
  change; the session edited `ColumnMap.php`, wrote a functional test, ran seven
  suites and amended the commit, still under review rules. It says there was no
  moment at which anything marked the crossing.
- `feedback/2026-08-07-130022` is the same failure from the other side. No skill
  activated at all for "bitte rebasen". `typo3-core-patch-checkout` covers it —
  its description carries "rebase it where the branch has moved under it" — but
  every noun around that phrase is about a change fetched from review.typo3.org.
  The session read it as somebody else's patch and did not open it, and on that
  description that reading is correct.
- The three are two different failures with one consequence. Two skills name
  their successor in prose that a model reads and does not act on; one skill's
  description does not describe the task that reached it.
- `D-SKL-021` already settled that triage and fetching a patch end before
  somebody else's act. This is the crossing that follows, and nothing carries
  it.

## Decided

- A sentence in a skill body is documentation of a boundary, not a transition. A
  skill is selected on its description by the client, and prose inside an active
  skill competes with everything else in the window; two sessions holding
  exactly the handoff it describes is what says so.
- So the crossing is written as an instruction the session performs — invoke the
  named skill — at the point the trigger occurs, rather than as a closing
  paragraph about ownership. The closing paragraph stays; it is what tells a
  reader where the boundary is.
- `typo3-core-patch-checkout` is a description problem and not a handoff one.
  Whether it widens to cover a commit the session wrote itself, or
  `typo3-core-patch-development` gains the rebase-before-push step, is the
  question the todo carries; both were offered by the session and neither is
  decided here.
- The cost is not hypothetical. What the first session improvised includes the
  changelog obligation, which it answered from its own knowledge — and
  `D-ANS-061` is the same obligation not arriving by another route in a
  different session.

## Assumed

- What did not fire would have helped. Neither session reports a wrong outcome
  from improvising, and the first says everything it decided held. What is
  claimed is that the order was reconstructed, not that it was reconstructed
  wrongly.
- An explicit instruction fires where prose did not. That is the shape
  `D-AUD-003` already argued for the entry point, and it is untested for a
  crossing between two skills.

## Wrong if

- A session reports invoking the successor and finding the two skills'
  instructions in conflict while both are loaded, which would say the crossing
  needs the first to close rather than the second to open.
- Widening `typo3-core-patch-checkout` is reported as pulling it into sessions
  that wanted the review-server workflow, which would say the two tasks are one
  description apart for a reason.
- A session with no client-side skill invocation at all reports the same
  crossing, which would say the lever is in the tools rather than in the skills.

**Since then**, on 2026-08-07, it was built and the open question was answered.
`typo3-core-issue-triage` and `typo3-core-patch-review` each end in a section
that says to invoke `typo3-core-patch-development` at the point the crossing
happens, with the moment named — the verdict is "still happens" and a fix is
asked for; the reader accepts the findings and asks for the change. Both keep
the ownership paragraph, which is what tells a reader where the boundary is.
`R-SKL-018` holds it.

The `typo3-core-patch-checkout` question was put to the maintainer with both
answers priced, and the answer was both with a pointer.
`typo3-core-patch-development` gains a rebase-before-push step carrying the two
parts a session worked out for itself — a running `runTests.sh` suite reads the
mounted tree, so it is stopped and its containers cleared before rebasing, and
the `Change-Id` is confirmed afterwards because losing it opens a second change.
`typo3-core-patch-checkout` keeps its subject, a change fetched from review, and
its description now names where a commit of your own belongs. So the stopping
rules are written once and the boundary is stated from the side a caller reads
first.
