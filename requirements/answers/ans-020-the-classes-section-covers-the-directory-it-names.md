---
id: R-ANS-020
status: held
restsOn: [D-ANS-045]
---

# R-ANS-020 — The Classes section covers the directory it names

**`typo3_extension_scope` names every directory below `Classes/` and counts
every PHP file under it, and nothing it reads off the file tree is presented as
a registration.**

The answer promises the shape of an extension's `Classes/` directory. Thirteen
directory names were recognised and anything else was dropped — not reported
under another kind, not counted in a total, absent. A caller who checks the
section against `find` got a different answer and concluded the section is
wrong, which is
[`D-ANS-008`](../../decisions/answers/ans-008-a-number-a-reader-cannot-reproduce-is-read-as-wrong.md)
one level up: there it was a number nobody could reproduce, here it is the list.

The same holds for what this answer states with no declaration behind it.
`fluidRoots` is three `is_dir()` calls, so the line rendering it stands apart
from the service tags and the middlewares and says the extension ships the
directory rather than that it declared a root.

## From

`feedback/2026-08-03-164651`, a conformance audit of EXT:guidedtour against a
TYPO3 14.3.5 installation. Re-run on 2026-08-03:
`Classes/Utility/MascotResolver.php` is in no line of the answer, and it is the
only class in that extension touching the public-resource URL API the audit was
asking about. Measured against `.checkouts/14.3` the same day, `core` reports
106 of the 1508 PHP files below its `Classes/`.

## Held by

- `ProjectTest::everyDirectoryBelowClassesIsInTheAnswer` — the first half. Every
  directory below `Classes/` is named, a PHP file lying directly in it is
  counted where it lies, and the total is the number `find` gives.
- `ProjectTest::aFluidRootIsRenderedAsADirectoryRatherThanAsADeclaration` — the
  second half: the line says the directory is shipped rather than declared.
