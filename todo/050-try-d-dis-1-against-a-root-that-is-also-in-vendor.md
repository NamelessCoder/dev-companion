# Try `D-DIS-1` against a root that is also in vendor

**Serves:** decisions/

Two shapes are named and neither has been seen here: a monorepo whose root
declares a TYPO3 package type without being the thing worked on, and a setup
that installs the root into the vendor directory as well, where both entries
resolve to one realpath under one key. Build both as fixtures and check that the
second collapses as intended and the first does not silently win. What would
hold it is those two fixtures as `InstanceTest` cases — the decision is about
resolution, and resolution is testable without an installation.
