---
id: D-SKL-011
title: 'The call plan a skill writes down is measured'
date: 2026-08-03
status: revoked
revokedBy: D-SKL-043
coveredBy:
  - SkillTest::aRuleQueryCarriesTwoSubjectsAndAThirdIsACallOfItsOwn
---

# D-SKL-011 — The call plan a skill writes down is measured

**A skill that tells a caller how many calls a subject takes states what the
corpus was measured to do, and obligations that share a document are one call.**

`typo3-core-patch-review` told a reviewer to ask `typo3_rule_lookup` per
obligation, on the grounds that *a query that names two reaches neither*. That
is a claim about the ranker written into a file the ranker does not touch, and
the ranker moved under it on the day it was followed.

## Evidence

- `feedback/2026-08-03-144324` reports the cost from the REVIEW-03 run of
  2026-08-03: two calls, `changelog entry` then `breaking change`, the second
  returning roughly 80% of the first. `scenarios/runs/REVIEW-03.json` records
  both in its `toolTrace`, in that order, so the split was the skill's and not
  the session's invention.
- The report reproduces. Re-run on 2026-08-03 through `bin/typo3-dev-companion`
  from this worktree: `changelog entry` returns `## Breaking Changes`,
  `## Changelog Files`, `## Core Contribution Guide` and `## Common Commands`;
  `breaking change` returns `## Breaking Changes`, `## Changelog Files`,
  `## Review Readiness` and `## Summary Line`. The first two are the same
  sections at 100% of the query terms in both answers.
- The advice is what cost the round trip. `breaking change changelog entry`, one
  call, returns all five sections the two calls between them returned that are
  about an obligation — both shared sections at 100%, and
  `## Core Contribution Guide`, `## Review Readiness` and `## Summary Line` at
  53%. So the sentence is false for the pair it was followed on: a query naming
  two reaches both, and reaches what the sibling query would have added.
- It is not simply inverted, and the limit is length.
  `breaking change deprecation changelog entry review readiness` returns three
  sections and drops `## Deprecations`, which `deprecation` alone returns first
  at 100%. `## Summary Line` goes with it. Four subjects dilute the share each
  section covers below `Documents::MIN_COVERAGE`, which is
  [`D-ANS-037`](../answers/ans-037-a-compound-rule-query-is-owed-the-section-its-score-prefers.md)'s
  mechanism reached from the query's side rather than the document's.
- The ground moved the same day the feedback was written. `D-ANS-037`'s **Since
  then** put the document title into `Documents::FIELD_WEIGHTS` at weight 2 on
  2026-08-03, and gave a compound miss the subsets to re-ask with. A skill
  sentence about what a compound query reaches is therefore a statement about
  `src/` that no test under `skills/` can hold true.
- The claim lived in one place — `skills/typo3-core-patch-review/SKILL.md` — and
  nothing established it. No decision states it, no test asserted it, and
  `knowledge/server-scope.json`'s routing line says only *`typo3_rule_lookup`
  per obligation the diff raises*, which names no ranking behaviour and is left
  alone.
- The session's own conclusion was the measurement's: *if I ran this review
  again I would make one call for the pair*.

## Decided

- **Step 4, wording, closed on the spot.** The rule was delivered, was followed
  and was wrong about the corpus. Nothing about TYPO3 had to be looked up, and
  the skill's contract — its `description`, the ownership boundary it closes on
  — is untouched, so the rewrite lands in the commit that judges it.
- The rewrite says what was measured rather than what was assumed: the sections
  are named by subject, obligations sharing a document are one call, and length
  rather than count is what drops a section. `SkillTest` holds it, so a
  reorganisation cannot take it out silently.
- **The session-level suppression the feedback offers is not built.** It would
  make `typo3_rule_lookup` remember a caller between calls, and
  [`D-FBK-020`](../feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md)
  says what a session is charged: the round trip. Returning *already returned
  for query X* keeps the round trip and saves the tokens, which is the half that
  was not the cost.
- **Naming the sibling sections in the answer is not built either.** One call
  already returns them, and the answer already carries the hint footer that
  points sideways. What the feedback asked the tool to compensate for was the
  advice, and the advice is what changed.

## Assumed

- That the pair generalises to the obligation cluster of
  `typo3-commit-messages`. It was measured on breaking, changelog, deprecation
  and review readiness, and no sweep put every pair of that document through the
  search.
- That no other skill carries the same assertion. `per obligation` reaches only
  this skill and the routing line above, and neither
  `typo3-core-patch-development` nor `base.md` states a call count for a rule
  query.
- That a reviewer reads *length is the limit* as a limit. Nothing measures
  whether the rewrite changes how many calls the next REVIEW-03 run makes.

## Wrong if

- A later run asks `breaking change changelog entry` in one call and gets one of
  the two sections. Then the floor moved again and the pair is no longer one
  call — which is `D-ANS-037`'s first **Wrong if** arriving here.
- A review follows the rewrite, asks four obligations in one query and reports
  the deprecation rules missing. Then *length is the limit* was read as
  permission rather than as a bound, and the sentence needs the count back.
- A sweep of the prose corpus finds the shared-document pair is peculiar to
  `typo3-commit-messages`, because its sections are short and its headings carry
  the subject words. Then this is one document's property written as a rule.
- The next skill sentence that states a retrieval behaviour is found stale the
  same way. Then the lesson is not this wording but that a skill may not state
  one at all, and what belongs in the file is the subject to ask in.

## Revoked on 2026-08-14

By its own second **Wrong if**. A review of Gerrit change 93319 followed the
rewrite on 2026-08-13, asked `changelog entry testing review readiness` in one
call and got nothing back — *length is the limit rather than the count* read as
permission, which is the sentence this entry put in place of a count.

The statement above is what is revoked, and the clause is the second one:
*obligations that share a document are one call* is unbounded, and a sweep of
every triple of headings inside one document empties 34 of 351. The first clause
holds and is carried into the successor, which states a count of two and what it
was measured over —
[`D-SKL-043`](skl-043-a-rule-query-carries-two-subjects.md).

The evidence above stays. The pair it was measured on still answers in one call,
the round trip it removed was real, and the two proposals it rejected — a
session-level suppression and naming the sibling sections in the answer — were
not what this feedback asked for either.

