---
id: D-KNW-076
date: 2026-08-14
status: open
---

# D-KNW-076 — What a new backend label costs before it resolves is a gap this server owns

**How a new XLF trans-unit reaches the backend's `~labels/` module is inside
this server's boundary and absent from it, so the feedback is queued rather than
closed.**

A session adding labels to a backend TypeScript module got a green build — the
types regenerate, the editor autocompletes, lint passes — and a runtime throw,
and read the two producers out of the checkout itself.

## Evidence

- The feedback's own query still reaches only what it reported. Run in this
  checkout on 2026-08-14,
  `bin/cli hints:probe "JavaScript labels module cache flush after adding XLF trans-unit"`
  classifies it `typescript, xliff` and matches `language-files`,
  `backend-typescript` and `backend-ui`.
- None of the three answers it. `language-files` in `labels.json` is authoring —
  the locale prefix, the `target-language` trap, the XLIFF linter — and names no
  JavaScript. `backend-typescript` in `backend-ui.json` states the twin failure
  for the other producer: "The generated file is committed, so a source change
  that is not built leaves the two disagreeing and the browser running the old
  one."
- `~labels` occurs once in the whole corpus, in `testing.json`, and it is the
  functional test middleware that returns `{ get: key => key }`. Nothing says
  where the served module comes from.
- The mechanism is real and younger than the covered lines.
  `JavaScriptLanguageDomainProvider` is absent from `.checkouts/12.4` and
  `13.4`, and present in `14.3` and `main` as
  `core/Classes/Localization/JavaScriptLanguageDomainProvider.php`,
  `backend/Classes/Controller/JavaScriptLanguageDomainController.php` and an
  install middleware of its own. `D-KNW-067` read the same boundary from the
  test side.
- The symptom string is the one somebody searches with.
  `Build/Sources/TypeScript/backend/localization/label-provider.ts:55` on `main`
  throws `'Label is not defined: ' + String(key)`.
- The two producers are genuinely different things.
  `Build/lib/generate-label-types.js`, named by `Build/Gruntfile.js` and
  `Build/tsconfig.json`, writes the type stub; `createLanguageDomainResponse()`
  builds the module the browser runs from
  `LanguageService::getLabelsFromResource()` and sends it with
  `$lifetime = 3600 * 24 * 365`, as `Cache-Control: private, max-age=` and a
  matching `Expires`.
- The label module is exempt from the cache busting every other module gets, and
  carries an infix of its own instead. `ImportMap::resolvePaths()` on `main`
  skips a `VIRTUAL:` address before the `?bust=` suffix is added, and
  `JavaScriptLabelImportMapEntryResolver` builds the `language_domain` route
  with a `cacheBustInfix` taken from `package-dependent-cache-identifier` hashed
  with `JavaScriptLanguageDomain`.
- Whether that infix moves when a trans-unit is added was deliberately not
  established here, and it is what decides half of the report.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about TYPO3 read on both sides of a version boundary, and this
  run has read this repository and two core checkouts.
- Bound `since: 14`. The provider exists on `14.3` and `main` and on neither
  covered LTS, so an unbound sentence would describe a mechanism half the
  callers do not have.
- Not step 2, so nothing is moved. `backend-typescript`'s sentence about
  generated JavaScript going stale is about a build artefact that is committed;
  the label module is an HTTP response with a year on it, and carrying the one
  sentence to the other case would state the wrong repair.
- The feedback's **Suggestion** is not copied down. It asks for `cache:flush`
  and a hard reload as two steps that are both needed, and the `cacheBustInfix`
  is a mechanism it does not mention.
- Inside the boundary. This is the core's own backend asset pipeline, which
  `backend-typescript` already answers for at scope `core`.

## Assumed

- That the session's account of its own installation holds: the `.d.ts`
  regenerated while the module the browser ran did not. Nothing here started a
  build or a browser.
- That the answer is one statement rather than two. The server-side invalidation
  and the browser-side one may turn out to be a single step.

## Wrong if

- The `cacheBustInfix` moves when a label file changes. The browser then asks
  for a URL it has never seen, no hard reload is needed, and what is left is the
  server-side half alone.
- `cache.l10n` keys the parsed file on its `filemtime()`. Then no flush is
  needed either and the statement is a correction rather than a procedure —
  which is what `D-KNW-027` found when the same question was asked of the Fluid
  template cache.
- The statement lands on `language-files`. That hint is authoring, is asked from
  `xliff`, and holds on every covered major; this holds from 14 and is asked
  from a TypeScript task.
