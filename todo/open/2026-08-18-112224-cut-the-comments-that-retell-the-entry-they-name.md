# Cut the comments that retell the entry they name

**Serves:** src/, tests/
**Priority:** low
**Run:** bin/cli prose:check

The sweep was run on 2026-08-18, longest first, from the 59 lines above
`src/Upkeep/Todo.php` down to 14: every comment over 13 lines was read against
the entry it names and cut to what the code cannot say, which took the list from
240 to 166 and the comment share of the PHP from 33.5% to 32.2%. What is left is
the tail, and `D-DOC-035`'s **Since then** is what to read before working it:
the 33 still over 13 lines are ones this sweep already cut, and the ten-line
line counts the delimiters, the summary and the `@param` of a docblock, so an
annotated one has three lines of prose to fit a sentence and an id into. The
step that is open is the rest of the tail, at 11 to 13 lines, which was not
opened one at a time — read each against its entry, and where reading it shows a
cross-reference rather than a retelling, leave it and say so in that entry.
