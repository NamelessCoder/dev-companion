# Move the file a renumber was handed, not the first one sharing its id

**Serves:** src/Upkeep/
**Priority:** normal

`decisions:renumber` takes its decision "by id or by file", and
`DecisionRenumber::id()` turns a file back into an id before anything moves.
`Renumber::file()` then returns the first file whose name matches that id,
sorted by name. So where two files carry one id — the only state the command
exists for — the file the caller named is not the file that moves.

It went wrong twice on 2026-08-18, both times moving the entry already on `main`
instead of the branch's. Passing
`decisions/answers/ans-081-the-project-answer-states-how-its-three-php-numbers-relate.md`
renamed `ans-081-a-symptom-is-answered-across-the-domain-it-was-observed-in.md`
to `D-ANS-082`, and the same run moved
`knw-085-when-ddev-writes-additional-php-is-a-gap-this-server-owns.md` rather
than `knw-085-which-php-a-covered-version-runs-on-is-a-gap-this-server-owns.md`.
Both were reverted and the branch's own entry moved by hand instead, because
whichever branch merged first keeps its number —
[working-todos-in-parallel.rst](../../documentation/records/working-todos-in-parallel.rst).

Nothing fails afterwards. Both ids exist and both files are real, so the run
looks like it did what was asked, and what it renamed is an entry a reader on
`main` already knows by its old number.

What the fix is: carry the file through where the caller named one, and refuse
an id that two files claim rather than pick between them. The docblock on
`Renumber::file()` says "the file one id is written in", which is the assumption
that does not hold here.
