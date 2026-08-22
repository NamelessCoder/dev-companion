---
id: R-DIS-017
title: "An extension below Tests/ is the test setup's"
status: held
---

# R-DIS-017 — An extension below Tests/ is the test setup's

**An extension below a `Tests/` directory is reported as `fixture` rather than
as the project's own, in the project scope and in the extension scope alike.**

It stays in the answer: a package an installation loads and the answer omits is
one nobody can account for.

## From

`REVIEW-02`. `demo_package` below `Tests/Packages/` came back with origin
`project`, which the schema defines as "inside the repository, so what it is
working on" (2026-07-31).

## Held by

- `InstanceTest::aPackageBelowTestsIsTheTestSetupsRatherThanTheOneBeingWorkedOn`
