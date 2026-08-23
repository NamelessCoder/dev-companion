---
id: D-ANS-002
title: Rarity, field length and corpus length decide a lookup's rank
date: 2026-07-30
status: confirmed
---

# D-ANS-002 — Rarity, field length and corpus length decide a lookup's rank

**An unknown term, a diluted long field and a per-corpus reference length decide
what a lookup answers.**

Scoring the hint text as well as its keywords is the change; keeping it from
answering everything is the part that took the work. Three constants came out of
it, and each was picked off a sweep over sixteen queries with a known right
answer rather than argued for. That is also what makes them worth writing down:
a number that fell out of one afternoon's corpus is a number the corpus can grow
out of.

## Decided

- A term nothing in the corpus carries weighs `log(total) / 2`, as if the square
  root of the corpus held it. At zero — the previous behaviour — «how do I write
  a good sonnet» decayed into a query about *writing* and about *good*, and
  something always answers that. At full weight, one unfamiliar word sank
  queries the corpus does answer: nobody wrote «upload», and the storage hint
  stopped being the answer to «file upload storage configuration». The sweep was
  flat between a quarter and three quarters, so the middle was taken.
- A term found in a field longer than the corpus's ordinary field counts less,
  on a log scale. Without it the longest hint — over a thousand words — answered
  at full confidence whenever a query's words all appeared in it somewhere. This
  is the one that carried the precision: with the unknown-term weight already in
  place, turning dilution off put the returned hints back up from 33 to 49 for
  the same recall.
- The reference length is per corpus, not global — 200 words for the hints, 400
  for the prose. Hint bodies are uncapped and differ by more than twenty times,
  so the mean is the meaningful reference; prose sections are cut at
  `MAX_SECTION_LENGTH`, so a section at that length is ordinary rather than an
  outlier. A single global value was tried first and it broke
  `typo3_script_lookup` on «php-cs-fixer and phpstan», where two sections each
  answer half of a two-word query and sat exactly on the floor.

## Assumed

- The hint corpus keeps roughly its current shape. 200 is its mean body length
  today, and a corpus that doubles its typical hint makes the ordinary hint look
  like an outlier to this code.

## Wrong if

- Hints get longer on average — the dissolution of the architecture prose into
  this corpus is queued and moves in that direction — or a query that should hit
  goes missing while its hint is plainly about it. Both show up in
  `bin/cli hints:coverage` once that exists; until then the eight queries in
  `HintsTest` are the only tripwire, and they are a sample.

## Since then

The stopword list is English, so German filler words behave like unknown
technical terms and sink the whole query. «wie lege ich ein neues
Content-Element an» reached `content-elements` before this change and does not
now. It was left open here rather than papered over with a lower floor, and the
measurement that followed settled it the other way round: the server is queried
in English and says so to the agent (`R-AUD-006`). A lexical matcher over an
English corpus has nothing else to match on, so the alternative was never a
stopword list — it was translating the knowledge base. Twelve German queries
also surfaced two genuine bugs on the way, both language-independent and both
fixed under `R-ANS-008b`: a three-letter term matched as a prefix, and
`appliesTo` matched by plain substring.

## Confirmed on `2026-08-01`

The first symptom happened and the decision holds anyway.
`bin/cli hints:coverage` reads a mean hint body of 266 words against the 200 the
reference was picked as — 212 on the day, taken to a round number — so the
corpus has grown a quarter, the dissolution of the architecture prose accounting
for 20 words of it and the seven hints written since for 34. The second symptom
did not happen: the fourteen recoverable queries of the sweep of eighteen all
still answer as they were recorded to.

What the re-measurement settles is that the number should not follow the mean it
was named after. Swept again over those fourteen, recall is whole from a
reference of 120 words up to 320, and the hints returned climb the whole way —
21 at 120, 26 at 200, 30 at the mean of 266 — so the low end of the range is the
precise one, and 200 is a floor the ordinary hint now sits above rather than the
average it was measured as. At 340 «how do I write a good sonnet» is answered by
`installation-upgrade`, which is long enough by then to contain it: the exact
failure the weight exists to prevent, and the far wall.

So the corpus has roughly a fifth of mean growth left, and both walls are
watched now instead of one. `ArchitectureHints::MAX_MEAN_BODY_WORDS` is a
ceiling of 300 that `HintsTest` and `bin/cli hints:coverage` fail on, which
catches the corpus growing; the sweep is a data provider in `HintsTest` rather
than eight hand-picked queries, which catches the reference being raised to
chase it. Four of the eighteen were never written down anywhere and are lost —
which is the reason the remaining fourteen are in the test now rather than in a
commit message.

## Since then

The second half of the **Wrong if** happened on 2026-08-02, and neither wall saw
it: individual hints, rather than the mean, walked past a length at which the
dilution weight stops damping a coverage share and starts deciding candidacy.
Past `UNDILUTED_WORDS * e` a one-term query cannot clear the floor at all, so 12
hints were dropped from every question they were not curated for, while the mean
this entry watches sat inside its headroom and the sweep it is held by kept
passing — all of its queries are several words long. The three constants stand
and none of them moved; what was wrong was reading a share of the query off a
number that says how long the hint is.
[`D-ANS-025`](ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md)
is the measurement and the fix, and it leaves both walls where this entry put
them.
