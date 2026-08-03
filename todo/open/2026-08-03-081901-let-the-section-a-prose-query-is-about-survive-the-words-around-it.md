# Let the section a prose query is about survive the words around it

**Serves:** feedback/2026-08-01-115115-in-the-same-session-typo3-project-scope.md
**Priority:** normal

Judged as
[`D-ANS-037`](../../decisions/answers/ans-037-a-compound-rule-query-is-owed-the-section-its-score-prefers-and-a-miss-that-names-the-words.md),
a gap in `Documents::MIN_COVERAGE` rather than in any wording:
`typo3_rule_lookup "commit message summary line length"` returns two
`typo3-gerrit-workflow` sections at coverage 0.525 and score 38, while
`## Summary Line` — score 124, and the section that carries the 52-character
rule — sits at 0.429 and is gated out, because `Documents::searchable()` matches
a heading and a body and the document title is neither. Measure the three
answers `D-ANS-037` declines to choose between — yielding the gate to the score,
admitting the largest covering subset the way
[`D-ANS-016`](../../decisions/answers/ans-016-a-miss-names-the-query-that-would-have-hit.md)
does, and weighting the document title into the searched fields — over the whole
prose corpus the way
[`D-ANS-025`](../../decisions/answers/ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md)
measured the other end of this gate: how many of the 208 curated `appliesTo`
patterns and the scenario prompts change their first hit, and whether any query
that reached nothing starts returning the nearest unrelated section, which is
what the floor is there to stop. Hold the outcome with an assertion that
`commit message summary line length` reaches `## Summary Line`.
