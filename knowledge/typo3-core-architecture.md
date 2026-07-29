# TYPO3 Core Architecture Hints

This document collects practical architecture hints for TYPO3 core work. Keep
entries short, conservative, and tied to established TYPO3 core patterns. Prefer
official documentation or nearby core code over broad framework advice.

## System Extension Boundaries

- Core code lives in `typo3/sysext/*`.
- Keep changes inside the owning system extension unless a cross-extension
  contract really changes.
- Reuse public APIs from another system extension instead of reaching into
  internal implementation details.
- Check extension-local tests before adding shared behavior.

## Dependency Injection and Services

- Autowiring and autoconfiguration are on for the core system extensions: their
  `Configuration/Services.yaml` sets `autowire`, `autoconfigure`, and
  `public: false` under `_defaults` and registers the whole `Classes/` tree as a
  resource. A new service under `Classes/` needs no Services.yaml entry.
- Prefer constructor injection with a readonly promoted property.
- `Services.yaml` is for what autowiring cannot resolve: a scalar or otherwise
  non-autowirable argument, an alias, a service that has to be public, or a
  factory. For a single such argument, the `#[Autowire]` attribute on the
  constructor parameter keeps the wiring in the class.
- Avoid new `GeneralUtility::makeInstance()` calls for regular service
  dependencies unless nearby core code or lifecycle constraints require it.
- Runtime service wiring changes usually need functional tests.

## Events, Hooks, and Extension Points

- A listener is registered with the `#[AsEventListener]` attribute from
  `TYPO3\CMS\Core\Attribute`, on the class or on a single method. Its arguments
  are `identifier`, `event`, `method`, `before`, and `after`, and it is
  repeatable. Autoconfiguration picks it up; do not add an `event.listener` tag
  to `Services.yaml`.
- Event classes live in `Classes/Event/` of the extension that dispatches them,
  are `final`, and are `readonly` where the payload is immutable. A listener
  that may change the outcome gets setters on the event rather than a return
  value.
- Prefer a new event over a hook. A hook is only right where the subsystem still
  has hook-based extension points.
- Event payloads should expose stable, minimal state and avoid leaking mutable
  internals.
- A PSR-14 event is public API: it needs careful naming, a changelog entry, and
  tests.

## FormEngine Data Providers

- FormEngine assembles its data through providers registered per form data group
  in `$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']` in
  `typo3/sysext/core/Configuration/DefaultConfiguration.php`. The groups are
  `tcaDatabaseRecord`, `tcaSelectTreeAjaxFieldData`, `flexFormSegment`, and
  `tcaInputPlaceholderRecord`.
- Each provider maps to an array with `depends` and `before`, both listing other
  provider classes. That graph orders the run — not the order the entries are
  written in. A provider that sees stale data is usually fixed by adding a
  dependency, not by changing the provider class.
- A provider that reads merged page TSconfig has to depend on
  `PageTsConfigMerged`, or it runs before the TSconfig it reads exists.
- `DefaultConfiguration.php` holds the defaults of `TYPO3_CONF_VARS`. An edit
  there changes behaviour in every installation, so say what changed and why in
  the patch description.
- Registering or reordering a provider is a runtime-configuration change and is
  covered by a functional test that renders the form data, not by a unit test of
  the provider in isolation.

## Backend TypeScript Modules

- TypeScript source lives in `Build/Sources/TypeScript/`.
- Generated JavaScript lives in `typo3/sysext/*/Resources/Public/JavaScript/`.
- Generated JavaScript should match the TypeScript source output.
- Prefer existing TYPO3 imports, event patterns, and custom element conventions
  used nearby.
- Run `./Build/Scripts/runTests.sh -s build`, `lintTypescript`, and relevant
  `unitJavascript` tests.

## Backend UI and Web Components

- Keep custom elements focused on rendering and interaction orchestration.
- Keep state transitions explicit and testable.
- Prefer existing TYPO3 backend UI utilities and module patterns before
  introducing new client-side infrastructure.
