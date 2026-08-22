---
id: R-DIS-002
title: 'The packages are read from the declared vendor directory'
status: held
---

# R-DIS-002 — The packages are read from the declared vendor directory

**The packages of a Composer installation are read from the vendor directory it
declares, not from the default.**

## From

The extension checkout with `config.vendor-dir=.build/vendor` that was reported
as "no installation found" (2026-07-29).

## Held by

- `InstanceTest::aProjectThatMovedItsVendorDirectoryIsStillFound`
