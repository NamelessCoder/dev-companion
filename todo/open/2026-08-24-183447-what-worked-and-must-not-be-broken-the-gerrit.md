# Say which comment thread is open, and count them as the server does

**Serves:** feedback/2026-08-24-183447-what-worked-and-must-not-be-broken-the-gerrit.md, D-ANS-079
**Priority:** normal

State the thread in the change answer instead of leaving it to be derived, and
stop the text half contradicting the review server. `GerritLookup::comments()`
heads its list with a tally of the per-comment `unresolved` flag, which is
neither the number `standing()` prints beside it from `unresolvedCommentCount`
nor anything a caller wants: on change 91127 it says `7, 1 unresolved` where the
server says none, and on 85224 `12, 4 unresolved` against
`2 unresolved of 12 comments` eight lines above. Confirm first against Gerrit's
own documentation what `unresolved_comment_count` counts and where a thread's
state is written — this side has only the derivation, that a thread's state is
the flag on its last comment, which reproduces both counts exactly on those two
changes. Then group the comments by `inReplyTo`, since the relation and the
order are already in the answer and no further call is needed, and say each
thread's state where the head comment's flag is today. The schema description of
`unresolved` and the paragraph the text prints under the comments both state the
thread's rule on the per-comment field and have to say what the field is
instead. What may not change is the refusal itself: this server hands over the
state and does not judge whether a question was answered, and a resolved thread
can still hold an open question. `D-ANS-079`'s section of 2026-08-25 has the
measurement and the **Wrong if** this answers.
