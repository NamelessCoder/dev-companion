# Judge what a path list handed to a functional run owes its caller

**Serves:** feedback/2026-08-24-133651-phpstan-rejected-a-new-core-test-and-finding.md
**Priority:** low
**Branch:** todo/phpstan-rejected-a-new-core-test-and-finding
**Claimed:** 2026-08-24

Trimmed on 2026-08-24 to the part the PHPStan work left. The narrowing idiom is
the `core-static-analysis` hint and the hint-versus-rule routing is a clause in
each of the two descriptions; `D-KNW-114` and `D-ANS-072` carry both readings
and neither is open here.

What is left is unjudged. A session handed
`./Build/Scripts/runTests.sh -s functional` a path list carrying
`typo3/sysext/tstemplate/Tests/Functional`, the whole container run died on
"Test file not found" before a test ran, and the run had to be repeated with the
path removed.

The one reading already made: on `.checkouts/main` at `3cbdea24dd`, five system
extensions carry no `Tests/Functional` at all — `belog`, `filemetadata`,
`opendocs`, `reports` and `tstemplate`. That is a list one commit changes and a
caller reads out of the checkout it already has open, so `D-FBK-027` is the
entry the judgement has to argue against: what a statement can carry is that the
dispatcher validates no path and that one missing entry costs the whole run,
which is the part no `ls` tells you in advance.

Judge it before writing anything. Where it lands is
`knowledge/test-suite-hints.json` or the `core-tests` hint, and where it lands
nowhere the answer is a decision saying so.
