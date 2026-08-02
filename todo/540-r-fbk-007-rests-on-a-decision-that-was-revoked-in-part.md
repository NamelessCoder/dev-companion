# `R-FBK-007` rests on a decision that was revoked in part

**Serves:** requirements/, decisions/
**Run:** `bin/cli backlog:list`

`bin/cli backlog:list` has been reporting one crossing every session, and
nothing has been back to it: `R-FBK-007` says in its front matter that it
`restsOn: [D-FBK-005]`, and that entry is `revoked`. The requirement is `held`
and its tests pass, so the reading it stands on is what went, not the behaviour.

The revocation is partial and says so: "the order this entry is mostly about —
appointments, then the queue, then the sightings — is untouched and is what the
evidence above bought; what was wrong is the number beside it", five feedback
per sighting rather than one. `R-FBK-007` states that order and states the size
of the reading, so half of what it rests on stands and half was replaced by the
rule that a judgement is written into `decisions/` rather than into a commit.

The step is to decide which of the two shapes it is: the entry was revoked where
it should have been corrected — the format has a **Since then** for a change
that does not overturn what was decided — or the requirement now rests on
whatever entry carries the portion, and its `restsOn` names that instead. Read
`D-FBK-005`'s foot against `R-FBK-007`'s statement first, because the answer
turns on whether the order and the portion were one decision or two. A revoked
entry also owes its reader a `revokedBy`, and this one carries none — which is
the third possibility: that the successor was never written.
