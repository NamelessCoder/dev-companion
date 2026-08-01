# Let `typo3_task_guide` be asked about more than one area

**Serves:** R-AUD-2

The audience is decided per path now (`D-SCO-8`), and this tool is the one that
cannot use it: `area` is a single string, so the `META-03` prompt reaches it as
one question and gets one answer. Give it the paths of the work — a `paths`
array beside `area`, decided by the same `Scope::audiences()` the two path tools
call — and split what the brief states per audience: the checklist, the checks
and the checkout discovery are already filtered entry by entry, so what is new
is which paths each filtered list is for. `outsideCore` and `audience` stay as
they are; `audiences` is the addition. It sits at the end of the queue because
the todo it comes out of named it as out of its own scope, which makes it new
work rather than the half that was left.
