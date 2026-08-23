---
id: D-ANS-041
title: The changelog title is read where the file names carry nothing
date: 2026-08-03
status: open
coveredBy:
  - ChangelogTest::aMethodNameOnlyTheStatedTitleSpellsReachesTheEntry
  - ChangelogTest::aMissCountsEachWordOverTheTitlesItSearched
  - ChangelogTest::theTitlesAreReadOnlyWhereTheFileNamesCarryNothing
---

# D-ANS-041 — The changelog title is read where the file names carry nothing

**The file names are what `typo3_changelog_lookup` scans, and the title stated
inside each file is what an empty answer falls back to.**

[`D-ANS-030`](ans-030-the-changelog-matcher-runs-over-the-title-it-prints.md)
settled that the field the matcher runs over has to change and left where the
read goes open, because the read is per file and the scan is per name.

## Evidence

- Measured on 2026-08-03 against `/home/benji/projects/typo3-cms`, whose core
  package ships 3795 entries, on a warm page cache. Scanning the names is 71 ms.
  Opening every one of them for its title is 103 ms on top, against the 818 ms
  `D-ANS-030` measured cold the day before. Narrowed to the 352 entries of 13 it
  is 14 ms.
- `query: "getTemporaryImageWithText"` returns
  `7.1/Deprecation-46770-LocalImageProcessorGraphicalFunctions` in 103 ms, alone
  and in one call. The same query returned nothing before this change, which is
  the miss `feedback/2026-08-01-115112` was answered on.
- 573 of the 3795 entries carry a word of four letters or more in the stated
  title that their file name does not, splitting the title on everything that is
  not a letter or a digit. `D-ANS-030` counted 708 of 3794 by its own splitting
  the day before, so the two agree on the size of the gap rather than on a
  number.
- A call the names answer is unchanged. `query: "deprecation"` matches 972
  entries in 54 ms and opens only the 20 it prints, exactly as before.
- Punctuation around the identifier does not block it. The match is containment,
  so the title `Deprecate LocalImageProcessor::getTemporaryImageWithText` is
  reached by the bare method name — the third **Wrong if** of `D-ANS-030` does
  not hold for the title.

## Decided

- **The read is the fallback and not the scan.** Where the names carry the whole
  query the answer is what it was and costs what it cost; where they carry
  nothing there is no answer for the read to slow down. Reading always was the
  alternative and it puts 818 ms cold on the call that already succeeds, which
  is the second **Wrong if** of `D-ANS-030`.
- **What that gives up is stated rather than left to be discovered.** A query
  the names answer partially never reaches the entry only a title carries: the
  caller gets the smaller answer and nothing says a larger one existed.
- **The counts and the subsets a miss prints run over the titled entries too**,
  so what the answer says a word reaches is what was searched for it.
- **The whole-changelog scan a narrowed miss makes stays on the names.** What it
  establishes is which filter emptied the answer — `D-ANS-016` — and the
  whole-file read is paid by the call the caller then makes without that filter.
- **The title is a field of the entry rather than more words in `source`.**
  `LabelSearch::haystack()` is the one place a corpus says which of its fields a
  query is matched against, so a second field is added there and not by a second
  matcher.

## Assumed

- That a caller whose query reaches something stops there rather than suspecting
  a better entry behind the one it got. This is what the fallback rests on and
  nothing measured here says how often it holds.
- That the stated title carries the identifier a caller types. It does for
  `Deprecation-46770`, which is one entry; an identifier a title abbreviates is
  the same miss one field further in.
- That 818 ms cold on a miss is affordable. It is paid by a call that would
  otherwise return nothing, and the caller's alternative is another call.

## Wrong if

- A feedback reports an entry unreached whose title carries the query, because
  the names reached a different entry first. That is the case this shape gives
  up, and reading always is what answers it.
- A miss becomes the slow call: an unfiltered one opens every file in the
  changelog before saying that nothing matched, and a caller narrowing by
  version pays it once per version it tries.
- A query reaching by title returns entries about another subject, because a
  title names what a change replaces as well as what it deprecates.
- The names stop being the cheap field, because a checkout ships enough versions
  that one scan of them is no longer 71 ms.
