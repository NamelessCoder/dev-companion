---
id: D-DOC-052
title: A revoked entry names no test
date: 2026-08-23
status: open
restsOn: [D-DOC-048]
coveredBy:
  - DecisionsTest::aRevokedEntryNamesNoTest
---

# D-DOC-052 — A revoked entry names no test

**A test declaring a revoked decision fails `bin/cli decisions:check`, and the
attribute belongs on the entry that revoked it.**

A revoked statement no longer describes this server, so a test declaring it
claims to hold something the repository says it stopped doing.

## Evidence

- Read on 2026-08-23. Of 39 revoked decisions, 11 were named by a test, and 9 of
  those named a test the successor already declares. The attribute on the dead
  entry said nothing the live one was not already saying.
- One of them said the opposite. `D-KNW-003` kept `provenance` apart from
  `binding` and was revoked by `D-KNW-005`, which made the four vocabularies one
  enum — and `KnowledgeTest::everyScopeInTheCorpusIsOneTheEnumDeclares` declared
  both. The test disproves the entry it was declared on.
- Two named a test about something else entirely: a drawing's type size on an
  entry about the design system, an exclusion check on an entry about API
  stability. Neither would have caught anything the entry decided.
- The listing already separates them. A revoked entry sits in a run of its own
  under `Revoked, and kept as the record`, because mixed into the rest it looked
  like something to build on — and a `coveredBy` beside it reads the same way.

## Decided

- `bin/cli decisions:check` fails, naming `revokedBy` where the entry has one,
  so the reader is told where the attribute goes rather than only that it is
  wrong.
- The 16 attributes standing on the 11 entries were removed rather than moved.
  Nine were already on the successor; the other three held a claim about
  something else, and moving one of those would be the mislabelling
  `D-DOC-043`'s first **Assumed** describes.
- Decisions only. A requirement has no revoked state — one that is withdrawn is
  deleted, which takes its `heldBy` with it — so there is nothing for the same
  rule to hold there.

## Assumed

- That a test which held a revoked entry holds its successor, where one exists.
  Nine of eleven did already; nothing measures whether the two are the same
  claim.

## Wrong if

- A revocation drops the last guard on behaviour that is still there, because
  the successor never declared what the dead entry had. The check names
  `revokedBy` for exactly that moment, and nothing forces the move.
- An entry is left `open` to keep its tests. That would be visible as a
  statement nobody can read as true beside a passing test, and it is the reason
  `D-DOC-046` asks a title to say what is.
