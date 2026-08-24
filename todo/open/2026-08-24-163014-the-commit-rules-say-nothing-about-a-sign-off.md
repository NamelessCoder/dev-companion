# The commit rules say nothing about a core commit's sign-off

**Serves:** feedback/2026-08-24-110851-signed-off-by-is-in-no-rule-document-and.md, D-KNW-109
**Priority:** normal

Write the sign-off section into
`knowledge/documents/core/contribution/commit-messages.md`, beside the trailers
it already lists, saying which source asks for the trailer rather than one rule
— what `D-KNW-109` records, read in `.checkouts/main` at `9dd4e1bfd7`: the
core's `AGENTS.md` demands `git commit -s` on the Developer Certificate of
Origin, `Build/git-hooks/commit-msg` line 44 strips the trailer only to hash the
`Change-Id` and checks it against nothing, `CONTRIBUTING.md` is silent, and 5 of
the last 500 commits on `origin/main` carry it. What is still owed before
writing is the official Contribution Guide, through
`typo3_documentation_lookup`. The priority is `normal` because three sessions
from one checkout hit the same gap on one day, and not higher because it is one
section on a page that already exists.
