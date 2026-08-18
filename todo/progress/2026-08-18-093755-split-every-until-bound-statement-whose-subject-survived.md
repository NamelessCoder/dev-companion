# split every until-bound statement whose subject survived its boundary

**Serves:** feedback/2026-08-18-070632-installation-setup-tags-the-create-site.md
**Priority:** normal
**Branch:** todo/split-every-until-bound-statement-whose-subject-survived
**Claimed:** 2026-08-18

Read each statement in `knowledge/hints/` carrying `until` against the checkouts
on both sides of its boundary, and split the ones whose subject is still there
under a new condition into an unbound half and a bound one — the shape
`D-VER-006` names, established on the `--create-site` statement this feedback
reported. `grep -rn '"until"' knowledge/hints/` is the list to walk; the range
being contiguous is what makes the defect invisible to `D-VER-001`'s reading, so
each one has to be read rather than checked.
