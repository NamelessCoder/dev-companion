# Decide whether a conformance audit records what it dropped

**Serves:** R-SKL-011
**Priority:** low
**Branch:** todo/decide-whether-a-conformance-audit-records-what-it-dropped
**Claimed:** 2026-08-03

`D-SKL-007` gave the core patch review a dropped-candidate section and scoped
itself to that skill deliberately. The conformance checklist already carries two
thirds of the same idea — the finding gate records an unproven claim as a
question rather than a violation, and a security verdict has to be disproved
before it can be dismissed — and it carries no obligation to name what an audit
raised and let go.

The question is whether the third part belongs there, and it is not obvious. A
patch review reads one diff and a handful of candidates; an audit reads six
surfaces of a whole package and would raise more candidates than a reader will
sit through. Read a recorded conformance run in `scenarios/runs/` for how many
dismissals it actually made before deciding: where the count is small, widen
`R-SKL-011` and hold it over both checklists; where it is not, say so in a dated
section under `D-SKL-007` and delete this card.
