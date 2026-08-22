---
id: D-ANS-015
date: 2026-08-02
status: open
---

# D-ANS-015 — A registration the extension answer misreads is inside its boundary, not evidence about where it runs

**`typo3_extension_describe` answers the registrations an extension carries, and
one it reports wrongly or never reaches is a defect inside that boundary rather
than an argument about where the boundary runs.**

A conformance audit reports eight things it had to establish elsewhere and draws
the line itself: registration is answered, implementation is read from the
checkout. Six of the eight are on the implementation side. Two are
registrations.

## Evidence

- `feedback/2026-07-31-193109`, re-run on 2026-08-02 through
  `bin/typo3-dev-companion` from `/home/benji/projects/site-new`, the directory
  it was written in. `.mcp.json` there names this repository's binary.
- Six costs are the contents of files the caller already has open: TCA,
  TypoScript, Fluid, controllers, repositories, eight test classes,
  `Initialisation/data.xml`, and a `PageTitleProvider` the extension does not
  have. The answer names `Initialisation/data.xml` as a registration file and
  says nothing about the uids in it, which is the line the feedback itself calls
  reasonable.
- The `PageTitleProvider` rule was delivered.
  `knowledge/architecture-hints/general.json`, hint `frontend-records`, states
  that the `<title>` of such a detail view is a `PageTitleProvider` and nothing
  else, and `feedback/2026-07-31-193005` records `typo3_hint_lookup` returning
  that hint on both of the session's calls. What no lookup states is that this
  extension has none, which is the audit's.
- The PHP number is
  [`D-ANS-011`](ans-011-a-scope-answer-states-what-a-manifest-declares.md), from
  the same directory 25 minutes earlier. This one pairs the project's declared
  `^8.4` against a runtime 8.3.23 read by bash, which is declared against
  effective; `feedback/2026-07-31-193611` carries that half and has a card of
  its own.
- The German-label claim does not hold.
  `Configuration/Sets/Printworks/settings.definitions.yaml` carries English
  labels throughout — "Company details", "Template root path" — and last changed
  on 2026-07-30 11:39, a day before the report. The XLF half is answered: the
  extension answer names all three files with "source-language de, no
  translations beside it", shipped in `fc80db8` (2026-07-31 02:08).
- `printworkssitepackage_catalogue` and `printworkssitepackage_teaser` are
  Extbase plugins. `Configuration/TCA/Overrides/tt_content.php` registers them
  with `ExtensionUtility::registerPlugin()`, `ext_localconf.php` configures
  them, and `products.typoscript` sets
  `plugin.tx_printworkssitepackage_catalogue < lib.printworksPlugin` and nothing
  under `tt_content.`. The answer lists both under "Content elements it adds" as
  "no templateName in this extension's TypoScript; another extension or the site
  may set it". A plugin renders through the dispatcher and has no `templateName`
  to be missing, so the feedback's finding #3 — `Catalogue.typoscript` and
  `Teaser.typoscript` do not exist — is two files nothing was ever going to
  write.
- `Configuration/Form/Printworks/config.yaml` is reached by nothing.
  `Extension::ROOT_FILES` is a fixed list of paths, and since v14.2 (#109412) a
  form set is discovered by its directory: `FormYamlCollectorConfigurator`
  collects `Configuration/Form/<SetName>/` in `.checkouts/14.3` and
  `.checkouts/main`, and the class exists in neither `.checkouts/13.4` nor
  `.checkouts/12.4`. Site sets are already answered from a convention of that
  same shape.
- All four extension files above last changed on 2026-07-29 and 2026-07-30, so
  the reading is of the state the session saw, not of one that moved under it.

## Decided

- The six implementation costs are closed. The strength the feedback states —
  registration and runtime metadata are answered well — is read as evidence
  about this boundary rather than as a confirmation of the conformance skill,
  which is
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md).
  Nothing was added to `D-SKL-001` or `D-SKL-002`.
- The two registration costs are queued, one todo each, and the feedback is
  trimmed to them. Both touch `src/` and both need something about TYPO3 looked
  up, which is what [judging.md](../../documentation/records/judging.rst) puts
  on the far side of closing on the spot.
- The feedback's own suggestion is not taken. "Which TypoScript files are
  missing for registered CTypes" is the question that produced finding #3, and
  the catalogue case is what shows it malformed: the CType was registered, the
  file was absent, and nothing was wrong. What is queued instead is the answer
  saying which kind of registration it is looking at.
- Both queued items are the same failure `R-ANS-012` was written against — an
  absence reported as a defect the extension does not have, and a file the
  answer never opens. Neither gets a requirement of its own.

## Assumed

- That the two identifiers reach the content-element list from the booted
  installation rather than from the file parser. `Extension::cTypes()` prefers
  the runtime list and attributes by the `EXT:` reference on the label or icon,
  and `registerPlugin()` sets both; the parser path recognises `addRecordType()`
  and `addTcaSelectItem()`, which that file does not call.
- That no site set in this installation supplies a `templateName` for either
  identifier. Nothing under `Configuration/Sets/` writes one, and the answer's
  hedge — another extension or the site may set it — was checked against this
  project only.

## Wrong if

- A site can set `tt_content.<plugin>.templateName` and have it take effect on
  an Extbase plugin. The absence would then be a real one, and the first queued
  item is a wording point rather than a defect.
- An extension answer that names its form sets still leaves a session reading
  `Configuration/Form/` by hand. The cost would then have been the content of
  the file rather than its registration, and the wrong half is queued.
- A session reports reading TCA, Fluid or a test class as a gap this server
  could close, and names the answer shape it wanted. The six closed here would
  then be a boundary taken from one session's own sentence rather than from what
  these tools can say.

## Since then

The first queued item is done, and reading it against `.checkouts/14.3` and
`.checkouts/13.4` fired the first **Wrong if** on its predicate while leaving
its conclusion standing. A site can set `tt_content.<plugin>.templateName` and
have it take effect: `configurePlugin()` puts the plugin's CType on
`lib.contentElement` with `templateName = Generic`, so the value is the
wrapper's template and overriding it is meaningful. The absence in this
extension's TypoScript is still no gap, because core has already set what the
extension does not — which is why the item was a defect and not the wording
point this entry allowed for. What was built on that reading is
[`D-ANS-018`](ans-018-a-plugin-is-a-kind-of-content-element-not-one-whose-template-is-missing.md).

The second **Assumed** was not needed. The kind is read from the
`registerPlugin()` call in the override file rather than inferred from what the
CType list does not carry, so how the two identifiers reached that list no
longer decides whether they are told apart. The queued form-set item is
untouched.
