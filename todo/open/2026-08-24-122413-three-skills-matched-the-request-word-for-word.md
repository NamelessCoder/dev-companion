# The change answer carries the order rather than the names of the two workflows

**Serves:** feedback/2026-08-24-122413-three-skills-matched-the-request-word-for-word.md, D-SKL-038
**Priority:** normal

`GerritLookup::workflow()` names `typo3-core-patch-review` and
`typo3-core-patch-checkout` on every answered `change` form, and a session that
read that answer on 2026-08-24 reviewed change 95179 by hand with no skill open
— `D-SKL-038`'s first **Wrong if**, which names the step: the order itself in
the answer, in the shape `TestRunGuide::SCRIPTS_GUIDE` took. Read that pair and
the two skills for what a caller holding one change actually does next, write it
into the tail, and hold it against `bin/cli tools:measure` and against the same
entry's second **Wrong if** — a session with the skill already open reading the
tail as noise on a narrow question.
