# Carry the unlisted push into the Gerrit workflow the skill routes to

**Serves:** feedback/2026-08-02-144848-task-fix-forge-105403-and-push-the-patch-to.md
**Priority:** high

Establish and write into `knowledge/documents/typo3-gerrit-workflow.md` the four
things the last section of
[`D-SKL-005`](../../decisions/task-skills/skl-005-core-contribution-earns-a-skill-and-the-domain-is-the-work.md)
found missing: what `%private` does to a change and how it differs from the
`%wip` already documented there, how a session reads where a checkout pushes
rather than only how to set it — `git config --get remote.origin.pushurl`, and
`.gitreview`, whose four keys are readable in `.checkouts/main/.gitreview` —
whether `HEAD:refs/for/<branch>` holds unchanged from a git worktree, and
whether a change may hang off a closed Forge issue. Three of the four are Gerrit
and Forge facts rather than checkout facts, so they come from the contribution
workflow manual at docs.typo3.org and from review.typo3.org's own documentation.
Then re-run `typo3_rule_lookup` with `gerrit push private change` and confirm
the unlisted form comes back, because
`skills/typo3-core-patch-development/SKILL.md` already tells the caller it will.
