---
id: D-ANS-111
title: A change answer says which comment thread is open
date: 2026-08-26
status: open
coveredBy:
  - GerritTest::aReplyToACommentNobodyCanSeeStandsAsAThreadOfItsOwn
  - GerritTest::everyCommentSaysWhichThreadItIsInAndWhatThatThreadStandsAt
  - GerritTest::theTextHalfListsOneThreadAtATimeAndSaysWhatEachStandsAt
---

# D-ANS-111 — A change answer says which comment thread is open

**Every comment carries the thread it is in and what that thread stands at, and
the text half lists one thread at a time under a heading that says which.**

The answer printed two counts of one thing eight lines apart, and the caller was
left to work the threads out of the reply ids for itself.

## Evidence

- Gerrit's own REST documentation, read on 2026-08-26:
  `unresolved_comment_count` is the "Number of unresolved inline comment threads
  across all patch sets", and `CommentInfo.unresolved` says "The state of
  resolution of a comment thread is stored in the last comment in that thread
  chronologically". So the field and the flag are a thread and a comment, and
  this answer counted them as one thing.
- Measured against `review.typo3.org` on 2026-08-26, anonymously and over the
  same path everything else here reads (`D-ANS-033`): change 91127 carries seven
  comments in five threads, the review server states none unresolved, and a
  tally of the flag says one. 85224 is twelve comments in nine threads, two
  against four. 95179 is four in two, none against two.
- Reading each thread's last comment reproduces the review server's count on all
  three, and on the forty open core changes that carry an unresolved thread,
  read the same day. Nothing measured disagrees with it.
- The other candidate — the unresolved comments nobody replied to — agrees on
  thirty-nine of those forty and is wrong on change 84448, whose one branched
  thread it counts twice. That is the case the documented rule and this one come
  apart on, and the documented rule is the one the review server follows.
- `feedback/2026-08-24-183447` ranked the threads itself, from "a top-level
  comment flagged unresolved with two resolved replies under it", and asked that
  the answer mark what is unresolved **and** top-level. `D-ANS-079`'s section of
  2026-08-25 measured that pair: it selects a settled thread on 91127 and misses
  the open one on 85224.
- The comments payload already carries `in_reply_to` and the order, so the
  thread costs no further call. It is the read the answer was already paying
  for.

## Decided

- Two fields on each comment, `thread` and `threadUnresolved`, rather than a
  list of threads holding lists of comments. The comments are a flat list
  clients already read, and a field can be added where a shape cannot.
- **The thread is the head the reply chain reaches**, and a reply whose parent
  is not in the answer opens one of its own. A draft is invisible to a reader
  without credentials (`R-ANS-027`), so its replies arrive naming an id nothing
  here carries, and putting them in no thread would drop them from the listing.
- **The state is the flag on the thread's last comment**, which is Gerrit's rule
  rather than this side's derivation. The leaf rule was measured beside it and
  is the one that breaks on a branch.
- The text half lists a thread per heading with its state, the author who opened
  it and where it sits, and the comment lines under it carry neither a state nor
  the reply relation the order already says. Seven states on a change with five
  threads is the contradiction this replaces.
- **The count in that heading is derived here** rather than the review server's
  field printed a second time, so the listing and its count are one reading. It
  reproduces `unresolvedCommentCount` beside it, and a change where the two
  disagree is visible rather than silently reconciled.
- `standing()` says "unresolved threads" where it said "unresolved of n
  comments". The field's own name counts comments and its value counts threads,
  and this answer is not the place that repeats the mistake.
- **The refusal is unchanged**: this server hands over the state and does not
  judge whether a question was answered. A resolved thread can hold an open
  question, and an unresolved one can carry the reply that settled it —
  `D-ANS-079`.

## Assumed

- That `updated` is the order Gerrit means by "chronologically". It is the one
  date the comment payload carries, and a comment somebody edited later would
  sort by the edit rather than by when it was written.
- That a caller reads `threadUnresolved` as a flag somebody set. It is the same
  reading `unresolved` needed and did not get, one level up.

## Wrong if

- The derived count and `unresolvedCommentCount` come apart on a change somebody
  reads. Forty changes and the documentation say they do not, and a thread shape
  nothing here has seen is what would show it — the reply relation is the whole
  of what this side knows.
- A caller reports a resolved thread as settled and stops reading it. That is
  the reading `D-ANS-079` refuses to make, moved from the flag to the thread,
  and the paragraph under the comments is what stands against it.
- The threads turn out to be what a caller wants nested — a review answering
  them one at a time, or a client rendering the tree — and the two fields are
  then a shape everybody rebuilds.
