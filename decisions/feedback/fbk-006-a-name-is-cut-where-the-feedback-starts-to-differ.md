---
id: D-FBK-006
date: 2026-08-01
status: confirmed
---

# D-FBK-006 — A name is cut where the feedback starts to differ

**A feedback whose slug another feedback already carries is named from the first
word at which the two observations differ.**

Not from a counter, and not from a longer cut of the same opening.

The name has always been the first 48 characters of the observation. That is the
right end of the text — an agent writes what happened first — and it fails
exactly when a session files a series, because a series announces itself before
it says anything.

## Evidence

- 25 of 56 open feedback carried one of three names, 17 of them
  `debrief-of-the-typo3-14-testimonials-session`. Their observations shared nine
  words before the first that differed, and the shared part was 57 characters —
  longer than a name has room for, so no cut of the opening could have separated
  them. The eight feedback filed in thirteen seconds that morning were about
  Extbase, pid semantics, functional-test databases, cache clearing and four
  other things.

## Decided

- On a collision, read the first line of the feedback that already carry the
  name, skip the words they open with in common, and cut the slug from what is
  left. Only the colliding feedback is read, so recording still costs one
  directory listing in the ordinary case. The counter stays as the fallback for
  a feedback that has nothing else to say. What this rejects is asking the agent
  for a title: one more field in a tool whose parameters are already its
  documentation, and the field a session fills last is the one it fills worst —
  the observation is what it came to write.

## Assumed

- That the first difference is the part worth naming. Where a series is numbered
  — "5. Recommended: add en.xlf files" — the number leads and the name starts
  with it; the words after it still say what the feedback is about, so the name
  is ugly rather than wrong.
- That the first feedback of a series keeping the shared opening is acceptable.
  Nothing at the time it is written says a series is coming, and renaming it
  later would change a name that a commit or a `**Serves:**` line may already
  have quoted.

## Wrong if

- Feedback start colliding on a slug without sharing an opening at all — two
  different sentences cut to the same 48 characters — in which case skipping the
  shared words changes nothing and the counter is doing the work again. Or a
  series turns out to differ only in its middle, so the first difference is a
  word like "second" and every name in the group is a numeral.

## Confirmed on 2026-08-22

Neither **Wrong if** fired over 457 archived feedback. 456 slugs are distinct,
the one pair that shares a slug shares its opening as well — two `REVIEW-03`
reports of 2026-08-02, 83 minutes apart — and no name in the corpus is a numeral
except the three the first **Assumed** predicted: `5-recommended-`,
`16-suggested-` and `19-suggested-`, each of which still says what it is about
after the number.

That pair is the limit of the statement rather than a breach of it.
`Channel::uniquePath()` reads the directory it writes into and not
`feedback/archive/`, and the first of the two was closed the day it was filed,
so by the time the second arrived there was nothing to collide with. The names
stay distinct by timestamp, which is what the counter is for, and reading the
archive as well would cost every recording a second directory listing to
separate two files in 457.

The **Decided** holds where it is measurable: recording still costs one
directory listing, because the Finder call is narrowed to the slug the name
would take.
