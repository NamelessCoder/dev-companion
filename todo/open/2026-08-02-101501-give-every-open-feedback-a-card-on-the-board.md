# Give every open feedback a card on the board

**Serves:** feedback/
**Priority:** normal

Write one todo per open feedback, with `**Serves:**` naming it and nothing of
its text copied — the card points and the feedback is read where it lies, so
the two cannot drift in what they say. What the card asks for is the
**judgement**, not the fix, which is the distinction
[judging.md](../documentation/feedback/judging.md) opens with: a feedback is a
report and not a work item, and the work item is deciding what to do about it.
That page stays where it is and is what the card names. More than one card for
one feedback is legitimate, because a single report can need more than one
change.
`bin/cli todo:sync` is what reconciles and `bin/cli repository:check` what
reports the drift: every open feedback has at least one card, and no card
serves an archived one, which `Todo::unreadable()` already refuses. A card
nobody has judged carries no priority and therefore sorts below everything
judged, which is exactly where the sighting used to leave it — so
`todo/recurring/run-the-next-open-feedback-s-query-against-the-server.md` goes
with this commit; what it said about re-running a feedback's own query belongs
in `judging.md` beside the rest, and nothing new is written for it.
`R-FBK-007` is
rewritten onto the new mechanism: the property it holds is the same, and what
carries it is the priority rather than the separation.
