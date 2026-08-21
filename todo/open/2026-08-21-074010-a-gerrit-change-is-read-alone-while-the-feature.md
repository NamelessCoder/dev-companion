# A Gerrit change is read alone while the feature it belongs to is a relation chain

**Serves:** feedback/2026-08-21-074010-a-gerrit-change-is-read-alone-while-the-feature.md
**Priority:** normal

Carry the relation chain in `typo3_gerrit_lookup`'s answer for every change the
`change` form returns, the Change-Id siblings included. The source is
`/changes/<number>/revisions/current/related` on the anonymous REST API `Gerrit`
already reads, one call per change entry and by the number rather than by the
Change-Id, which 404s where a backport shares one. An entry becomes the change's
number, its status and its commit subject, and the answer says which of them is
the change that was asked about. A chain that is empty is the ordinary case and
reads as one rather than as a failure.

The text half is where the two relations are told apart: a stack is different
changes built on one another, a shared Change-Id is one patch on several
branches, and the sentence `D-ANS-080` already prints for the second is what the
first sits beside. Say what a chain entry's status is about — the entry rather
than the change that was asked about — and settle whether the patch set the
chain names is worth carrying, since it can be older than the entry's current
one. Read the response shapes off the review server rather than off this
paragraph; `D-ANS-094` names what was measured on 2026-08-21 and what would show
the decision wrong.

The tool's `description` and `outputSchema` move with it, and so does the
`instead` sentence in `knowledge/server-scope.json` that today names the
Change-Id siblings as the only relation this tool answers.
