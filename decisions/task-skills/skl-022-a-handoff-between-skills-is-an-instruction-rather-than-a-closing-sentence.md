---
id: D-SKL-022
date: 2026-08-07
status: confirmed
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

One session held both forms of the crossing, twenty turns apart, and each
behaved as this entry predicted. `feedback/2026-08-08-224450` reports invoking
`typo3-core-patch-development` at the moment the triage verdict turned into a
patch — the step written above — and names what came out of it, including the
`breaking-not-assessed` line that caught a breaking API change in its draft. The
same session then finished a push-ready patch, ran the project's checks and
handed it over without ever invoking `typo3-core-patch-review`, whose crossing
is the one still written as ownership: "it does not own judging its own". That
settles the second **Assumed**, which said an explicit instruction fires where
prose did not and that it was untested for a crossing between two skills. The
two forms were distinguishable in one session, in one model, on one task.

The crossing it did not fire on is the one `R-SKL-018` was never applied to.
Both skills that hand over *to* `typo3-core-patch-development` say to invoke it;
that skill's own handoff, back to the review, is a sentence about who owns what.
Read across `skills/` on 2026-08-09 it is the only one: every other crossing is
already an act — `Activate typo3-extension-documentation`,
`Activate typo3-extension-conformance`, hand the worked list back — or a routing
boundary a session reads before it starts rather than a step it owes mid-work,
which is what `typo3-core-patch-checkout`'s closing paragraph is. So the work is
one skill and one name in
`SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor`, and the todo serving
that feedback carries it.

It also narrows the proxy `D-EVI-002` accepts as the only one available. That a
successor is named is not what holds a crossing: this one was named, in a
paragraph the session was holding, and nothing fired. What an assertion has to
read is the imperative, which is what the two crossings that fired carry and
what that test already matches on.

## Since then

On 2026-08-11 the review crossing fired on a sentence that commissioned nothing.
`feedback/2026-08-11-055317`: the session had reported that the patch changes
covered behaviour and adds no test, the reader answered "I think the tests
should prove it", and it invoked `typo3-core-patch-development`. The reader had
meant that the missing test was reason enough to reject the patch, since a test
file for that class already exists. The session backed out, stayed in the review
and re-ranked the finding, so the cost was one turn under the wrong skill's
rules.

That is the price of this entry rather than a case against it, and the same
session reports the imperative working everywhere else in the skill. What it
shows is which half of a crossing the imperative leaves undecided: the review
crossing described its trigger as "a sentence in a conversation" and warned that
the transition is invisible, which primed the switch and named nothing that
distinguishes the two sentences. The triage crossing, written in the same
commit, names the instruction instead — "you are asked to fix it" — and no
session has reported it firing early.

So the crossing names the instruction that fires it and the remark that does
not, where the trigger is something the reader says. A review reaffirming a
finding and a reader commissioning work arrive in one register, and where the
sentence could be either the session asks rather than switches: switching costs
a turn under the wrong rules and asking costs one sentence. `R-SKL-018` carries
that half; nothing holds it, because which sentences a trigger has to exclude is
a reading of the workflow rather than a property of the file.

What would satisfy this reading is the fourth **Wrong if** above, which it
wrote.
## Since then

On 2026-08-14 the rule the crossing guards held under the pressure it was
written for. `feedback/2026-08-13-214927` reviewed a Playwright diff and names
four things it could have fixed on the way past — a missing `isReady()`, a
discarded return value, a dropped assertion, trailing blank lines — and reports
that what stopped it is the sentence saying a session crossing that line looks
like nothing from the inside. That sentence is this entry's own reading of
`feedback/2026-08-07-132559` written back into the skill, and the session's
account is that it names the failure mode concretely enough to recognise while
it is happening.

Nobody asked that session for the change, so what it reports on is the rule
rather than the trigger beside it, and a report is evidence about where the
boundary runs rather than a confirmation — `D-FBK-018`. What it does settle is
which half of the paragraph is doing the work: the concrete failure mode, not
the ownership sentence above it.

## Since then

On 2026-08-17 the imperative was read and nothing crossed.
`feedback/2026-08-17-213027` followed `typo3-content-element-development` to
completion on a six-element sitepackage and quotes that skill's closing
sentence, which tells the session to activate `typo3-extension-testing`,
`typo3-extension-documentation` and `typo3-extension-conformance`. None of the
three fired: no test was written at all, three README files were written by
hand, and the user reviewed the result himself and listed ten defects, seven of
them inside the conformance skill's own stated scope.

That is the reading of 2026-08-09 above, and this is what it got wrong. It read
across `skills/` and counted `Activate typo3-extension-documentation` and
`Activate typo3-extension-conformance` as already an act, so `R-SKL-018` was
applied to three core crossings and to none of the extension ones. An imperative
is what that pass could see in a file; what the two crossings that fired carry
beside it is the moment — "the verdict is 'still happens' and a fix is asked
for", "once the checks pass and the commit is written". The sentence that failed
names three successors, no moment, and sits after the commit section, which is
where a workflow is being left.

`D-SKL-053` is the judgement and carries the card. What it does not license is
writing the closing paragraph out as a list of everything the workflow still
owes: that is `D-SKL-049`'s gate, which is proposed and waiting on an answer.

## Confirmed on 2026-08-22

Two readings held this decision and changed nothing in it, so each is a line
here rather than a section of its own. Both are the crossing being built, and
the second sits after the 2026-08-09 reading that asked for it rather than
before it.

- 2026-08-07: `typo3-core-issue-triage` and `typo3-core-patch-review` each end
  in a section saying to invoke `typo3-core-patch-development` at the point the
  crossing happens, with the moment named, and both keep the ownership
  paragraph. `R-SKL-018` holds it. The question the third **Decided** bullet
  left open was put to the maintainer with both answers priced, and the answer
  was both with a pointer: `typo3-core-patch-development` gains a
  rebase-before-push step carrying the two parts a session worked out for itself
  — a running `runTests.sh` suite reads the mounted tree, so it is stopped and
  its containers cleared before rebasing, and the `Change-Id` is confirmed
  afterwards because losing it opens a second change.
  `typo3-core-patch-checkout` keeps its subject, a change fetched from review,
  and its description now names where a commit of your own belongs.
- 2026-08-09, the crossing running the other way: `typo3-core-patch-development`
  ends in a section saying to invoke `typo3-core-patch-review` on the diff once
  the checks pass and the commit is written, before the patch is pushed or
  handed over, with the ownership paragraph beneath it.
  `SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor` reads a successor per
  skill rather than one name for all of them.
