# The three changes an extension author makes have no intent

**Serves:** knowledge/
**Priority:** high

`knowledge/task-intents.json` holds nineteen intents and none of them is a
change inside an extension that already exists: a column added to a table's TCA,
a setting added to a site set, an event listener registered. The knowledge is
written already — `tca-formengine` and `tca-schema-api` in
`knowledge/hints/tca.json`, `site-set-settings` in `site-sets.json`,
`events-extension-points` in `events.json` — so what is missing is the routing
that turns the request into one brief, not what the brief would say. Write the
three intents in the shape `content-element` has: `match`, `matchWeak`, a
`condition` separating each from its neighbour, and a `checklist` that stops
where the hints take over. Leave `skill` out, because no published skill owns
these three and naming one that does not exist is worse than naming none.

The contract side is uneven in a way that says which of the three is worst off.
`SITE-08` holds the routing for a field added to an existing element's backend
form, so the TCA half is watched and only its brief is missing. A site setting
and an event listener appear in `SITE-01` and `SITE-03` as ingredients of a
larger task and are nobody's task shape, so those two need a contract case as
well as an intent.

Measured outside this repository, these are the three shapes where a session
with this server cost more than one without it, while lookup tasks got cheaper.
That is one project and one model, so it is a reason to write the intents rather
than proof they will pay.
