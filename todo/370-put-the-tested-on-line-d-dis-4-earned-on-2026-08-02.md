# Put the Tested on line D-DIS-4 earned on 2026-08-02 into it

**Serves:** decisions/

The reading is done and the guards are committed; what is missing is the line at
the foot of `decisions/discovery/dis-4-...-not-from-the-console.md` and the
`status: tested` in its head. It is separated out for one reason only: the status
is part of the generated listing under `decisions/readme.md` and
`decisions/discovery/readme.md`, and the session that did the reading was working
a todo that may touch neither listing by hand or by command. Whoever takes this
one either owns the index run or is merging anyway, and it is a two-minute edit
plus `bin/cli decisions:index` — `DecisionsTest::everyGroupListsWhatIsInIt` fails
until that runs, which is the whole reason this is its own file.

What was established on 2026-08-02, so it does not have to be re-derived: the
constant is still right. In the checkouts at `bin/cli checkouts:update` state of
2026-08-01, 12.4 (HEAD `31f881a2`) and 13.4 (HEAD `1d104f3b`) carry no
`TranslationDomain*` class anywhere below `typo3/sysext/`, while 14.3 (`faf60eea`)
and main (`c71b2bdb`) both carry `TranslationDomainMapper` and
`TranslationDomainResolver` in `core/Classes/Localization/`, and the feature is
filed as `Changelog/14.0/Feature-93334-TranslationDomainMapping.rst`. So
`TranslationDomainLookup::SINCE = 14` holds and the backport has not happened.
The first **Wrong if** is no longer a promise, in two halves: `bin/cli
catalog:check` asks every covered checkout for a `TranslationDomain*` class and
fails where the major it derives is not the constant — the only place a backport
is visible, being a release rather than an edit here — and `VersionsTest` ties
the constant to the majors `knowledge/versions.json` declares, so a covers list
that stops carrying a major below it fails rather than leaving the withheld
answer addressed to versions nothing covers. The constant was made public for
those two readers and is still one number in one place.

The second **Wrong if** is live, and reading for the first one is what showed it:
`TranslationDomainLookup` asks `Instance::typo3Major()` directly and takes `path`
alone, while `SystemExtensionLookup`, `TestRunGuide`, `CatalogScope` and
`DocumentationLookup` all take a `targetVersion` through `Versions::target()`.
That is a **Since then** line rather than a correction, and the feature behind it
is queued separately as `360-let-the-translation-domain-answer-be-asked-for-a`.
