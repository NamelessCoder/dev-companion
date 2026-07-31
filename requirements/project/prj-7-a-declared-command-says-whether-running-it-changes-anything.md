---
id: R-PRJ-7
status: held
---

# R-PRJ-7 — A declared command says whether running it changes anything

**Every command the project answer lists carries the body it was declared with
and what running that body does to the sources: a check that hands the code back
as it was, a change that rewrites something, or unknown where the body does not
say.**

It is read off the declaration and never by running it, `@name` references are
followed to the script they name, and a script that reaches one unreadable line
is unknown rather than safe. Unknown is a third answer and not a quiet no: a
test suite runs the project's own code, and nothing in a manifest covers what
that code writes. Nor is a check a promise that nothing is written — a checker
may keep a cache of its own; what it does not do is hand the code back
different.

Without it, "run the ones that change nothing" is an instruction nobody can
follow. A name does not carry the property and never will: `cgl` and `cgl:ci`
are the same tool one `--dry-run` apart.

**From:** three recorded `REVIEW-02` runs in two repositories (2026-07-31) that
were told not to change files and ran none of the fifteen commands they were
offered — among them `composer cgl:ci` and `composer test:php:lint`, which
change nothing and would have settled two of the findings the syntax run
derived from CI configuration instead.

**Held by:** `ProjectTest::aDeclaredCommandSaysWhetherRunningItChangesTheSources`,
`ProjectTest::aCommandThatDeclaresNothingReadableIsNotCalledSafe`
