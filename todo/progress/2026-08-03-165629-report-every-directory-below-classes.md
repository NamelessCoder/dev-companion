# Report every directory below Classes/, and every PHP file under it

**Serves:** R-ANS-020, feedback/2026-08-03-164651-task-full-conformance-audit-of-the-project.md
**Priority:** high
**Branch:** todo/report-every-directory-below-classes
**Claimed:** 2026-08-03

`Extension::CLASS_KINDS` at `src/Installation/Extension.php:34` is a closed list
of thirteen names, and `Extension::classes()` below it iterates that and nothing
else. So `Classes/Utility/` leaves the answer without a trace, and so does a PHP
file lying directly in `Classes/` — 106 of 1508 files for `core`, measured
against `.checkouts/14.3`. Settle against the tool which shape carries the
coverage: a row per directory, the `Other` bucket the feedback asks for, or a
total beside the breakdown. Then carry it through `Extension::classes()`, the
`classes` schema at `src/Tool/ExtensionScope.php:125`, the rendered line at
`:284`, and a test beside `ProjectTest::aClassCountSaysWhatItCounted`.
`D-ANS-045` is the boundary the shape has to meet.
