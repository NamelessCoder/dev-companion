---
id: D-ANS-025
title: A query a hint carries whole is not diluted out of it
date: 2026-08-02
status: confirmed
coveredBy:
  - HintsTest::aHintThatCarriesPartOfAQueryStillDoesNotAnswerIt
  - HintsTest::aTermOnlyOneHintStatesReachesItHoweverLongThatHintIs
  - HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay
---

# D-ANS-025 — A query a hint carries whole is not diluted out of it

**A hint whose own words carry every term of the query meets the coverage floor,
however long that hint is.**

Dilution goes on deciding the ranking, and below a whole cover it goes on
deciding the floor. `MIN_COVERAGE` asks what share of the query a hint accounts
for, and the dilution weight damped that share by the length of the field the
terms were found in. Past a body of `UNDILUTED_WORDS * e` words that made a
one-term query uncoverable in principle: the whole query is matched, the share
is still under a half, and the hint stops being a candidate for a question it
used to answer.

## Evidence

- The arithmetic, and it is exact. Coverage of a single matched term is
  `1 / (log(words / 200) + 1)`; at a floor of 0.5 that solves to `200 * e`, so
  **544 words** is the length past which no one-term text query admits a hint at
  all. `bin/cli hints:coverage` reads a mean of 291 and a longest of 1147, and
  12 of the 66 hints are over that line.
- The sweep, run over the corpus that can actually see the gate: every hint's
  own body vocabulary — its body terms minus everything its title and its
  `appliesTo` already index — asked one term at a time against the hint it
  belongs to. Read over the categories a query with no domain signal selects at
  all, because the gate of
  [`D-KNW-009`](../knowledge/knw-009-a-domain-keyword-is-a-phrasing-not-a-word.md)
  sits upstream of this one and holds Backend CSS, Fluid and TypoScript out at
  3% whatever their length. The 30 always-selected hints under 544 words are
  reached by **2427 of their 2438** body terms. The 10 over it, by **30 of
  2273**. It is not a degradation with length, it is a cliff at one length.
- Four identifiers this corpus states in exactly one hint each, in none of its
  titles and in none of its patterns: `showitem`, `allowProperties`,
  `sys_registry` and `PidInList` reached nothing at all — 40 hints returned as
  the index. Three of the four are asserted to be in those hints by tests
  written before this, so the repository already held that the answer is there
  and that nobody could ask for it by name.
- The shape
  [`D-ANS-022`](ans-022-the-matcher-takes-a-hyphenated-compound-apart-measured-over-the-corpus-first.md)
  used, over the queries a caller writes: the 208 multi-word bare `appliesTo`
  patterns and the 41 scenario prompts, taken apart into 1360 one-term queries
  against the hint they belong to. 419 reached before, 572 after, none lost. All
  153 gains fall on the 8 hints over the line, and **not one on a curated
  pattern's own words** — an `appliesTo` field is a dozen words and is never
  diluted, which is why curating `list_type` and `mod.web_layout` repaired the
  reported case, and why the curated half of a sweep cannot see this gate.
- What it moves, over the same 302 whole queries — 41 prompts, 66 titles, 208
  patterns: **0 changed their first hit and 0 entries left a top-6 answer**; 71
  gained entries below what was already there. The 12 recoverable queries of the
  [`D-ANS-002`](ans-002-rarity-field-length-and-corpus-length-decide-a-lookups-rank.md)
  sweep answer as they were recorded to, both negative controls included, 943
  tests pass, and `bin/cli hints:coverage` prints the same page.
- The two alternatives that change the number instead of the question, measured
  the same way. Raising `UNDILUTED_WORDS` to 422 — the value that clears the
  longest hint — buys the same 153 one-term queries and answers «how do I write
  a good sonnet», moves 136 of the 302 whole queries, changes 4 first hits and
  drops 14 entries out of top-6 answers. Taking dilution out of coverage
  outright buys the same 153 and answers the sonnet too.

