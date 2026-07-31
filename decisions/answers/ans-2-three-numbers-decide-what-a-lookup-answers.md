---
id: D-ANS-2
date: 2026-07-30
status: standing
---

# D-ANS-2 — Three numbers now decide what a lookup answers, and they were measured, not reasoned

**An unknown term, a diluted long field and a per-corpus reference length decide
what a lookup answers, and each of the three constants came off a sweep over
sixteen queries with a known right answer.**

Scoring the hint text as well as its keywords is the change; keeping it from
answering everything is the part that took the work. Three constants came out of
it, and each was picked off a sweep over sixteen queries with a known right
answer rather than argued for. That is also what makes them worth writing down:
a number that fell out of one afternoon's corpus is a number the corpus can grow
out of.

- **Decided:** a term nothing in the corpus carries weighs `log(total) / 2`, as
  if the square root of the corpus held it. At zero — the previous behaviour —
  «how do I write a good sonnet» decayed into a query about *writing* and about
  *good*, and something always answers that. At full weight, one unfamiliar word
  sank queries the corpus does answer: nobody wrote «upload», and the storage
  hint stopped being the answer to «file upload storage configuration». The
  sweep was flat between a quarter and three quarters, so the middle was taken.
- **Decided:** a term found in a field longer than the corpus's ordinary field
  counts less, on a log scale. Without it the longest hint — over a thousand
  words — answered at full confidence whenever a query's words all appeared in
  it somewhere. This is the one that carried the precision: with the unknown-term
  weight already in place, turning dilution off put the returned hints back up
  from 33 to 49 for the same recall.
- **Decided:** the reference length is per corpus, not global — 200 words for
  the hints, 400 for the prose. Hint bodies are uncapped and differ by more than
  twenty times, so the mean is the meaningful reference; prose sections are cut
  at `MAX_SECTION_LENGTH`, so a section at that length is ordinary rather than an
  outlier. A single global value was tried first and it broke
  `typo3_script_lookup` on «php-cs-fixer and phpstan», where two sections each
  answer half of a two-word query and sat exactly on the floor.
- **Assumed:** the hint corpus keeps roughly its current shape. 200 is its mean
  body length today, and a corpus that doubles its typical hint makes the
  ordinary hint look like an outlier to this code.
- **Wrong if:** hints get longer on average — the dissolution of the
  architecture prose into this corpus is queued and moves in that direction —
  or a query that should hit goes missing while its hint is plainly about it.
  Both show up in `bin/cli hints coverage` once that exists; until then the eight
  queries in `HintsTest` are the only tripwire, and they are a sample.
- **Since then:** the stopword list is English, so German filler words behave
  like unknown technical terms and sink the whole query. «wie lege ich ein neues
  Content-Element an» reached `content-elements` before this change and does not
  now. It was left open here rather than papered over with a lower floor, and
  the measurement that followed settled it the other way round: the server is
  queried in English and says so to the agent (`R-AUD-6`). A lexical matcher
  over an English corpus has nothing else to match on, so the alternative was
  never a stopword list — it was translating the knowledge base. Twelve German
  queries also surfaced two genuine bugs on the way, both language-independent
  and both fixed under `R-ANS-8b`: a three-letter term matched as a prefix, and
  `appliesTo` matched by plain substring.
