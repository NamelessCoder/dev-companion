# Name the entry back from the test that holds it

**Serves:** D-DOC-043
**Priority:** normal
**Run:** bin/cli decisions:check

Take one test file at a time and put the entry's id into the docblock over each
method a decision names, in the form the corpus already uses — a trailing
clause, `— \`D-ANS-050\``, rather than a line of its own.
`bin/cli decisions:check` reports how many are left; the reading behind it is
`Decisions::unnamedByItsTests()`, which walks back from the declaration rather
than splitting on an indented docblock, because a run of `composer cgl` moves
that and a number nobody can quote is worse than none.

What earns the work is the direction the naming does not run today. A session
that changes a behaviour stands in the test, not in `decisions/`: it makes the
test green again and never learns which entry rested on it, which is how
`D-ANS-045` came to record a closed list of thirteen names against a method that
reads the directory. Where the reading finds a test that says nothing about the
entry **because it holds a different claim**, that is the finding rather than a
missing clause — the entry's **Covered by** names the wrong test, and saying so
is worth more than the id.

The guard lands when the count reaches zero and not before, since nothing may
fail on a corpus written under the older rule. `D-DOC-043` carries why the
absence of a test is read out rather than demanded, and the same holds here.
