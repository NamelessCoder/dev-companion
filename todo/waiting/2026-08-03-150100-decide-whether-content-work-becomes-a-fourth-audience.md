# Decide whether content work becomes a fourth audience

**Serves:** R-AUD-001
**Priority:** normal
**Waiting on:** put to the maintainer on 2026-08-04 with both options priced,
    and deliberately left open — not unread. The answer was that this one waits,
    so a session that reaches this card is not the first to look at it and
    re-deriving the question buys nothing. What would move it is evidence rather
    than another reading: a session that needed a record and said so, or an
    adopted interface contract fixing what a conformant server owes on identity
    — the draft RFC read on 2026-08-04 is a community proposal and settles
    nothing (`D-SCO-010`). The question as it stands: stay on the model side and
    write it into `doesNotCover`, or open the record side and take backend
    identity, workspaces and the DataHandler with it? The second is a second
    server rather than a further tool, because the stdio process boundary is the
    whole of this one's security and a record read is where that stops holding.
    Nothing in the checkout decides this; `R-AUD-001` counts three audiences
    because somebody chose three.

Decide in `decisions/audience/` whether an editor — or an agent acting on
records — becomes a fourth audience beside the core contributor, the extension
author and the site developer that
[`R-AUD-001`](../../requirements/audience/aud-001-three-audiences-not-one.md)
names, and record the answer whichever way it goes. The boundary is already half
crossed: `typo3_schema_lookup` returns a table as the installation's container
assembles it, so the content *model* is answered here, while no tool touches a
record. The reason is the trust model rather than taste — this server runs as a
stdio subprocess of the client, which makes the process boundary the whole of
its security, and a single record read would put the shell user's database
access where a backend user's permissions belong, which is precisely the mapping
the draft RFC would make mandatory. So the two honest options are to stay on the
model side and say so as a `doesNotCover` entry in
`knowledge/server-scope.json`, or to open the record side and take on backend
identity, workspaces and the DataHandler with it, which is a second server
rather than a further tool. Write down which, because `doesNotCover` currently
says neither.
