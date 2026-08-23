---
id: D-ANS-007
title: '`unsupported` and `unavailable` are two answers, and `cause` says why'
date: 2026-08-02
status: confirmed
coveredBy:
  - DocumentationTest::aVersionOutsideTheCoveredOnesIsNotAskedFor
---

# D-ANS-007 — `unsupported` and `unavailable` are two answers, and `cause` says why

**`typo3_documentation_lookup` keeps `status`, and its `unavailable` object
carries a `cause` — the same word the installation answer uses, with values of
its own.**

Two tools said "this source could not be asked" in two vocabularies:
`unsupported` with a `cause` on the installation-backed eight, `status` with
`answered`, `empty` and `unavailable` on the manual. `D-KNW-005` settled four
spellings of one idea into `scope`, and the question was whether this is the
same case.

## Evidence

- It is not the same idea. `unsupported` means the question cannot be answered
  from where this server is standing, and every field it requires says how to
  change that: where discovery walked, which variables name an installation and
  its console. The manual is reachable from anywhere or from nowhere, and none
  of those fields has a value there — merging would either fake them or loosen
  the required list the eight promise.
- The remedies are opposite. `unsupported` is answered by naming a root; a
  manual that did not respond is answered by asking again.
- What was actually wrong is inside the manual's own field. `unavailable`
  covered both a release outside the covered versions — permanent, and asking
  again changes nothing — and docs.typo3.org not responding, which is the
  transient one. That is the distinction `D-ANS-005` introduced `cause` for on
  the other side, where prose carried it and only prose did.

## Decided

- Two shapes, and the line is what the caller can do about it: `unsupported`
  where the question is not supported here at all, `status: unavailable` where
  the source it would have been answered from did not answer.
- One word for why. `cause` is `version-not-covered` or `source-not-answering`
  on the manual, and `no-installation`, `misconfigured` or
  `installation-not-answering` on an installation — one key, disjoint values,
  each naming a remedy.
- `status` stays a discriminator rather than becoming a `oneOf`. The manual's
  three states are states of the answer, and `empty` is a result: the caller
  rephrases. On the installation side the same case is a count of zero, which is
  why the shape there splits in two and this one does not.

## Assumed

- A client that has learned `cause` on one tool reads it on the other without
  learning a second vocabulary, which is the whole of what the two shapes now
  share. If that turns out not to be enough, the next step is the schema, not
  the prose.

## Wrong if

- A caller retries a `version-not-covered` answer, or gives up on a
  `source-not-answering` one. Both would mean the value is not reaching the
  behaviour, and the fix is to say the remedy in the reason rather than to add a
  third field.
- A third source arrives that is neither an installation nor a manual and fits
  neither shape. Then the split is between "here" and "elsewhere" rather than
  between the two sources this server has.

## Confirmed on 2026-08-22

The third source arrived and fits, which is the second **Wrong if** read the
right way round. `Source` names five kinds today — installation, packages,
knowledge, network and checkout — and the two that were not here when this was
written answer in the shape the line predicts: the TER lookup, the Forge and the
Gerrit reads are reachable from anywhere or from nowhere, so they carry `status`
with `answered`, `empty` and `unavailable`, and an `unavailable` object with a
`cause`. None of them took `unsupported`, which stays what a question this
server cannot be asked from where it stands answers with.

The vocabulary grew by one value rather than by a second key.
`source-not-parseable` stands beside `source-not-answering` — a host that
answered with something that is not the document — and it is a remedy of its own
in the same word, which is what the **Decided** asked of a new value.

The first **Wrong if** is unreported. Nothing in the archive says a caller
retried a `version-not-covered` answer or gave up on a `source-not-answering`
one, and what would show it is a session's own account of what it did next.
