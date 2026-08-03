---
id: D-ANS-030
date: 2026-08-02
status: open
---

# D-ANS-030 — The changelog matcher runs over the title it prints

**`typo3_changelog_lookup` matches the entry's file name and the words that name
spells, while the title the same answer prints is searched by nothing, and the
body is not either.**

The `query` field says "matched against its title" and the answer lists that
title back. One entry is titled *Deprecate
LocalImageProcessor::getTemporaryImageWithText* and cannot be reached by that
method name.

## Evidence

- `feedback/2026-08-01-115112` re-run on 2026-08-02 from
  `/home/benji/projects/typo3-cms`, against the 3794 entries that checkout ships
  in `Documentation/Changelog/7.5` through `15.0`.
- The reported cause is disproved.
  `query: "GifBuilder placeholder preview thumbnail"` with `version: "15"`
  returns nothing and reports that "preview" reaches 1 entry, which is the count
  inside `15.0`. Drop the version and every word reaches: gifbuilder 4,
  placeholder 10, preview 28, thumbnail 9.
- The entry the session wanted is in `13.0`, not in 15.
  `query: "image generation"` with no version returns
  `13.0 Breaking: Removed public methods related to Image Generation (#101955)`,
  alone, in one call. So the title is what would have reached it, and the
  version filter is what emptied the answer.
- What the re-run does find is the reverse of the report.
  `7.1/Deprecation-46770-LocalImageProcessorGraphicalFunctions` is titled
  *Deprecate LocalImageProcessor::getTemporaryImageWithText* inside the file,
  and the tool prints that title; `query: "getTemporaryImageWithText"` reaches
  nothing. `Changelog::entries()` matches `key` and `source`, both derived from
  the file name, while `Changelog::read()` opens the file for the title, the
  removal and the tags and hands none of it to the matcher.
- 708 of the 3794 entries carry a word of four letters or more in their in-file
  title that the file name does not. `15.0/Breaking-110196` is titled *PHP class
  Rfc822AddressesParser removed* against `Rfc822AddressesParserRemoved`, and
  `14.3/Deprecation-109517` spells the full
  `TYPO3\CMS\Setup\Event\AddJavaScriptModulesEvent` where its name says
  `AddJavaScriptModulesEvent`.
- The body carries more again. `getTemporaryImageWithText` is in the body of
  three entries — `13.0/Breaking-101955`, `8.0/Breaking-72426` and
  `7.1/Deprecation-46770` — in the title of one of them, and in no file name at
  all. Across the corpus there are 10842 distinct `:php:` roles in 1951 of the
  3794 entries.
- What each field costs, measured in the same run. Scanning the names of all
  3794 entries is 48 ms. Opening every one of them is 818 ms cold and 45 ms
  against a warm page cache, which is the read `D-ANS-006` priced at 600 ms for
  the tag filter. Narrowed it is small: 4 ms for the 352 entries of 13, 1 ms for
  the 75 deprecations of 14. Extracting the `:php:` roles is 30 ms on top of a
  warm read.

## Decided

- **Step 1b of the ladder, and not step 4.** No rewrite of the schema makes
  `getTemporaryImageWithText` reach the entry whose printed title carries it.
  The field the matcher runs over has to change, so the shape is what is missing
  rather than the wording.
- **Queued rather than closed on the spot.** Both halves are `src/`, and the
  `query` description is a declared schema, which
  [judging.md](../../documentation/feedback/judging.md) puts on the far side of
  the autonomous line.
- **The suggestion is taken on its subject and rejected on its diagnosis.** The
  report asks for the `:php:` names and the Removed-lists to be indexed because
  matching is "lexical against titles only". Matching against titles is what it
  is not, and the smaller half of the same gap sits one rung below the
  suggestion: the title is already opened and read for every entry the answer
  returns.
- **The title half does not answer this feedback on its own.** It reaches
  `Deprecation-46770`, which names the method and points at
  `GraphicalFunctions`; the entry the session was actually after,
  `Breaking-101955`, carries the method in a Removed-list and nowhere else. Two
  todos, because the reads and the failure modes differ.
- Recorded here rather than against `D-ANS-006`. That entry decided how a term
  is spelled and left the matcher right, and this is about which field the
  matcher is given — a question no entry states.
- The version-filter half of the same re-run is recorded against `D-ANS-016`,
  where the miss's wording already stands.

## Assumed

- That a caller after a removed method types the method name. The feedback says
  so outright in its own suggestion, and it is still one session.
- That the read stays affordable as the changelog grows. It is 3794 entries here
  against the 3766 `D-ANS-016` measured in `.checkouts/14.3`, and the cost is
  per file rather than per entry matched, so an unfiltered miss pays it whole.
- That the `:php:` roles are worth indexing rather than only the Removed-lists
  the report names. 10842 distinct roles is a wide index, and nothing measured
  here says how much of it is a class an entry merely mentions.

## Wrong if

- A query naming one identifier comes back with entries about another, because
  the body names every class the change touches. `Breaking-101955` writes
  `GraphicalFunctions` 44 times while being titled about image generation, so
  that query would gain an entry whose subject is a different class.
- The read shows up as latency where the names answer today. The unfiltered miss
  above costs 88 ms off the names alone and would carry the whole-file read on
  top of it.
- The title lands and a later feedback reports the identifier still unreached,
  because the file spells it `->getTemporaryImageWithText()` and the caller
  types it bare.
- A caller narrows by version, gets nothing, and reads the silence as the
  answer, exactly as this session did. Then the field the matcher reads was not
  what cost this feedback its call, and `D-ANS-016` carries the whole of it.

## Since then

### The title half

The title half is in the code. `Changelog::titled()` reads the stated title into
a field of the entry, `LabelSearch` matches it beside the file name and the
words that name spells, and `query: "getTemporaryImageWithText"` now returns
`7.1/Deprecation-46770-LocalImageProcessorGraphicalFunctions`. Where the read
goes was the open half of this entry and is settled in
[`D-ANS-041`](ans-041-the-changelog-title-is-read-where-the-file-names-carry-nothing.md):
the names are scanned on every call and the titles are opened only where those
names carry nothing, so a call that already answers costs what it did. The
**Wrong if** about a spelling the caller does not type does not hold for the
title: the match is containment, so
`LocalImageProcessor::getTemporaryImageWithText` is reached by the bare method
name. The ones about a query reaching another class and about the read showing
up as latency are open, and `D-ANS-041` restates both against the read as it now
stands. Nothing here was read about the body half.

### The body half

The body half is answered, by
[`D-ANS-042`](ans-042-an-identifier-reaches-the-changelog-entries-whose-body-names-it.md):
an identifier a body writes reaches that entry, where the file names carried
nothing of the query. `getTemporaryImageWithText` returns
`13.0/Breaking-101955`, `8.0/Breaking-72426` and `7.1/Deprecation-46770`, so the
sentence above saying the body is searched by nothing describes what this entry
was written against rather than what the tool does.

The first **Wrong if** above did not happen and cannot, in the shape it was
written in. `GraphicalFunctions` is answered by the six entries named after it
and never by the eighteen whose bodies write it, because the bodies are read
only where the names reached nothing. What replaces it is the same failure for
an identifier nothing is named after, and `D-ANS-042` carries it.

Both halves fall to the same read. The title and the identifiers are taken out
of one opening of the file, in the one place a query that no file name carried
falls back to, so the second half costs nothing the first had not already paid.

