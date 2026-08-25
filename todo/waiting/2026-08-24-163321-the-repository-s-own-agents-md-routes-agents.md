# The repository's own AGENTS.md routes agents around a tool that answers better

**Serves:** feedback/2026-08-24-163321-the-repository-s-own-agents-md-routes-agents.md
**Priority:** normal
**Waiting on:** whether to propose a core patch to `typo3/AGENTS.md` naming
    `typo3_forge_lookup` beside the `curl` recipe. That file is tracked in the
    core repository, so the correction is a change through Gerrit like any
    other, and what nothing in this checkout decides is whether this server's
    maintainer puts the name of an experimental server into the core's own
    instructions. `documentation/records/judging.rst` says an outward claim is
    never made quietly. The reading behind it is in
    [`D-AUD-013`](../../decisions/audience/aud-013-a-competing-route-is-corrected-where-it-is-written.md).

Judged on 2026-08-25 as the ladder's step 3, routing, against a routing table
this repository does not own, and the feedback is trimmed to the half above. Its
changelog half was answered by the split of 2026-08-24 and is recorded under
[`D-KNW-111`](../../decisions/knowledge/knw-111-the-changelog-procedure-is-a-guide-of-its-own.md);
its commit-message half is evidence about the same boundary and is recorded
under `D-AUD-013`.

What the reading established about the half that is left, so it is not done
again. Nothing on this server failed: the session held both the recipe and the
tool and took the tool, saying what the tool gave it that `curl` could not. The
core's `AGENTS.md` still carries the paragraph verbatim on 2026-08-25 and names
no MCP server anywhere in the file.

The next step once it is answered: the paragraph is the Context list of
`typo3/AGENTS.md`, and what a patch adds is one line — the tool where the server
is available, the recipe as the fallback for a session without it. Everything
the patch owes beyond that is the core's own process, which `typo3_task_guide`
and `skills/typo3-core-patch-development` already carry. Answered the other way,
`D-AUD-013`'s third **Wrong if** is what the outcome is read against.
