---
id: D-DOC-062
title: "A todo's head is front matter"
date: 2026-08-27
status: open
restsOn: [D-DOC-045, D-DOC-061, D-FBK-002]
coveredBy:
  - FeedbackTest::aRecordedFeedbackArrivesWithTheCardThatAsksForItsJudgement
  - TodoTest::everyTodoSaysWhatItIsBeforeItSaysAnythingElse
---

# D-DOC-062 — A todo's head is front matter

**A todo declares what it serves, where it stands and what it waits on in front
matter, read by the `Entry` a requirement and a decision are read by.**

`D-FBK-002` put the head in the bold-label shape because that was the shape
`requirements/` and `decisions/` were read in. Those two moved to front matter
with `D-DOC-045` and the todos stayed, so the head was the last data in this
repository parsed out of prose. The next concrete step is not part of the move:
it stays the paragraph under the title, which is what a session starts from.

## Evidence

- What the labelled shape cost `Todo` was 104 lines: a pattern per line, a rule
  putting an indented line back onto the field above it, a rule telling a blank
  line inside a head from the one that ends it, and a rule telling a head from
  the first paragraph of a `reference/` file that has none. Reading the same six
  values out of front matter is 34.
- The labelled reader was the second one of the same idea, and it had a bug of
  its own: it appended through an offset and lost the pair, which `D-COD-005`
  records as one of the two behaviour fixes level 7 found.
- 40 files carried the head — 26 queued, 8 waiting, 5 recurring — and the one in
  `reference/` carried none, which the special case existed for. A file with no
  front matter now has no fields and needs no case.
- A question is what the labels could not carry. The eight waiting todos wrap
  theirs over several lines and two carry a second paragraph, which the reader
  joined with spaces; folded YAML keeps the paragraph and needs no quoting for
  the colon two of them contain.

## Decided

- Six keys, and `Todo::FIELDS` is the list: `serves`, `priority`, `every`,
  `checked`, `run`, `waitingOn`. `bin/cli todo:check` reports a key that is none
  of them, so a misspelled `waiting_on` is a question that stops being asked
  rather than one nobody notices.
- No `id:` and no `title:`. A todo's id is its file name (`D-DOC-061`) and every
  listing prints its heading, so either would be a copy that nothing reads —
  what `D-DOC-045` names as the thing to refuse. An entry carries both because
  the generated group listing reads front matter and its file name is a slug of
  the title rather than the id alone.
- `waitingOn` is a folded block, `>`. It takes a question of any length, and a
  plain scalar carrying a colon and a space is a mapping.
- `serves` and `run` are flow lists, so an ordinary todo's head is two lines and
  a `serves` of two ids stays one.
- The step stays the paragraph under the title. `D-FBK-002` rejected turning it
  into a field, and that is still what a session reads first and starts from.
- `Todo::park()` moves the file instead of rewriting it from what was read.
  Nothing in a move changes, and re-emitting the front matter would rewrite
  somebody's question as whatever a dumper makes of it.
- What writes `checked` back is the session that ran the todo, as before.
  `bin/cli todo:next` hands the recurring todo over with that sentence, and no
  command edits a todo.

## Assumed

- That a session writing a todo by hand writes the front matter correctly. It
  already writes one for every requirement and decision it adds, and the card a
  feedback arrives with is written by `Card::write()` rather than by a session.
- That nothing outside this checkout reads a todo. `todo/` is not published, no
  tool answers from it, and the queue is read by `bin/cli` and by people.

## Wrong if

- Front matter that will not parse reads as front matter that is empty.
  `Entry::matter()` answers `[]` on a parse error, so a file nobody can read is
  reported as a todo that serves nothing. What would show it is a session
  repairing a `serves:` line that was never the problem.
- The keys grow into a second document. Six is what a command or a check reads,
  and a seventh that only a person reads belongs in the step.
- A long `waitingOn` stops being read, because the question now stands above the
  title rather than under it and `bin/cli prose:format` leaves front matter
  alone. `bin/cli todo:waiting` prints it whole, which is the reading that
  matters.
