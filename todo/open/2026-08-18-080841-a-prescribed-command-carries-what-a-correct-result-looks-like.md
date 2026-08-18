# A prescribed command carries what a correct result looks like

**Serves:** feedback/2026-08-17-212800-four-commands-reported-success-while-doing.md
**Priority:** normal

Sweep `knowledge/hints/` for the statements that prescribe a command — 24 name a
`typo3 <subject>:<verb>` one over 11 distinct commands, plus the `ddev`,
`composer` and `runTests.sh` ones — and read each command's success path in
`.checkouts/` to establish which of them answer success having done nothing.
Where one does, the hint takes a closing sentence naming what a correct result
looks like outside that message, in the form `impexp-artifact` and
`extension-schema-sql` already carry; then write the requirement in
`requirements/knowledge/` with a `HintsTest` assertion per hint it reaches.
`D-KNW-093` is the judgement, what the rule is bounded to and what it costs.
