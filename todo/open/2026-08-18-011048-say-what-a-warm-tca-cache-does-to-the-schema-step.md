# Say what a warm TCA cache does to the schema step of extension:setup

**Serves:** feedback/2026-08-17-212117-extension-setup-reports-success-without.md
**Priority:** normal

The judgement is `D-KNW-089`, step 1a: the schema step migrates from the cached
TCA and the command reports success either way, and no statement below
`knowledge/` says so. The mechanism is read out in the entry's evidence, so what
is left to establish is the binding — whether the sentence is about a table,
which `extension-schema-sql` already binds with `since: 13`, or about the columns
`DefaultTcaSchema` enriches, which a warm cache hides on 12.4 as well — verified
on both sides of whichever boundary it takes. Then state it on
`extension-schema-sql` in `knowledge/hints/extension.json` with the check beside
it, ask the database rather than the command, add the `appliesTo` phrasings that
make the symptom reach the hint at all — no probe reaches it today, and
`bin/cli hints:probe` on the feedback's query is what shows it does afterwards —
decide whether `installation-boot` and `installation-upgrade` owe a neighbour
line, and write the requirement and the `HintsTest` case that hold it.
