# Answer a change with its Change-Id and the siblings sharing it

**Serves:** feedback/2026-08-12-092654-the-forge-answer-s-reviews-array-exposed-a.md
**Priority:** normal
**Branch:** todo/the-forge-answer-s-reviews-array-exposed-a
**Claimed:** 2026-08-13

Judged on 2026-08-14 as step 2 of the ladder, delivery: the review server
returns the backport to the query `typo3_gerrit_lookup` already runs, and it
reaches the caller through the Change-Id form of `change` and not through the
number form. `D-ANS-080` carries the measurements and what was left to this
work.

The step is to add the change's own Change-Id to each entry of the answer, read
from the `change_id` the response already carries and dropped today in
`Gerrit::change_()`, and to answer the changes sharing it whichever handle was
passed — which costs the number path one further query. What the sibling is
called in the schema and what the text half says about it is decided here,
against the shape `D-ANS-068` set for `fetch`.

The Change-Id is asked for a second time by `feedback/2026-08-13-214644`, whose
card is in hand elsewhere for the review comments and votes of the same tool.
Whichever lands first carries the field.
