# Put SiteTest back on the theme that exists

**Serves:** build/guides/
**Priority:** high

`composer ci` is red on `main` and has been since `c2bef123` took the tokens
from the package instead of a copy:
`SiteTest::everyTokenTheThemeNamesIsDeclared` still reads
`build/guides/theme/assets/tokens`, which that commit removed, and Finder throws
`DirectoryNotFoundException` in a checkout that has not installed the package.
`SiteTest::theThemeWritesNoColourOfItsOwn` fails beside it because `51e70499`
wrote `#8A8378` and `#726C63` into the header comment of `site.css`, explaining
that very rule, and the regex reads a comment as a declaration. Decide per test
whether it reads the installed package or is dropped, and strip comments before
the colour scan. Both were found by a session working an unrelated todo on
2026-08-11, and neither is what `D-DOC-023` is about being wrong.
