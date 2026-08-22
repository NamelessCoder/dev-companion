---
id: R-ANS-024
title: 'A field that is answered empty is one nothing could fill'
status: held
restsOn: [D-ANS-056]
---

# R-ANS-024 — A field that is answered empty is one nothing could fill

**A record answers a field empty because the source did not carry it, never
because the call that would have carried it was not made.**

An empty key is read as a fact about the thing: no area, nobody assigned,
nothing has moved since it was filed. Where the value exists one call away, the
answer is a false statement rather than a short one, and the caller has nothing
in front of it that says which of the two it is holding.

## From

Two searches returned 50 rows with `category`, `assignedTo`, `createdOn` and
`updatedOn` empty in every one, on issues that all have an area and two dates
(`feedback/2026-08-05-033902`). The reporting session had already given up on
that path for age; one that trusted the fields would have concluded the backlog
was uncategorised and untouched.

## Held by

- `ForgeTest::aSearchHitIsFilledFromTheIssuesTheHitsAre`
- `ForgeTest::aPageThatCouldNotBeFilledIsStillTheHitsThatMatched`
- The other half is not guarded: that every other lookup filling a record from
  one source names no field a second source holds. Nothing reads the answer
  shapes against the endpoints behind them.
