# Check whether `D-KNW-3` has grown a fourth value

**Serves:** decisions/

`provenance` and `binding` stay two axes as long as no value reads naturally on
both; a fourth that does would mean they were one axis after all. Read the
current value sets of the two and settle whether that has happened. What would
hold it is a `KnowledgeTest` assertion that the two sets stay disjoint — cheap,
exact, and it fails on the day the merge becomes the right entry instead of
waiting for somebody to notice the overlap.
