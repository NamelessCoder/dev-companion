---
id: D-SKL-022
title: A handoff between skills is an instruction rather than a closing sentence
date: 2026-08-07
status: confirmed
coveredBy:
  - SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor
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
- A session reports switching on a sentence that reaffirmed a finding, after the
  counter-case is in the skill. That would say the boundary cannot be drawn in
  prose at all, and that the crossing has to be a question the session asks
  rather than a trigger it recognises. Written on 2026-08-11, from the reading
  below.

## Confirmed on 2026-08-09

One session held both forms twenty turns apart and each behaved as predicted: it
invoked the successor at the moment the verdict turned into a patch, and then
finished a push-ready patch without ever invoking the review, whose crossing is
written as ownership. That settles the second **Assumed** — the two forms were
distinguishable in one session, on one task.

The crossing it did not fire on is the one the requirement was never applied to,
and reading across the skills it is the only sentence of its kind. It also
narrows the proxy `D-EVI-002` accepts: that a successor is named is not what
holds a crossing, since this one was named in a paragraph the session held. What
an assertion has to read is the imperative.

## Since then

The review crossing then fired on a sentence that commissioned nothing: the
reader meant that a missing test was reason to reject the patch, and the session
switched, backed out and re-ranked the finding — one turn under the wrong
skill's rules. That is the price of this entry rather than a case against it,
and it shows which half an imperative leaves undecided: that crossing described
its trigger as "a sentence in a conversation" and named nothing that
distinguishes two of them, where the triage crossing names the instruction and
has never fired early.

So the crossing names the instruction that fires it and the remark that does
not, and where the sentence could be either the session asks rather than
switches. Nothing holds that half, because which sentences a trigger excludes is
a reading of the workflow.

## Since then

The rule the crossing guards held under the pressure it was written for: a
review names four things it could have fixed on the way past and reports that
what stopped it is the sentence saying a session crossing that line looks like
nothing from the inside. Nobody asked it for the change, so it is evidence about
where the boundary runs rather than a confirmation. What it settles is which
half of the paragraph does the work: the concrete failure mode, not the
ownership sentence above it.

## Since then

The imperative was read and nothing crossed: a session followed a skill to
completion, quoted its closing sentence naming three successors, and none of the
three fired — no test written, three READMEs by hand, and ten defects the user
listed himself. That is what the 2026-08-09 reading got wrong. It counted
`Activate <skill>` as already an act, so the requirement was applied to three
core crossings and none of the extension ones. What the two that fired carry
beside the imperative is the moment; the sentence that failed names three
successors, no moment, and sits where a workflow is being left. `D-SKL-053` is
the judgement.

## Confirmed on 2026-08-22

Two readings held this decision and changed nothing, both of them the crossing
being built. The two skills that end at the patch say to invoke it at the point
the crossing happens, with the moment named and the ownership paragraph kept;
the question the third **Decided** bullet left open was answered *both, with a
pointer*, which is where the rebase-before-push step and its two parts came
from. And the crossing running the other way was written, which is why the test
reads a successor per skill rather than one name for all of them.

## Since then

An extension-side crossing fired, the first since the three that did not: a
session loaded the testing skill before writing a functional test, naming the
section that opens with the imperative and the moment beside it — which is what
the three-successor closing sentence was missing. Nobody asked it about the
crossing, so it is evidence about the boundary rather than a confirmation. What
it places is which of the two ways into a skill was working: the crossing inside
an active one fired, and the client's own listing reached the same session with
four matching descriptions and opened none.

## Since then

A session met the crossing *into* the checkout skill three times and crossed it
never, while holding one of the other two core skills for two of them — so the
tool was reachable and what stayed shut was the third. Neither of the two tells
a session to open it: one names it in a sentence about who owns what, the other
not at all, and the session quotes that sentence as read and attributes it to
the wrong skill.

So the 2026-08-09 sweep missed an edge by direction: it read the crossings
running *out of* the three core skills and never the one running *into* this
one. What that costs is the fetch arriving as a step inside two other workflows
and never as the task, which is the only shape a description could be chosen on.
