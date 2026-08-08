---
id: R-KNW-066
status: open
restsOn: [D-KNW-065]
---

# R-KNW-066 — A core PHP change is told what the class's public surface commits it to

**A change to a core PHP class is told, before the diff is finished, that a
public or protected method on a non-final class is an override point.**

Adding a parameter to one is a signature change for every subclass that
overrides it, an optional parameter included, and PHP fails those subclasses on
load. Nothing in a core checkout reports it: no core class has to override the
method, so the unit, functional, coding-guidelines and static-analysis runs are
all green on the breaking draft.

The answer owes the consequence in the direction the session meets it. What is
gained by the widening is one call site; what it costs is `[!!!]`, a Breaking
changelog entry, an extension scanner matcher entry and the release managers'
decision about the target branch — and a fix owed to two maintained release
lines cannot carry the signature change at all. The additive setter and making
the class or the method final first are the two shapes that avoid it, and which
of them the project prefers is part of what the answer has to establish.

The two places that state the rule today state it for what a change removes and
narrows: the `breaking-not-assessed` check of `typo3_commit_message_guide`, and
`## Breaking Changes` of the commit message rules that
`typo3_rule_lookup(query "breaking change")` returns. Both are read as not
covering an added optional parameter, and both are reached after the diff is
written.

## From

The feedback of 2026-08-08 22:43, from a core patch for Forge #58705 in
`/home/benji/projects/typo3-cms`. The first draft added an optional third
parameter to the public `GifBuilder::start()` on a class that is not final;
`typo3_hint_lookup` with both changed paths returned `fal-processing`,
`fal-basics` and `system-extension-boundaries`, and 3613 functional tests, 235
unit tests, cgl over 6300 files and phpstan over 6265 were green on it. What
raised it was the `breaking-not-assessed` info line of
`typo3_commit_message_guide`, after the diff was finished and every suite had
run.

## Held by

Nothing. The statement is not written: `bin/cli hints:probe` reaches no hint on
the rule, and `non-final`, `override point` and `optional parameter` occur
nowhere below `knowledge/` or `skills/`.
