# Verify every suite range against the checkouts, rather than trusting it

**Serves:** R-KNW-024

`since`/`until` on a suite in `knowledge/test-suite-hints.json` is a claim about
`Build/Scripts/runTests.sh`, and nothing re-reads that script to check it. The
whole apparatus of `R-KNW-024` — the filtered checks, the filtered suite
listing, and now the restriction on what the prose documents may name — rests on
those numbers, so a wrong one is invisible everywhere and wrong everywhere at
once. `build` sat unranged until 2026-08-02 and was handed to 12.4 callers as a
command, while its own tool description promised it would not be.

The step is a check in `bin/cli` that reads the suite listing out of
`.checkouts/<branch>/Build/Scripts/runTests.sh` for each branch in
`knowledge/versions.json`, derives the range each suite actually holds on, and
reports every entry whose declared range differs — plus every suite the script
offers that the hints do not carry at all. `bin/cli catalog:check` is the shape
to follow: it verifies curated data against the checkouts and is a command
rather than a test, because `.checkouts/` is gitignored and no test may depend
on it. The suite names are readable off the usage block, one per line as
`- <name>[ (default)]: <description>`, which is stable across 12.4 to main.
