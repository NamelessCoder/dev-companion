# Give three pages back the answer a booted TYPO3 gives

**Serves:** documentation/clients/tools/
**Priority:** normal

`typo3_changelog_lookup`, `typo3_extension_scope` and `typo3_project_scope` lost
their second answer on 2026-08-03. Each carried one from the E-SITE, each of
those three broke the schema its own class declares — `removal`,
`unlistedFlexForms` and `environment` are required and were in none of them —
and re-recording against the checkout alone is what made
`ToolAnswersTest::everyAnswerOnAPageIsOneItsSchemaAllows` green. It cost 1583
lines, and no core checkout can produce them: what a booted TYPO3 answers is a
shape those three pages now show nothing of.

`bin/cli tools:record <root> <date> <tool>...` takes the tools to answer for, so
this is the three of them rather than the whole surface. Without an E-SITE it
drops the second recording again, and says so when it does.

Settle first whether an E-SITE is still the right root. `tests/Support/FakeInstallation.php`
writes a `vendor/autoload.php` that the real probe boots into, which is the
proof that the nine installation-backed tools can be answered without DDEV. A
fixture built per state makes these three derivable and checkable rather than
recorded, and this card is then closed by that work. Recording again is the
fallback.

What is already done: `typo3_task_guide` and `typo3_documentation_lookup` were
re-recorded on 2026-08-03 and are current. The `Relevant checks:` block that
`D-KNW-031` left behind is gone with them.
