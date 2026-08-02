# Write the core patch creation skill

**Serves:** feedback/2026-08-02-144357-task-evaluate-forge-105403-find-related-issues.md, feedback/2026-08-02-145315-task-fix-forge-105403-and-deliver-it-as-a-core.md
**Priority:** high

`D-SKL-005` decided it, and `2026-08-02-145315` offered its body outright: the
one session that carried a Forge issue to a pushed Gerrit change filed the order
in nineteen parts, and `D-SKL-005`'s **Since then** lists them. Write it in that
order — assess the issue before believing the report, reproduce against the
target branch as a functional test, implement, decide the changelog from its own
tree, run the suites through `Build/Scripts/runTests.sh`, then deliver — and
carry the three traps the session paid for: a worktree needs its own
dependencies before any suite runs (`144950`), `cglGit` passes falsely inside one
(`144326`), and `origin` fetches from GitHub while it pushes to Gerrit
(`144848`). What belongs to the tools rather than to this skill stays out of it:
`typo3_project_scope` not naming `runTests.sh` is `2026-08-02-144350`, and the
Forge and Gerrit access recipes are `145217` and `145230`, each with its own
card. Written against `documentation/clients/writing-a-skill.md`, published with
`bin/typo3-cms-mcp install`, description held to `R-SKL-010`. The review skill is
the sibling card and the two share a middle: name the crossing explicitly, as
`R-SKL-003` requires.
