---
id: R-ANS-8
status: held
---

# R-ANS-8 — The files answer where the console cannot

**An installation-backed answer is not lost to a console that cannot boot where
the files hold it anyway.**

`typo3_label_lookup` falls back to the XLF files of the same packages and
`typo3_fluid_namespace_list` to their `Configuration/Fluid/Namespaces.php`;
both report `answeredBy: "packages"` and name what the weaker source leaves
out. Where nothing can answer, the failure is diagnosed rather than passed
through: a query against a missing table means the database has no schema, not
that the installation is broken.

**From:** an installed TYPO3 13.4.33 before the dump was imported, where the
labels sat in the files and both console-backed lookups returned a raw SQL
stack trace (2026-07-29).

**Held by:**
`PackageSourcesTest::withoutAConsoleTheDeclarationsAreTheAnswerAndSaySoAsOne`,
`LabelSearchTest::aConsoleThatCannotBootIsAnsweredFromTheFilesItWouldHaveRead`,
`LabelSearchTest::aDatabaseWithoutASchemaIsNamedRatherThanLeftAsAStackTrace`,
`Typo3CliTest::aFailureIsDiagnosedOnlyWhereTheMessageDoesNotSayEnough`
