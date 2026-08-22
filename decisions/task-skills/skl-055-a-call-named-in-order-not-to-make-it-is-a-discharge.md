---
id: D-SKL-055
title: 'A call named in order not to make it is a discharge'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::everyDischargedCallIsWrittenAsOneAndRoutedNowhere
  - SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder
  - SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence
---

# D-SKL-055 — A call named in order not to make it is a discharge

**A tool a skill names in order not to call it is written as a discharge, and a
routing is the first mention outside one.** The construct is the tool's name,
`is discharged by`, and what answers it instead.

`everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder` asserts a routing by
finding the tool's name anywhere in the body, so the sentence telling a caller
to skip the call satisfies the assertion for it. Which of the two a mention is
cannot be read off free prose, so the disowning half is given a form and the
routing half is everything else.

## Evidence

- `73cff0ab` rewrote `typo3-development-installation`'s step to say
  `typo3_server_scope` is discharged by the base's `typo3_project_describe`
  (`D-ANS-083`), and `ROUTING_SKILLS` went on listing the tool first for that
  skill. Nothing failed. `everySkillStartsFromTheBaseBeforeItsOwnEvidence` took
  the same sentence as the skill's first routing, so it asserted that the base
  is established before a call the body says is not made.
- The mentions are not uniform enough for the opposite rule. Read over `skills/`
  on 2026-08-18, its 116 backticked tool mentions continue with `with`, `for`,
  `before`, `says`, `answers`, `owns`, `carries`, `names`, `states`, `reports`,
  `marks`, `takes` and `is what`, and stand as the object of a sentence that
  began three lines earlier. No shape separates a routing from a disowning; the
  disowning is the marked case and there is one of it.
- A count per skill does not generalise. The guard this replaces held
  `typo3-development-installation` at one mention of `typo3_server_scope`, and a
  tool a skill legitimately routes to twice is ordinary — `typo3_hint_lookup` is
  named eight times in that same body.
- The construct was already in the file. `73cff0ab` wrote "is discharged by the
  base's", and the word is this repository's own for the relation: `D-ANS-083`
  states that the step is "discharged by any `typo3_project_describe` answer".

## Decided

- `DISCHARGED_TOOLS` records which tool each skill discharges, beside the
  routings and exclusive with them.
- The routing helper in `SkillTest` is what both order assertions read a
  position from: the first mention outside a discharge, and `false` where every
  mention is one.
- `everyDischargedCallIsWrittenAsOneAndRoutedNowhere` runs over the directory
  and holds both directions — a discharge nobody recorded, and a recorded one
  nobody wrote — so the next skill to discharge a call is held without its
  author having seen the list.
- The bodies are read flat. The construct is a sentence, and a rewrap at 80
  columns moves its line break through the middle of it.
- Rejected: a vocabulary of disowning words — "skip", "already answered", "no
  need" — matched against the sentence a routing was found in. It bans the
  phrasings that have happened and lets the next one through, and it misreads a
  routing whose own sentence carries one of the words.
- Rejected: holding every mention of a tool to being either a routing or a
  discharge. The four calls the base fixes are named in bodies that do not route
  to them, deliberately, so most mentions are neither.

## Assumed

- That an author who wants to disown a call reaches for the construct rather
  than inventing a sentence. What makes that likely is that it is written down
  in the authoring contract and that there is nowhere else to record the tool:
  leaving it in `ROUTING_SKILLS` is what the routing assertion then fails on.

## Wrong if

- A skill disowns a call in prose that carries no discharge, keeps the tool in
  its routing list, and passes. The construct would then be documentation rather
  than a discriminator, and what separates the two would have to be read off the
  routing side instead — the mention written as a call, which the corpus does
  not support today.
- A second skill discharges a call and the sentence reads worse for being forced
  into these words. The construct would be costing prose to buy an assertion,
  and the assertion is worth less than one readable step.
- `DISCHARGED_TOOLS` grows past a handful of entries. A skill discharging
  several calls is a skill restating what the base already fixes, which is the
  thing the base exists to stop.
