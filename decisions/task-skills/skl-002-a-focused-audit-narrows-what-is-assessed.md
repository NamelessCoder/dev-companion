---
id: D-SKL-002
date: 2026-08-02
status: open
---

# D-SKL-002 — A focused audit narrows what is assessed, not the list it closes on

**A focused audit narrows what a review assesses, never the surface list its
report closes on.**

A surface nobody was asked about is reported as out of scope. Dropping it from
the list is the failure `R-SKL-004` was written against, and a cheaper review is
not a reason to reopen it.

## Evidence

- The feedback of 2026-07-31 18:36 asks for "a quick-start mode that skips the
  full surface list for focused reviews (e.g. security-only or
  configuration-only audits)", from a session that had just used the skill for a
  full audit and reported the order, the severity rubric and the finding gate as
  what made the review work. It is the cost of a design reported by a session
  that also reported its benefit.
- The permission it asks for already exists and is one clause deep in a
  reference: `references/checklist.md` line 3, "Read the relevant sections for a
  scoped review; read all sections for a full extension audit." It is the only
  place in `skills/` or `scenarios/` where a scoped review is named at all.
- The two operative steps in `SKILL.md` never mention what was asked for. Line
  20 builds the work list from "the checklist's surfaces narrowed to the ones
  this kind of checkout can have" — narrowed by the kind of checkout, never by
  the request — and line 98 closes on "the surface list written in step 5, every
  entry marked assessed or unassessed". A session doing a security-only review
  reads the clause, then is told to write the whole list and answer every entry
  on it, so the clause is outranked by the step that builds the work list.
- `bin/cli hints:probe "quick start audit skip full surface list"` reaches
  nothing, and "security-only focused conformance review scope" reaches only
  `security-sinks`, which is about sinks. Nothing in `knowledge/` was ever meant
  to carry this: how much of a task to do is a skill's job.
- Against it, `R-SKL-004` is built out of runs that went narrow: a run that read
  three XLF files without asking what governs them, a run that filed
  translations as "assessed and clean" with `source-language="de"` on screen,
  two runs that produced no finding about static analysis in a repository with
  no analyser, and one whose absent `Documentation/` appeared neither as a
  finding nor as unassessed. In every one of them the cheap review and the
  thorough one produced the same-looking report.

## Decided

- Step 4 of the ladder, wording. The rule is here, it was delivered — `SKILL.md`
  orders the checklist read — and it did not take, because the sentence that
  permits scoping sits in a reference while the sentences that build and close
  the work list do not know about it.
- The work is queued rather than done in the judging run. It rewrites the two
  operative steps of a published `SKILL.md`, and
  `SkillTest::theBaseIsEstablishedBeforeTheCheckoutIsOpened` asserts one of them
  as a literal string in an ordered block. That is a skill's contract, and
  `judging.md` puts it on the far side of what a judgement may change on the
  spot.
- Rejected: the feedback's own suggestion taken literally. A mode that skips the
  full surface list removes exactly what makes an absent surface visible, and
  the report then cannot separate "you did not ask about this" from "there is
  nothing here" — which is the distinction the surface list exists for and the
  one every run in `R-SKL-004` failed.
- Rejected: closing the feedback on the clause that already exists. It is
  answered in the reference and contradicted in the instruction, and a session
  that follows the instruction is following the right file.

## Assumed

- That the two halves come apart — that what a review *reads* can be cut to the
  requested surfaces while what it *lists* stays whole, at a cost of one line
  per surface in the report. Nothing has measured that. If the reading is what
  the list drags along, then a focused mode is not a rewording and the honest
  answer is that this skill has one mode.
- That a focused request is legible to the session at all. "Security-only" was
  the feedback's example, and a request that names no surface leaves the
  narrowing to a judgement the skill would then have to describe.

## Wrong if

- A review given a focused prompt writes a focused surface list and reports it
  clean, so a reader cannot tell an unrequested surface from an unexamined one.
  Then the narrowing reached the list after all and the clause has to go rather
  than be strengthened.
- Or the reverse: runs given a focused prompt keep writing the full list
  unprompted, in which case nothing was outranked, the cost the feedback reports
  is the reading and not the list, and a paragraph was spent in a file that is
  load-bearing because it is short.

## Covered by

- `SkillTest::aFocusedRequestNarrowsTheReadingAndNeverTheSurfaceList`, which
  reads back the narrowing where the list is built, the third state where the
  report closes, and the clause in the reference that used to be outranked.
- `SKILL-11`, which is what measures the behaviour. The test above is a
  sentence; `SkillTest::theBaseIsEstablishedBeforeTheCheckoutIsOpened` and
  `SkillTest::anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk` hold the
  list being written and answered, and none of the three can see whether a
  session given a focused prompt narrowed the reading or the list.

## Since then

The wording landed on 2026-08-02, and what settled the assumption first is the
two runs the decision had not read for it. They answer the smaller question —
whether an entry closed without reading stays distinguishable — and they answer
it in the report rather than in a rule. `REVIEW-01`'s answer carried five
categories where one would have done: High/Medium/Low, "Recommendations (no
verified violation)", "Assessed and clean", "Not applicable" for the subsystems
the extension does not register, and "Unassessed — not the same as clean", with
a reason per entry on the last. It then invented a sixth nobody had asked it
for — the consent checkbox reported as "a legal call, not a TYPO3 conformance
defect" — which is a surface placed outside the review rather than filed as a
finding. A report that already keeps four kinds of "no finding here" apart
keeps a fifth apart too, and the fifth is the cheaper one to write: not
requested says who narrowed it.

The other half of the assumption — that an entry can be closed at a cost of one
line, without the reading — is `REVIEW-02`. Its coverage table closes
`contentElements: []`, `middlewares: []` and `backendRoutes: []` off the scope
call without opening the checkout for any of them, and files translations as
correctly absent because `crowdin.yml` owns them. So listing without reading is
already what the runs do; the two halves come apart, and the assumption stands
on evidence rather than on nothing.

What neither run measures is the narrowing this decision is about. Both were
given an open prompt, so every entry they closed cheaply was closed because the
surface is **absent**, never because the request left it out — and an absent
surface costs no reading to begin with. Whether a session told "security only"
writes the whole list is unmeasured, which is why `SKILL-11` exists and carries
`open` rather than `held`. The **Wrong if** above is unchanged and still needs a
run with a focused prompt in it.

One thing the wording decides that the entry did not. The state is called **not
requested** rather than "out of scope": `REVIEW-01` already used "Not
applicable" for a surface the checkout does not have and "outside what this
server answers" for a question that is not TYPO3's, and a third phrase near
those two has to say which of them it is not.
