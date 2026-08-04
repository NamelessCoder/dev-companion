# Name a document by its URI everywhere

**Serves:** src/Knowledge/, knowledge/
**Priority:** normal

One document is written down two ways today. `knowledge/server-scope.json` names
it by its resource URI, and `TaskIntents::RULE_DOCUMENTS`, `ScriptLookup`'s
restriction to the script notes, `Prose::topics()` and every test name it by the
bare path — the same thing in two spellings, which is what `AGENTS.md` calls one
thing and one word. Settle which of the two is written down and make the other
derived: the URI is the form anything outside this checkout addresses a document
by, and `Documents::search()` and `Documents::read()` take the other. Whichever
wins, exactly one place converts, and `ResourceHandler::DOCUMENT_PREFIX` is
where that already happens. Read `D-KNW-058` first for what a resource name may
carry, which is not the path: the SDK holds a name to alphanumerics, underscores
and hyphens, so the flattening in `Server\Factory` stays whatever this decides.
