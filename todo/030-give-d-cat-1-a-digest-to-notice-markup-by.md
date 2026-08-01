# Give `D-CAT-1` a digest to notice markup by

**Serves:** decisions/

The binding is derived from the names in an entry, so markup that changed while
every name stayed reads as unchanged, and a range with a hole in it reports as
no binding at all. Both are readable from `.checkouts/`: derive the binding for
every catalog entry on every covered version and compare it against what the
entry claims. What would hold it is a digest of the matched markup recorded per
entry per checkout in `bin/cli catalog check`, so a silent rewrite fails the
check instead of aging quietly into a wrong answer.
