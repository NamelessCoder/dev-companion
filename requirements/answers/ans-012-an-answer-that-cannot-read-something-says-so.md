---
id: R-ANS-012
status: held
---

# R-ANS-012 — An answer that cannot read something says so

**An installation answer that cannot read something says so instead of returning
a shorter list.**

Reading a declaration file follows a value the file assigns to a variable once;
a value assembled at runtime, taken from a constant, or read from a variable the
file reassigns is still declined, and the answer names which of those it cannot
follow. Where a whole file yields nothing — its list exists only once it has run
— the file itself is named, because a section left out for being empty reads the
same as a file that was never there. A file the answer never opens at all is
said too, and said apart from those: it is not a degradation, so the list of
files that defeated the parser is no place to look for it and its emptiness is
no claim that everything was read.

## From

The third `REVIEW-01` run (2026-07-31), where `typo3_extension_scope` reported
three content elements of four. The fourth wrote `$contentType = '…'` above its
`addRecordType()` call, and an earlier run had already read the omission as a
template with no registration — a defect the extension does not have.

## Held by

- `ProjectTest::anIdentifierThatTookADetourThroughAVariableIsStillRead`
- `ProjectTest::aRegistrationFileBuiltInALoopIsNotDeterminableRatherThanWrong`
- `ProjectTest::theFilesThatRegisterByRunningAreSaidToBeUnread`
- `ProjectTest::anExtbasePluginIsToldApartFromAnElementWhoseTemplateIsMissing`
