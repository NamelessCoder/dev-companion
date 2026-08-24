# Let a test class declare the entry it holds

**Serves:** D-DOC-048
**Priority:** low

Give `#[Decision]` and `#[Requirement]` the class target, and have whatever
writes `coveredBy` and `heldBy` read a class-level attribute as covering every
test in the class. `AGENTS.md` described that as how it works until 2026-08-24;
both attributes declare `Attribute::TARGET_METHOD` alone, PHPStan rejects the
class-level form outright, and the sentence was corrected to what the code does
rather than the other way round.

## What it buys

`BackendCssTest` holds one decision in eleven methods and repeats the attribute
eleven times. A class whose whole subject is one entry says that once, and a
reader of the file sees it above the class rather than inferring it from a
repetition.

## What has to be settled

- Whether a class-level attribute and a method-level one compose or the method
  wins. Both are defensible and only one can be implemented without a rule
  nobody remembers.
- Where the scan lives. `bin/cli decisions:cover` writes the front matter and
  `tests/Support/HeldEntries.php` prints what a failing test held; both would
  have to learn the second place an attribute can sit, and a class-level one
  that only half of them reads is worse than none.
