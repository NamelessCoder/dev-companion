---
id: D-SKL-039
title: A brief that changes nothing routes only the workflows that change nothing
date: 2026-08-14
status: open
coveredBy:
  - HintsTest::aReviewOfAChangeRoutesTheReviewAndNotTheWorkflowThatWritesIt
  - HintsTest::aBriefNamesTheSkillThatOwnsTheWork
---

# D-SKL-039 — A brief that changes nothing routes only the workflows that change nothing

**Where `typo3_task_guide` answers a review, a triage or a boot, it names only
the task skills whose own work writes no change.**

A review request names the change it is about, and the words of that change are
the words of writing one. `breaking` is an intent with a skill behind it, so
"review core patch 95169 and say whether it is breaking" was routed into the
workflow for authoring a breaking change.

## Evidence

- **Measured on 2026-08-14, before the change.** "Review core patch 95169 and
  say whether it is breaking" matches `breaking` strongly and nothing else, and
  names `typo3-core-patch-development`. The German brief of the same run —
  "bitte review mir 95169 … und sag mir ob der breaking ist" — matches
  `breaking` strongly and `patch-checkout` weakly. Neither reaches
  `typo3-core-patch-review`, which only the `audit` intent carries.
- **It takes both repairs, and neither alone is enough.** Withholding the route
  needs the review to have been recognized, and `audit`'s needles were "review
  the", "review this", "review of" and "reviewing" — a request naming its change
  by number arrives in none of them. Adding the shapes alone leaves both skills
  named, `breaking` first, which is the state `D-SKL-013`'s **Since then**
  already ruled out for the `tests` intent: an assertion that the right name is
  among them holds just as well while a whole workflow the task has nothing to
  do with is loaded first.
- **The checklist is the counter-example to withholding more than the route.**
  The `breaking` intent's first item — "Settle first that the change is breaking
  at all: `@internal` says the API is not public and does not decide it" — is
  the answer to what that brief asked. Dropping the intent would take the answer
  out with the route.
- **The three negatives are this repository's own prompts.** `CORE-03`'s "Review
  says my commit message is wrong", `SKILL-13`'s "Pull down that patch from
  review" and `CORE-07`'s "pushing it for review" match none of the added
  shapes, and a bare `review` needle would have matched all three — the matcher
  ends a needle at a non-letter, so it reaches `review.typo3.org` as well.

## Decided

- **The property is the intent's, in data.** `changesNothing` on an entry in
  `knowledge/task-intents.json`, true for `audit`, `triage`, `patch-checkout`
  and `installation-operations` — the four whose work reads a change, a report
  or an installation rather than writing one. Marking those four is the shorter
  list, and it is the same fact `TaskGuide` already forks its skeleton on.
- **Only the route is withheld.** The intent stays recognized, its title stays
  in `Recognized as:` and its checklist items stay in the brief. A skill is a
  workflow the caller enters and a checklist item is a statement they read, so
  what the intent knows about a breaking change still reaches the reviewer while
  the workflow for making one does not.
- **A stated `changeType` keeps its route**, because it keeps the skeleton. That
  is `D-GUI-009`: "review the patch that deprecates X" with
  `changeType="deprecation"` is authoring work described from the reviewer's
  side, and the fork here is the one that decision already draws.
- **`audit` gains three shapes and not the word.** `review patch`,
  `review core patch` and `review change` are how a request naming its change
  arrives; `review` on its own is what the three negatives above rule out.
- **The German brief is left where the corpus leaves it.** Everything below
  `knowledge/` is English and every free-text parameter says so, so what matched
  there were the two loanwords in the sentence. It routes nothing now rather
  than the wrong thing, and being recognized as a review is what translating it
  buys.

## Assumed

- **That an adjective the shapes do not carry is rare enough.**
  `review core patch` is in the list because it was measured;
  `review open patch` would miss, and the needle mechanism has no way to state
  "review, then a change noun".
- **That a reviewer reads an authoring checklist item as an obligation to
  check.** The `audit` intent already states the same obligations in the
  reviewer's voice, so the brief carries both, and no run has been read for
  whether the author-voiced copy costs anything.

## Wrong if

- A session hands back a changelog file, a scanner matcher or a `[!!!]` prefixed
  commit for a task that asked only whether a change is breaking. Then the
  checklist is doing what the route was doing, and withholding the intent
  outright is the next step.
- A review request that names a change is still routed to a workflow that writes
  one, in a shape the three needles do not carry. Then the shapes are an
  enumeration that will not close, and what is needed is a rule about the
  sentence rather than more needles.
- An author's brief loses its route because the words also read as a review.
  `remove the public method and make it a breaking change` still names
  `typo3-core-patch-development`; a task that stops doing so is this decision
  taking the route from the caller who was in the workflow.
