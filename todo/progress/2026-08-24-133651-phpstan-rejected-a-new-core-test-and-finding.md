# Write what the core's PHPStan run demands of the code a patch adds

**Serves:** feedback/2026-08-24-133651-phpstan-rejected-a-new-core-test-and-finding.md
**Priority:** normal
**Branch:** todo/phpstan-rejected-a-new-core-test-and-finding
**Claimed:** 2026-08-24

Judged on 2026-08-24 as the ladder's step 1a and written up in `D-KNW-114`: the
one hint the query reaches is `extension-static-analysis`, which is
`scope: extension` and answers how to configure PHPStan rather than how to
satisfy it. A core-scope hint is taken on beside it.

The reading comes first, in `.checkouts/main`, and it settles what the hint may
state: whether the `@var` narrowing is the core's idiom or four sites that agree
— `TypoScriptStringFactory.php:51`, `Bootstrap.php:102` and
`RedirectServiceTest.php` lines 125 and 897 are what was found — what each of
the four rules in `Build/phpstan/phpstan.neon` rejects, and what the core's own
`AGENTS.md` says about `Build/phpstan/phpstan-baseline.neon`. That last one
needs a fetch: the worktree stands at 2026-08-18 and the file landed after it.

The second step is smaller and separate. `typo3_hint_lookup` and
`typo3_rule_lookup` both read as "how does this codebase do X" and neither
description names what the other takes; the clause each owes the other is the
change, and `D-ANS-072` carries why.
