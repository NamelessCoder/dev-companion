# Name both files and the renumber in the duplicate-id failure

**Serves:** feedback/2026-08-13-224118-todo-home-refuses-a-decision-id-collision-with.md
**Priority:** normal
**Branch:** todo/name-both-files-and-the-renumber-in-the-duplicate-id-failure
**Claimed:** 2026-08-14

Judged as `D-FBK-046`: step 2, delivery. `bin/cli decisions:renumber` repairs
the one collision this procedure predicts, and the assertion that catches it
says `two decision files claim the same id` and a size mismatch — no id, no
path, no command. `normal` because two feedback from this checkout report it and
one of them counted ten renumbers over three rounds.

`tests/Unit/DecisionsTest.php` line 40 is the assertion, and
`tests/Unit/RequirementsTest.php` line 41 is the same one for requirement ids.
Both compare `count(files())` against the id-keyed `all()`, so the duplicated id
and the files claiming it are already there to be computed — the count is what
throws them away. The requirement half names the two paths and no command,
because there is none.

Put the computing where a test can reach it rather than inside the assertion, so
what the message says is held by something other than reading it. Whether the
suite may reach `Decisions::files()` twice or wants a duplicates helper beside
`all()` is the first thing to settle, and both files sit in `src/Upkeep/`.

Archive the feedback in the same commit.
