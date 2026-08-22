---
id: D-FBK-002
title: The order of the work is declared, not inferred
date: 2026-07-31
status: confirmed
---

# D-FBK-002 — The order of the work is declared, not inferred

**Every todo declares what it serves and how it recurs, and `bin/cli todo:next`
reads that rather than guessing at the prose around it.**

The file always held the order of the work, and only a person could see it.
Three kinds of section sit in it and they are indistinguishable from outside:
the standing ones that are never deleted, the queue, and the sections that are
neither — the environment table, the list of what is deliberately not queued. A
command that says what to do next has to tell them apart, and the question was
whether to declare it or derive it.

## Evidence

- The file on the day the line was added. Eleven sections: three standing, six
  items, two that are neither. Six of the six items already opened with "This
  serves X and Y" in prose, so the coupling existed as a sentence and was read
  by nothing. The one thing that did read the file, `Unresolved`, searched it
  for an id anywhere — which the "not queued, and deliberately so" section
  answers as loudly as an item does.

## Decided

- One line per section, in the bold-label shape `requirements/` and `decisions/`
  are already read by. Three alternatives were rejected with it. Inferring the
  kind from position (the standing ones come first) breaks the day somebody
  inserts a section, silently and in the direction that hides work. Moving the
  queue into a structured file turns the next concrete step into a field, and
  that paragraph — written for whoever has read nothing else — is the most
  valuable thing in the repository's upkeep. Making it a tool of the server
  rather than a command of `bin/cli` was rejected for the reason `Cli` already
  gives: this server is about the TYPO3 core, not about the repository it lives
  in.

## Assumed

- That a session offered the start as one command runs it, rather than reading
  the four files itself as before. Nothing in this repository can show that — a
  forward run happens in someone else's checkout, and a session here may not
  grade its own behaviour as evidence. What it rests on instead is that the
  command is strictly cheaper than what it replaces and returns what the reading
  would have.
- That the line stays current because it is also what makes an item readable. It
  is written when the item is, it names what the commit that finishes the item
  will delete, and `bin/cli todo:check` fails when it names a feedback that is
  already gone. A marker nobody would otherwise need would have been the kind
  that rots.

## Wrong if

- Sections start arriving without the line and `bin/cli repository:check`
  becomes the thing that adds it after the fact — then it is bureaucracy, and
  the kind has to be derived from the text after all. Or if the paragraph under
  a heading thins out while the `Serves:` line grows, which is the same file
  becoming a fourth backlog by another route: what a session cannot start from
  is a list of ids.

## Since then

The one line became a head of several, and the three kinds became two: a todo
that recurs carries `**Every:**` and a todo that does not is the queue. The file
then became a directory, where the kind is what the todo sits in and the head
carries the rest. What this entry settled survived both, which is the point of
declaring it: each was an edit rather than a rewrite. See
[`D-FBK-003`](fbk-003-a-session-is-handed-one-todo-not-the-file.md) and
[`D-FBK-008`](fbk-008-one-todo-is-one-file-and-the-queue-is-in-the-names.md).

## Confirmed on 2026-08-02

Neither **Wrong if** has happened, measured against a queue that had grown from
six items to twenty-six. Every queued todo carries the line, so nothing has had
to be added after the fact — and `bin/cli todo:check` reports a bad one rather
than repairing it, which it did that day for a `Serves:` naming a file where a
directory was meant. The second was the one worth measuring: the shortest body
in the queue is 79 words, the median 95, and not one is under 40. The
correlation the entry feared runs the other way — the only two todos naming two
ids have the longest bodies of all, 142 and 188 words, because a step answering
for two things needs more saying, not less. What holds the mechanical half was
already there rather than being added:
`TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead` asserts a body on
every todo, and over all four states rather than the queue alone.
