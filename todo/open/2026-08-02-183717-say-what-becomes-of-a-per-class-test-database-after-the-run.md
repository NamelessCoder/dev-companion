# Say what becomes of a per-class test database after the run

**Serves:** feedback/2026-08-01-003929-functional-test-database-sprawl-and-loss-of.md
**Priority:** low

Step 1a of the ladder, on the evidence in
[`D-KNW-022`](../../decisions/knowledge/knw-022-the-per-class-test-database-is-stated-and-its-lifetime-is-not.md):
`project-extension-tests` states that every test class gets a database of its
own and nothing says the database is still there once the run ends, so
`bin/cli hints:probe "clean up functional test databases"` reaches that hint on
the sentence that says what creates one. Read `Testbase::setUpTestDatabase()`
and `FunctionalTestCase::tearDown()` on `.checkouts/testing-framework/8`, `9`
and `main` for what drops a test database and when, what `$initializeDatabase =
false` and the `pdo_sqlite` files under `functional-sqlite-dbs/` do to that, and
whether the `_ft<7 hex>` suffix is the whole of what tells a leftover from the
live database — that last part is what the reporting session actually lost.
Then measure where the clause fits before writing it: the sentence it belongs
beside is on `project-extension-tests`, which this run counted at 981 words
against the 544-word ceiling
[`D-ANS-025`](../../decisions/answers/ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md)
measured, so a hint of its own is the alternative. Write it with a requirement
for what has to keep holding.

The feedback's other halves need nothing from this card. That seeding goes
through DataHandler rather than a direct `INSERT` is stated on
`datahandler-persistence` and is the `003216` sibling's whole subject, and the
premise that a test sees only what it primed is
[`D-KNW-019`](../../decisions/knowledge/knw-019-a-functional-test-sees-only-what-it-primed-and-nothing-here-says-so.md)'s.
