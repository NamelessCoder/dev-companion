# Whether a Forge miss spends a call on an any-word re-read

**Serves:** feedback/2026-08-24-163235-multi-term-query-returns-zero-where-a-single.md
**Priority:** low
**Branch:** todo/multi-term-query-returns-zero-where-a-single
**Claimed:** 2026-08-25

Half of this feedback was answered on 2026-08-24: the `query` description and
the miss now say that every word has to be in the same issue, and the
measurement is in the last **Since then** of `D-ANS-038`. What is left is its
second suggestion — a miss that re-reads the tracker itself rather than handing
the caller a rewording — and that is a judgement rather than a fix, so it is
made where the first half was, against `D-ANS-038`, whose third **Wrong if**
watches a search growing an answer of its own.

The re-read is one call and not one per term. `search.json` with an empty
`all_words=` answers 5 where the same URL without it answers 0, measured on
2026-08-24, and `all_words=0` and `all_words=false` do not turn it off. What the
judgement decides is whether a miss is worth that call, and what it would say
about hits the caller's own words did not reach.