- Check accessibility behavior when adding controls, dialogs, drag and drop, or
  status indicators.

## Fluid Templates and ViewHelpers

- Templates, partials, and layouts live under
  `typo3/sysext/*/Resources/Private/{Templates,Partials,Layouts}/`. Which file
  extension applies is a property of the branch — `.fluid.html` where it is
  supported, plain `.html` before that — so follow the neighbouring templates in
  the same directory instead of assuming one.
- Only `f:` and `core:` are globally available. Namespaces are registered per
  extension in `Configuration/Fluid/Namespaces.php`; core registers `f:` (for
  `TYPO3Fluid\Fluid\ViewHelpers` and `TYPO3\CMS\Fluid\ViewHelpers`), `core:`
  (for `TYPO3\CMS\Core\ViewHelpers`), and `formvh:`.
- Every other namespace is declared in the template itself, the backend one
  included: `xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers"` on
  the root element, plus `data-namespace-typo3-fluid="true"` so the declaration
  is stripped from the rendered output.
- A ViewHelper is a `final` class extending `AbstractViewHelper` with
  `initializeArguments(): void` declaring every argument through
  `registerArgument()`, and a typed `render(): string`. Dependencies are
  constructor-injected.
- `renderStatic()` and the `CompileWithRenderStatic` trait are the predecessor
  of that shape and are on their way out; no core ViewHelper carries them any
  more. Do not write a new one with them. Whether the branch already marks them
  deprecated is in its own `Documentation/Changelog/` and extension scanner
  matchers.
- Fluid escapes output by default. `protected bool $escapeOutput = false` opts
  out for a ViewHelper that returns markup, `$escapeChildren = false` for one
  whose children are markup. Both are security-relevant; say why in the class
  docblock.
- ViewHelpers are covered by functional tests under
  `Tests/Functional/ViewHelpers/`, not unit tests — a ViewHelper needs a
  rendering context, so a unit test asserts the wrong thing.
- A new ViewHelper or a changed argument list is a public API change and needs a
  changelog entry.
- Fluid parsing is configured in `$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']`
  in `typo3/sysext/core/Configuration/DefaultConfiguration.php`: `interceptors`,
  `preProcessors` (including `NamespaceDetectionTemplateProcessor`), and
  `expressionNodeTypes`.

## TCA, FormEngine, and Backend Forms

- TCA changes can affect persisted data, editor workflows, and generated backend
  forms.
- Prefer established TCA option names and existing FormEngine behavior.
- Add functional coverage when behavior depends on TCA processing, data
  providers, or backend form rendering.
- Check language labels when new UI fields or options are introduced.

## DataHandler and Persistence

- DataHandler changes are high-impact and usually need functional tests.
- Preserve workspace, localization, permissions, and hook or event behavior
  unless intentionally changed.
- Avoid changing persistence side effects without focused regression coverage.
- Test edge cases with deleted, hidden, localized, versioned, or workspace
  records when relevant.

## Routing, Middleware, and Request Handling

- Prefer PSR-7 request and response objects for HTTP behavior.
- Keep middleware focused and ordered deliberately.
- Functional tests are expected for routing, authentication, permission, and
  request lifecycle behavior.
- Avoid global state when request-scoped data is available.

## TypoScript, Site Sets, and TSconfig

This is how an extension ships TypoScript — a system extension and a project's
own alike. Configuring a single installation is where this stops being a
convention and becomes that site's own decision.

- A site set is how a system extension ships TypoScript: a directory
  `Configuration/Sets/<SetName>/` whose `config.yaml` carries a composer-style
  `name` (`typo3/fluid-styled-content`), an optional `label`, and an optional
  `dependencies` list naming other sets by that same name.
- Next to `config.yaml` a set may hold `setup.typoscript`,
  `settings.definitions.yaml`, `settings.yaml`, `labels.xlf`, `page.tsconfig`,
  and `route-enhancers.yaml`. Only `config.yaml` is required.
