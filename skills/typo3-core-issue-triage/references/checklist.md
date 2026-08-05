# The verdicts, and what each one owes

One issue gets one verdict. They are not degrees of confidence in a single
finding: each names a different thing that is true, and each is owed different
evidence. Pick the verdict first and the missing evidence names itself.

## Still happens

The behaviour the report describes is what the branch does today.

Owes: the code path, at its file and line, or the steps that reproduced it and
what was seen. A failing test where a layer can hold it. The branch the
reproduction ran on.

Not enough on its own: that the code looks like it would still do this. Reading
a method is evidence about the method and the report is usually about the
interaction of two.

## Gone

The behaviour cannot happen any more, and the reason is in the checkout.

Owes: what changed, named — the method that no longer exists, the branch that is
no longer taken, the entry that says it was reworked. A reproduction that came
out clean is not this verdict; it is the one below.

The trap: fixed by accident is as final as fixed on purpose, but only where the
mechanism is named. "It works for me now" is not a mechanism.

## Not reproducible as written

The steps in the report do not produce what it describes, and nothing says
whether that is the report or the branch.

Owes: what was tried, what happened instead, and which of the two it is — the
steps were incomplete, the environment differs, or the report never carried
enough to try. Say which parts of the report were verifiable and which were not
tested at all.

This is the verdict that most often gets written as "gone". They are opposite
outcomes: one closes the issue, the other asks the reporter a question.

## Superseded

Something else already covers this — a patch under review, a merged change, a
duplicate, a rework that made the request moot.

Owes: the change or issue number, its state, and whether it does the same work.
An alternative closes an issue only where what it drops is nothing the reporter
was reaching for. Name the arguments and the behaviour the original had and the
replacement does not.

## Not a defect

The branch behaves as the project intends. What the report wants is a change of
intent.

Owes: where the intent is stated — the documentation, the docblock, the test
that pins the behaviour, the changelog entry that introduced it. A verdict of
"works as designed" with no source is an opinion in a maintainer's voice.

This does not close the need. Say what it would take as a feature, and that the
argument for it is a different one: the argument that carries a bugfix is the
same inconsistency inside one version, and finding the place where the system
already does the right thing is what turns a wish into a defect.

## Cannot be settled here

The question is real and this checkout cannot answer it.

Owes: what specifically is missing — a running installation, a database that
behaves differently, a browser, the reporter's configuration, a version no
longer covered. And what would settle it, so the next person starts where this
stopped.

Legitimate and underused. It is the honest end of a triage whose reading ran out
before the evidence did.

# Before writing any of them

- Which of the report's three claims was verified: what was seen, what was
  believed to cause it, or what was wanted. Only the first is what a checkout
  settles, and verifying the cause and reporting the issue invalid is the
  standard failure of this work.
- Which branch the verification ran on, said out loud. A verdict with no branch
  is unrepeatable.
- Whether the review server was asked. The cheapest outcome sits there and it
  costs one call.
- What was not established. The part that was skipped is the next person's whole
  task, and a verdict that reads as complete hides it.
- Whether anything here is a recommendation to close, reassign or reopen. That
  is the maintainer's act; the triage supplies what it rests on and stops.
