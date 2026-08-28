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

The other half of what those two feedback asked for is a filter rather than a
phrasing: the lookup takes a tag now. One tag says which deprecations the
Extension Scanner has a matcher for and another which system extension a change
is in — a number no wording reaches, because the tags are inside the files
rather than in the names the search reads. That is why it is a field: the filter
reads what the version and the type narrowed to, in a fortieth of the time.

What the corpus does not have is an extension key of the caller's own: the tags
name the system extension a change is in, never the package it affects, and a
miss says which tags exist rather than leaving that to be guessed.
