# `typo3_changelog_lookup`

Search the TYPO3 changelog of the installation you are working in: one entry per
breaking change, deprecation, feature and important note, in the version it was
released in. Answers "what did this version deprecate", "what changed about X",
"which release introduced Y". This is the first stop when building on a major
you have not built on recently: what separates a current answer from a
two-major-old one is written down here and almost nowhere else. A deprecation
carries the version it stops working in where the entry states one, and the rule
that answers the rest beside it. Read from the core package on disk, so it
covers exactly the versions that installation ships and grows with a Composer
update. Every word of the query has to be carried by an entry; narrow further
with type and version. A method or class you found in the code is a query of its
own: an identifier reaches the entries naming it, whether or not the change was
titled after it. Answers from: packages.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`packages`](answer-sources.md#packages).

## Takes

```yaml
# Words the entry has to carry, matched against its file name and the words that
# name spells. Where no entry carries all of them by name, the title stated
# inside the file is searched as well, which reaches a method name the file name
# leaves out; and a class, method or constant name reaches the entries that
# write it in their text, so a removed API can be asked for by the identifier
# you have, in any spelling of it: bare, qualified by its class, or fully
# qualified. When nothing carries all of them there either, the answer names the
# largest part of the query that does reach entries, which is what to ask again
# with. Omit to list a version or a type as a whole.
query: string  # optional
# One of: breaking, deprecation, feature, important. Restrict to one kind of
# change. Breaking and deprecation are what affects existing code.
type: string  # optional
# Restrict to a version, by prefix: "14" covers 14.0 through 14.3.x, "13.4"
# covers 13.4 and 13.4.x.
version: string  # optional
# Restrict to entries carrying this index tag: "ext:form" for the system
# extension a change is in, "FullyScanned" or "NotScanned" for what the
# Extension Scanner has a matcher for, "PHP-API", "TCA", "Backend", "Frontend"
# for the surface. This is what a sweep is bounded by where words are not: every
# entry of a version and type is read for its tags. The changelog says nothing
# about which third-party extension a change affects, so an extension key of
# your own matches no tag.
tag: string  # optional
# Maximum number of entries.
limit: integer  # optional
```

## Answers with

```yaml
query: string
# Entries carrying every word of the query and the tag, before the limit.
matchCount: integer  # optional
# Where the query was carried: "name" for the entry names, "body" where no name
# carried it and the inside of the file did — the title as it is stated, or an
# identifier the text writes. A body match can name the identifier without being
# about it, so read the title of each. Returned where the answer carries
# entries.
matchedIn: string  # optional
# Every index tag the entries of this version and type carry, with the ones
# already filtered by among them. Returned where a tag was asked for, so a tag
# that matched nothing can be replaced by one that exists.
tags: [string]  # optional
entries:  # optional
  - # One of: Breaking, Deprecation, Feature, Important.
    type: string
    # The version directory it was released in.
    version: string
    # Forge issue number.
    issue: string
    title: string
    # The version a Deprecation states the deprecated thing stops working in —
    # what an upgrade decides on. Empty on the other three types, and on a
    # deprecation whose entry states none, which is most of a major and is not
    # "no removal planned": removalRule is what answers it there.
    removal: string
    # Index tags. FullyScanned or PartiallyScanned means the extension scanner
    # has a matcher for it.
    tags: [string]
    # EXT: reference of the entry, to read the description and the migration.
    file: string
# What each word of the query reaches on its own, inside the version and the
# type that were asked for. A word at 0 is the one that emptied the answer —
# it is misspelled, or nothing here is named after it. Returned on a miss that
# carried words. These are counts and not a query: termSubsets is what can be
# asked outright.
termCounts:  # optional
  - # The word, lowercased as it was searched for.
    term: string
    matchCount: integer
# The same words counted over the whole changelog rather than inside the version
# and the type. Returned only where a word reaches there and nothing inside the
# narrowing, which makes the filter what emptied this answer rather than the
# words: ask again without it.
termCountsWithoutTheNarrowing:  # optional
  - # The word, lowercased as it was searched for.
    term: string
    matchCount: integer
# The largest parts of the query that do reach entries, narrowest first —
# every one of them, because the one a tie-break puts first is not always the
# one being looked for. Withheld where a tag was asked for: these are counted
# off the entry names and a tag is read inside the file, so a subset offered
# there would promise entries the same call does not return.
termSubsets:  # optional
  - # Words of the query, as a query to ask again with.
    terms: [string]
    # Entries carrying every word of this subset, inside the same version and
    # type.
    matchCount: integer
# When a deprecation stops working where the entry itself does not say. Returned
# where the answer carries a deprecation.
removalRule: string  # optional
# The versions this installation ships changelog entries for, newest first.
# Anything outside them is not in this answer.
versions: [string]  # optional
# One of: packages. packages: read from the files the installed packages ship,
# because the console could not be asked — overrides applied at runtime are
# not reflected.
answeredBy: string  # optional
unsupported:  # optional
  # One of: no-installation, misconfigured, installation-not-answering.
  # no-installation: nothing to ask from here, and searched says where it
  # looked. misconfigured: an installation was named and could not be used, so
  # nothing was searched for. installation-not-answering: one was found and its
  # console did not answer — a stopped container or a database with no schema,
  # which is a state that ends without reinstalling anything.
  cause: string
  # What stopped it, in the words the attempt produced.
  reason: string
  # What the reason means where the message alone does not say it — a console
  # that starts and then fails on a missing table has a database without a
  # schema, not a broken installation. Empty where nothing beyond the reason is
  # known.
  diagnosis: string  # optional
  # Every directory the discovery walked, in order. "Nothing was found" and "the
  # server was started somewhere else" wear one sentence, and only this tells
  # them apart. Empty where discovery never ran.
  searched: [string]
  # What was set and could not be used. Null where nothing was set.
  misconfiguration: string or null  # optional
  settings:
    # Environment variable that names the installation root.
    root: string
    # Environment variable that names the console command.
    console: string
```

The answer carries exactly one of these sets of fields: `query`, `matchCount`,
`entries`, `versions`, `answeredBy` — or `query`, `unsupported`.

## Answered

Recorded on 2026-08-03 by `bin/cli tools:record`. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.0, the
installation this repository writes below .fixtures/, whose console answers.
The tools that declare `answeredBy` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks what is below this heading; everything above it is
derived from the class that answers the call, and `bin/cli tools:check` holds
it.

### changelog: hit

Called with:

```json
{
    "query": "ext_tables.php"
}
```

#### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
1 changelog entry carrying "ext_tables.php":
- 14.3 Deprecation: ext_tables.php in extensions (#109438) — removed in v15.0
  EXT:core/Documentation/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.rst — PHP-API, NotScanned, ext:core

Read the file for the description and the migration. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.
```

Data:

```json
{
    "query": "ext_tables.php",
    "matchCount": 1,
    "matchedIn": "name",
    "tags": [],
    "entries": [
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "109438",
            "title": "ext_tables.php in extensions",
            "removal": "15.0",
            "tags": [
                "PHP-API",
                "NotScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.rst"
        }
    ],
    "versions": [
        "14.3",
        "14.3.x",
        "14.2",
        "14.1",
        "14.0",
        "13.4",
        "13.4.x",
        "13.3",
        "13.2",
        "13.1",
        "13.0",
        "12.4",
        "12.4.x",
        "12.3",
        "12.2",
        "12.1",
        "12.0",
        "11.5",
        "11.5.x",
        "11.4",
        "11.3",
        "11.2",
        "11.1",
        "11.0",
        "10.4",
        "10.4.x",
        "10.3",
        "10.2",
        "10.1",
        "10.0",
        "9.5",
        "9.5.x",
        "9.4",
        "9.3",
        "9.2",
        "9.1",
        "9.0",
        "8.7",
        "8.7.x",
        "8.6",
        "8.5",
        "8.4",
        "8.3",
        "8.2",
        "8.1",
        "8.0",
        "7.6",
        "7.6.x",
        "7.5",
        "7.4",
        "7.3",
        "7.2",
        "7.1",
        "7.0"
    ],
    "answeredBy": "packages",
    "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
}
```

#### From the installation this repository writes below .fixtures/, whose console answers

Text:

```
1 changelog entry carrying "ext_tables.php":
- 14.3 Deprecation: ext_tables.php in the fixture extension (#900001) — removed in v15.0
  EXT:core/Documentation/Changelog/14.3/Deprecation-900001-ExtTablesPhpInTheFixtureExtension.rst — PHP-API, FullyScanned, ext:acme_events

Read the file for the description and the migration. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.
```

Data:

```json
{
    "query": "ext_tables.php",
    "matchCount": 1,
    "matchedIn": "name",
    "tags": [],
    "entries": [
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "900001",
            "title": "ext_tables.php in the fixture extension",
            "removal": "15.0",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:acme_events"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-900001-ExtTablesPhpInTheFixtureExtension.rst"
        }
    ],
    "versions": [
        "14.3"
    ],
    "answeredBy": "packages",
    "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
}
```

### changelog: swept by tag

Called with:

```json
{
    "type": "deprecation",
    "tag": "FullyScanned"
}
```

#### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
384 of the 964 entries narrowed by version and type are tagged "FullyScanned" — showing the first 20:
- 14.3 Deprecation: Lowlevel DatabaseIntegrityCheck class (#107931) — removed in v15.0
  EXT:core/Documentation/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.rst — PHP-API, FullyScanned, ext:lowlevel
- 14.3 Deprecation: BackendUtility item list label methods (#109519) — removed in v15.0
  EXT:core/Documentation/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.rst — PHP-API, FullyScanned, ext:backend
- 14.3 Deprecation: GeneralUtility::isOnCurrentHost() without PSR-7 request (#109523)
  EXT:core/Documentation/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.rst — PHP-API, FullyScanned, ext:core
- 14.3 Deprecation: GeneralUtility::sanitizeLocalUrl() needs PSR-7 request (#109544)
  EXT:core/Documentation/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.rst — PHP-API, FullyScanned, ext:core
- 14.3 Deprecation: GeneralUtility::locationHeaderUrl() without PSR-7 request (#109548)
  EXT:core/Documentation/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.rst — PHP-API, FullyScanned, ext:core
- 14.3 Deprecation: GeneralUtility::getIndpEnv() (#109551)
  EXT:core/Documentation/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.rst — PHP-API, FullyScanned, ext:core
- 14.2 Deprecation: BackendUserAuthentication::recordEditAccessInternals() and $errorMsg (#108568)
  EXT:core/Documentation/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.rst — PHP-API, FullyScanned, ext:core
- 14.2 Deprecation: BackendUtility TSconfig-related methods (#108761) — removed in v15.0
  EXT:core/Documentation/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.rst — PHP-API, FullyScanned, ext:backend
- 14.2 Deprecation: BackendUtility localization-related methods (#108810) — removed in v15.0
  EXT:core/Documentation/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.rst — PHP-API, FullyScanned, ext:backend
- 14.2 Deprecation: ExtensionManagementUtility::addFieldsToUserSettings (#108843) — removed in v15.0
  EXT:core/Documentation/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.rst — PHP-API, FullyScanned, ext:core
- 14.2 Deprecation: Deprecate `PageRenderer->addInlineLanguageDomain()` (#108963)
  EXT:core/Documentation/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.rst — Backend, JavaScript, FullyScanned, ext:backend
- 14.2 Deprecation: Move `language:update` command and events to `EXT:core` (#109027) — removed in v15
  EXT:core/Documentation/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.rst — CLI, PHP-API, FullyScanned, ext:install
- 14.2 Deprecation: FormResultCompiler (#109230) — removed in v15
  EXT:core/Documentation/Changelog/14.2/Deprecation-109230-FormResultCompiler.rst — Backend, FullyScanned, ext:backend
- 14.2 Deprecation: TypoScript-based form YAML registration (#109412) — removed in v15.0
  EXT:core/Documentation/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.rst — YAML, Frontend, Backend, FullyScanned, ext:form
- 14.1 Deprecation: Fluid namespaces in TYPO3_CONF_VARS (#108524)
  EXT:core/Documentation/Changelog/14.1/Deprecation-108524-FluidNamespacesInTYPO3_CONF_VARS.rst — Fluid, LocalConfiguration, FullyScanned, ext:fluid
- 14.1 Deprecation: Deprecate CommandNameAlreadyInUseException (#108667)
  EXT:core/Documentation/Changelog/14.1/Deprecation-108667-DeprecateCommandNameAlreadyInUseException.rst — PHP-API, FullyScanned, ext:core
- 14.0 Deprecation: Various methods in BackendUtility (#106393)
  EXT:core/Documentation/Changelog/14.0/Deprecation-106393-VariousMethodsInBackendUtility.rst — TCA, FullyScanned, ext:core
- 14.0 Deprecation: GeneralUtility::resolveBackPath (#106618) — removed in v15.0
  EXT:core/Documentation/Changelog/14.0/Deprecation-106618-GeneralUtilityresolveBackPath.rst — Backend, Frontend, JavaScript, TypoScript, FullyScanned, ext:core
- 14.0 Deprecation: Move upgrade wizard related interfaces and attribute to `EXT:core` (#106947)
  EXT:core/Documentation/Changelog/14.0/Deprecation-106947-MoveUpgradeWizardRelatedInterfacesAndAttributeToEXTcore.rst — PHP-API, FullyScanned, ext:install
- 14.0 Deprecation: ExtensionManagementUtility::addPiFlexFormValue() (#107047) — removed in v15.0
  EXT:core/Documentation/Changelog/14.0/Deprecation-107047-ExtensionManagementUtilityAddPiFlexFormValue.rst — Backend, FlexForm, TCA, FullyScanned, ext:core

Read the file for the description and the migration. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.
```

Data:

```json
{
    "query": "",
    "matchCount": 384,
    "matchedIn": "name",
    "tags": [
        "Backend",
        "CLI",
        "Database",
        "FAL",
        "FileList",
        "FlexForm",
        "Fluid",
        "Frontend",
        "FullyScanned",
        "JavaScript",
        "LocalConfiguration",
        "NotScanned",
        "PHP-API",
        "PartiallyScanned",
        "RTE",
        "Scheduler",
        "TCA",
        "TSConfig",
        "TypoScript",
        "YAML",
        "ext:adminpanel",
        "ext:backend",
        "ext:core",
        "ext:css_styled_content",
        "ext:dashboard",
        "ext:dbal",
        "ext:extbase",
        "ext:extensionmanager",
        "ext:feedit",
        "ext:felogin",
        "ext:filelist",
        "ext:fluid",
        "ext:fluid_styled_content",
        "ext:form",
        "ext:frontend",
        "ext:impexp",
        "ext:indexed_search",
        "ext:info",
        "ext:install",
        "ext:lang",
        "ext:linkvalidator",
        "ext:lowlevel",
        "ext:recordlist",
        "ext:recycler",
        "ext:redirects",
        "ext:reports",
        "ext:rsaauth",
        "ext:rte_ckeditor",
        "ext:saltedpasswords",
        "ext:scheduler",
        "ext:setup",
        "ext:t3editor",
        "ext:taskcenter",
        "ext:tstemplate",
        "ext:workspaces"
    ],
    "entries": [
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "107931",
            "title": "Lowlevel DatabaseIntegrityCheck class",
            "removal": "15.0",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:lowlevel"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "109519",
            "title": "BackendUtility item list label methods",
            "removal": "15.0",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:backend"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "109523",
            "title": "GeneralUtility::isOnCurrentHost() without PSR-7 request",
            "removal": "",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "109544",
            "title": "GeneralUtility::sanitizeLocalUrl() needs PSR-7 request",
            "removal": "",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "109548",
            "title": "GeneralUtility::locationHeaderUrl() without PSR-7 request",
            "removal": "",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "109551",
            "title": "GeneralUtility::getIndpEnv()",
            "removal": "",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.2",
            "issue": "108568",
            "title": "BackendUserAuthentication::recordEditAccessInternals() and $errorMsg",
            "removal": "",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.2",
            "issue": "108761",
            "title": "BackendUtility TSconfig-related methods",
            "removal": "15.0",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:backend"
            ],
            "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.2",
            "issue": "108810",
            "title": "BackendUtility localization-related methods",
            "removal": "15.0",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:backend"
            ],
            "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.2",
            "issue": "108843",
            "title": "ExtensionManagementUtility::addFieldsToUserSettings",
            "removal": "15.0",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.2",
            "issue": "108963",
            "title": "Deprecate `PageRenderer->addInlineLanguageDomain()`",
            "removal": "",
            "tags": [
                "Backend",
                "JavaScript",
                "FullyScanned",
                "ext:backend"
            ],
            "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.2",
            "issue": "109027",
            "title": "Move `language:update` command and events to `EXT:core`",
            "removal": "15",
            "tags": [
                "CLI",
                "PHP-API",
                "FullyScanned",
                "ext:install"
            ],
            "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.2",
            "issue": "109230",
            "title": "FormResultCompiler",
            "removal": "15",
            "tags": [
                "Backend",
                "FullyScanned",
                "ext:backend"
            ],
            "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-109230-FormResultCompiler.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.2",
            "issue": "109412",
            "title": "TypoScript-based form YAML registration",
            "removal": "15.0",
            "tags": [
                "YAML",
                "Frontend",
                "Backend",
                "FullyScanned",
                "ext:form"
            ],
            "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.1",
            "issue": "108524",
            "title": "Fluid namespaces in TYPO3_CONF_VARS",
            "removal": "",
            "tags": [
                "Fluid",
                "LocalConfiguration",
                "FullyScanned",
                "ext:fluid"
            ],
            "file": "EXT:core/Documentation/Changelog/14.1/Deprecation-108524-FluidNamespacesInTYPO3_CONF_VARS.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.1",
            "issue": "108667",
            "title": "Deprecate CommandNameAlreadyInUseException",
            "removal": "",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.1/Deprecation-108667-DeprecateCommandNameAlreadyInUseException.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.0",
            "issue": "106393",
            "title": "Various methods in BackendUtility",
            "removal": "",
            "tags": [
                "TCA",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.0/Deprecation-106393-VariousMethodsInBackendUtility.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.0",
            "issue": "106618",
            "title": "GeneralUtility::resolveBackPath",
            "removal": "15.0",
            "tags": [
                "Backend",
                "Frontend",
                "JavaScript",
                "TypoScript",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.0/Deprecation-106618-GeneralUtilityresolveBackPath.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.0",
            "issue": "106947",
            "title": "Move upgrade wizard related interfaces and attribute to `EXT:core`",
            "removal": "",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:install"
            ],
            "file": "EXT:core/Documentation/Changelog/14.0/Deprecation-106947-MoveUpgradeWizardRelatedInterfacesAndAttributeToEXTcore.rst"
        },
        {
            "type": "Deprecation",
            "version": "14.0",
            "issue": "107047",
            "title": "ExtensionManagementUtility::addPiFlexFormValue()",
            "removal": "15.0",
            "tags": [
                "Backend",
                "FlexForm",
                "TCA",
                "FullyScanned",
                "ext:core"
            ],
            "file": "EXT:core/Documentation/Changelog/14.0/Deprecation-107047-ExtensionManagementUtilityAddPiFlexFormValue.rst"
        }
    ],
    "versions": [
        "14.3",
        "14.3.x",
        "14.2",
        "14.1",
        "14.0",
        "13.4",
        "13.4.x",
        "13.3",
        "13.2",
        "13.1",
        "13.0",
        "12.4",
        "12.4.x",
        "12.3",
        "12.2",
        "12.1",
        "12.0",
        "11.5",
        "11.5.x",
        "11.4",
        "11.3",
        "11.2",
        "11.1",
        "11.0",
        "10.4",
        "10.4.x",
        "10.3",
        "10.2",
        "10.1",
        "10.0",
        "9.5",
        "9.5.x",
        "9.4",
        "9.3",
        "9.2",
        "9.1",
        "9.0",
        "8.7",
        "8.7.x",
        "8.6",
        "8.5",
        "8.4",
        "8.3",
        "8.2",
        "8.1",
        "8.0",
        "7.6",
        "7.6.x",
        "7.5",
        "7.4",
        "7.3",
        "7.2",
        "7.1",
        "7.0"
    ],
    "answeredBy": "packages",
    "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
}
```

#### From the installation this repository writes below .fixtures/, whose console answers

Text:

```
1 of the 2 entries narrowed by version and type are tagged "FullyScanned":
- 14.3 Deprecation: ext_tables.php in the fixture extension (#900001) — removed in v15.0
  EXT:core/Documentation/Changelog/14.3/Deprecation-900001-ExtTablesPhpInTheFixtureExtension.rst — PHP-API, FullyScanned, ext:acme_events

Read the file for the description and the migration. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.
```

Data:

```json
{
    "query": "",
    "matchCount": 1,
    "matchedIn": "name",
    "tags": [
        "FullyScanned",
        "NotScanned",
        "PHP-API",
        "ext:acme_events"
    ],
    "entries": [
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "900001",
            "title": "ext_tables.php in the fixture extension",
            "removal": "15.0",
            "tags": [
                "PHP-API",
                "FullyScanned",
                "ext:acme_events"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-900001-ExtTablesPhpInTheFixtureExtension.rst"
        }
    ],
    "versions": [
        "14.3"
    ],
    "answeredBy": "packages",
    "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
}
```

### changelog: miss

Called with:

```json
{
    "query": "quantumflux"
}
```

#### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
No changelog entry in this installation carries all of "quantumflux".
The changelog here covers 14.3, 14.3.x, 14.2, 14.1, 14.0, 13.4, 13.4.x, 13.3 and older. A version this installation does not ship is not in it — read that one in the core repository or at https://docs.typo3.org.
```

Data:

```json
{
    "query": "quantumflux",
    "matchCount": 0,
    "tags": [],
    "entries": [],
    "versions": [
        "14.3",
        "14.3.x",
        "14.2",
        "14.1",
        "14.0",
        "13.4",
        "13.4.x",
        "13.3",
        "13.2",
        "13.1",
        "13.0",
        "12.4",
        "12.4.x",
        "12.3",
        "12.2",
        "12.1",
        "12.0",
        "11.5",
        "11.5.x",
        "11.4",
        "11.3",
        "11.2",
        "11.1",
        "11.0",
        "10.4",
        "10.4.x",
        "10.3",
        "10.2",
        "10.1",
        "10.0",
        "9.5",
        "9.5.x",
        "9.4",
        "9.3",
        "9.2",
        "9.1",
        "9.0",
        "8.7",
        "8.7.x",
        "8.6",
        "8.5",
        "8.4",
        "8.3",
        "8.2",
        "8.1",
        "8.0",
        "7.6",
        "7.6.x",
        "7.5",
        "7.4",
        "7.3",
        "7.2",
        "7.1",
        "7.0"
    ],
    "answeredBy": "packages",
    "termCounts": [
        {
            "term": "quantumflux",
            "matchCount": 0
        }
    ]
}
```

#### From the installation this repository writes below .fixtures/, whose console answers

Text:

```
No changelog entry in this installation carries all of "quantumflux".
The changelog here covers 14.3 and older. A version this installation does not ship is not in it — read that one in the core repository or at https://docs.typo3.org.
```

Data:

```json
{
    "query": "quantumflux",
    "matchCount": 0,
    "tags": [],
    "entries": [],
    "versions": [
        "14.3"
    ],
    "answeredBy": "packages",
    "termCounts": [
        {
            "term": "quantumflux",
            "matchCount": 0
        }
    ]
}
```
