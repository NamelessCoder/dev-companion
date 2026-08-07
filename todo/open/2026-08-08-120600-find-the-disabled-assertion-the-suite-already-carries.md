# Find the disabled assertion the suite already carries

**Serves:** feedback/2026-08-07-233543-nothing-surfaced-that-the-core-test-suite.md
**Priority:** normal

The best outcome of a core triage is "the project already knows, here is the
disabled assertion and here is the fixture", and nothing here can produce it.
For Forge 15984 the core's own functional suite carries the defect as three
commented-out data-provider rows —
`typo3/sysext/frontend/Tests/Functional/SiteHandling/SlugLinkGeneratorTest.php`
lines 417, 421 and 428, each marked `// @todo Fails, not expanded to sub-pages`
— and `Fixtures/SlugScenario.yaml` already models the exact constellation the
2006 report describes. The reproduction needed no new test and no new fixture,
only one comment removed. The session reached it in four steps of hand reading
and says no lookup pointed at it.

This is a capability rather than a repair, so price it before building: a lookup
over the checkout's test suites for disabled or known-failing assertions —
commented-out data-provider rows adjacent to `@todo`, plus `markTestSkipped` and
`markTestIncomplete` — searchable by the words of a report and by path. It reads
the checkout rather than the corpus, which puts it beside `typo3_schema_lookup`
rather than beside the lookups over bundled knowledge, and it is worth
establishing first how many such markers a core checkout actually carries and
whether their text is specific enough to match a report against. What it would
turn a triage into is the argument for it: the failing test the skill asks a
triage to produce had already been written and switched off.
