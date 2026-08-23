---
id: D-DOC-050
title: What is written about a class is a lookup
date: 2026-08-23
status: open
restsOn: [D-DOC-044, D-DOC-048]
coveredBy:
  - EntriesTest::aPathAnswersWithTheClassesItDeclares
  - EntriesTest::aTestIsListedWhereItNamesTheClassAndHoldsAnEntry
  - EntriesTest::anEntryIsAnsweredForTheClassItNames
  - EntriesTest::bothCorporaAreOneListKeyedById
---

# D-DOC-050 — What is written about a class is a lookup

**`bin/cli entries:lookup <path>` answers which decisions and requirements name
the code at that path, and which tests hold them.**

The attributes answer from the failing end, which is after the change. This is
the same coupling read before one.

## Evidence

- Read on 2026-08-23. 242 of 450 decisions and 219 of 222 requirements are held
  by a test, so a change that breaks one is printed by the run — `D-DOC-044`.
- The other end is not reachable at all. The attributes sit on tests, so a
  session opening `src/Knowledge/Hints.php` sees nothing written about it until
  something goes red, and 85 decisions name a class of ours and are held by no
  test, where nothing ever goes red.
- Both halves of the reading already existed: `Sources` maps a class to the file
  declaring it, and `RecordsTest` holds every backticked `Class::member` in the
  corpora to a class that has it. What was missing was the command.

## Decided

- One subject for both corpora, `Upkeep\Entries`, because "what is written about
  this" is one question. Which corpus an entry is filed in is what the answer
  carries rather than what the caller asks.
- A backticked class name is the reach. It is what `RecordsTest` already holds,
  so a name in the answer resolves or the suite is red.
- A test class is listed with the entries it holds where they can be read and
  with their count where a hundred of them would be the whole answer. What a
  long line says is how much rides on the class; the entries themselves are what
  the failure prints.
- Named in `AGENTS.md` and in the reading a todo starts with, since a command
  nobody runs before the change answers nothing.

## Assumed

- That an entry about a behaviour names the class it belongs to. It is
  `D-DOC-043`'s second **Assumed**, and this command is where it now costs
  something: a quiet answer reads as "nothing was decided".
- That a test naming a class runs over it. A test that names it in a docblock
  and asserts nothing about it is listed and holds nothing.

## Wrong if

- The answer is quiet for a class an entry does govern, and somebody changes the
  behaviour on the strength of the quiet. That is the first **Assumed** failing,
  and what would show it is an entry going stale under a session that ran this.
- The listing is read as what will break. It says what names the class, which is
  neither what runs over it nor what a change would falsify.
- Nobody runs it. Then what is needed is a check that fails rather than a
  command that answers, and this entry is the record of the cheaper thing having
  been tried.
