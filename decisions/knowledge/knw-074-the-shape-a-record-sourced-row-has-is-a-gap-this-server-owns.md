---
id: D-KNW-074
date: 2026-08-14
status: revoked
revokedBy: D-KNW-078
---

# D-KNW-074 — The shape a Record-sourced row has is a gap this server owns

**What `RecordFactory` moves out of a record's properties, and that
`$row['hidden']` is therefore empty rather than false, is inside this server's
boundary and missing from it.**

The corpus answers which rows a query brings back and stops there. What the
object it brings back is shaped like is one layer further on, and the failure it
leaves open is silent: an enable field that is not in the array reads as
permitted, so a hidden record renders and nothing throws.

## Evidence

- Re-run on 2026-08-14 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own task — "Record API SystemProperties hidden starttime
  endtime fe_group enable fields" — reaches `persistence-reading`,
  `frontend-access-restriction` and `frontend-records`, which are the three the
  feedback reported and in that order.
- The vocabulary is absent. `SystemProperties`, `RecordFactory`,
  `getSystemProperties`, `isDisabled` and `toArray(true)` occur nowhere below
  `knowledge/` or `skills/`.
- The two neighbours are each about something else. `persistence-reading` is
  restriction containers, `versionOL` and the language overlay — which rows come
  back at all. `frontend-records` names the `record-transformation` data
  processor that produces the objects and says nothing about what is in one, so
  a caller who reaches it is told where records come from and left to assume the
  fields are still there.
- The mechanism holds. In `.checkouts/main`, `RecordFactory` unsets from
  `$properties` and constructs `SystemProperties` — `RecordFactory.php:215`,
  `:272`, `:331` and `:334` — and `Record::toArray()` adds `'_system'` only when
  asked, `Record.php:65` and `:75`.
- The subject has a version boundary. `Domain/Record.php`,
  `Domain/RecordFactory.php` and `Domain/Record/` are in `.checkouts/13.4`,
  `14.3` and `main`, and none of them is in `12.4`.
- The worked example the feedback names does not resolve.
  `RecordIdentityRenderer`, `resolveUserGroupRestriction` and `resolveTimestamp`
  are in no covered checkout; the session that reported it was working in a core
  checkout of its own, which is where an unmerged change would be.

## Decided

- Built, as a hint of its own rather than more sentences on
  `persistence-reading`. One hint is one question (`D-KNW-030`): that one
  answers which rows a query returns, this one what the row it returns is shaped
  like, and the second is what a caller asks after the first has already worked.
- The boundary is the object a `Record` hands out: what `RecordFactory` moves
  into `SystemProperties`, the `_system` keys with their types, the accessors,
  and the two shapes a row reaches a caller in — a flat array with the enable
  fields at top level against `toArray(true)`. Which rows come back stays with
  `persistence-reading`, and what a data processor produces stays with
  `frontend-records`.
- The hint is written around the silent failure rather than around the accessor
  list. A caller who is told the accessors exist still has no reason to stop
  reading `$row['hidden']`, because that read returns a value and the value
  looks right.
- The card goes to `normal`. One session reported it, which is not the weight
  that lifts a card on its own, and what lifts this one is that the failure
  reads as a correct answer — a gap whose cost is a wrong lookup is paid once
  per session, and this one is paid by whoever visits the rendered page.
- What any of it says about TYPO3 waits for the reading. This judgement ran a
  probe, two searches over this repository and one over `.checkouts/main`, so
  the `_system` key list, the types and the second shape are established against
  `13.4`, `14.3` and `main` and bound there, which is the todo's first step.

## Assumed

- That a caller reaches this without knowing the class exists. The reporting
  session queried `SystemProperties` by name, but a session that has the bug
  asks about `hidden` on a row, so `appliesTo` has to carry both the class names
  and the field names.
- That the second shape is FormEngine's `databaseRow`. That is where the
  reporting session met it and it was not read here, so the reading may find the
  pairing is with something else or with several things.

## Wrong if

- The reading finds the enable fields are still among the properties on one of
  the covered lines, which would make this a version boundary rather than a
  statement.
- `typo3_schema_lookup` turns out to answer the record's shape from the
  installation, which would make it an answer rather than a hint.
- The `_system` keys differ enough across `13.4`, `14.3` and `main` that the
  hint is a table of versions rather than something a caller can hold.

## Confirmed on 2026-08-14

Read across the three checkouts that have the API. The third **Wrong if** does
not hold and could not have: `Domain/Record/SystemProperties.php` is the same
file on `13.4`, `14.3` and `main`, `Domain/Record.php` differs between `13.4`
and `main` in one spelling correction inside an exception message, and the ten
system capabilities `RecordFactory` unsets by are the same list. So the key
list, the types and the accessors are one statement bound with `since`, and the
hint is what a caller holds rather than a table.

The first **Wrong if** holds in one place and moves the boundary rather than the
subject. Since 14 `RecordFactory` returns a `Page` for the `pages` table, and
`Page::toArray(true)` does not add `_system` to the properties — it rebuilds the
row from the raw record, so every selected column is there and `hidden` with
them. On `13.4` `Page` is not a `Record` at all and the factory builds none. One
statement carries it, bound `since: 14`, and `Page::toArray()` without the
argument is the ordinary shape on both.

What the judgement did not have and the reading found is the asymmetry in the
property access: `Record::get()` reaches the raw record only where the record
has no record type, so `$record->get('hidden')` answers on a table without a
`typeField` and throws `RecordPropertyNotFoundException` on `tt_content` or
`pages`. A caller told to ask the object instead of the array needs that, or the
advice fails on exactly the tables the failure was reported from.

The second **Assumed** was not settled and the hint no longer rests on it.
FormEngine's `databaseRow` is the flat shape, and that half holds; the value
formats in it are a data provider's, not the row's — `DatabaseRowDateTimeFields`
makes every `type=datetime` column a `\DateTimeImmutable` on `main`, where the
report had ISO strings. So the pairing is stated as the enable fields at top
level against them under `_system`, and no value format is claimed for the
second shape.

The concrete case the report named is not reproducible in a covered checkout
either. `LocalizationRepository::getPageTranslations()` returns `RawRecord`
objects, whose `toArray()` carries every column, so
`PageLayoutContext::getLocalizedPageRecord()` on `main` has `hidden` in it.
`RecordIdentityRenderer` is still in none of the four.

## Revoked on 2026-08-14

By the work this entry queued. Its statement says the shape is "missing from
it", and `record-system-properties` carries it now, so `confirmed` over that
sentence would read as a claim about a gap that is closed. What holds from here
is
[`D-KNW-078`](knw-078-the-corpus-states-the-shape-a-record-sourced-row-has.md),
whose **Wrong if** is a different list: what can go wrong now is the core moving
under a statement, not a subject nobody wrote down.
