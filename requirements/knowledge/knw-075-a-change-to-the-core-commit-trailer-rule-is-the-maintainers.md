---
id: R-KNW-075
title: "A change to the core commit trailer rule is the maintainer's"
status: open
restsOn: [D-KNW-125]
heldBy: not guarded
judged: 2026-08-28
---

# R-KNW-075 — A change to the core commit trailer rule is the maintainer's

**Which trailers a core commit message carries is settled here, and a session
that would change it asks the maintainer first.**

The rule is not derivable from a checkout. It rested for four weeks on the
opposite of what the core's own `AGENTS.md` says, and it now agrees with that
file — and neither state was readable off the files, because what moved was a
board recommendation published outside every checkout (`D-KNW-125`).

What follows is that the trailer list is not derived. It is read out of
`core/contribution/commit-messages` and out of `typo3_commit_message_guide`,
both of which name the same trailers, and a session that believes one more is
owed brings that to the maintainer rather than to the file.

## From

The maintainer's ruling on 2026-08-24, given in the session that recorded
`feedback/2026-08-24-170208` and worked the queue around it: the commit rules
are to be followed strictly, and a change to them is theirs to approve.

Exercised on 2026-08-25, which is what the requirement is for: the maintainer
brought the TYPO3 Association board's statement on GPL and AI-generated code and
reversed the sign-off rule. Two sessions had reported the check as wrong against
the core's `AGENTS.md` and neither could have reached that source (`D-KNW-125`).

## Held by

Nothing guards it. A test can hold what the list is, which
`CommitMessageTest::aCoreDraftRefusesTheTrailersTheProjectDoesNotSet` does, and
no test can hold that somebody asked before changing it.

Read on 2026-08-28 and left as it is. `open` is what "nothing to build" is
written as here: what would make it `held` is a mechanism and there is none, and
what holds it instead is that it was exercised — the reversal of 2026-08-25 came
from the maintainer with a source no session could reach. What would show it
wrong is a session changing the trailer list without asking, which is the event
the requirement names and nothing here can see.
