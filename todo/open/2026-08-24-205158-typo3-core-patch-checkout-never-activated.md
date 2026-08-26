# Both skills that reach typo3-core-patch-checkout owe it a crossing

**Serves:** feedback/2026-08-24-205158-typo3-core-patch-checkout-never-activated.md
**Priority:** normal

Judged on 2026-08-26 as the ladder's step 4, and the dated section on
`D-SKL-022` is the reading: `R-SKL-018` was written on 2026-08-07 and never
applied to the crossing running *into* `typo3-core-patch-checkout`, so a session
holding two other patch skills fetched three patches without opening it. Apply
the rule. In `skills/typo3-core-patch-development/SKILL.md` the sentence at the
foot of the rebase section, which says fetching somebody else's patch belongs to
`typo3-core-patch-checkout`, becomes an instruction to invoke that skill with
the moment it stands at beside it, and the ownership paragraph stays;
`skills/typo3-core-patch-review/SKILL.md` names that skill nowhere at all and
owes the same crossing at the point a review needs the patch in a working copy —
write that half after `todo/reviewing-open-gerrit-changes-materialises-refs`
lands, because that branch is rewriting the same section to say a shortlist is
triaged without fetching anything. Then add both edges to the `$crossings` map
in `SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor`, which holds a
successor per skill and names this one in none of them. The feedback's other
half was trimmed: the refspec it worked out by hand is `fetch.ref` and
`fetch.remote` on `typo3_gerrit_lookup`, and the recoverability test it asks for
is carried by the card serving `feedback/2026-08-24-195307`.
