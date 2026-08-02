# Say what the two listed files cost, in the answer that already lists them

**Serves:** feedback/2026-07-31-172757-the-two-newest-typo3-v14-3-extension.md
**Priority:** normal
**Branch:** todo/say-what-the-two-listed-files-cost-in-the-extension-answer
**Claimed:** 2026-08-02

Step 2 on the ladder in `D-ANS-009`, and the half that archives the feedback.
Both statements now exist in the `extension-files` hint of
`knowledge/architecture-hints/php.json` — `#108345` bound `since 14 until 14`
and `#109438` written by step 1a, bound the same way with the removal at
`since 15` — while `typo3_extension_scope` prints `ext_tables.php` and
`ext_emconf.php` in its `files` list beside four files nothing is wrong with and
says nothing about either. Carry the cost into that answer, in
`src/Tool/ExtensionScope.php`: `files` is declared in `outputSchema()` and
rendered as `Registration files` in the text, and what is missing is the
predicate each deprecation actually turns on — for `ext_tables.php` the file
being present in a package that is not a system extension, for `ext_emconf.php`
the file being present while `composer.json` declares neither `providesPackages`
nor a version in `extra.typo3/cms.version` or the top-level `version` field.
Settle first whether this is a field of its own or the hint carried beside the
answer, which `D-ANS-009` deliberately did not decide, and settle it against the
second **Wrong if** there: a Composer installation is unaffected by `#108345`,
so a bare "composer.json missing version/providesPackages: yes" is true and
misread by the majority, and an answer that volunteers deprecations must not
read as a compatibility verdict where it stays silent. `bootstrap_package` at
`/home/benji/projects/bootstrap_package` is the extension the feedback was
reported from and fires both.
