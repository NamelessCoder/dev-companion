# Say what a functional test's database holds before the test primes it

**Serves:** feedback/2026-08-01-003003-repeatedly-did-not-understand-the-functional.md
**Priority:** normal
**Branch:** todo/say-what-a-functional-test-database-holds-before-the-test-primes-it
**Claimed:** 2026-08-02

Step 1a of the ladder, on the evidence in
[`D-KNW-019`](../../decisions/knowledge/knw-019-a-functional-test-sees-only-what-it-primed-and-nothing-here-says-so.md):
the corpus states every fixture rule and never the premise under them, so
`bin/cli hints:probe "empty database per test run"` reaches neither test hint.
Read `FunctionalTestCase::setUp()` and
`Testbase::initializeTestDatabaseAndTruncateTables()` on
`.checkouts/testing-framework/8`, `9` and `main` for what exists before the
first import, what the truncation between tests of one class covers, and what
`$initializeDatabase = false` and `withDatabaseSnapshot()` do to both — that is
what decides whether the statement holds as one sentence. Then write it beside
the CSV fixture statement on the `core-tests` hint in
`knowledge/architecture-hints/php.json`, with a requirement for what has to keep
holding. It goes there rather than onto `project-extension-tests` because that
one is 947 words against a 544-word ceiling, and the placement is the entry's
first **Wrong if**.

The other halves of the feedback need nothing from this card. The request type
is already stated on `project-extension-tests`, and the DataHandler and
test-database halves are the whole subject of the `003216` and `003929`
siblings.
