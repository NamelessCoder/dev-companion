# A todo cannot serve a decision and the page says it can

**Serves:** documentation/records/
**Priority:** normal

`Todo::unreadable()` accepts a requirement, a scenario, a feedback and a
directory, and answers anything else with *which is none of a requirement, a
scenario, a feedback, or a directory of this repository* — so
`**Serves:** D-DOC-035` fails `bin/cli todo:check` and `TodoTest`.
`documentation/records/working-a-todo.rst` opens its reading list with "the
``Serves:`` line names a requirement, a decision, a feedback, a directory" and
goes on to say what a todo serving a decision usually is. Found on 2026-08-18 by
writing exactly that line.

Two sides, and which one moves is the whole of the question. Either the page is
corrected to the four the code takes, which costs a sentence and leaves a todo
carrying a decision's id nowhere to put it; or `unreadable()` learns the
`D-XXX-000` shape against `Decisions::all()` beside the requirement branch it
already has, which costs a branch and a test case and makes the page true as
written. The second is the recommendation: the page describes serving a decision
as the ordinary case, and a queue where the reason for the work is a decision
has no other field to say so.
