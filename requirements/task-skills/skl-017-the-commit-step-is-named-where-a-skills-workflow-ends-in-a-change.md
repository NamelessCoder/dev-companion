---
id: R-SKL-017
status: held
restsOn: [D-GUI-010, D-SKL-014]
---

# R-SKL-017 — The commit step is named where a skill's workflow ends in a change

**A published skill whose workflow ends in a change to a repository outside the
core names `typo3_commit_message_guide` with `workflow="project"`, at the point
its own workflow ends.**

The argument is what the skill adds. Which repository the commit lands in is the
one thing the message itself cannot say, and the core's half of the answer — the
Forge issue, the release branches, the trailers that go with them — is not one a
session committing in an extension can use. So the skill standing in that
repository is what says which it is, stated rather than left to the default
`D-GUI-010` set: a call site that means project says so.

Six skills carry the step, read off each body: backend modules, content
elements, the development installation, documentation, testing and the upgrade
all end in files that are written.

`typo3-extension-conformance` does not carry it. It is pure analysis: it reports
findings and hands every change to the skill that owns the area, so it has no
message to write, and a commit line in a review's answer is the patch checklist
`R-GUI-006` exists to keep out of one.

The two core skills are not among them. Both name the guide already, both commit
in the core, and the argument's default is the answer there.
`workflow="project"` in either of them would drop the rules a core patch is held
to.

The step is in the skill body rather than in the order every task starts in.
That order is copied into each published skill, review-only ones included, and
in the two core skills it would restate what their own commit sections already
say.

## From

A session in `/home/benji/projects/syntax` on 2026-08-04, told to reproduce a
frontend defect in the extension it was standing in, fix it and commit it
(`feedback/2026-08-04-012644`). It had this server on stdio, all 26 tools in its
context and the nine published skills beside them, made 37 tool calls — every
one of them Bash, Read, Edit or Write — called none of the tools and activated
no skill. It committed from its own habits. `D-GUI-002` had been waiting for
that run since 2026-07-29 and counts four channels that could have carried the
step: the tool's own description, the covered topic in
`knowledge/server-scope.json`, `typo3_task_guide`, and the skills. The skills
were the one channel that carried nothing — the commit guide was named in
`typo3-core-patch-development` and `typo3-core-patch-review` and in none of the
seven an extension author reaches for, which is what `D-SKL-014` decided to
close.

## Held by

- `SkillTest::theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange`
- `SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`
- `SkillTest::theWorkflowStepRunsInEverySession`
