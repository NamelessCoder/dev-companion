# META-05 — Install and update Codex support

**Environment:** `E-SITE`, with Codex project configuration containing unrelated
settings · **Contract:** `held` — `R-DIS-012`
**Held by:**
`InstallerTest::codexInstallAndUpdateTrackTheirSkillsCentrally`,
`InstallerTest::codexInstallRefusesAConflictingServerEntry`,
`InstallerTest::codexUpdateReplacesAModifiedGeneratedSkill`

> Set this TYPO3 assistant up for Codex in this project. Keep everything already
> configured. Then update the generated integration once so I know future
> releases can refresh it safely.

**What the agent needs from this server**

- One explicit install path for the Codex MCP configuration and task skills.
- An idempotent update path that knows which generated files belong to this
  package.
- A conflict result that leaves existing foreign or user-modified content
  untouched.

**What has to come out of it**

- Unrelated Codex configuration survives byte-for-byte in meaning, an existing
  matching server entry is reused, and a different entry is declined.
- Repeating install or update produces no duplicate entry and no unnecessary
  file change.
- User-modified generated files are reported rather than silently replaced.

**How it fails**

- The whole Codex configuration is regenerated from a template.
- Update overwrites a file whose current content is no longer the version this
  package generated.
- The MCP entry is installed but the task skill is left stale.
