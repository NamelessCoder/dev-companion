# Cut the comments that retell the entry they name

**Serves:** src/, tests/
**Priority:** normal
**Run:** bin/cli prose:check
**Branch:** todo/cut-the-comments-that-retell-the-entry-they-name
**Claimed:** 2026-08-18

Read the comments the report names, longest first, and cut each one to what the
code cannot say: `bin/cli prose:check` lists them under the comment measure,
240 of them today, from 59 lines above `src/Upkeep/Todo.php` downward. A comment
that names a decision keeps the id and loses the retelling — that is AGENTS.md's
"the reason lives in one place" — while what is measured, what breaks if
somebody changes it back, and what was rejected stay where they are. Where
reading one shows it is a cross-reference rather than a retelling, leave it and
say so in `D-DOC-035`'s third **Wrong if**, because that is the entry the shape
of this report rests on. Commit in batches small enough that a reviewer can
check a cut against the entry it points at.
