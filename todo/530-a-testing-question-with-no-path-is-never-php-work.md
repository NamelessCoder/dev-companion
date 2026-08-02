# A testing question with no path is never PHP work

**Serves:** decisions/
**Run:** `bin/cli hints:probe "Set up tests for our site package extension"`

«Set up tests for our site package extension» with a path now reaches
`project-extension-tests`; without one it still comes back with
`sitepackage-layout` alone, and the reason is a rung below the ranking. The
domains of that text are `fluid, typoscript` — "site package" is a keyword of
both and nothing in it is a PHP one — so `Domains::hintCategories()` never makes
PHP a candidate and every testing hint in `php.json` is filtered out before
anything is scored. `Domains::KEYWORDS[PHP]` carries `unit test` and
`functional test` and neither `test` nor `tests`.

The step is to measure what adding the bare word costs before adding it. A test
is PHP work here — the suites are phpunit — but "browser tests" are Playwright
and `browser-tests` lives in `general.json`, and "test the frontend rendering"
is a sentence about neither. So probe the corpus for how many queries gain PHP
that should not have it: `bin/cli hints:coverage` reports what each hint is
reached by, and the phrasings in
`HintsTest::settingTestsUpInAPackageReachesTheHintAboutThat` are the ones any
change has to keep. Where the bare word is too wide, the narrower shape is the
pair that already works — `unit test`, `functional test` — extended by the
words a caller who has none of them yet would use.
