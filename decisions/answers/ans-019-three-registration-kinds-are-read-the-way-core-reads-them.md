---
id: D-ANS-019
title: 'Three registration kinds are read the way core reads them'
date: 2026-08-02
status: confirmed
coveredBy:
  - ProjectTest::aFlexFormBoundThroughACallThisDoesNotReadIsStillReported
  - ProjectTest::aFormSetIsAnsweredWithTheDefinitionsItStores
  - ProjectTest::aSiteSetIsAnsweredByTheFilesCoreReadsItFor
  - ProjectTest::theFlexFormAContentElementBindsIsOnItsEntry
---

# D-ANS-019 — Three registration kinds are read the way core reads them

**Each of the three registrations `R-ANS-014` names is read by what core reads
it by, and each of the three moved inside the covered range.**

That is four call shapes for a FlexForm binding, eight file names in a site set
directory, and two ways a form configuration is registered.

`D-ANS-014` left what each of the three actually is to this step, because naming
a fix from a single sitepackage is a copy-down. The reading is against
`.checkouts/` at 12.4, 13.4 and 14.3, and it changed the answer in two places:
the FlexForm call the feedback named is deprecated on the version the audit ran
on, and one of the three kinds does not exist at all on the oldest covered
major.

## Evidence

- A FlexForm binding is four calls, not one.
  `ExtensionManagementUtility::addPiFlexFormValue()` binds by its third argument
  on 12.4 (`ExtensionManagementUtility.php:1032`) and 13.4 (`:945`), and on 14.3
  (`:971`) it raises `E_USER_DEPRECATED`, is removed in v15, and points at two
  replacements. Both are in 14.3: `addPlugin()` takes the data structure as its
  second argument (`:921`) where 13.4's second argument is the plugin type
  (`:875`), and `ExtensionUtility::registerPlugin()` takes it as a seventh
  (`extbase/Classes/Utility/ExtensionUtility.php:119`). The fourth is the
  `columnsOverrides` assignment on `pi_flexform` the deprecation message names,
  which is what the first one writes into TCA anyway.
- The binding stands in the file's own text, and the identifier it belongs to
  usually does too. Every core caller passes the data structure as a `FILE:EXT:`
  literal. The identifier is a literal in the `columnsOverrides` and direct
  forms; in the shape core writes itself — felogin and form on 13.4 — it is the
  variable `$contentTypeName` holding what `registerPlugin()` returned, and that
  return is `strtolower($extensionName)` without its underscores plus `'_'` plus
  `strtolower($pluginName)`, composed from two arguments that do stand in the
  file, on all three majors.
- A site set directory is read for eight names beside `config.yaml`.
  `YamlSetDefinitionProvider` reads `settings.definitions.yaml`, `settings.yaml`
  and `labels.xlf` on 13.4 and 14.3, and `route-enhancers.yaml` on 14.3 only
  (`:123`) — the feature is `14.1/Feature-107837-RouteEnhancersInSiteSets.rst`.
  `page.tsconfig` is the default `pagets`, and the set directory is the default
  `typoscript` path, which `SysTemplateTreeBuilder::handleSetInclude()` reads
  `constants.typoscript`, `setup.typoscript` and `include_static_file.txt` from.
  `AbstractServiceProvider::configureSetCollector()` is the walk, at depth 1.
- Site sets do not exist on 12.4: `typo3/sysext/core/Classes/Site/Set/` is not
  in that checkout. The kind is answered where it is there and is nothing on the
  oldest covered major, which needs no binding because an extension that ships
  no such directory gets an empty list either way.
- A form configuration is registered two ways.
  `14.2/Feature-109412-FormYamlAutoDiscovery.rst` and
  `FormYamlCollectorConfigurator` collect
  `Configuration/Form/<SetName>/config.yaml` from every active package at depth
  1, identified by the `name` the file declares rather than by the directory,
  ordered by `priority` and disabled through `EXTENSIONS.form.disabledSets`.
  Before it, and still read in 14.3 with a deprecation, a YAML file is
  registered by TypoScript under `plugin.tx_form.settings.yamlConfigurations` or
  the `module.` one beside it — `Mvc/Configuration/ConfigurationManager.php:71`,
  deprecated by `14.2/Deprecation-109412`. Both files carry
  `persistenceManager.allowedExtensionPaths`, which is where the definitions
  are.

