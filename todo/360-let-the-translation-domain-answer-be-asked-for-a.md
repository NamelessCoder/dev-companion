# Let the translation domain answer be asked for a stated version

**Serves:** decisions/

D-DIS-4's second **Wrong if** — a caller working on a version other than the
installation the server found — was confirmed live on 2026-08-02 while its first
one was being held. `TranslationDomainLookup::answer()` reads
`Instance::typo3Major()` directly and its `inputSchema()` takes `path` alone, so
a session on a backport branch or a second checkout is answered for whichever
installation the server started in and has no way to say otherwise. The answer
this gets wrong is the one D-DIS-4 singles out as withheld rather than qualified:
below `SINCE` the domain is refused and the `LLL:EXT:` reference handed over
instead, which is the wrong answer on a 14 branch read from a 13 installation and
the reverse on a 13 branch read from a 14 one.

The shape is already in the repository four times over: `SystemExtensionLookup`,
`TestRunGuide` and `CatalogScope` each declare a `targetVersion` string in their
input schema, pass it to `Versions::target()` and report the major back in their
output; `DocumentationLookup` requires one outright. Give this tool the same
`targetVersion` property, replace the `Instance::typo3Major()` call with
`Versions::target()`, and say in the answer which major it was composed for —
the withheld branch already names the version in its prose, and the branch that
hands over a domain says nothing at all. Settle while writing it whether the
answer should also carry the major in `outputSchema()`, as the three above do;
this tool's output is `path`/`domain`/`domainOnNewerVersions` today.

Do not widen to `Versions::targets()`: an extension declaring `^13.4 || ^14.3`
has no single domain answer, and a two-major answer for a question whose whole
point is one withheld string is a different decision than the one D-DIS-4 took.
The constant itself is settled and guarded — `VersionsTest` and
`bin/cli catalog:check` both hold it — so this todo is the caller's half only.
