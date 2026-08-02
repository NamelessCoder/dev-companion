# Say what the server no longer refuses to read

**Serves:** feedback/2026-08-01-115713-while-reviewing-patch-7175fcaf7fe-i-had-to-read.md, feedback/2026-08-01-122326-to-review-a-core-patch-i-had-to-retrieve-the.md, R-SCO-008
**Priority:** high

The first `doesNotCover` entry of `knowledge/server-scope.json` tells every
client that this server "never reads, inspects, or runs anything against a TYPO3
core checkout. It cannot be pointed at one." Started in
`/home/benji/projects/typo3-cms` on 2026-08-03, `typo3_project_scope` answers
`root: /home/benji/projects/typo3-cms`, `kind: core-checkout`,
`typo3Version: 15.0.0-dev`, `phpConstraint: ^8.5` and the four `composer
gerrit:setup` scripts it read out of that checkout's `composer.json` — so the
sentence is false, and it is one of the four things AGENTS.md says describe this
server outward. Two feature reports die on it and on the `API signatures` entry
below it before any judgement reaches them: an API-stability lookup that would
say whether a removed method sits in a public or an `@internal` class, and a
change-scope answer for the paths and the message that `typo3_test_run_guide` and
`typo3_commit_message_guide` both demand as input while the scope text sends the
caller to `git show`. Rewrite both entries to the boundary that actually holds —
what the installation half reads, and where it stops — then decide the two
capabilities against
[`D-FBK-027`](../../decisions/feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
and queue what that decides as cards of their own; the two feedback stay open
behind them. `ScopeTest::whatTheScopeExcludesIsNotWhatTheServerAnswers` holds
`R-SCO-008` on two named topics today and would not have caught this one.
