# A symptom about a missing asset reaches the layout hint that explains it

**Serves:** feedback/2026-08-17-212010-the-knowledge-is-indexed-by-subject-and-a.md
**Priority:** normal
**Branch:** todo/a-symptom-about-an-asset-reaches-the-layout-hint
**Claimed:** 2026-08-18

Curation, not matching:
`bin/cli hints:probe "f:asset.css does not appear in the rendered page"` selects
Fluid, and `fluid-layouts-sections` is inside it and still misses, because the
query carries none of the hint's words — it reaches the hint only as "a
viewhelper call outside a section is never executed". Give that hint the words a
caller arrives with, in `appliesTo`, in a statement, or in both, and verify
against the checkouts what a ViewHelper call outside a section actually does
before writing it. `D-ANS-081` measured the miss and `D-ANS-084` is the crossing
rule that does not reach this one, because the gate was never what withheld it.

The rest of the feedback is worked off: the same probe on "the content elements
render in reverse order" now returns `datahandler-placement`, and the `task`
parameter says a symptom is a query it takes.
