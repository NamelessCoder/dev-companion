---
id: D-SKL-009
date: 2026-08-03
status: confirmed
---

# D-SKL-009 — The rule that keeps not landing is written as an act with an object

**A rule three runs read and none followed is rewritten as an act with a named
argument, and what the skill gained without a run behind it comes back out.**

Naming the suites that were not run was stated as a property the report should
have. It was present at every length the skill has had, and no run has ever
produced it.

## Evidence

- Three recorded `REVIEW-03` runs report green suites and name none of the ones
  `typo3_test_run_guide` returned beside them. The rule was in `SKILL.md` for
  all three: `7553cb3` at 1009 words, `e6c1067` at 1165, `8c01355` at 1562.
  Failing at the shortest length is what rules out the skill being too long as
  the cause of this one.
- What the sentence said was "Anything it did not run stays labelled as
  available rather than passing", followed by the example "The tests would
  presumably still pass" is not a review sentence. Both are about a **claim**
  that an unrun suite passed. A report that simply omits the suites makes no
  such claim, so all three runs were compliant with the sentence and none with
  the demand.
- The fourth run did drop a call, and not the one being watched: it never called
  `typo3_hint_lookup`, which the run before it did. Between the two, `SKILL.md`
  grew from 1165 to 1562 words, and the paragraph added directly beside that
  call — that the diff's content routes as well as its paths — had no run behind
  it. It came out of a reading of somebody else's review pipeline, not out of a
  session here.

## Decided

- The rule becomes an act with an object: the review writes out, by name, the
  suites on the guide's own list that it did not run. The consequence is stated
  as what the omission does to a reader, and the example is the omission itself
  rather than the false claim — an unnamed suite is that sentence with the words
  taken out.
- The checklist's `Tests` surface says it is answered with both halves, so the
  demand is where the report is closed against the surfaces and not only where
  the suites are chosen.
- The content-routing paragraph is removed. It was written from a reading rather
  than from a run, it sits beside the one call the next run stopped making, and
  nothing here can say it ever helped.
- The identifier paragraph in the Gerrit step is cut from ten lines to seven.
  Every load-bearing part of it survives; what goes is the restatement.
- What stays is what a run used: the two lookups and the `Change-Id`, the series
  reading, the dropped-candidate section, the three dispositions and the surface
  the review server answers. Each of those is visible in the fourth run's
  answer.

## Assumed

- That the wording is what failed rather than the placement. The sentence is the
  last one of the last paragraph of its section, and this change does not move
  it, so a fifth run that still omits the suites leaves placement as the reading
  that was not tried.
- That one dropped call is a signal rather than variance between two sessions of
  the same model.

## Wrong if

- The next run still reports its suites without naming the rest. The wording
  would then have been the wrong hypothesis twice, and what is left is where the
  sentence sits and how much stands in front of it.
- The next run stops making a call the fourth one made. That would say the
  section is over its budget whatever the sentences say, and the answer is
  subtraction rather than another rewrite.
- A run starts listing every suite the guide returned as unrun without having
  considered any of them, which is the demand satisfied as a formality.

## Covered by

- `SkillTest::aReviewNamesTheSuitesItDidNotRun`

## Confirmed on 2026-08-03

The fifth recorded `REVIEW-03` run, on the same patch and the same criteria with
the reworded skill, wrote the list out: the suites `typo3_test_run_guide`
returned and it did not run, by name, with the `Tests` row of its surface table
repeating it. Three runs before it read the old sentence and produced nothing.
Neither of the first two **Wrong if** holds, so the placement this entry kept in
reserve is not the reading that was needed.

The second one is answered better than it was asked. `typo3_hint_lookup` stayed
uncalled after the paragraph beside it was removed, which rules that paragraph
out as what displaced it — and the run's own answer says why the call is not
missing: the hints it quotes came back inside `typo3_task_guide`, domain names
included. The call is redundant on this task rather than lost, and the
assumption that one dropped call is a signal was the wrong question.

What the run leaves open is smaller and new: those hints are cited as
`typo3_hint_lookup`, which was never called. The rules are quoted correctly and
came from this server, so it is a mislabelled source rather than an invented one
— but the checklist asks a finding to name the lookup that owns its rule, and
neither the skill nor the checklist says the guide carries hints of its own.

## Since then

That last question is settled. The two payloads were run against each other on
this run's own call: the brief carries the four strongest of
`typo3_hint_lookup`'s hints for those paths, quoted statement for statement, and
the lookup answers with three more. So the citation was right and what was
missing is a sentence in the brief saying whose the hints are, which
[`D-GUI-007`](../guides/gui-007-the-brief-carries-a-selection-of-the-hints-and-says-whose-they-are.md)
adds. The reading above holds for this task and does not generalise: the four
that came back were the ones the findings rested on, and the three the call
would have added are the reason it stays in the skill.
