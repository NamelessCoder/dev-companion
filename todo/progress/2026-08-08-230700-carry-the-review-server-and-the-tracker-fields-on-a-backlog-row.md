# carry the review server and the tracker fields on a backlog row

**Serves:** feedback/2026-08-08-224333-the-triage-skill-says-how-to-list-a-backlog-but.md
**Priority:** normal
**Branch:** todo/carry-the-review-server-and-the-tracker-fields-on-a-backlog-row
**Claimed:** 2026-08-08

Widen the enumeration row in `Forge::open()` and `Forge::entry()` to the
`relations` and `attachments` the index already answers when the call asks
`include=relations,attachments`, and add whether the review server holds a
change: one batched `message:<issue> OR …` query per page against Gerrit with
`o=CURRENT_COMMIT`, each hit held against the commit message by the rule
`Gerrit::names()` already applies to a single-issue hit. Carry the three per row
in `ForgeLookup`'s `results` schema and in the rendered text, and say of the
review flag what the issue answer says of `reviews` — a change named here is a
handle for `typo3_gerrit_lookup` and not a statement about its state. The
comment count is deliberately not part of this.
[`D-ANS-069`](../../decisions/answers/ans-069-a-backlog-row-carries-the-review-server-and-not-the-journal.md)
carries the measurements and why.
