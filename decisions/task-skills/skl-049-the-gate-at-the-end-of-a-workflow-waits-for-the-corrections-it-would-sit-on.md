---
id: D-SKL-049
date: 2026-08-18
status: open
---

# D-SKL-049 — The gate at the end of a workflow waits for the corrections it would sit on

**A closing gate naming what a build workflow still owes waits on the three
corrections its own sightings produced, and whether it is written is the
maintainer's answer.**

One session reported three prescriptions partly executed and named the pattern
as the finding. Each of the three was corrected at the point of use the next
day, and none of the corrections has been in front of a session yet.

## Evidence

- `feedback/2026-08-17-212218`, a v14 demo site built as a sitepackage plus a
  distribution extension on 14.3.6. It reports the five bullets of
  *Verify at the right layers* in `typo3-content-element-development` with one
  executed, names two earlier instances of the same shape in the same session,
  and states the repetition rather than any one bullet as what it found.
- It is a summary, so it is mapped onto its siblings before anything else
  ([`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)).
  Its first instance is `feedback/archive/2026-08-17-211118`, judged in
  [`D-SKL-044`](skl-044-a-step-that-names-two-hint-ids-says-what-each-one-alone-answers.md)
  and corrected by `7dab8ef8`; its second is
  `feedback/archive/2026-08-17-211306`, judged in
  [`D-KNW-087`](../knowledge/knw-087-a-listed-neighbour-says-what-it-prevents.md)
  and corrected by `9111d6a8` and `1f189b5f`; its third is the browser bullet of
  the section it is about, judged in
  [`D-SKL-045`](skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md),
  held by `R-SKL-024` and corrected by `4ec22687`.
- What the three corrections have in common is the mechanism the summary names.
  A clause per hint id, a neighbour that says what it prevents, a guide named by
  `documentId` at the step that needs it — each one changes what a caller reads
  at the moment it decides, which is where the summary says the decision is
  made.
- The query cannot be re-run here: it is a six-element sitepackage build, and
  what this repository runs and records is a repository review
  ([AGENTS.md](../../AGENTS.md)). What was re-read on 2026-08-18 is the section
  itself. Its other four bullets are present, correct and undisputed by the
  feedback, and the browser bullet now carries both guide ids.
- The corpus is one session. `bin/cli feedback:list` on 2026-08-18 reports 13
  open feedback, all of them from `/home/benji/projects/site-demo` and all from
  one build.
- The same debrief reports the other side. `feedback/2026-08-17-212600` measures
  the two skills at 2,945 and 1,290 tokens, defends the size discipline as what
  made them read in full, and names these same two failures as legible *because*
  of it. Its own suggestion names "the order, the obligations, the terminal
  check", so the session asking for a gate is also the one that says what a gate
  may not cost.
- The repository already carries both halves of the question.
  [writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst)
  says judgment keeps a checklist and construction does not;
  [`D-SKL-010`](skl-010-the-assessment-that-precedes-a-core-patch-reads-the-issue-and-the-review-server.md)
  carries *the rungs read as a checklist and are skipped as one* as its own
  **Wrong if**; and `R-SKL-020` is a terminal stop written into a workflow
  already, for the step that publishes. The skill in question also ships
  `references/checklist.md`, read before writing code, which
  `feedback/2026-08-17-212600` calls the most useful document of the build.

## Decided

- **Step 5 of the ladder for the half nothing else reports.** The prescription
  was present, correct, delivered into an active skill and read; what the
  feedback names as missing is the form a workflow has. That is the design being
  the price rather than a gap, a delivery, a routing entry or a wording.
- **Proposed, so it waits for an answer.** The card carries the question and
  stays with the feedback, which keeps `D-FBK-017`'s invariant.
- **The recommendation is to wait, and the trigger is named.**
  `D-SKL-044`'s first **Wrong if** is a session that fetches one id and stops
  with the corrected step in front of it, and `D-SKL-045`'s is a session that
  ships a view unverified with the guide named at the step. Either firing is the
  gate's case made on a corrected file.
- **Rejected: writing the gate now.** It would be the fourth intervention
  against three sightings, three of which landed on 2026-08-18 and none of which
  a session has met. Afterwards nothing could say which of the four carried a
  session past the step, and the cheap corrections would be credited to the
  expensive one or the other way round.
- **Rejected: archiving on the three corrections.** The pattern claim is the
  half only this file reports, and closing it would tell the reporting session
  its finding was worked off when what landed answers its three examples.
- **What the work would open with is a placement, not a text.** The skill
  already gates before the code is written, so the question is whether the
  closing step re-reads that reference or states its own list — and that is the
  todo's first question rather than this entry's answer.

## Assumed

- That the three corrections address the mechanism the summary names. Each was
  made where the caller decides, which is where the feedback puts the failure,
  and no one of them was written for the pattern.
- That one session reporting a shape three times is one pattern rather than
  three coincidences. Nothing separates them, and nothing else in the corpus
  reports it.
- That a session meeting the corrected files will file again where they do not
  take. This session filed thirteen times, which is evidence about it and not
  about the next one.

## Wrong if

- A session with all three corrections in front of it reports a prescription
  partly executed a fourth time. The corrections were then not the lever, the
  gate is what is left, and the waiting cost that session its step.
- A second session, from another task shape, reports the same pattern. The
  corpus is then more than one build and the question is answered by weight
  rather than by an ask.
- The answer comes back that the gate is wanted and it lands as one closing
  sentence pointing at the reference the skill already ships. What waited was
  then cheaper than the weighing written here.
- A gate is written later and a session reports skipping it as a list. That is
  `D-SKL-010`'s **Wrong if** arriving here, and it would make the deferral right
  for a reason this entry does not give.
