# Write the backend label module hint

**Serves:** feedback/2026-08-13-215652-a-new-xlf-trans-unit-compiles-into-the-labels.md
**Priority:** normal
**Branch:** todo/write-the-backend-label-module-hint
**Claimed:** 2026-08-14

Judged as `D-KNW-076`: step 1a, the knowledge is missing. That entry establishes
the two producers, the symptom string and the version boundary, so what is left
is what actually invalidates the module, and where the statement goes.

- Establish what a new trans-unit needs before the module serves it. Two things
  decide it and neither was read: whether `cache.l10n` keys the parsed file on
  its `filemtime()`, which would mean no flush is needed at all, and whether the
  `cacheBustInfix` in the `language_domain` route moves when a label file
  changes, which would mean no hard reload is needed either. That infix comes
  from `package-dependent-cache-identifier`, so read what feeds that identifier.
  `D-KNW-027` is the precedent: the same question turned a command into a
  correction once the Fluid cache turned out to key on `filemtime()`.
- Bind it `since: 14`. `JavaScriptLanguageDomainProvider` is absent from
  `.checkouts/12.4` and `13.4`, which `D-KNW-067` recorded from the test side.
- State the symptom in the words somebody will search with: the runtime
  `Label is not defined: <key>` thrown by `label-provider.ts`, and that every
  build-time check stays green because `Build/lib/generate-label-types.js`
  regenerates only `Build/types/labels/*.d.ts`.
- Name where the module comes from — `createLanguageDomainResponse()` and its
  year-long `max-age` — because that is what makes the browser half findable
  rather than mysterious.
- Place it. `backend-typescript` in `backend-ui.json` is scope `core` and
  carries the twin sentence about generated JavaScript going stale;
  `language-files` in `labels.json` is authoring and asked from `xliff`. A hint
  of its own beside one of them is the third option.
- `appliesTo` decides whether it arrives. The feedback's own query reached three
  subjects and none of them answered, so `bin/cli hints:probe` with that query
  is what says the placement works.

The feedback is archived by the commit that lands the hint.
