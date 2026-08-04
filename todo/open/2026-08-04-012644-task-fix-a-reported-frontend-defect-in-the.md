# Name the commit step in the extension-facing skills and in the routing entry

**Serves:** feedback/2026-08-04-012644-task-fix-a-reported-frontend-defect-in-the.md
**Priority:** normal

Judged as delivery, step 2 of the ladder, in `D-SKL-014`: the commit step is
named in the two core skills and twice in `typo3_task_guide`'s answer, and in
none of the seven extension-facing ones. Read each of those seven for whether
its workflow ends in a change to the repository, give the ones that do a closing
step naming `typo3_commit_message_guide` with `workflow="project"`, name the
argument in the `"Writing or amending the commit message"` routing entry of
`knowledge/server-scope.json`, and write the requirement that holds it in the
same commit; the draft goes to whoever asked for the skills before it is
published, per `documentation/clients/writing-a-skill.md`. `normal` rather than
`low` because it closes the channel `D-GUI-002` has called the worst of four
since 2026-07-29, and not `high` because one session reported it and that
session activated no skill at all, so this change would not have reached it.
