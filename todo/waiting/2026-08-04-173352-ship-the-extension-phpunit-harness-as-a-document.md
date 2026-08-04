# Ship the extension PHPUnit harness as a bound document

**Serves:** knowledge/documents/
**Priority:** normal
**Waiting on:** whether a file skeleton belongs in the document corpus at all.
    `D-KNW-056` settled that it does and that its section carries the binding.
    The reading that started this card found `D-VER-002`, which is `confirmed`
    and decided the opposite for the same corpus — "no binding mechanism for
    prose. It would need per-bullet metadata in markdown, a parser, a renderer
    and a test". It was re-read on 2026-08-02, its **Wrong if** fired, and its
    own remedy absorbed it without a mechanism. `D-KNW-056` was written without
    it, so this is a collision rather than a difference of emphasis.

    What makes it more than two entries disagreeing is that the promise is
    shipped. `Prose::NOT_VERSION_BOUND` renders in every answer of
    `typo3_rule_lookup`, `typo3_script_lookup` and `typo3_task_guide` — "These
    sections are prose and are not filtered by version" — and two of those three
    are outside this card's scope.
    `KnowledgeTest::noProseDocumentDatesAStatementInItsSentence` guards the same
    premise over every document file, and its comment names it:
    `typo3_rule_lookup` has no `targetVersion` and searches every document.

    Four answers, priced. **Give the skeletons their own corpus and surface** —
    the files stay files below `knowledge/`, diffable against `.checkouts/` and
    holdable by a check, `D-VER-002` and the guard untouched; it costs the tool
    the document route was chosen to avoid. **Bind the document corpus** —
    `D-VER-002` is revoked and superseded, `NOT_VERSION_BOUND` becomes a
    per-section statement, three tools and two guards change. **Ship one unbound
    skeleton** for the current release line alone — nothing else changes, and a
    caller on the older line writes out a file naming a PHPUnit schema it does
    not have, which is `D-VER-002`'s **Wrong if** with a file in place of a
    sentence. **Make the skeleton a hint statement** — `since` and `until` per
    statement, `typo3_hint_lookup` filtering by `targetVersion` and `HintsTest`
    holding the binding, all of which exist, and it is what `D-VER-002` itself
    prescribes for a statement the prose cannot carry; it costs a 1.9 KB XML
    file as an escaped JSON string, in a corpus whose worth is that a reviewer
    can read it.

Answer the question above, then either revoke `D-KNW-056` and write its
successor, or confirm it and take the two further tools and the two guards it
moves into this card's scope. Nothing is broken while it waits:
`project-extension-tests` in `knowledge/hints/testing.json` answers the setup
question in prose today, and that is what a caller reaches.
