# Hold a backticked name to what the repository owns

**Serves:** requirements/, D-DOC-040
**Priority:** normal
**Run:** bin/cli decisions:check

**The design.** A backticked name this repository owns is a claim that the thing
exists now. Four guards already say that of four kinds — a tool
(`ToolNamingTest`), a test method and a requirement id (`DecisionsTest`), a
decision id and a test (`bin/cli requirements:check`) — and nothing says it of
the fifth, a member of one of this repository's own classes. `D-DOC-040` settled
the escape for the one corpus that needs one: a name being talked about rather
than pointed at is written plainly, which is why the tool names could be guarded
with no exception list at all. The same escape covers a member.

**What the reading found on 2026-08-22.** Of 1673 `Class::member` references the
records make to classes this repository owns, 1648 resolve and 25 do not, and
all 25 sit in `decisions/` — `documentation/`, `requirements/`, `skills/`,
`knowledge/` and `todo/` are clean. `CommandRunner::class` is `::class` and not
one of them. Every one of the remaining 24 is a present-tense claim:
"`Extension::CLASS_KINDS` is a closed list of thirteen names",
"`Documentation::links()` reads: 189 pages", "`Scope::isOutsideCore()` combines
the signals `R-SCO-001` orders". The members are gone or renamed —
`isOutsideCore` is `isOutsideTheCore`, `Schema::architectureHintRecord` is
`Schema::hintRecord`, `Environments::DEVELOPMENT_DRIVER` has `DEFAULT_DRIVER`
beside it — and `Domains::JAVASCRIPT` was removed by the entry that names it, so
that sentence is about then and wants no backticks.

**The step.** Read each of the 24 against the class it names, which is what
decides between correcting the name and unbackticking a sentence that is about
what a thing was called. A likeness between two identifiers is not evidence and
is what this entry refuses: the sweep of 2026-08-22 took four passages of 157
that had to be read one at a time, and guessing the other 153 would have been
indistinguishable from reading them. Then the guard lands in the same commit,
matching a fully backticked `Class::member` against the members of the classes
below `src/` and `tests/`, over every corpus — the five clean ones hold a
boundary, `decisions/` holds the rest. The decision is written by that commit
and not before.
