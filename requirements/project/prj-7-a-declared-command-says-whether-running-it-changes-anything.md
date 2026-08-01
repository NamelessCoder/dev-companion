---
id: R-PRJ-7
status: held
---

# R-PRJ-7 — A declared command says whether running it changes anything

**Every command the project answer lists carries the body it was declared with
and what running that body does to the sources.**

That is a check, which hands the code back as it was; a change, which rewrites
something; or unknown, where the body does not say.

It is read off the declaration and never by running it, `@name` references are
followed to the script they name, and a script that reaches one unreadable line
is unknown rather than safe. What comes before the tool is not the tool: a
composer prefix and a leading `NAME=value` are the environment the command is
given, so they are out of the tool name and stay in the declaration the answer
shows. Unknown is a third answer and not a quiet no: a
test suite runs the project's own code, and nothing in a manifest covers what
that code writes. Nor is a check a promise that nothing is written — a checker
may keep a cache of its own; what it does not do is hand the code back
different.

Without it, "run the ones that change nothing" is an instruction nobody can
follow. A name does not carry the property and never will: `cgl` and `cgl:ci`
are the same tool one `--dry-run` apart.

**From:** a `REVIEW-02` run against `georgringer/news` (2026-07-31), whose six
declared commands all came back `unknown` because each of them starts with
`PHP_CS_FIXER_IGNORE_ENV=1` — so the `--dry-run` line and the fixing line were
the same answer, and the run picked the safe one by its own reading rather than
from anything this server told it; and three recorded `REVIEW-02` runs in two
repositories (2026-07-31) that
were told not to change files and ran none of the fifteen commands they were
offered — among them `composer cgl:ci` and `composer test:php:lint`, which
change nothing and would have settled two of the findings the syntax run
derived from CI configuration instead.

**Held by:** `ProjectTest::aDeclaredCommandSaysWhetherRunningItChangesTheSources`,
`ProjectTest::aCommandThatDeclaresNothingReadableIsNotCalledSafe`,
`ProjectTest::anEnvironmentAssignmentInFrontOfACommandIsNotTheCommand`
