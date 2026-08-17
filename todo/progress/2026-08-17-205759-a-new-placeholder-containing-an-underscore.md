# Say what a NEW placeholder in a relation field may contain

**Serves:** feedback/2026-08-17-205759-a-new-placeholder-containing-an-underscore.md
**Priority:** normal
**Branch:** todo/a-new-placeholder-containing-an-underscore
**Claimed:** 2026-08-17

Judged on 2026-08-17 as a knowledge gap: the probe reaches
`datahandler-relations` and `datahandler-writing` and neither says how a NEW
placeholder may be spelled — `D-KNW-081` carries the mechanism, the placement
and what the statement rests on.

Write one statement into `datahandler-relations`, beside the sentence about NEW
placeholders resolving in the same run: a placeholder named in a relation field
may carry no underscore, because `DataHandler::processRemapStack()` reads it as
the `<table>_<uid>` notation and the value is dropped by
`RelationHandler::readList()` afterwards. Name the symptom with it — the
children exist, the parent's counter stays 0, `uid_foreign` is 0 and nothing is
logged — and name `StringUtility::getUniqueId('NEW')` as the id that always
conforms. It is unbound: the block is identical on 12.4, 13.4, 14.3 and `main`.

Two things are still to establish. Whether an underscore costs anything outside
a relation value, which decides whether the rule is about the placeholder or
about what a relation field is handed. And whether the drop is logged somewhere
neither method shows, which is what the symptom half of the statement claims.
Then the `appliesTo` words a session with the symptom would ask in, and a
requirement recording what must hold.
