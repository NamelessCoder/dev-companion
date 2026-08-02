# Write the core patch review skill

**Serves:** feedback/2026-08-02-222817-task-review-03-the-forward-review-review-the.md, feedback/2026-08-01-115220-proposal-add-a-dedicated-mcp-skill-typo3-patch.md, feedback/2026-08-01-121847-reviewed-the-core-patch-task-deprecate.md
**Priority:** high

`D-SKL-005` decided it and its **Since then** carries the order, read out of the
three review sessions rather than invented: read the diff and the target branch
in the checkout, because this server never does (`2026-08-01-121852`); enumerate
what the diff removes or renames and require an ExtensionScanner matcher plus a
Breaking or Deprecation `.rst` for each, `@internal` waiving only the `[!!!]`
marker (`115711`, `115525`); `typo3_changelog_lookup` for the precedent;
`typo3_script_lookup` with `typo3_test_run_guide` for the narrowest
`runTests.sh` suites the changed paths imply; `typo3_commit_message_guide` last
(`115716`). Write it against `documentation/clients/writing-a-skill.md` — which
demands calling the tools it routes to before routing to them — publish with
`bin/typo3-cms-mcp install`, and hold the description to `R-SKL-010`. The
proposal in `115220` is answered by writing the skill, not by its shape: it
specifies a callable tool with inputs and outputs, and a skill here is the
order a task runs in. `REVIEW-03` is the measure afterwards, and its rerun is
what settles the first **Wrong if** in `D-SKL-005`.