- `settings.definitions.yaml` declares the set's settings — a `categories` map
  and a `settings` map whose entries carry `default`, `type`, and `category`. It
  replaces TypoScript constants; reaching for `constants.typoscript` in new code
  is the mistake to avoid.
- The set's `labels.xlf` resolves to the translation domain
  `<ext>.sets.<setname>`.
- `ExtensionManagementUtility::addTypoScriptSetup()` and `addStaticFile()` are
  the predecessor, still called from a few system extensions;
  `addTypoScriptSetup()` has an `includeInSiteSets` flag that folds the content
  into the set mechanism. Both coexist — new code ships a set.
- TypoScript files use the `.typoscript` extension.
- Site set resolution is covered by functional tests under
  `typo3/sysext/core/Tests/Functional/Site/Set/`. The labels have their own
  check, `checkIntegritySetLabels`.

TSconfig is a separate mechanism and means TYPO3 page and user TSconfig here —
not the `tsconfig.json` of the TypeScript build, which is unrelated.

- A system extension ships defaults in `Configuration/page.tsconfig` and
  `Configuration/user.tsconfig`. Both are auto-loaded from that path; there is
  nothing to register in `ext_localconf.php`.
- An option is read from the merged page TSconfig. In FormEngine the merge is
  done by the `PageTsConfigMerged` data provider, so a provider that reads the
  option depends on it.
- A new TSconfig option is user-facing configuration: it needs a Feature
  changelog entry and documentation, not only the default.
- The TypoScript and TSconfig parsers live under
  `typo3/sysext/core/Classes/TypoScript/` and are covered by functional tests.

## Language Files

- XLIFF labels should use clear, stable identifiers and concise wording.
- Reuse existing labels where the meaning is identical.
- Reference labels by their translation domain (`backend.alt_doc:key`) rather
  than by the `EXT:.../locallang_alt_doc.xlf:key` file path.
- Never delete a `trans-unit`; mark it `x-unused-since="<next version>"` and
  remove it in a later major. See the XLIFF label lifecycle rules.
- Run `checkIntegrityXliff` and consider `normalizeXliff -n` after editing
  language files.

### How the translation domain of an XLF file is derived

The domain is not registered anywhere: it follows from the file path, by the
rules the core applies below `TYPO3\CMS\Core\Localization\` — the class that
transforms a file path into a resource has been both `TranslationDomainMapper`
and `TranslationDomainResolver`, so read which of them your branch has before
comparing against it. A file added by
a patch therefore already has its domain, and guessing it wrong only fails at
runtime. `typo3_translation_domain_lookup` computes it for any path, in any
extension.

The form is `package[.subdirectory...].resource`:

- The package part is the extension key: `EXT:backend/...` gives `backend`.
- `Resources/Private/Language/` is dropped from the path.
- Remaining subdirectories become dot-separated parts, UpperCamelCase converted
  to snake_case: `Language/SudoMode/locallang.xlf` gives `sudo_mode.messages`.
- `locallang.xlf` becomes `messages`.
- `locallang_<suffix>.xlf` becomes `<suffix>`, underscores kept:
  `locallang_alt_doc.xlf` gives `alt_doc`.
- Any other file name becomes its own snake_case form: `SudoMode.xlf` gives
  `sudo_mode`, `Database.xlf` gives `database`.
- `Configuration/Sets/<Set>/labels.xlf` becomes `sets.<set>`.
- A locale prefix is ignored: `de.locallang.xlf` derives the same domain as
  `locallang.xlf`.
- When two files map to the same domain, the one without the `locallang` prefix
  wins.

## Documentation and Changelog

### The changelog file format

The format is rigid enough that it is not reproducible from the filename
prefix, and getting the anchor, the title fence, or the tags wrong fails
`checkRst` — a slow review round for something purely mechanical.
`typo3/sysext/core/Documentation/Changelog/Howto.rst` is the authority.

A changelog file is needed for a Breaking change, a Deprecation, a Feature, or
an Important message. A casual bug fix needs none; its commit message carries
the information.

The filename is `<Type>-<forgeIssueNumber>-<UpperCamelCaseDescription>.rst`, in
the directory of the minor version the change is released in. A change
backported to an LTS branch goes into that branch's `<lts>.x` directory
instead, in every branch that carries it.

The skeleton:

```rst
..  include:: /Includes.rst.txt

