# Write the hint for the shape a Record-sourced row has

**Serves:** feedback/2026-08-13-215619-the-record-api-moves-enable-fields-into-system.md
**Priority:** normal
**Branch:** todo/the-record-api-moves-enable-fields-into-system
**Claimed:** 2026-08-13

Judged on 2026-08-14 as a knowledge gap: the probe reaches the three hints the
feedback named and nothing else, and the vocabulary is absent from `knowledge/`
and `skills/` — `D-KNW-074` carries the boundary and what the hint is written
around. Read `Domain/RecordFactory.php`, `Domain/Record.php` and
`Domain/Record/SystemProperties.php` in `.checkouts/13.4`, `14.3` and `main` for
which fields are moved out, the `_system` keys with their types and the
accessors, bind the statements with `since` because none of it is in `12.4`, and
write them as a hint of their own in `knowledge/hints/records.json` whose
`appliesTo` carries the field names as well as the class names. The second shape
the hint pairs against — FormEngine's `databaseRow` — and the core's own
fallback example are both still to be located: `RecordIdentityRenderer` is in no
covered checkout.
