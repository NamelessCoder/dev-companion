---
id: D-ANS-006
title: An identifier is found however it is spelled
date: 2026-08-02
status: open
coveredBy:
  - PackageSourcesTest::anIdentifierReachesTheEntryTitledInWords
---

# D-ANS-006 — An identifier is found however it is spelled

**A query term carrying `_`, `.` or `-` is also compared with those separators
removed, on both sides, in the one matcher the label search and the changelog
search share.**

`ext_tables.php` is written that way where it is used and spelled
`ExtTablesPhpInExtensions` where it is titled, so the caller's own name for the
thing reached neither the file name nor the words derived from it.

## Evidence

- Six shapes reported from two checkouts by `feedback/2026-07-31-194504-…` and
  by `feedback/2026-07-31-172753-…`: `ext_tables.php`, `ext_emconf.php`,
  `list_type`, `backend_layout`, `SC_OPTIONS` and `mod.web_layout`. Every one
  has an entry in `.checkouts/14.3` and every one reached nothing; all six reach
  their entry now.
- The multi-word queries those sessions actually typed still reach nothing, and
  that is the documented rule rather than the defect: the miss now names
  `"list_type" reaches 1`, `"sc_options" reaches 1`,
  `"mod.web_layout" reaches 1`, which is the next step it could not print while
  those terms reached nothing either.

## Decided

- In `LabelSearch::carryingEvery()`, which both searches go through. A second
  matcher for the changelog is what `D-DIS-003` and `R-ANS-004` exist against —
  one rule serves both, and the answer means what the description says.
- Only a term that carries a separator is compared that way. A query without one
  behaves exactly as it did, and nothing this matched before matches less.
- Not by splitting the term into words. `labels.save_document` is one thing a
  caller asks for, and three terms joined by "carries every" would also match a
  label that happens to carry the three words apart.

## Assumed

- Removing the separators from the haystack too is what makes the comparison
  work, and it lets a term span what were two words there — `esphp` reaches
  `ExtTablesPhp`. That is only reachable for a term already carrying a
  separator, so the cost is bounded by how rarely such a term is also a fragment
  of something else.

## Wrong if

- A query naming one identifier comes back with entries about another, because
  the separatorless form of the term is a fragment of an unrelated title.
- The label search starts answering more widely than its callers expect: the
  same rule now reaches a label whose id spells the query's identifier apart.

## Since then

The other half of what those two feedback asked for is a sweep, and it is a
filter rather than a phrasing: `typo3_changelog_lookup` takes a `tag` now.
`FullyScanned` says which deprecations the Extension Scanner has a matcher for,
`ext:form` which system extension a change is in, and 34 of the 75 deprecations
of TYPO3 14 carry the first — a number no wording reaches, because the tags are
inside the files rather than in the names the search reads. That is why it is a
field: the filter reads the entries the version and the type narrowed to, 23 ms
for one major's deprecations against 600 for the whole changelog.

What the corpus does not have is what `feedback/2026-07-31-172753-…` asked for
outright: an extension key of the caller's own. The tags name the system
extension a change is **in**, never the package it affects, so
`bootstrap_package` matches none of them and cannot — and a miss says which tags
exist rather than leaving that to be guessed. The feedback is archived on that
reading: the enumeration it wanted has existed all along by omitting the query,
the two entries it named are reachable by name since the identifier rule above,
and the third is a filter this adds.

The reading above — that the reach line is the next step the miss could not
print — holds only where a term reaches nothing. Both queries of
`feedback/2026-07-31-194819` are the other case: every term reaches something,
the intersection is still empty, and the numbers name no call. There the
smallest reach is the term to keep, and two words had to go before either query
matched anything. `D-ANS-016` is that judgement, and it is queued rather than
made here.

Both bounds are still unreachable from the order a review runs under.
`feedback/2026-07-31-194459` and `feedback/2026-07-31-194819` are two models
sweeping the same sitepackage on the same day with words, because
`skills/base.md` step 5 tells them to derive the query set from what the
extension ships. Re-run on 2026-08-02 from `site-new`, `type: deprecation` with
`version: 14` and no query returns 75 entries and `tag: ext:form` narrows them
to the 6 that carry `#109412`, which the words missed. So what this entry added
is right and reaches nobody: the judgement is `D-SKL-003`, and the wording is
queued there.
