# Record the tool answers again, with an E-SITE

**Serves:** documentation/clients/tool-answers/
**Priority:** low

Left behind by `D-KNW-031`.

`documentation/clients/tool-answers/` is stale for the two tools that carried
architecture hints: `typo3_architecture_lookup` and `typo3_task_guide` still
show a `Relevant checks:` block under every hint, which no answer prints since
the `checks` field left the corpus. The brief's own `checks` are the base suites
of the domain now, which is a different list and a different reason.

Do not re-record without an E-SITE. `bin/cli tools:record` writes what it can
reach, and reached from a checkout alone it drops the second recording of every
page — the one from a booted TYPO3 — which is 3831 lines of evidence no core
checkout can produce. `bin/cli environment:create E-SITE` makes it first, and
`bin/cli tools:record` then writes both halves, which is the state the pages
were committed in.
