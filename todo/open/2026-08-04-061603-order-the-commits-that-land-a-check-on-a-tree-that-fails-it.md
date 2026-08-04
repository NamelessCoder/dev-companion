# Order the commits that land a first check on a tree that does not pass it

**Serves:** feedback/2026-08-04-055741-task-ich-w-rde-der-extension-gerne-einen.md, feedback/2026-08-04-055715-the-server-sees-the-four-calls-i-made-and.md
**Priority:** low

Step 1a in `skills/`, and the workable half is one paragraph:
`references/static-quality.md` of `typo3-extension-testing` says "Keep a
formatting pass in its own commit, apart from behavioural change" and says
nothing about the order, so the obvious split introduces `ci:editorconfig` onto
a tree whose XLF files still hold tabs — a commit that fails the check it adds.
Write the rule beside that sentence: where a check is introduced onto a
repository that does not yet pass it, the conformance commits come first and the
commit adding the check comes last, so no commit fails the check it introduces,
and it is verified by running the check at the new HEAD. The rest of the card is
a question about what is wanted, asked in both feedback and answerable by nobody
here, so it is written out rather than decided — `055741`: "Consider also
letting base.md's order be shortened where a skill has already routed the task
and the change touches no TYPO3 API: on this session steps 3 and 5 were
prescribed and skipped, and a prescription that gets skipped teaches the next
reader to skip the ones that matter too." And `055715`: "Say in
typo3_task_guide's description what it adds when a skill has already routed the
task — if it is the same workflow the skill carries, base.md should not spend a
step on it for tasks the skill covers end to end." `R-SKL-005` is what a
shortened order would touch, and the priority is `low` because one session
reported it and its second half waits on that answer, not because nobody has
looked.
