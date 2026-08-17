# State how the three PHP numbers in the project answer relate

**Serves:** feedback/2026-08-17-211157-three-php-versions-are-reported-side-by-side-in.md
**Priority:** normal

Derive the relation between `phpConstraint`, `corePhpConstraint` and
`environment.php` in `typo3_project_describe` and state it in both halves of the
answer: whether the declared floor clears what the installed core requires, and
whether the environment the repository configures runs that floor or only some
version inside the range. The comparison is new machinery — `Versions::admits()`
answers per TYPO3 major and `^8.3` against `^8.2` is a minor-granular question —
so the first step is a reading of the PHP constraint spellings in the vendor
trees below `.environments/`, the way `D-VER-004` was measured one level up, and
a `#[DataProvider]` case per spelling. Add the field rather than renaming one,
say what it may not claim (nothing was executed on any of these versions), and
write the requirement beside `R-PRJ-008`.
