---
id: D-SKL-049
title: 'The gate at the end of a workflow waits for its corrections'
date: 2026-08-18
status: open
---

# D-SKL-049 — The gate at the end of a workflow waits for its corrections

**A closing gate naming what a build workflow still owes waits on the three
corrections its own sightings produced, and whether it is written is the
maintainer's answer.**

One session reported three prescriptions partly executed and named the pattern
as the finding. Each of the three was corrected at the point of use the next
day, and none of the corrections has been in front of a session yet.

## Evidence

- `feedback/2026-08-17-212218`, a v14 demo site built as a sitepackage plus a
  distribution extension on 14.3.6. It reports the five bullets of *Verify at
  the right layers* in `typo3-content-element-development` with one executed,
  names two earlier instances of the same shape in the same session, and states
  the repetition rather than any one bullet as what it found.
- It is a summary, so it is mapped onto its siblings before anything else
  ([`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)).
  Its first instance is `feedback/archive/2026-08-17-211118`, judged in
  [`D-SKL-044`](skl-044-a-step-that-names-two-hint-ids-says-what-each-answers.md)
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
  [`D-SKL-010`](skl-010-the-assessment-before-a-core-patch-reads-the-issue.md)
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
- **The recommendation is to wait, and the trigger is named.** `D-SKL-044`'s
  first **Wrong if** is a session that fetches one id and stops with the
  corrected step in front of it, and `D-SKL-045`'s is a session that ships a
  view unverified with the guide named at the step. Either firing is the gate's
  case made on a corrected file.
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

## Since then

**2026-08-18.** The second **Wrong if** fired. Two sessions from another task
shape report the same pattern, and both come from `/home/benji/projects/blog` —
an extension repository booted in DDEV, where every one of the thirteen feedback
behind this entry came from one sitepackage build in
`/home/benji/projects/site-demo`.

`feedback/2026-08-18-070611` is the boot itself. What it reports partly executed
is a prescription of [`base.md`](../../skills/base.md) rather than of a build
workflow: *a report names the step it did not reach*, stated in step 2 and again
in step 5, and complied with in neither. Its own reading is that the failure is
placement rather than wording — the rule sits in the preamble and the report is
written last, after a long tool-heavy session.

`feedback/2026-08-18-074327` is the same directory later the same morning, and
it names the mechanism in this entry's own terms: steps 2, 4 and 5 of the order
skipped on a task that changed code, with nothing naming the skips, because "the
order is walked once, at the start, against the task as it was first phrased"
and nothing re-raised it when the task became something else.

The same boot carries a third instance of the shape, and it is what a gate would
have to route rather than restate. `feedback/2026-08-18-070611` found the
deprecation log at 63 KB after the first request, read it out of the file, and
reported it without putting it to the server, because the workflow was over. Two
probes on 2026-08-18 say where such a gate would have to send it:
`bin/cli hints:probe` on the symptom as the session would phrase it matches
nothing, and on its subject it reaches `deprecated-apis` and `tca-formengine`,
neither of which carries what `ctrl.searchFields` must declare. So the corpus
does not answer it and `typo3_changelog_lookup` is the route — which is
[`D-SKL-048`](skl-048-a-build-workflow-says-a-symptom-is-a-lookup-trigger.md)'s
second **Wrong if** tested here and not firing.

What this moves is the corpus and not the recommendation. The three corrections
this entry weighs are still unmet by any session, and none of the three new
sightings is about them, so the case against a fourth intervention against the
same three stands. What is gone is the narrower half of it: a gate is no longer
an intervention against one build, and the question the card carries is now
asked with two task shapes and two directories behind it rather than one.

**2026-08-24.** The second **Wrong if** fired again, from a third task shape and
a third directory. `feedback/2026-08-24-100534` is a core patch review in
`/home/benji/projects/typo3-cms` reworked into a patch, and it reports five
prescribed calls not made, naming two of them as instructions that were present,
correct and in its context. Two siblings from the same day say the same:
`feedback/2026-08-24-140239` read none of the pages named to it, and
`feedback/2026-08-24-133515` wrote a whole core patch with zero calls to this
server.

One of the five is judged as a placement and queued —
[`D-SKL-072`](skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md),
the crossing between two core skills naming the calls the order restarts with.
The other four are the shape this entry carries and none of them has a
correction to be met by, so the recommendation is unchanged. What is answered is
the weight: the gate is now asked with four directories and four task shapes
behind it, and the four sightings above landed after every correction this entry
weighs.
