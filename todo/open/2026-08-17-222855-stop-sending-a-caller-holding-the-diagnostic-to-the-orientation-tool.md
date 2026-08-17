# Stop sending a caller holding the diagnostic to the orientation tool

**Serves:** feedback/2026-08-17-205904-typo3-server-scope-cost-11k-tokens-to-restate.md
**Priority:** normal

Take `typo3_server_scope reports the installation and its console.` out of the
text `Result\Unsupported::because()` builds, and rewrite the
`typo3_server_scope` step in `skills/typo3-development-installation/SKILL.md` so
any `typo3_project_describe` answer discharges it — `cause` already separates
nothing-installed from installed-and-not-running. `D-ANS-083` is the judgement
and the measurement; `SkillTest::ROUTING_SKILLS` still expects the tool named in
that skill, and four `documentation/server/tools/*.rst` recordings quote the
sentence, so `bin/cli tools:record` runs with the change.
