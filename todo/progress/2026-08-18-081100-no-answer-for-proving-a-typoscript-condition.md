# No answer for proving a TypoScript condition verdict against a running frontend, and the two near...

**Serves:** feedback/2026-08-18-081100-no-answer-for-proving-a-typoscript-condition.md
**Priority:** normal
**Branch:** todo/no-answer-for-proving-a-typoscript-condition
**Claimed:** 2026-08-18

Judged as step 1a on 2026-08-18 and taken on — `D-KNW-102` has the evidence, the
boundary and why `core/testing/proving-a-rendering` is not rescoped to reach it.

Write `knowledge/documents/any/testing/proving-a-condition.md`: how a condition
verdict is made observable against a running installation. Establish each step
against `.checkouts/` first, and settle there what has to be flushed between two
runs — the verdict is in the page cache identifier on all four covered lines, so
the report's reason for flushing does not hold and the TypoScript cache is the
candidate. Declare `typoscript-conditions` in its `hints:`, so a caller asking
about a condition reaches it.
