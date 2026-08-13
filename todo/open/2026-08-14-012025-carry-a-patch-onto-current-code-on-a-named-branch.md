# Carry a patch onto current code on a named branch

**Serves:** feedback/2026-08-13-214754-the-checkout-skill-prescribes-a-detached-head.md
**Priority:** normal

Read what the contribution guide's cherry-pick page says about carrying a change
into a checkout and whether it names a branch for it — the URL is in
`knowledge/documents/core/contribution/sources.md` and
`typo3_documentation_lookup` is what reaches it — then write the way in into
`skills/typo3-core-patch-checkout/SKILL.md` beside the two that are there. The
reading settles the two things `D-SKL-041` left open: what the branch is called,
and whether the rebase path the skill already has is folded into the new way in
or stays beside it. The command form and the scope of the detach rationale go to
`knowledge/documents/core/contribution/gerrit-workflow.md` instead of into the
skill, and "Put the checkout back" gains the branch's deletion — its step 2 says
today that leaving the fetched commit loses nothing, which the rebase path
already made false.
