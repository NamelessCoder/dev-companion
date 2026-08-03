# Rename the hint corpus and the tool that answers from it

**Serves:** decisions/
**Priority:** normal
**Waiting on:** which name the tool takes. `typo3_hint_lookup` says what comes
    back and `typo3_convention_lookup` says what it is about, and the second is
    closer to the old subject without carrying "architecture", which the corpus
    outgrew. The directory and the class follow whichever wins; renaming one of
    the three alone is the two-names-for-one-thing this session spent four
    commits removing.

The corpus is no longer architecture. It holds label rules, upgrade sequences,
test-harness setup, browser tests and changelog conventions, and the word used
everywhere else — the glossary, the tests, every commit message — is `hint`.
The owner asked for the rename and called it internal restructuring worth not
shying away from.

What it touches: `knowledge/architecture-hints/`, `Knowledge\ArchitectureHints`
in fourteen files, and `typo3_architecture_lookup` in the tool registry,
`knowledge/task-intents.json`, `knowledge/server-scope.json`, the installed
skills, `documentation/clients/`, and the tests.

`scenarios/runs/REVIEW-01.json` and `REVIEW-02.json` name the old tool and are
evidence of a run that happened. Do not rewrite them: add a line to each saying
the tool was called by its former name at the time, the way a recording says
what it is of (`D-DOC-006`).

`documentation/clients/tool-answers/` is stale for other reasons already and is
re-recorded rather than edited — see the todo that asks for an E-SITE first.