## Decided

- In the gate rather than in the shared matcher. `TermSearch::score()` returns
  the numbers and each corpus says what it admits on, which is why the third
  return value it already carried was enough:
  `ArchitectureHints::coversEveryTerm()` reads it. The other two corpora do not
  have this to fix — the prose sections are cut at `MAX_SECTION_LENGTH` and the
  longest is 400 words against a ceiling of 1087, and the manual reads the score
  and no coverage at all. Hint bodies are the one uncapped field, which is
  `D-ANS-002`'s third bullet arriving where it pointed.
- Dilution stays in the score untouched, and stays in the floor for every
  partial cover. That is where it earns its keep: «how do I write a good sonnet»
  is covered in part by a text long enough to contain *writing* and *good*, and
  nothing in the corpus carries "sonnet", so the cover is never whole there and
  the floor still drops it.
- Not `UNDILUTED_WORDS`. `bin/cli hints:coverage` says the fix is to re-run the
  sweep and pick the constant again, and the sweep says the constant is not what
  is wrong: every value that clears the longest hint is a value at which a
  partial cover answers, because those are the same wall.
- Not curating the identifiers into `appliesTo`, which is what the reported case
  was repaired with. It is 153 patterns over 8 hints today and it grows with
  every hint written; most of the words are ordinary prose rather than
  vocabulary anybody would index; and a pattern outranks a text hit, so curating
  for reachability also decides what comes first. The two patterns already added
  stay for that reason — removing them now would move a ranking this did not
  measure.
- Not splitting the hints that outgrew the line. It keeps the text route working
  by changing which id a caller is routed to, it has to be done again each time
  one grows, and with the gate fixed nothing asks for it.
- `MAX_MEAN_BODY_WORDS` is left where it is. The failure it watches is the
  partial cover — a mean past which a query the corpus cannot answer is answered
  anyway — which this does not touch, and the report still says 9 words of
  headroom.

## Assumed

- A one-term query is best answered by the hint that says the term, long hint
  included. The measured gains are identifiers a caller naming one is after, but
  they are not all of them: the prompt half of the sweep gained ordinary words —
  `somebody`, `yet`, `now` — and 25 one-term queries that returned nothing now
  return a long hint. Nothing was displaced in doing it, so the bet is that
  something is better than the index here.

## Wrong if

- A one-term query comes back with a long hint that merely mentions the word
  where a shorter one is about it. The 25 queries that went from nothing to
  something are where that would show first, and `bin/cli hints:probe` prints
  which way in earned each hit.
- The corpus grows until one common word is in every long hint and the same
  handful answers every short query — the crowding this floor was put there to
  stop, arriving through the exception instead of through the mean.
- A hint over the line is found to have been *usefully* unreachable: a caller
  asking one word and wanting the index rather than the hint that says it.

## Confirmed on 2026-08-22

The crowding the second **Wrong if** watches has not arrived, and the corpus has
more than doubled since the gate was opened: 149 hints against 66, mean body 274
words against 291, longest 842 against 1147.

Swept the way this entry swept: 596 one-term queries taken from hint bodies,
skipping every word the title or `appliesTo` already indexes, one term at a time
against the whole corpus. 101 distinct hints come back first and the five
commonest answer 144 of 579 hits, so a quarter of the answers are shared by five
hints out of 149 and the rest is spread over a hundred. 17 of the 596 reached
nothing at all, which is the floor still doing its work on a word no hint is
about.

Two of those five are the two longest hints — `installation-boot` at 842 words
and `project-build-and-scripts` at 824 — and the other three are 117, 146 and
165. So length is not what puts a hint at the front: `di-service-not-found` is
under half the corpus mean and answers 29 of them.

The first and third **Wrong if** are unreported. Nothing in the archive says a
one-term query came back with a long hint that merely mentions the word, and
nothing says a caller wanted the index instead of the hint that says it.
