---
id: D-FBK-021
title: A summary feedback is judged against its series, not on its own
date: 2026-08-02
status: open
---

# D-FBK-021 — A summary feedback is judged against its series, not on its own

**A feedback that summarises a session which filed one report per subject is
judged by mapping its halves onto those siblings, not walked down the ladder
again.**

Such a summary carries no lever of its own. Every subject in it is a subject
somewhere else too, so judging it as a report produces what the ladder's
preamble names: a gap with a fourth entry written next to three that exist.

## Evidence

- `feedback/2026-08-01-002951` is the first feedback of the series
  [`D-FBK-006`](fbk-006-a-name-is-cut-where-the-feedback-starts-to-differ.md)
  was written from — the one that kept the shared opening. Its **Suggestion**
  names four things to cover, and each is the whole subject of a named sibling
  filed within six minutes of it:

  | Half of the summary                        | The sibling that owns it                            |
  | ------------------------------------------ | --------------------------------------------------- |
  | `f:if` needs an explicit `f:then`          | `003448`, with the working markup; `003000`         |
  | preview data is the record, not TypoScript | `002926`, `002928`, `002930`, `002745`              |
  | Record API field access                    | `002928`, whose query is three Record API phrasings |
  | test request type and dataset priming      | `003003`; `003929` for the database half            |

- Two of those four are already judged, and by another card than this one. The
  record variable is
  [`D-KNW-014`](../knowledge/knw-014-the-record-a-v14-preview-template-is-handed-is-a-subject-this-server-owns.md),
  step 1a, with `todo/progress/2026-08-02-133246` serving `002745`. The Fluid
  half and the functional-test half are unjudged, with their own cards in
  `todo/open/`.
- A series produces summaries in the plural. `003103` is a second one of the
  same session — its query is "whole-session roundtrip audit: backend preview,
  Record API, Functional test, Fluid" — open, with a card of its own.
- The summary's query reaches less than its halves do. `bin/cli hints:probe` on
  the four joined by semicolons reaches `project-extension-tests` and
  `core-tests`, and nothing else. Asked one at a time the same four reach
  `fluid-templates`, `content-elements`, `frontend-records` with
  `tca-schema-api`, and `project-extension-tests` — so the joined query misses
  three of the four hints its own parts find. That is
  [`D-ANS-021`](../answers/ans-021-the-manual-lookup-says-why-a-short-query-ranks-better.md)
  measured on a hint probe rather than on the manual.
- Nothing about TYPO3 was established here, deliberately. Whether an `f:else`
  really forces an explicit `f:then`, and what `project-extension-tests` already
  says about priming, is the reading `003448` and `003003` owe.

## Decided

- The judgement of a summary is the mapping above, and it is written here so it
  is not derived a second time. What getting it wrong costs is one knowledge
  entry written twice, from two judgements of one session, with no reader left
  who could tell which reading either rests on.
- No todo is derived from this feedback. Every step it would name is already a
  step on a sibling's card, and a second card for the same step is the overlap
  `bin/cli todo:claim` was taught to warn about.
- Nothing on another branch was touched. `002930`, `003000` and the
  record-variable todo are in hand in other worktrees as this is written, so
  adding this feedback to their `Serves:` lines would edit one file in two
  worktrees at once.
- Whether the summary may be archived now is **not** decided here. It is the
  question on the card, and the card stays in `waiting/` serving the feedback,
  which keeps
  [`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)'s
  invariant either way the question is answered.

## Assumed

- That an agent reads its summary being closed differently from its specific
  reports being closed. Nothing has measured it. `D-FBK-017` assumes the
  opposite reading for the ordinary case, which is why the archiving is a
  question here rather than an answer.
- That the mapping is complete, and no half of the summary is orphaned. It was
  read off the four clauses of the **Suggestion** against the `Query` line of
  every feedback that session filed.

## Wrong if

- ~~A half turns out to have no sibling. The mapping is then wrong, the summary
  is the only report of that subject, and the ladder is owed after all.~~ Fired
  from the fourth summary on, and the mapping was right each time. An orphaned
  half is what the summary adds, and the ladder was walked over that row rather
  than over the file.
- ~~Every sibling lands and the summary is still open, because each closing
  commit archives only the feedback it worked off. The summary would then need
  an owner rather than a mapping.~~ Fired on 2026-08-03 on the first three
  summaries, and what they needed was a reading of where their siblings had
  landed. The ninth was closed by the mapping itself.
- A session judges a summary by walking the ladder anyway, and writes the entry
  that a sibling's todo writes again a week later. That is the failure this is
  written against, and nothing checks for it.

## Since then

Ten readings, compacted here on 2026-08-28 with the form (`D-DOC-066`). Every
one of them decomposed a summary feedback into findings that landed in the
entries they were about — `D-FBK-017`, `D-FBK-020`, `D-KNW-100` and a dozen more
name what each changed — which is the rule holding rather than a series of its
own. The reports are in `feedback/archive/`, where the account of one session
belongs.
