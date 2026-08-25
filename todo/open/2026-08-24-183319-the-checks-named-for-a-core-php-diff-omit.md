# Each of the three suite lists says which narrowing it is

**Serves:** feedback/2026-08-24-183319-the-checks-named-for-a-core-php-diff-omit.md
**Priority:** normal

Say in the declared schemas what each of the three suite lists is a narrowing
of, and which one is the list to run. `typo3_task_guide`'s `checks` is
`TestSuiteHints::baseFor()` — the suites of the task's domains that run whatever
the task turns out to be — plus the confirmed intents' own; its `testSuites` is
the four strongest of `TestSuiteHints::find()` over the same domains; and
`typo3_test_run_guide`'s `suites` is the whole of that same `find()`, narrowed
again by `query` wherever one scores, which is what returned a single suite for
`query="functional"`. `Schema::listOf(Schema::testSuiteRecord())` in
`TaskGuide::outputSchema()` carries no description at all, and `checks` says
only "Commands to run, ready to execute from the core root". The judgement is
`D-ANS-108`, which answered the other half of this feedback.
