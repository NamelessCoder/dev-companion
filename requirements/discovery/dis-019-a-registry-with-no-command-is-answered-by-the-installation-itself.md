---
id: R-DIS-019
status: held
---

# R-DIS-019 — A registry with no command is answered by the installation itself

**Where TYPO3 exposes no console command for a registry, the installation is
booted in a subprocess and its container is asked.**

The files the packages ship are the fallback, and the answer says which of the
two answered and what the fallback leaves out.

Nothing is booted in this process. A container that came up without essential
configuration is core-only and looks complete, so it is never handed on as a
result — it is a reason to read the files and to say why. `answeredBy` carries
the source in the one vocabulary every tool uses: `installation` for the booted
registry, `packages` for the files, and where it is `packages` the answer states
the limitation in its text rather than only in its data.

Without it, the parsed registry is presented as the registry. It is exact for
what a file declares and blind to everything a package builds at runtime, so a
review comparing it against the tree reports registrations that are missing and
files that are unused — defects that do not exist.

## From

A `REVIEW-02` run against `georgringer/news` (2026-07-31) that was told the
extension registers `provider` and `source`; and the measurement that followed
it — on a set-up site with news installed the booted registry holds 1314 icon
identifiers against 1289 read from files, the 25 extra being the ones its
`Configuration/Icons.php` builds in a `foreach` plus one TYPO3 derives from TCA,
while a second site without news agreed on all 1287. In the extension's own
repository, which has no `settings.php`, the same boot yields a failsafe
container with 1259 core icons and none of the extension's own.

## Held by

- `Typo3RuntimeTest::theProbeReachesAnInterpreterAndAnswersAsData`
- `Typo3RuntimeTest::theAutoloaderIsTheOneTheInstallationDeclares`
- `Typo3RuntimeTest::aStatedConsoleIsKeptAsTheWayInAndPointedAtPhp`
- `Typo3RuntimeTest::aStatedConsoleNoInterpreterCanBeDerivedFromIsSaidOutLoud`
- `Typo3RuntimeTest::withoutAConsoleTheReasonIsTheConsolesOwn`
- `IconLookupTest::aRegistryReadFromTheFilesSaysThatInTheAnswerItself`