..  _feature-109444-1759230001:

===============================================
Feature: #109444 - Short sentence, imperative
===============================================

See :issue:`109444`

Description
===========

What changed, and in which part of the core.

Impact
======

What a user, integrator, or extension author notices.

..  index:: Backend, ext:form
```

- The anchor is `<type>-<issue>-<unique identifier>`, the identifier a UNIX
  timestamp by convention.
- The overline and underline rows of `=` must be at least as long as the title
  line. The section headings below are underlined only.
- Every type has a `Description` section. Every type except `Important` has an
  `Impact` section. `Deprecation` and `Breaking` additionally have
  `Affected installations` and `Migration`.
- The `.. index::` line at the end carries at least two and at most about five
  tags from the fixed list in `Howto.rst` (Backend, CLI, Database, FAL,
  FlexForm, Fluid, Frontend, LocalConfiguration, JavaScript, PHP-API, RTE, TCA,
  TSConfig, TypoScript, YAML, `ext:<key>`). A `Deprecation` or `Breaking` file
  must carry exactly one of `NotScanned`, `PartiallyScanned`, or `FullyScanned`.
- Prose uses role markup for literals — `:php:`, `:yaml:`, `:typoscript:`,
  `:file:` — rather than plain double backticks.
- `Build/Scripts/validateRstFiles.php`, which `checkRst` runs, reports a missing
  title, issue number, unique identifier, or tag list.

- User-facing behavior changes may need ReST documentation or changelog entries.
- Deprecations need explicit migration guidance and scanner considerations when
  applicable.
- Run `checkRst` for ReST changes.

## Testing Strategy

- Start with the narrowest test that exercises the changed behavior.
- Add unit tests for isolated logic.
- Add functional tests for TYPO3 service wiring, persistence, TCA, DataHandler,
  routing, backend integration, and configuration behavior.
- Add frontend unit tests for TypeScript modules with meaningful logic or state
  transitions.
- Run broader checks before review when shared behavior or generated assets are
  touched.

### How a core test is written

A test that does not look like its neighbours is sent back in review, and the
idiom is not guessable from the strategy above.

- A unit test extends `TYPO3\TestingFramework\Core\Unit\UnitTestCase`, a
  functional test `TYPO3\TestingFramework\Core\Functional\FunctionalTestCase`.
  Both are `final` and carry `#[Test]` attributes rather than `test` prefixes.
- A functional test declares its environment through properties:
  `protected array $coreExtensionsToLoad`, `$testExtensionsToLoad`, and
  `$configurationToUseInTestInstance`. Only load what the test needs — every
  extension costs setup time per test class.
- State is set up and asserted with CSV fixtures, not with hand-written inserts:
  `$this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv')` in `setUp()`, and
  `$this->assertCSVDataSet(__DIR__ . '/Fixtures/Expected.csv')` for the result.
  The fixture files live in a `Fixtures/` directory next to the test class. This
  is the expected form for persistence tests.
- Services come from the container: `$this->get(SomeService::class)`, not
  `new`, so the test exercises the real wiring.
- DataHandler scenarios extend `AbstractDataHandlerActionTestCase` instead of
  `FunctionalTestCase` — it carries the scenario setup those tests share.
- Tests mirror the class path: `typo3/sysext/core/Classes/Foo/Bar.php` is tested
  by `typo3/sysext/core/Tests/{Unit,Functional}/Foo/BarTest.php`.
