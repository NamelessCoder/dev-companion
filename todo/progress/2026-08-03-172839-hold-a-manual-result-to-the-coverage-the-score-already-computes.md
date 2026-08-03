# Hold a manual result to the coverage the score already computes

**Serves:** feedback/2026-08-03-164734-installation-the-extension-ships-a-fork-of-the.md
**Priority:** normal
**Waiting on:** the floor has no value that both empties the reported queries
    and keeps a short one that works today. At 0.5 the three queries of
    `feedback/2026-08-03-164734` stop returning six collisions, and `login
    screen layout` and `login form template` are emptied with them — the two
    queries `D-ANS-046`'s own evidence offers as proof that *LoginProvider* is
    reachable. Returning that page needs a floor at 0.34 or under; emptying
    `Fluid template file naming convention v14` needs one above the collision it
    tops out at, 0.40. Three answers. Ship 0.5 as decided, and a query of three
    words or more is answered as a miss unless a page is titled after it, which
    is the rule `D-ANS-021` shipped enforced rather than advised. Or carry the
    coverage on every result and say in the text where nothing covers half the
    query, which tells the caller a thin answer is thin without taking a page
    away — the feedback's "at minimum" half rather than its second sentence. Or
    keep the drop and put it on a measure that survives a three-word question,
    which nobody has designed. The recommendation is the second: it is what the
    reported session could not see, it costs no query that answers today, and it
    leaves the floor available once there is a measure for it. Putting the todo
    back is one of the answers.
**Branch:** todo/hold-a-manual-result-to-the-coverage-the-score-already-computes
**Claimed:** 2026-08-03

The floor itself is three lines and the constant is what has none.
`TermSearch::score()` returns the coverage as its second value and
`Documentation::lookup()` discards it in the destructuring, so what is missing
is only the division by the query's asked-for weight and the drop. What that
value does to the corpus is measured in the **Since then** of
[`D-ANS-046`](../../decisions/answers/ans-046-a-manual-result-covers-the-question-it-is-returned-for.md)
— the live 14.3 index at 1419 pages, every query the feedback and the entries
behind it turn on, at 0.4 through 0.7 — and so is the offer a miss would carry:
`layout login` returns the collisions the feedback reported and not
*LoginProvider*, and `fluid file` reaches 23 pages where the query it narrows
reached none. What is built once the value is settled is unchanged: the drop in
`lookup()`, `empty` where nothing clears it, `Search\Subsets::largestReaching()`
over the same searchable fields and `TermSearch::carries()`, then the corpus
sentence `D-ANS-043` built, pointing at `typo3_changelog_lookup` — which returns
`Feature-108166` alone for `fluid file extension`. What must hold from then on
is written beside `R-DOC-002`, and the commit that ships it archives the
feedback.
