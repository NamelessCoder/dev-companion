---
id: R-PRJ-010
status: held
restsOn: [D-ANS-082]
---

# R-PRJ-010 — The project answer relates its PHP numbers rather than listing them

**Where the project declares a PHP floor, the answer states where that floor
sits against what the installed core requires and against what any configured
environment runs.**

Three numbers have been in that answer since `R-PRJ-008` put the environment's
beside the two declared ones, each field's description naming the other two to
say which number it is not. What none of them said is how the values stand to
each other, and that is where the defect lives: a floor above what the core
needs is a range the project narrowed itself, and a floor no configured
environment runs is a claim every check passes without testing.

Read from the constraints as the files spell them, so `R-PRJ-001` still holds
and the answer arrives on a fresh clone. Nothing is executed to find it out,
which is also the whole of what it may not claim: it says what the project
promises, never that the promise works.

Stated even where the three agree, for the reason the answer already states the
core's floor where it repeats the project's own — a line dropped when nothing is
wrong cannot be told from one that was never computed.

A floor is a floor and not a range. Only the lowest version each constraint
admits is compared, so an environment above what a constraint's own upper bound
allows reads like one inside it, and a spelling the reading will not claim to
read costs the whole statement rather than buying a wrong one.

## From

`feedback/2026-08-17-211157` (2026-08-17), a v14 demo site in
`/home/benji/projects/site-demo`. `typo3_project_describe` reported
`phpConstraint: "^8.3"`, `corePhpConstraint: "^8.2"` and
`environment.php: "8.4"` in one answer. The session declared a floor two minors
above what its own core required, ran every command on a third version, and
executed no line on the one it claimed to support — and reports that the answer
which would have shown it was one it had already read.

## Held by

- `ProjectTest::theThreePhpNumbersAreRelatedAndNotOnlyListed`
- `ProjectTest::aFloorTheEnvironmentRunsIsSaidToBeRunAndNotLeftOut`
- `ProjectTest::aFloorTheCoreRefusesAndAnEnvironmentUnderItAreBothSaid`
- `ProjectTest::aProjectWithNoReadableFloorIsRelatedToNothing`
- `VersionsTest::aPhpSpellingFromTheCheckoutsAnswersItsLowestVersion`
- `VersionsTest::theFloorIsReadOneLevelBelowTheMajorTheRestOfThisAnswers`

The reading is minor-granular, which `Versions::admits()` is not: it answers per
TYPO3 major, so `^8.3` and `^8.2` are one answer to it. What a manifest actually
spells was measured rather than assumed — every `require.php` below
`.checkouts/{12.4,13.4,14.3,main}` and their vendor trees on 2026-08-18, 556
constraints in 36 distinct spellings. Each one is a case in the provider above
and each expectation is composer/semver's own answer for that spelling, taken by
asking it for the lowest major.minor it admits any release of. The one shape
left unread is Composer's hyphen range, which occurs in none of them and which
read as the comparators it splits into would answer its ceiling.
