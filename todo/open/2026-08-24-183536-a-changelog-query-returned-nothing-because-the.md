# the changelog miss names a tool only where it offers a re-query

**Serves:** feedback/2026-08-24-183536-a-changelog-query-returned-nothing-because-the.md
**Priority:** normal

Name a tool where `typo3_changelog_lookup` comes back empty with no re-query to
offer. The corpus sentence hangs on the offered subset today, and `Subsets`
offers none for a query of two words at all — so the shortest misses are the
ones that end with no route out, which `R-ANS-018` demands they do not.
`D-ANS-043` is the judgement, the re-run and the reason this is step 3 rather
than a matching fault.

Settle which tool the branch names before writing it. `D-ANS-010` routes "does
this still hold" to `typo3_documentation_lookup`, and the reported question —
whether a kind of change owes an entry — is a rule and is `typo3_rule_lookup`.
One sentence carrying both is a paragraph on every miss, so decide between them
in `decisions/` beside `D-ANS-043`. The answer is
`src/Tool/ChangelogLookup.php`, and `R-ANS-018` gains the case over this branch.
