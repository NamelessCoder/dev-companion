---
id: D-ANS-018
title: 'A plugin is a kind of content element'
date: 2026-08-02
status: confirmed
coveredBy:
  - ProjectTest::anExtbasePluginIsToldApartFromAnElementWithoutATemplate
  - ProjectTest::aPluginTheInstallationReportsIsStillToldApart
---

# D-ANS-018 — A plugin is a kind of content element

**`typo3_extension_describe` says which kind of registration a content element
is, and an Extbase plugin points at `plugin.tx_<identifier>` where an element
points at a `templateName`.**

Both are items of `tt_content.CType`, so one list holds them and the identifier
alone tells them apart from nothing. Reporting a plugin as an element with no
`templateName` cost a real audit a finding about two TypoScript files nobody was
ever going to write.

## Evidence

- `ExtensionUtility::configurePlugin()` generates the rendering definition
  itself, in `.checkouts/14.3` and in `.checkouts/13.4` alike:
  `tt_content.<signature> =< lib.contentElement`, `templateName = Generic`, and
  `20 = EXTBASEPLUGIN` with the extension and plugin name below it, added to the
  setup after `defaultContentRendering`. So the answer's "no templateName in
  this extension's TypoScript" was true of the extension and false of the
  element.
- `Generic.fluid.html` in `.checkouts/14.3` — `Generic.html` in
  `.checkouts/13.4`, the same body — renders `{content}` where there is any and
  otherwise `<f:cObject typoscriptObjectPath="tt_content.{data.CType}.20">`.
  That cObject is the `EXTBASEPLUGIN`, which is what "renders through the
  dispatcher" means concretely.
- `tt_content.<identifier>.templateName` therefore does reach a plugin: it is
  the wrapper's template, core sets it to `Generic`, and one set over it has to
  render `tt_content.<identifier>.20` or `{content}` or the plugin's own output
  is gone. That is the first **Wrong if** of
  [`D-ANS-015`](ans-015-a-registration-the-extension-answer-misreads-is-inside-its-boundary.md)
  firing on its predicate and not on its conclusion — the absence is still no
  gap, because what the extension does not set core has already set.
- The plugin's own templates come from `plugin.tx_<signature>.view`.
  `FrontendConfigurationManager::getPluginConfiguration()` reads
  `plugin.tx_<strtolower(extensionName)>` merged with
  `plugin.tx_<strtolower(extensionName . '_' . pluginName)>` in both checkouts,
  and that second string is the same signature `registerPlugin()` writes into
  the CType column. One identifier addresses both places, so nothing has to be
  derived to name the second.
- `registerPlugin()` can only stand in `Configuration/TCA/Overrides/`, which is
  the directory this answer already parses:
  `ExtensionManagementUtility::addPlugin()` below it needs the extension key an
  override file passes and throws with 1404068038 anywhere else, and
  `.checkouts/13.4` says so in the message.
- On `.checkouts/14.3` `addPlugin()` writes to `CType` and nothing else
  (#105538). On `.checkouts/13.4` it writes to `list_type` unless
  `configurePlugin()` recorded `CType`, which is #105076 deprecating that form
  for removal in 14.0.

## Decided

- The `contentElements` entry carries `kind` — `element` or `plugin` — and, on a
  plugin, `pluginSettings`: the file of this extension's TypoScript that
  configures `plugin.tx_<identifier>`. That is what replaces the absence,
  because it is the file the caller was looking for when they went looking for a
  template.
- The kind is read from the `registerPlugin()` call rather than asked of the
  booted installation. The call is in a file this answer already tokenises and
  core allows it nowhere else, so a probe topic would have bought the same fact
  at the price of a boot. It also classifies on the installation path, where the
  CType list is one list for every extension and says nothing about how an entry
  arrived in it.
- A plugin signature read that way joins `contentElements`, which lengthens the
  answer on a checkout that does not boot: `registerPlugin()` was in neither
  recognised call before, so two registered plugins were previously absent from
  the file-read answer altogether.
- The `list_type` residue is said in prose rather than branched on by version.
  One sentence names it, `ext_localconf.php` and the version it went away in; a
  `Instance::typo3Major()` branch would have put a second shape of this answer
  into the code for a form that is gone in the newest covered major.
- `typoScriptValues()` now holds reference lines — `plugin.tx_x < lib.y` — and
  not only assignments. A path that is only ever referenced was invisible to a
  store of `=` lines, and that is the shortest way an extension configures a
  plugin at all.

## Assumed

- That `plugin.tx_<identifier>` is where the caller's next question goes, rather
  than the controller and action list. `configurePlugin()` in
  `ext_localconf.php` holds that list and is read by nothing here, so the answer
  names the file it can name.
- That a plugin whose `registerPlugin()` arguments are not literals is rare
  enough to leave to the general rule. Such an identifier is reported as an
  element again, with the `templateName` sentence that was wrong for the two in
  the audit.

## Wrong if

- A session reads `pluginSettings: null` as "this plugin is unconfigured". It
  says nothing about the site's TypoScript or another extension's, the same way
  `templateName` never did.
- An installation on 13.4 or older reports a `list_type` plugin among the
  content elements of an extension. The identifier is then in the answer as a
  CType it is not, and the prose sentence is where a reader has to catch it.
- An extension registers a plugin from a loop or a constant and the answer calls
  it an element with no template. The classification would then need the runtime
  after all, and
  `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']` is where the
  probe would read it.
- A caller wanted the Fluid template file and `plugin.tx_<identifier>.view` did
  not get them there — because the path is set in a site set, or because the
  controller and action decide the template name. The pointer would then be one
  step short of the answer.

## Confirmed on 2026-08-23

The split holds and none of the four **Wrong if** has been reported. A plugin
still answers `kind: plugin` with `pluginSettings` beside it and an element
answers with a `templateName`, and
`ProjectTest::anExtbasePluginIsToldApartFromAnElementWithoutATemplate`
holds both halves.

The second is the one this entry could only answer in prose, and the prose is
there: the plugin paragraph of the rendered answer says that before 14.0
`configurePlugin()` could register a plugin under `list_type` rather than as its
own CType, and that the call is in `ext_localconf.php`, which nothing here
reads. So a `list_type` plugin on an older installation is named as the thing
the answer cannot see rather than left to a reader to catch.

Nothing in `feedback/` since this was written mentions `pluginSettings` or
`plugin.tx_`. The two that do are `2026-07-29-234316` and `2026-07-31-193109`,
both older than the entry, and the second is the audit it was written from.

What a reading here cannot settle is the fourth: whether
`plugin.tx_<identifier>.view` got a caller to the template file. Only a session
that followed the pointer can say, and none has reported doing so.