## Decided

- The list is three kinds, and each is answered where its own mechanism exists
  rather than being bound to a version. What is read is the extension's files;
  which version reads them is the installation's, and the two are said in the
  schema and the rendered answer instead — `route-enhancers.yaml` from v14.1,
  the data structure argument and the form set from v14.2.
- The FlexForm goes on the `contentElements` entry, per `R-ANS-014`. A binding
  whose identifier no entry carries is reported in `unlistedFlexForms` rather
  than dropped: the two parsers disagree, and a registration this answer read
  and then said nothing about is the silence `R-ANS-012` is written against.
- `registerPlugin()`'s return value is resolved where it is assigned to a
  variable, which extends `stringVariables()` from one shape to two. Declining
  it would leave every FlexForm bound the way core binds its own unreadable on
  the two LTS lines.
- `registerPlugin()` and `addPlugin()` are **not** added as sources of content
  element identifiers. Whether an Extbase plugin belongs in that list and how it
  is described is `D-ANS-015`'s second queued item, worked on its own branch;
  taking it here would be two sessions editing one list.
- The site set answer is the file names and not their contents. What
  `route-enhancers.yaml` enhances is the file, and a caller holding its name can
  read it; that it is there at all is what no tree walk told anybody to look
  for.
- A form storage configured as a file mount is stated as unanswerable rather
  than left out. It is a record, and nothing that reads files reaches it.
- `todo/2026-08-02-120201-a-form-set-is-a-registration-the-extension-answer-never-reaches`
  is deleted rather than left queued. It is `D-ANS-015`'s form-set half, which
  `R-ANS-014` names as one of its three, and both questions it asked are
  answered above.

## Assumed

- That a `config.yaml` declaring its own `typoscript`, `pagets` or `labels` path
  is rare enough to be a sentence rather than a field. None of the sets in the
  three checkouts declares one, and the answer says the list is the defaults.
- That an inline FlexForm is worth reporting as being there and not worth
  quoting. The XML is a document rather than a fact about the registration, and
  no core caller writes one.
- That reading `plugin.tx_form.settings.yamlConfigurations` out of the
  extension's own TypoScript is the same statement as the registration. A site
  that registers another extension's form YAML from its own setup is answered
  under that site's extension, which is where the file that says it lives.

## Wrong if

- An extension binds a FlexForm through a fifth shape and the answer reports the
  element as binding none. `unlistedFlexForms` would not catch it — nothing was
  read — and only a session that opened the override file would notice.
- A form set turns out to be registered by running rather than by its directory.
  `FormYamlCollectorConfigurator` reads `TYPO3_CONF_VARS` at
  service-instantiation time, so an `ext_localconf.php` that writes
  `disabledSets` switches a set off that this answer still lists.
- A session holding the site set's file list still opens the set directory to
  find out what is in it. The names would then have been the wrong half, and
  what was wanted is what each file declares.
- The `registerPlugin()` signature stops being composed from its first two
  arguments. Every identifier resolved through a variable would then be wrong
  rather than missing, which is the failure `R-ANS-012` exists to prevent.

## Confirmed on 2026-08-23

The three readings are where this put them and the four tests under **Covered
by** still hold them. Nothing in `feedback/` has reported any of the four
**Wrong if** — the four open ones are a JavaScript dependency update, a build
workflow and an instruction budget, none of them about a registration.

The second is the one that gained half an answer since. `disabledSets` is named
in the schema of the site set entry — the `name` field says it is what
`disabledSets` matches against — so a caller reading the data is told what
switches a set off rather than finding out that the list is longer than the
installation's. What the answer still cannot do is read an `ext_localconf.php`
that writes it, which is the boundary `D-ANS-014` draws.

The fourth is unfired and guarded twice over: `registerPlugin()` is still
composed from its first two arguments in `Extension::declarationsIn()`, and
`R-ANS-012` is what says a signature resolved through a variable is missing
rather than wrong.
