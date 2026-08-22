---
id: D-ANS-095
title: A flex field is answered with the data structure the installation resolves
date: 2026-08-21
status: open
---

# D-ANS-095 — A flex field is answered with the data structure the installation resolves

**A tool of its own resolves one `type=flex` column through the installation's
own `FlexFormTools`, from a record the caller supplies the values of.** What is
answered today is the binding a registration declares; what may be written into
the column is the structure behind it, and nothing here opens one.

## Evidence

- Two sessions stop at the same boundary, from two task shapes.
  `feedback/2026-08-21-074351` compared this server against another one and
  found `typo3_schema_lookup` answering the DDL column with no call to make for
  what goes in it. `feedback/archive/2026-07-31-194510` audited
  `printworks_sitepackage` and named the two content elements the answer
  described least as exactly the two whose FlexForm it did not open. The first
  asks for the structure, the second asked for the binding, and the binding is
  what was built.
- The binding is where today's answer stops. `typo3_extension_describe` carries
  the data structure per content element "as the call declares it — a FILE:EXT:
  reference, or `inline`", and no second tool goes past it: `src/Tool/` holds
  none about flex, `bin/cli hints:probe "flex field data structure identifier"`
  matched nothing on 2026-08-21, and no skill names flex at all.
- Opening the file the binding names is not what the backend sees. On 14.3
  `FlexFormTools::parseDataStructureByIdentifier()` runs the found structure
  through `convertDataStructureToArray()`, `ensureDefaultSheet()`,
  `resolveFileDirectives()` and `checkMigratePrepareFlexTca()`, between
  `BeforeFlexFormDataStructureParsedEvent` and
  `AfterFlexFormDataStructureParsedEvent`. A structure with no sheet carries
  `sDEF` afterwards, a sheet held in a file of its own is resolved, every field
  is migrated and prepared, and a listener may have replaced the whole of it.
- The identifier is a second answer with a second pair of events.
  `getDataStructureIdentifier()` dispatches
  `BeforeFlexFormDataStructureIdentifierInitializedEvent`, falls back to the
  default identifier and dispatches the `After` one; the default on 14.3 is
  `{"type":"tca","tableName":…,"fieldName":…,"dataStructureKey":null}`.
- Which structure it is depends on the record, which is why values have to be
  supplied. On 12.4 and 13.4 `ds_pointerField` names up to two columns of the
  row, and the `ds` array is keyed by their values — `news_pi1,list` beside
  `default`.
- The mechanism differs across the covered majors, so a caller reasoning from
  one is wrong on another. `Breaking-107047` removed `ds_pointerField` and the
  multi-entry `ds` array in v14: `ds` names one structure, and a FlexForm per
  record type is assigned through `types` with `columnsOverrides`.
- The API differs too. `getDataStructureIdentifier()` and
  `parseDataStructureByIdentifier()` take `array|TcaSchema|null $schema` on 14.3
  and on `main`, and the default path throws `InvalidTcaSchemaException`
  (1753182123) where it is null; 12.4 and 13.4 read `$GLOBALS['TCA']` and have
  no such parameter.
- FormEngine resolves it with those two calls and nothing else —
  `Backend\Form\FormDataProvider\TcaFlexPrepare` — so an answer taken from them
  is what the backend form would build.
- The seam exists. `probe.php` boots the installation and asks its container,
  and `Typo3Runtime::configuration()` is the one topic that takes a parameter:
  the value is substituted into the payload and the memoized reading is dropped
  where it changes.
- No database is reached. The resolution reads TCA, files and events, unlike the
  derived columns of `D-DIS-012`, which ask the server for a version on three of
  the four drivers.

## Decided

- Step 1b, the shape. The answer is available to this server — the same
  container the probe already asks, one boot, no credential and no second host —
  and there is no argument to any tool that reaches it. Not step 4: the wording
  of `typo3_extension_describe` is accurate about what it carries.
- Taken on. What justifies it against a suggestion is the second session and the
  failure mode: the declared binding reads as the whole answer, and a caller who
  opens the file it names gets a structure the installation would migrate,
  normalize and let a listener replace before FormEngine ever sees it.
- A tool of its own, `typo3_flexform_lookup`, rather than a field on
  `typo3_schema_lookup`. The two answer the two halves of one column — the DDL
  side and what may be written into it — and the second half needs arguments the
  first has no use for, and returns sheets where the first returns columns.
  `lookup` is the verb `typo3_schema_lookup` already carries for a named table
  answered from the installation.
- The record is emulated from values the caller passes, and nothing loads a row.
  The feedback offers a `recordUid` that reads one; the content itself is what
  `knowledge/server-scope.json` declares this server does not touch, and a uid
  is that line.
- Priority `normal`. Two sessions reported it and neither lost a task, which is
  what keeps it below the work that has; the extension author is one of the
  three audiences, a plugin binding a FlexForm is ordinary work for them, and
  the mechanism changed inside the covered majors, which is what takes it off
  the `low` the card arrived at.

## Assumed

- Values a caller supplies are enough for the listeners in the wild. A listener
  that reads the row it is handed is served; one that queries the database by
  `uid` sees an emulated record and may answer differently from the backend.
- An extension author asking this has an installation that boots. Where it does
  not, the answer is the reason rather than the file the binding names, because
  a file read here would be the guess this tool exists to replace.

## Wrong if

- A run or a feedback reports a resolved structure that differs from what the
  backend form shows for the same record, and the emulated record is the cause.
  Then caller-supplied values are the wrong boundary, and the tool owes the
  caller a way to say what it could not resolve.
- The calls that arrive are about fields with one `ds`, no listener and no
  record type, where the answer is the file the caller could have opened. Then
  this is a file read wearing a boot, and the binding `typo3_extension_describe`
  already carries was the whole gap.
- It reports `unavailable` more often than it answers, which is the test
  `D-FBK-027` sets for a lookup that buys its caller nothing.
