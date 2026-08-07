# Say when an empty Gerrit answer is a permission effect

**Serves:** feedback/2026-08-07-132416-a-private-gerrit-change-is-reported-as-empty.md, R-ANS-027
**Priority:** high

`typo3_gerrit_lookup` reads review.typo3.org without credentials, so a private
change is indistinguishable from one nobody pushed, and the word it answers with
is `empty`. A review session made that its first finding, ranked it under what
blocks the patch from being submitted at all, and recommended coordinating with
another author over a change that was its own. Make the answer say what the tool
description already implies: where the query named a concrete Change-Id or
change number and came back with nothing, an anonymous reader cannot tell a
restricted change from an absent one. Two concrete parts the feedback names.
One, `Gerrit::` returns `empty` for a search and `unavailable` with
`source-not-answering` for a direct read, and in this case both had the one
cause — either report the same status or say why they differ. Two, the server
already holds the disambiguating evidence: where `typo3_forge_lookup` would
surface a review URL for the same issue, an empty Gerrit answer for that number
is positive evidence of a restricted change, and no credentials are needed to
use it. Check `typo3-core-patch-review` in the same pass — its instruction that
an answer of nothing is a result to be reported is only safe while `empty` means
absent. `D-ANS-062` is the judgement, and `R-ANS-024` is the requirement it sits
under.
