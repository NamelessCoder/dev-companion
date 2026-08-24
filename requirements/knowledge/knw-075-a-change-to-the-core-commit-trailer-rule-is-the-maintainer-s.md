---
id: R-KNW-075
title: "A change to the core commit trailer rule is the maintainer's"
status: open
restsOn: [D-KNW-110]
heldBy: not guarded
---

# R-KNW-075 — A change to the core commit trailer rule is the maintainer's

**Which trailers a core commit message carries is settled here, and a session
that would change it asks the maintainer first.**

The rule has no source in any checkout: the core's own `AGENTS.md` demands a
sign-off, and this project does not set one. So a session reading the checkout
for an answer finds the opposite of the rule and finds it stated with a
justification — which is how two drafts came to be written and struck
(`feedback/2026-08-24-133602`).

What follows is that the trailer list is not derived. It is read out of
`core/contribution/commit-messages` and out of `typo3_commit_message_guide`,
both of which say the same three names, and a session that believes a fourth is
owed brings that to the maintainer rather than to the file.

Nothing guards it. A test can hold what the list is, which
`CommitMessageTest::aCoreDraftRefusesTheTrailersTheProjectDoesNotSet` does, and
no test can hold that somebody asked before changing it.

## From

The maintainer's ruling on 2026-08-24, given in the session that recorded
`feedback/2026-08-24-170208` and worked the queue around it: the commit rules
are to be followed strictly, and a change to them is theirs to approve.
