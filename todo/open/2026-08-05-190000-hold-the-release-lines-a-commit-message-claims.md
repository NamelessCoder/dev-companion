# Hold the release lines a commit message claims

**Serves:** feedback/2026-08-05-033924-task-was-to-write-a-core-bugfix-patch-for-forge.md
**Priority:** normal

Establish which TYPO3 branches are maintained today and what each one is — dev,
sprint, LTS, ELTS, security-only, ended — and where that is readable from
something that stays current: `get.typo3.org` answers a JSON of releases, and
the core's own branch list and release notes are the other two candidates. Read
one, name it, and write what holds into `knowledge/` with the date it was read;
then have `typo3_commit_message_guide` hold its `releases` argument against it,
so a branch that is not a maintained line is a check finding rather than
silence. The judgement is `D-ANS-058`, which says what the boundary is and what
it is not — no release calendar, no advice about which branch a patch should
target. What the reading may not do is take the feedback's own list on trust: a
session inferred "main, 14.3, 13.4" from 40 commit trailers, which is what makes
this worth holding rather than what settles it.

Read what it serves and what the code does now before changing either; settle
what the step turns on rather than recalling it, and ask where nothing here can
answer: documentation/feedback/working-a-todo.md. Done means the file says so:
deleted, or trimmed to the part that is left.
