# Filter the instruction index by what a caller excluded, and spend the room on the commit guide

**Serves:** feedback/2026-08-18-113357-typo3-commit-message-guide-reads-as-core-only.md, D-AUD-011
**Priority:** normal
**Branch:** todo/typo3-commit-message-guide-reads-as-core-only
**Claimed:** 2026-08-18

`Coverage::instructions()` returns the stored string whole, so the "What to call
for what" index names tools a caller excluded — the case that binds `R-ANS-013`
at 2035 characters of 2048, against 1912 with the stale-skills notice and
nothing excluded. Make the index data in `knowledge/server-scope.json` rather
than prose inside `instructions`, drop the entries whose tool
`ExcludedTools::all()` names the way `Coverage::offered()` already drops a
`routing` entry, and spend what that frees on one more bullet: a commit message,
in a repository of your own as much as in the core, is
`typo3_commit_message_guide`.
`ScopeTest::theInstructionsIndexTheQuestionEachToolAnswers` asserts against the
prose today and moves with it; the three assertions in
`ScopeTest::theInstructionsFitWhatAClientKeeps` are what say the result fits.
