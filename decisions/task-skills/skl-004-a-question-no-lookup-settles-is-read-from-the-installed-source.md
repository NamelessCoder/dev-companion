---
id: D-SKL-004
title: A question no lookup settles is read from the installed source
date: 2026-08-02
status: open
coveredBy:
  - SkillTest::theInstalledSourceIsTheStepAfterTheLookups
---

# D-SKL-004 — A question no lookup settles is read from the installed source

**The order's answer to a question no lookup settles is a finding that says so.
A session that has to produce working markup cannot write one, and the installed
TYPO3 source is named nowhere as the step after it.**

`feedback/2026-08-01-003933` reports a session that guessed at a ViewHelper
contract and changed the markup until the user corrected it. Its sibling
`003356`, filed by the same session three minutes earlier, reports that the same
session read vendor source directly and calls that the reverse of the workflow.
Both are costs, and the boundary between them is where the reading sits in the
order. Nothing here states that boundary.

## Evidence

- The instance the feedback names is answered. `bin/cli hints:probe` with the
  query of the `003448` sibling — *f:if with f:else but no explicit f:then
  swallows the inline then-branch / f:link.typolink output* — reaches
  `fluid-templates` at `appliesTo(16) + text(132)`, and that entry now carries
  the branch rule with the working markup as its example.
  [`D-KNW-016`](../knowledge/knw-016-what-an-f-else-does-to-the-branch-beside-it-is-a-subject-this-server-owns.md)
  wrote the statement and
  [`D-KNW-024`](../knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md)
  is what makes a query written in Fluid tags reach it. `003448` is archived. So
  the source reading this feedback holds up as the example would not be needed
  today.
- What is left reaches nothing that answers it. This feedback's own query —
  *reading viewhelper source (IfViewHelper) when unable to determine expected
  behavior* — reaches `fluid-viewhelpers` at `appliesTo(10) + text(68)`, alone.
  That entry says what a ViewHelper class looks like and what its arguments are
  checked against. It does not say where a behaviour question goes once the
  lookups have been asked.
- `skills/base.md` names reading three times, and every one of them is about the
  project's own checkout or is a prohibition. "Do not fall back to general TYPO3
  knowledge or start reading the checkout" is the answer for a server that is
  not there. "**Then** read the checkout. Not before" orders the project's files
  against the lookups. Step 5 adds that "the installed core shows what one
  version implements rather than what it supports", which is a limit on a
  reading rather than an instruction to take one.
- The one sentence for the exhausted case is written for a review. "Where the
  manual has no page for it either, the finding says the question could not be
  settled." The session that filed this was building a content element in
  `site-new`. It had no finding to write and a template that had to render, so
  the sentence is not addressed to it.
- The skill this session names, `typo3contentelementdevelopment`, points the
  same way. Its reading bullet is "Read the nearby content elements, TCA files,
  TypoScript imports, templates, assets, schema and tests — the project's file
  organization is the thing a new element has to fit, and only the checkout has
  it." The installed TYPO3 is not among them.
- [`D-ANS-010`](../answers/ans-010-does-it-still-work-is-a-question-for-the-manual-not-the-changelog.md)
  is the only entry that decides anything about this reading, and it decides
  against it: a miss in the manual "is a finding rather than a licence to
  reconstruct the contract from the installed core". Its first **Wrong if** did
  not fire here. That one asks for a session that follows the routing, calls
  `typo3_documentation_lookup` at the target version and still reads the core by
  hand; this session called neither before the user asked it to.

## Decided

- **Step 4 of the ladder**, wording, on `skills/base.md`. Not 1a: the statement
  that would have prevented the named instance landed, and this feedback adds
  nothing about TYPO3 that is missing. Not 3: the routing that kept the query
  from the entry landed with `D-KNW-024`.
- **Queued, not closed on the spot.** `skills/base.md` is a skill contract, and
  [judging.md](../../documentation/records/judging.rst) puts that on the
  reviewed side. `D-ANS-010` queued its own skill half for the same reason.
- The feedback is **trimmed**. Its example is answered by the archived sibling
  and stays out of the card; what the card carries is the step after the
  lookups, which is the half no entry states.
- **Not the feedback's own wording.** "Read the source before guessing" as it
  proposes would break `D-ANS-010`'s boundary. The installed source says what
  this one installation does, and never what TYPO3 supports. A sentence that
  does not carry that distinction licenses exactly the reconstruction
  `D-ANS-010` refused, and the two entries would then say different things.
- The other lever is named and not taken here: a tool that resolves behaviour
  out of the installed source, which is what `D-ANS-010`'s first **Wrong if**
  reserves. This feedback does not establish it. The behaviour its session
  needed is in the corpus now, so what it shows missing is the named next step
  rather than an answer.

## Assumed

- That `skills/base.md` can carry another sentence at all.
  [`D-SKL-001`](skl-001-the-order-a-task-starts-in-is-one-file.md) watches its
  growth — 496 words when it was written, 960 after the sweep, 1099 now — and
  every sentence added is one the reading can swallow. Where the sentence
  displaces rather than adds is the card's first step, not this run's.
- That a sentence there would have reached this session. It would not have:
  `003356` records that no skill activated in that run at all. The activation
  half is that sibling's, held in `todo/waiting/` behind its own question, and
  the `D-AUD-003` description rewrite of 2026-08-02 is what stands against it.
  This entry is right about the order and says nothing about the reach.

## Wrong if

- A session reaches the sentence with the lookups exhausted and still reports
  that it reconstructed the behaviour by trial and error. Then the named step is
  not the lever and the tool `D-ANS-010` reserves is what was missing.
- A feedback reports the opposite cost once it lands: a session that read the
  installed core early because the base named it, and carried what one version
  implements into an answer as though it were what TYPO3 supports. Then the
  distinction did not survive the wording.
- The same task shape files again with a skill active and the Fluid statement in
  reach, and still names source reading. Then it is the activation rather than
  the order, and the lever is `003356`'s.

## Since then

The step landed as a section of its own after "**Then** read the checkout",
saying what answers the question, what the reading replaces and what it may not
be carried into. Step 5 was the other candidate and is where the reading would
not have been found: its paragraph asks what still works in a version, and the
session held a question about what an unaltered ViewHelper does. What step 5
gave up instead is the review-only sentence and the limit that stood beside it,
which moved to the section that orders the reading.

The **Assumed** was measured and its arithmetic was two commits stale. What the
wording says about the installed source was read rather than recalled: those
packages ship classes and no tests, so a step naming the tests would have named
something an installation does not have.

## Since then

A second session reached the same shape from the review side and settled three
questions by grepping its checkout. The section reaches two of them, both asking
what a named class does; whether an idiom is established is neither — it is a
sweep for call sites with no class to start at, and a reviewer asks it of every
alternative it proposes. That boundary is stated in the scope and only one tool
returns it, which this session never called, so what is left is step 2 rather
than 1a: the sentence exists and does not pass where the task does. The review
skill already carries it one subject over, which makes the placement a choice
between two files rather than a wording nobody has.

## Since then

The placement is the review skill rather than the base, and what decides is who
asks: a reviewer asks whether an idiom is established of every alternative it
proposes, while a session that is building asks how the core wires a subsystem,
which has a home at step 4. The bar is the reviewer's too. So the base does not
grow, and the bullet says what the base's own step cannot reach — that step
starts at a class, and this question has none.

Both halves were read rather than recalled: the checkout carries five
occurrences of the attribute and the hints carry the plain form alone. The
feedback's remaining doubt is settled with it — the manual does not answer its
first question either, which is this boundary from the other side.
