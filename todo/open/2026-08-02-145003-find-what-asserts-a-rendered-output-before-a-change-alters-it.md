# Find what asserts a rendered output before a change alters it

**Serves:** feedback/2026-08-02-145003-task-fix-forge-105403-by-changing-the-shape-of.md
**Priority:** normal

Step 1a of the ladder, on the evidence in
[`D-KNW-040`](../../decisions/knowledge/knw-040-what-asserts-a-rendered-output-is-a-gap-this-server-owns.md):
nothing here answers "I am changing rendered output, what asserts it", and
`core-tests` in `knowledge/hints/testing.json` sends the caller into the loop
that cost fifteen functional runs. Establish first, on `.checkouts/main` at
`c71b2bdb2f`, which shapes a functional test encodes a rendered URI in — the
four counted so far are `contentMatchRegExp` data-provider keys in
`ImageConvertIMViewHelperTest`, a PCRE whose capture group is used as a file
path at `ImageViewHelperTest:159`, `{$...}` placeholders in
`backend/Tests/Functional/Template/Fixtures/*.php`, and the encoded mail body in
`core/Tests/Functional/Mail/FluidEmailTest.php` — and settle whether one search
reaches them all or several are needed, because that decides whether what lands
is a sentence or a recipe. Then check the same shapes on `.checkouts/13.4` and
`.checkouts/14.3`, so the statement carries a version range only where the
majors differ. Then write it where such a change is worked: a statement on
`core-tests` for where the expectations hide, and the exception to the
iterate-narrowly sentence that `TestSuiteHints::invocation()` emits with every
`typo3_test_run_guide` answer, with a requirement for what has to keep holding.
