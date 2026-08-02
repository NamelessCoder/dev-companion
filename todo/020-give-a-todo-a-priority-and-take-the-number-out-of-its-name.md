# Give a todo a priority and take the number out of its name

**Serves:** todo/

Replace the `NNN-` prefix with a `**Priority:**` line in the head and a
timestamp in the file name, so that moving a todo is editing one line rather
than renaming a run of files. Three values, a closed list declared in one place
and held by a test, the way the five tool verbs are. `bin/cli todo:next` then
sorts rather than walks three groups: what has a clock and is due, then by
priority, then by age — and `bin/cli todo:check` loses its number branch
together with the collision two sessions produce when they both queue work and
both read the same last number. `D-FBK-008` is amended rather than revoked in
the entry that comes with the commit: it rejected a **position** field, whose
order is only readable once something has parsed every file, and a priority is
a class rather than a rank, so the age in the name is what a listing still
shows.
