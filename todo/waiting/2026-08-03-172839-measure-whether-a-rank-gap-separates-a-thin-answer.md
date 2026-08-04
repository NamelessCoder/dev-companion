# Decide what is left of the floor, now that three measures have failed

**Serves:** feedback/2026-08-03-164734-installation-the-extension-ships-a-fork-of-the.md
**Priority:** normal
**Waiting on:** whether the drop is abandoned, or the index is what changes.
    Both measures this card was written for were run on 2026-08-04 and neither
    separates, so nothing here is unread and re-deriving the question buys
    nothing. The reading is on `D-ANS-051`'s **Since then**, in full, with the
    numbers. Nothing is broken while it waits: every result carries the share of
    the query it covers and the answer says where nothing covers half, so the
    six collisions no longer arrive in the shape of a good answer. What is
    unbuilt is the feedback's second sentence alone — returning the fact that
    nothing clears a threshold, rather than the best six collisions.

    The question as it stands: the share does not separate, the rank gap does
    not separate in any of its three forms, and the share measured against what
    a page could carry separates one of the three collision queries by three
    thousandths. All three fail for one reason rather than three — what is
    indexed is a table of contents, and carrying a query's words is not
    distinguishable there from answering its question. So the remaining lever is
    the index rather than the threshold, and `D-ANS-046` already measured that a
    corpus of page bodies does not carry the answer to two of these three
    questions either. Close the drop out and archive the feedback saying so, or
    keep it open against a change of index that nothing has costed?

Once that is answered: on "close it out", archive `feedback/2026-08-03-164734`,
record the closing on `D-ANS-051`, and delete this card. On "keep it open",
rewrite this card as the index question — what a second searchable field would
cost per call, and whether the section headings of a page are reachable without
fetching all 1419 of them.

## What was measured, 2026-08-04

Over the live 14.3 index this lookup builds — 1419 pages from the four manuals —
driving `Documentation::lookup()` through a cached reader, so the numbers are
the ones a caller gets. `D-ANS-051` carries all of it; this is the short form.

- **The rank gap, as a difference in coverage.** Answers span 0.024 to 0.458 and
  collisions 0.020 to 0.294.
  `FunctionalTestCase executeFrontendSubRequest CSV fixture TYPO3 14` is an
  answer below two of the three collisions.
- **The rank gap, as a ratio.** 0.542 to 0.871 against 0.508 to 0.908 — the two
  bands lie on top of each other.
- **The rank gap in the ranking score**, which is what "one page well clear of
  the rest" means, since the ranking is by score and coverage is not monotone in
  rank: 0.129 to 0.457 against 0.093 to 0.327.
- **Worse than not separating.** `login screen layout` has three candidates on
  one score at the top and `login form template` five, so both sit at a
  difference of 0.000 and a ratio of 1.000 — the far collision end of both
  scales. Every threshold empties the two queries that price the measure first,
  and takes *LoginProvider* with them.
- **The share against what a page could carry**, the measure `D-ANS-051`'s
  fourth **Wrong if** names: the covered weight over the weight of the terms at
  least one page carries. It lifts *Functional tests* from 0.186 to 0.247 and
  *IRRE / inline* from 0.430 to 0.690, and reorders exactly one pair — a floor
  between 0.244 and 0.247 empties `layout root paths login screen override` and
  keeps all five that must survive. The other two collisions are untouched: a
  floor would have to sit above 0.598 and at or below 0.247.

The two that survive every measure are not artefacts. *Multi-language Fluid
templates* really does carry 0.598 of `fluid.html file extension templates` and
*Naming* really does carry 0.458 of `Fluid template file naming convention v14`.
Both answer a different question than the one that was asked, and no arithmetic
over the coverage tells those two things apart.

## What the drop would have been

Kept here because the answer may be to build it after all. The drop in
`Documentation::lookup()`, `empty` where nothing clears the measure,
`Search\Subsets::largestReaching()` over the same searchable fields and
`TermSearch::carries()`, then the corpus sentence `D-ANS-043` built, pointing at
`typo3_changelog_lookup` — which returns `Feature-108166` alone for
`fluid file extension`. The offer does not carry what a drop would take away
today: `layout root paths login screen override` offers `layout login`, which
returns three of the six collisions and not *LoginProvider*, so the offer is
part of the change rather than beside it. What must hold from then on is written
beside `R-DOC-002`, and the commit that ships it archives the feedback.
