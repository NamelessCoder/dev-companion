---
id: D-DOC-010
title: '`targetVersion` opens with one sentence and diverges after it'
date: 2026-08-03
status: confirmed
---

# D-DOC-010 — `targetVersion` opens with one sentence and diverges after it

**`targetVersion` opens with the same sentence in every tool that takes it, and
says what narrowing does in that tool's own words after it.**

Six tools take the parameter and stated it in six wordings. Five of them were
saying the same two things in different sentences; the sixth difference is real,
because what one tool does when the version is left out is not what another
does.

## Evidence

- The six descriptions are 2135 characters of the 11027 the input schemas carry,
  measured 2026-08-03. All six already opened with the shape
  `The TYPO3 version <what it is for>, for example "13.4" or "14".`
- The second halves are not interchangeable, which is why they were not merged:
  `typo3_component_lookup` withholds a component not verified on that version,
  `typo3_reference_list` leaves an example out rather than qualifying it,
  `typo3_hint_lookup` falls back to every major the repository declares
  `typo3/cms-core` for, and `typo3_translation_domain_lookup` answers with the
  `LLL:EXT:` reference instead of a domain. A caller that read one of those as
  the others would narrow and be handed a filter it did not ask for.
- `typo3_hint_lookup` and `typo3_task_guide` had drifted furthest apart at 511
  and 398 characters for a difference of one clause. They are 434 and 330 now,
  and the clause that differs is the whole of what differs.

## Decided

- The opening sentence is the shared half and is written the same way each time.
  A caller comparing two tools sees the difference where there is one.
- No shared helper. `Result\Schema` holds the shapes an answer is built from,
  and an input parameter is not one; a helper taking six different second halves
  is the six texts with an indirection in front of them.
- The repetition is paid in the payload rather than removed. Every tool ships
  its own schema, so one sentence written once would still travel six times.

## Assumed

- A caller reads one of the six at a time. The duplication then costs tokens and
  not confusion, which is what makes leaving it standing the cheaper side.

## Wrong if

- A seventh tool takes `targetVersion` and copies the second half of the wrong
  one, which no check would see: both texts are grammatical, both describe a
  real behaviour, and only the tool's own code says which.
- A feedback reports a caller that narrowed one tool expecting another's
  fallback. That would mean the divergence is not readable from the sentence
  meant to state it, and the wording is doing less than the payload it costs.

## Confirmed on 2026-08-23

The parameter has grown from six tools to ten and neither **Wrong if** fired:
nine open on the same sentence and then say their own fallback. The tenth is the
difference the **Evidence** predicted rather than drift — the one tool where
`targetVersion` is required opens on the manual that must answer, because a
sentence about being left out would describe a call it refuses. What the second
halves divide on is the source rather than the tool, which is `D-VER-004`'s
split. No feedback reports the second **Wrong if**.
