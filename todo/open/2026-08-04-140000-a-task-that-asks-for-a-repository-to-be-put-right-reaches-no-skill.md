# A task that asks for a repository to be put right reaches no skill

**Serves:** R-GUI-006
**Priority:** normal

`typo3-extension-conformance` stopped offering to change anything on 2026-08-04:
its `description` opened "Review, audit, or improve a TYPO3 project…" and the
word is gone, because a client selects a skill on that line and the skill was
being loaded for change requests whatever its body said (`D-SKL-014`, **Since
then**). What the word was also doing is the hole this card is about. A request
worded as a change — "look over my repository and put it right", "improve the
code quality of my sitepackage" — now reaches no workflow at all.

That was established rather than assumed. `TaskIntents::detect()` was run in
this checkout on 2026-08-04 against four such wordings, and none of them matches
an intent, so `TaskIntents::scoped()` returns nothing and
`TaskIntents::skills()` returns an empty list: `typo3_task_guide` names no
skill. The `audit` intent is the one that would have to carry it and its needles
are `audit`, `conformance`, `code review`, `review the`, `review this`,
`review of` and `reviewing` — every one of them a word for looking, none of them
a word for fixing. The same call with
`review the TYPO3 project and site package` returns
`typo3-extension-conformance`, so the intent works and the wording is what it
does not reach.

So the guide channel never carried this shape. Removing "improve" did not open
the hole; it removed the one route there was, and that route was the wrong one —
it sent a request for changes into the workflow that exists to make none
(`R-GUI-006`). The correction is right and the hole is real, and they are two
findings rather than one.

What is open is what such a task should reach, and it is a question about what
is wanted rather than one this checkout answers. Two things it is not: inventing
a skill for it, and widening another skill's `description` until the words fall
into it — the second is how the removed word got there. The candidates worth
putting to the maintainer are an intent in `knowledge/task-intents.json` that
recognises the shape and routes it to whichever workflow owns the change, a
brief that names conformance as the reading such a task starts with and hands
the changes on from its findings, and leaving it unrouted on the ground that a
task naming no subsystem cannot be routed to one workflow at all. Read
`D-SKL-013` first: it is where five of the thirteen intents route and where the
reason the other eight do not is written down, and three of those eight are
unrouted for exactly the reason the third candidate proposes.

`normal` rather than `high`: no recorded session has brought this wording, so
nothing measured what it costs, and `D-AUD-003` — the entry that measured a
review prompt reaching no skill — is about a wording the `audit` intent now
catches. `normal` rather than `low`: this is a whole shape of request, not one
phrase, and every session bringing it lands in Bash with none of the four owning
calls made.
