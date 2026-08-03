---
id: R-ANS-006
status: held
---

# R-ANS-006 — A miss says what there would have been to find

**A lookup that returns nothing says what there would have been to find, and
what it names can be asked for outright.**

`typo3_hint_lookup` lists the hint ids of the searched domains on every miss
and accepts one as `id`, so "your words did not match" is distinguishable from
"nobody wrote this down" without trying another phrasing.

## From

A query naming XLF, labels and language files returning the TCA hint and
nothing else, with no way to see that a Language Files hint existed
(2026-07-29).

## Held by

- `HintsTest::aMissNamesWhatThereWouldHaveBeenToFind`
- `HintsTest::aHintCanBeAskedForByItsIdInsteadOfGuessedAt`
- `HintsTest::anIdThatDoesNotExistIsAnsweredWithTheOnesThatDo`
- `LabelSearchTest::anEmptyResultNamesTheLargestPartOfTheQueryThatDoesReach`
- `PackageSourcesTest::aMissNamesTheLargestPartOfTheQueryThatWouldHaveHit`
- `PackageSourcesTest::whereNoTwoWordsMeetInOneEntryThePerWordReachIsWhatToAskWith`
