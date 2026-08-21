# No intent recognizes compatibility work, so nothing routes it

**Serves:** D-GUI-012
**Priority:** normal
**Branch:** todo/no-intent-recognizes-compatibility-work
**Claimed:** 2026-08-21

Measured on 2026-08-21 while placing the two `extension/compatibility/` pages:
"make the extension compatible with v14", "prove the package runs on TYPO3 13
and 14" and "does this still work on v13?" match no entry in
`knowledge/task-intents.json` at all, so `typo3_task_guide` answers each with an
empty `skills` and an empty `guides` — while `typo3-extension-upgrade` owns that
work, names
`extension/compatibility/running-on-a-declared-major-that-is-not-installed` in
its own step 3, and is reachable from the task text by nothing. Write the intent
that recognizes it: the needles read off task texts of that shape rather than
invented, `skill` naming that workflow, `guide` naming the page it owns, and
`skillCore` left empty because a major the core declares is not this work.
Settle against `TaskIntents::detect()` what those needles also match —
"compatible" and "runs on" are ordinary words, and an intent that fires on every
upgrade task takes `installation-upgrade`'s callers with it.
