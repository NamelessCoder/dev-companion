---
id: R-ANS-12
status: held
---

# R-ANS-12 — An answer that cannot read something says so

**An installation answer that cannot read something says so instead of
returning a shorter list.**

Reading a declaration file follows a value the file assigns to a variable once;
a value assembled at runtime, taken from a constant, or read from a variable
the file reassigns is still declined, and the answer names which of those it
cannot follow. Where a whole file yields nothing — its list exists only once it
has run — the file itself is named, because a section left out for being empty
reads the same as a file that was never there.

**From:** the third `REVIEW-01` run (2026-07-31), where `typo3_extension_scope`
reported three content elements of four. The fourth wrote `$contentType =
'…'` above its `addRecordType()` call, and an earlier run had already read
the omission as a template with no registration — a defect the extension does
not have.

**Held by:**
`ProjectTest::anIdentifierThatTookADetourThroughAVariableIsStillRead`,
`ProjectTest::aRegistrationFileBuiltInALoopIsNotDeterminableRatherThanWrong`
