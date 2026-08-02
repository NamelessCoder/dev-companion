# Render the tool surface from the registry, rather than writing it twice

**Serves:** documentation/

A tool declares everything a caller can see of it in the one class that answers
it — `name()`, `description()`, `inputSchema()`, `outputSchema()`,
`annotations()` — and the `Tool` docblock says why: a description that stops
describing the answer is then one file to change rather than a drift between
three. The readme writes all 23 of them out a second time by hand. They are all
named there today, and nothing holds them that way. No test reads
`Registry::definitions()` against `readme.md`, so a 24th tool would be named
nowhere, and a schema field added in a class leaves the prose beside it
standing. The half nothing writes down at all is the answer: `outputSchema()`
names the fields, and what a filled one looks like is visible only by calling
the server.

The step is a `bin/cli tools:index` beside `requirements:index` and
`decisions:index`, writing a generated reference under `documentation/clients/`:
per tool its name, description and annotations, then both schemas rendered as
fields with their type and whether they are required, between the block markers
the index commands already use, with `tools:check` failing where the file is
stale. That much is derivable from the registry alone and belongs in a test.
The response half is not, and that is what to settle before writing it: no
installation is discovered in a test run — `ToolContractTest` says so where it
drives `typo3_label_lookup` and `typo3_icon_lookup` for the unanswered path —
so a recorded answer needs `.checkouts/`, which is gitignored and which no test
may depend on. That makes recording responses a command in the shape of
`catalog:check`, run against a checkout of somebody's own and committed as
data, and it makes the call table the one `ToolContractTest::toolCalls` already
holds: one hit and one miss per tool, which is the pair a client is most likely
to need to see.
