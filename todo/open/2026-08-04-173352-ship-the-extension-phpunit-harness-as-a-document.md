# Ship the extension PHPUnit harness as a bound document

**Serves:** knowledge/documents/
**Priority:** normal

Write `knowledge/documents/typo3-extension-phpunit-setup.md` as `D-KNW-056`
settled it, one file per `##` section and its explanation in the section beside
it: `Build/UnitTests.xml` and
`Build/FunctionalTests.xml` copied from
`.checkouts/testing-framework/<line>/Resources/Core/Build/` with the testsuite
directories pointed at `Tests/Unit/` and `Tests/Functional/` and the `bootstrap`
attribute left on the vendor path, per `R-KNW-047`. Each XML needs two bound
sections, because lines 8 and 9 differ in the PHPUnit schema URL and in
`beStrictAboutTestsThatDoNotTestAnything` while line 9 and `main` are identical
— so `Documents::sections()` has to read a declared `since`/`until` per section
and `RuleLookup` has to filter by the target major the way `TestSuiteHints`
does, and that mechanism is part of this card rather than a separate one. Add
the topic to `knowledge/server-scope.json` with `scope: extension`, which is
what `ScopeTest` holds every document to and what keeps the document from being
withheld outside the core. Verify each section against both checkouts and name
both lines in the commit message.
