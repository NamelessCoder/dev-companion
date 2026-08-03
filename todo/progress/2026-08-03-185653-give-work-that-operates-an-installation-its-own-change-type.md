# Give work that operates an installation its own change type

**Serves:** feedback/2026-08-03-154508-start-the-environment-import-a-database-and.md
**Priority:** normal
**Branch:** todo/give-work-that-operates-an-installation-its-own-change-type
**Claimed:** 2026-08-03

[`D-GUI-008`](../../decisions/guides/gui-008-operating-an-installation-is-a-change-type-of-its-own.md),
which a session booting a Composer project paid for on 2026-08-03: it had no
`changeType` for work that writes no file, so `unknown` handed it the patch
skeleton — confirm the branch, keep the patch focused, add test coverage, write
the commit message. Add `operations` to the enum in `TaskGuide::inputSchema()`,
give it an entry in `CHANGE_TYPE_CHECKLIST` and one in `CHANGE_TYPE_TERMS`, and
fork the skeleton in `answer()`: `$changesNothing` is one flag over two shapes
today, and the review arm carries "report what the review did not reach", which
a boot does not owe either. Then the matcher half in
`knowledge/task-intents.json` — narrow `installation-setup`'s weak needle
`install`, which fires on `install dependencies` and on every text containing
`installation` because `Text::containsWord()` matches inside a word, and add the
third installation intent beside setup and upgrade for bringing up an
environment that already exists. What that intent's checklist says is the
reading rather than this step: the environment a repository declares and its
data import are what `feedback/2026-08-03-154501` reports missing from
`typo3_project_scope`, and `feedback/2026-08-03-162826`, `-162836` and `-162858`
carry what was established about the setup command, a distribution import and
DDEV in practice. The terms entry is the same commit's other half — `php` was
the only domain the reported call detected, which is why three of its four hints
were `datahandler-basics`, `fal-basics` and `public-assets`. The case belongs
beside `HintsTest::aTaskThatChangesNothingIsNotAnsweredWithAPatchChecklist`, and
the commit that lands it archives the feedback.
