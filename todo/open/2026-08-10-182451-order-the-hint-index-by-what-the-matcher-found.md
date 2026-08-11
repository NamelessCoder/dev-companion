# Order the hint index by what the matcher found

**Serves:** feedback/2026-08-10-182451-availablehints-ids-i-had-already-been-shown.md
**Priority:** normal

Answered on 2026-08-11 and recorded as
[`D-ANS-075`](../../decisions/answers/ans-075-the-hint-index-is-ordered-by-the-rank-the-matcher-already-computed.md):
ordered whole rather than cut to a band or withdrawn. Build `availableHints` in
`Hints::find()` from the candidates it has already scored — keep the ones the
coverage floor refuses where they are scored instead of dropping them, put what
the limit cut first in the matcher's own order and the refused ones after them
by score — and leave `index()` to the id path, where no rank exists. The copy in
`HintLookup` says the order, `documentation/tools/typo3_hint_lookup.md` carries
the examples it prints, and the feedback is trimmed rather than archived,
because `css-tokens-specificity` was refused by the floor and needs a matcher and
vocabulary reading against a core checkout instead.
