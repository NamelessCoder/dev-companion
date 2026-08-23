---
id: D-ANS-074
title: 'A path-narrowed suite list names what it withheld'
date: 2026-08-11
status: open
---

# D-ANS-074 — A path-narrowed suite list names what it withheld

**A suite list narrowed by paths says which domains no path reached, and that a
path in one of them means asking again.**

The answer named the two domains it kept and offered the whole list to anybody
who wanted it. It said nothing about the path set changing, and a rework that
grew a TypeScript file mid-session went to `runTests.sh -h` and a grep for a
suite name this server holds.

## Evidence

- Re-run on 2026-08-11 with the eight paths `feedback/2026-08-10-182435` reports
  and `targetVersion=15.0`: domains `css` and `fluid`, and the same ten suites
  in the same order the feedback quotes. The feedback describes the server as it
  is now.
- What that answer says about its own scope is one sentence: "Narrowed to the
  css and fluid domain(s) the given paths touch. Suites outside them cannot fail
  on this change; call again without paths to see all of them." It names what it
  kept, and its escape hatch is for a caller who wants everything rather than
  for one whose paths have moved.
- The narrowing withheld 17 of the 27 suites that branch offers, in five domains
  no path reached: `php`, `typescript`, `typoscript`, `docs` and `xliff`.
  `lintTypescript` and `unitJavascript`, the two the session went looking for,
  are among them.
- The cost was two round trips against the checkout for a fact in
  `knowledge/hints/testing.json`, and the session reports that nothing prompted
  the re-call.
- [`R-ANS-030`](../../requirements/answers/ans-030-a-bound-on-an-answer-is-asked-for-and-never-applied-by-default.md)
  already states the rule on another payload: a bound is asked for, and what was
  left out is counted in the answer either way. The `paths` argument is the ask;
  the count is the half this answer does not carry.

## Decided

- Step 4 of the ladder, wording. The suites, their domains and their version
  binding are all in `knowledge/hints/testing.json` and the tool reached exactly
  the right ten — nothing is missing but the sentence that says what the ten are
  a selection of.
- The omission is named by domain and counted, never enumerated. Seventeen suite
  names is the answer the narrowing exists to avoid; five domain names and a
  number is one line.
- The condition sits where the narrowing does. A caller that reads the tool
  description once and then holds an answer for the rest of a session is the
  case being answered, so the answer is what has to carry it.
- Queued rather than closed on the spot. The block is built in
  `src/Tool/TestRunGuide.php` and the counterpart in `outputSchema()` is a
  contract, which [judging.md](../../documentation/records/judging.rst) puts on
  the reviewed side of the line.
- Priority `normal`, set here: one session and one report, against a change that
  needs nothing established first because `R-ANS-030` already carries the rule.
  What keeps it off `high` is that the answer was correct and the session paid
  two greps rather than shipping something wrong.

## Assumed

- One session. No other feedback in the corpus reports an answer going stale
  under a growing input set, and `bin/cli feedback:list` on 2026-08-11 holds 13
  open, all from one checkout.
- That naming the withheld domains is what prompts the re-call. It is the lever
  `R-ANS-030` rests on — a count is what makes a silence readable — and no
  recorded run has measured a session acting on one.

## Wrong if

- A session grows its path set, reads the withheld line, and still does not call
  again. Then the answer is not where that habit is formed, and the tool
  description or the patch skill is.
- The withheld line is reported as noise, which it would be on a call whose
  paths already reach every domain — then it belongs only where something was
  actually withheld.
- A session reads it as an offer and calls again with the path set unchanged,
  which would say the sentence reads as the escape hatch beside it rather than
  as a condition.

## Since then

The sentence is built and is in the answer. Called on 2026-08-23 with one
TypeScript path at `targetVersion: 15.0`, `typo3_test_run_guide` says: "Narrowed
to the typescript domain(s) the given paths touch. … No given path reached php,
fluid, typoscript, xliff, docs and css, which leaves 20 suites out. A path
landing in one of those domains means calling again." Named by domain and
counted, never enumerated, and `withheld` carries the same two fields in the
data half.

All three **Wrong if** wait on a session and none has reported. No feedback
since 2026-08-11 describes a path set growing mid-session, reads the withheld
line as noise, or calls again with the paths unchanged — so what the sentence
does to a session's habit is still unmeasured, which the **Assumed** above
already said nothing had measured.
