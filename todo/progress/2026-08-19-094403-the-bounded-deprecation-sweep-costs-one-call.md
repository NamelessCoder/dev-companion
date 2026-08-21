# The bounded deprecation sweep costs one call per tag per major — eleven round trips for one question

**Serves:** feedback/2026-08-19-094403-the-bounded-deprecation-sweep-costs-one-call.md
**Priority:** normal
**Branch:** todo/the-bounded-deprecation-sweep-costs-one-call
**Claimed:** 2026-08-21

Judged on 2026-08-21 as the ladder's step 1b and written up in `D-ANS-093`: the
sweep of one major is one call `typo3_changelog_lookup` cannot return, because
`limit` caps at 50 while the deprecations of 14 are 75 — so the caller composes
it out of eleven tag calls that reach 72 of them at 1.7 times the payload. Let
that tool answer a version and a type whole, and decide between a ceiling that
applies where both narrow and a raised `limit` maximum against the largest
covered set, which is the 128 deprecations of 12 and the 128 breakings beside
them. Step 5 of `skills/base.md` says "one call per declared major per tag" and
changes with it, together with whatever `SkillTest` asserts of that sentence.
