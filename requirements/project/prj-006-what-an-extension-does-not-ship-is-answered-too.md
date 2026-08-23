---
id: R-PRJ-006
title: 'What an extension does not ship is answered too'
status: held
heldBy:
  - ProjectTest::whatAnExtensionDoesNotShipIsAnswerdRatherThanLeftOut
---

# R-PRJ-006 — What an extension does not ship is answered too

**An extension answer says what the extension ships beside its registrations,
and says it when the artifact is not there.**

Those are its manual, its README, the test layers it has, and its XLF files with
the source language each declares.

Everything else about an extension is found by reading further; these are the
ones with no file to find, so they are answered rather than left to be
discovered. The source language is reported as the fact the file states; which
one it ought to be stays a convention in the knowledge base.

## From

Three `REVIEW-01` runs (2026-07-31), none of which reported that the site
package ships no manual, and none of which reported the German `source-language`
of its three XLF files while reading them.
