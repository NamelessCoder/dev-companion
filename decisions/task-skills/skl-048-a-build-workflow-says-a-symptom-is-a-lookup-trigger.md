---
id: D-SKL-048
title: A build workflow says a symptom is a lookup trigger
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::theBuildWorkflowSaysASymptomIsALookupTrigger
---

# D-SKL-048 — A build workflow says a symptom is a lookup trigger

**The content-element workflow says a symptom is a query `typo3_hint_lookup`
takes, and the installation workflow does not, because that is where the
measurement stopped.**

Both workflows route their lookups by subject and at planning time, which is the
one moment a subject is what the session has. Debugging is where it does not.

## Evidence

- `feedback/2026-08-17-205945`, a v14 demo site on 14.3.6. It fetched twenty-one
  hints by id while planning and consulted the index at no point after the first
  symptom: nine debugging cycles and roughly 45 round trips, the largest cost
  item of the session. Four of those had an answer in the corpus, and in three
  cases the id was listed in an `availableHints` array sitting in the same
  context window.
- [`D-SKL-045`](skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md)
  left this half undecided and said why: a sentence sending a caller with a
  symptom to the index sent it to a miss, because the domain gate dropped the
  hint that explained the failure. That gate was closed the same week —
  [`D-ANS-084`](../answers/ans-084-a-curated-phrase-crosses-the-domain-gate.md)
  is the rule and
  [`R-ANS-031`](../../requirements/answers/ans-031-a-symptom-reaches-the-hint-that-explains-it.md)
  is what must keep holding.
- The two probes that entry named, re-run on 2026-08-18. "the content elements
  render in reverse order" now returns `datahandler-placement`. "f:asset.css
  does not appear in the rendered page" still misses `fluid-layouts-sections`,
  which is curation rather than the gate and is in hand on its own branch.
- A wider sweep with `bin/cli hints:probe` the same day. Of the four
  content-element symptoms the two feedbacks name that have an answer in the
  corpus at all, three reach it: the reverse order, the child rows that saved
  unlinked, and the backend preview that shows nothing, which returns
  `content-element-preview` first on a phrasing that borrows none of its words.
  The asset case is the fourth. The missing partial argument is a subject the
  corpus does not carry and is filed on its own.
- Installation symptoms reach in six of eleven, and the five that do not are the
  ones a session actually stops on. A 500 after the install returns
  `frontend-access-restriction`, a blanket not-found returns `extbase-arguments`
  first, a container that started green while the install failed matches
  nothing. What those failures are about is the container, Composer and the
  console, and the corpus is written about TYPO3.

## Decided

- **The line goes into `typo3-content-element-development` and nowhere else.**
  It is a section of its own between implementing and verifying, because putting
  it under `Establish evidence` returns it to planning time, which is the moment
  that already worked.
- **The example is the shape of the query, not the id that answers it.** An id
  in a published skill is a curation fact no release of this server corrects,
  and having to know the id is the work the caller was not doing.
- **The installation workflow is not touched.** Half its symptoms reach a hint
  from another subsystem, and promising a caller more than the matcher keeps
  costs the call the line was written to produce — `D-ANS-081`.
- **What the workflow adds to the base is the moment, not the tool.** Step 4 of
  [`base.md`](../../skills/base.md) already fixes `typo3_hint_lookup` with the
  paths in scope; this says the same tool takes the observation, and that the
  call comes before the reading the base fixes as the step after the lookups.
- **No requirement.** What one would state is every build workflow, and the
  measurement above says the opposite.

## Assumed

- That a session reads the section at the moment it breaks. A skill is loaded
  whole and nothing brings a session back to a section, which is
  [`R-SKL-024`](../../requirements/task-skills/skl-024-a-build-step-a-guide-answers-names-the-call-that-fetches-it.md)'s
  own open assumption one workflow over.
- That the content-element symptoms measured are the shape a session arrives
  with. They are this feedback's own, written down after the session ended.

## Wrong if

- A content-element session makes the call and is answered out of a layer it
  never asked about. Then the gate widened too far and the line sends a symptom
  to noise, which is `D-ANS-084`'s second **Wrong if** arriving through here.
- An installation session reports a symptom answered cheaply by the same call.
  Then the sweep above sampled the wrong queries and the line belongs in that
  workflow too.
- A session reads the section and reads the installed source first anyway. Then
  the surface is not the lever, and no wording at it will be.

## Since then

**2026-08-18.** The second **Wrong if** was tested against an installation
session and did not fire. `feedback/2026-08-18-070611` is a boot of the t3g/blog
DDEV installation on 14.3.6, and the symptom it ends on is a deprecation log at
63 KB after the first request, traced to TCA fields the extension declares as
searchable without naming them in `ctrl.searchFields`. Probed the same day,
`bin/cli hints:probe` on the symptom as that session would phrase it matches
nothing, and on its subject it reaches `deprecated-apis` and `tca-formengine`,
neither of which states what `ctrl.searchFields` must carry.

So the decision not to touch the installation workflow holds for the reason it
was made: what an installation session stops on is answered by
`typo3_changelog_lookup` at the major in play rather than by the hint index, and
a line sending the symptom to `typo3_hint_lookup` would have sent this one to a
miss. What the session reports instead is that nothing invited the question at
all once the workflow ended, which is the closing form
[`D-SKL-049`](skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md)
carries.
