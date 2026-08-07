# Extbase has no hint about its own persistence layer

**Serves:** feedback/2026-08-07-132426-extbase-persistence-paths-return-fal-storage.md
**Priority:** high

Nothing in the corpus covers what a caller standing in
`typo3/sysext/extbase/Classes/Persistence/` is working on. That was established
on 2026-08-07 by reading the two candidates rather than their titles:
`persistence-reading` is the core `QueryBuilder`, the four restrictions and
`PageRepository`'s workspace and language overlays, and `extbase-domain-mapping`
is the model, the table it maps onto and its orderings. Neither reaches
`Typo3DbQueryParser`, `ColumnMap`, `DataMapper` or `Backend::insertObject()`.
Both titles read as though they do, which is why the reporting session named
them and why the judgement that followed took its word for it — `D-ANS-060`
carries that correction. So write the hint the subsystem does not have: how a
`Query` becomes SQL, what a `ColumnMap` decides, and where `Backend` writing a
record diverges from what DataHandler would do. `bin/cli checkouts:update`
first, because all four are missing in this working directory and none of this
may be written from recall. Give it `appliesTo` vocabulary that reaches the
paths, since the pruning that stopped the wrong answers left the right one still
absent, and `R-ANS-026` deliberately demands only silence until this exists.
Read it against `todo/open/2026-08-07-160500` before writing: that card
establishes what a TCA type stores, and the two meet at what
`Backend::insertObject()` omits.
