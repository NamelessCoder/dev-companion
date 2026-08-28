---
id: D-SKL-079
title: 'A widened request re-establishes what the patch is and what it owes'
date: 2026-08-27
status: open
coveredBy:
  - SkillTest::aWidenedRequestReEstablishesWhatThePatchIsAndWhatItOwes
---

# D-SKL-079 — A widened request re-establishes what the patch is and what it owes

**Where the request widens after the patch is under way,
`typo3-core-patch-development` has the session say again what the change is,
which branches it reaches and what it owes.**

Both rules that would have carried it are in the file and both are bound to the
assessment, so a session whose task grew eight times re-derived its own scope
four times and threw away two rounds of work.

## Evidence

Measured on 2026-08-27 in this branch, against `feedback/2026-08-24-225243`.

- The session is `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]`, from
  "bitte review mir 93177" — roughly twenty user turns, twelve of them arriving
  mid-tool-call. What it became, in order: review, amend, write the Gerrit
  comment, reply to four threads, build a follow-up hardening patch, extend it
  to the client, screenshot it twice, rewrite the client fix twice, write a
  changelog entry, delete it.
- The four re-derivations are named in the report: is the hardening one patch or
  two, does it reach the older LTS, does it need the client, does it need an
  entry. Each is a question the skill already asks once.
- The skill's two nearest rules are written for a moment that has passed. "Keep
  the patch one change" stands at the point of narrowing, and `D-SKL-075` put a
  paragraph under it for the list a patch closes on — both about work arriving
  from outside the issue. The other is step 3's "Establish the blast radius here
  rather than meeting it while working", whose own sentence names this cost:
  discovered incrementally it arrives after the change has been characterised,
  and then it is the characterisation that has to be taken back. Nothing tells
  the session to take it back on purpose.
- The changelog rule was here and was delivered.
  `knowledge/documents/core/contribution/changelog.md` gives `Important` as
  "anything else that may require manual action" and says a casual bug fix owes
  none; the skill's own section says to decide the type from what the change
  does rather than from habit. The session reports reading that rule hours
  earlier and reasoning from precedent when the moment came, which is neither a
  gap nor a delivery failure.
- `bin/cli hints:probe` on the widening — re-establish what the change is and
  which branches it reaches once the request grows — reaches nothing and returns
  its 108 candidates as the index. That is right: what is missing is not a
  statement about TYPO3.
- `D-SKL-062` is the neighbouring lever and does not reach this. Its re-ask
  fires where the work enters a subject the opening did not name and it routes
  to `typo3_task_guide`, which answers which workflow a task belongs to. Here
  the workflow was right throughout, and what moved was the change's own
  characterisation inside it.
- Neither activation nor the crossing is at fault, and the report says so from
  the inside: the review skill's three disciplines each caught something, and
  the crossing into this skill fired on the user's own sentence.
- `D-SKL-075` already names this feedback from the other side — the same
  paragraph "fired, but only for the first split" — and left its card standing
  for this judgement rather than folding it in.

## Decided

- Step 4 of the ladder, wording. The skill fired, the rules were read, and what
  did not take is that both are written as one-time acts.
- Queued rather than made on the spot. It rewrites a published `SKILL.md`, which
  `judging.rst` puts on the far side of what a judgement may change in its own
  run, and it is the call `D-SKL-075` made on the same file.
- At `normal`, set by what the widening cost rather than by how many sessions
  reported it. One session reports it, and it counts the loss: two rounds of
  client-side work discarded, an entry written and deleted, four re-derivations.
- Where the paragraph goes is the todo's first step. Two places are candidates —
  under "Keep the patch one change", where `D-SKL-075`'s paragraph sits and
  where a session decides what this patch is, and at the foot of "Make the
  change", where a widening is met. Deciding between them is a reading of the
  body this run has not made.
- The three things re-established are the skill's own vocabulary — the change
  type from step 2, the branches from the blast-radius paragraph, the entry from
  its own section — so the wording can point at them rather than restate them.
- The feedback's second ask is not part of this. `D-KNW-127` answers it, and the
  report is trimmed to this half in the same commit.
- `coveredBy: []`, because nothing has been written yet. What could hold this is
  a `SkillTest` sentence over the skill body, and it belongs to the commit that
  writes the wording.

## Assumed

- That a session which read a rule at the assessment reads it again at the
  moment the request widens. Nothing here measures that, and it is the
  assumption `D-SKL-062` records for the act list it moved into the
  `instructions`.
- That a widening is a moment the session notices itself in. The reporting one
  did — it says the honest answer was a different one each time — and nothing in
  this server sees a user turn arriving mid-tool-call.

## Wrong if

- A session re-establishes all three and still ships the wrong characterisation.
  Then the wording is not the lever, and what is missing is a gate, which is
  what `feedback/2026-08-17-212218` reports for another skill.
- The widening arrives without the session recognising one as such. Then the
  paragraph never fires whatever it says, and the moment has to be named by the
  acts around it the way `D-SKL-062` names its own.
- A reviewer asks for two patches out of one widening to be squashed. Then
  re-establishing the change bought a split the project did not want, and it is
  the same objection `D-SKL-075` carries as its first.

## Since then

The placement is settled: the third paragraph of the narrowing cluster, because
a widening is a session deciding again what this patch is, and the reporting
session names that sentence as the one that fired. The foot of the making
section is where a widening is met and not where anything is decided.
`D-SKL-075` settled the same shape one paragraph earlier. The three are pointed
at rather than restated, so a rewrite leaves no second copy here, and the first
**Wrong if** stays unmeasured for the same reason that entry is open on it.
