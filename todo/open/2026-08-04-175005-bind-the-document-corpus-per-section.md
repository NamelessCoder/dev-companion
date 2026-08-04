# Bind the document corpus per section

**Serves:** src/Knowledge/, knowledge/documents/
**Priority:** high

`D-VER-005` is what this carries out. Teach `Documents::sections()` to read a `**Since:**` and an `**Until:**` line
directly under a `##` heading, strip both from the body so neither reaches a
caller as part of what they bind, and carry them beside `heading` and `body`;
then filter in `Documents::search()` the way `Hints::forVersion()` does, over
`Versions::holds()`, with no target meaning no filtering. Give
`typo3_rule_lookup`, `typo3_script_lookup` and `typo3_task_guide` the optional
`targetVersion` in one change, resolved with `Versions::target()` as
`typo3_test_run_guide` resolves it, because all three render `Prose::sections()`
and splitting them makes one corpus answer two ways. Replace
`Prose::NOT_VERSION_BOUND` with the range of the section being returned, from
`Versions::label()`. Rewrite
`KnowledgeTest::noProseDocumentDatesAStatementInItsSentence` and
`noProseDocumentNamesACheckOnlySomeBranchesHave` around the new premise rather
than dropping them — a version in a sentence and a suite only some branches
carry stay wrong, because neither is something a filter can read — and add the
assertions `D-VER-005` names, then put them on its **Covered by**. No document
declares a binding yet, so every answer this corpus gives has to come back
unchanged; that is the proof this step owes.
