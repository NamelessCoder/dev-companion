# Say what a core changelog entry owes, as knowledge rather than as a skill

**Serves:** feedback/2026-08-02-145315-task-fix-forge-105403-and-deliver-it-as-a-core.md
**Priority:** normal

The order half of this feedback is in `typo3-core-patch-development`; what is
left is the half a skill may not carry. `typo3_rule_lookup` today answers that a
changelog entry lives below `Documentation/Changelog/`, is named
`<Type>-<issue>-<slug>.rst` and is checked with `checkRst` — and stops there.
The session that filed this had to establish the rest from neighbouring files:
that the directory is named for the upcoming version rather than for the branch,
the entry's skeleton down to the underline rule, the roles it uses, and above
all **which type a change owes**, which it settled by reading neighbours because
nothing states it. Read that against `.checkouts/12.4`, `13.4`, `14.3` and
`main` — the types and the skeleton are stable enough to bind, the version
directory is not — and write it where `typo3_rule_lookup` already answers the
changelog question, so the answer arrives with the rule instead of one file
away.
