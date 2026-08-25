---
id: D-ANS-043
title: 'A miss is answered in data'
date: 2026-08-03
status: open
coveredBy:
  - PackageSourcesTest::aMissThatOffersARequeryNamesTheCorpusToAskNext
  - PackageSourcesTest::theNarrowingAMissComputesIsAField
---

# D-ANS-043 — A miss is answered in data

**The narrowing a miss computes is a field of the answer as well as a line of
its text. Where the re-query it offers comes back empty too, the miss names the
corpus that answers what still holds.**

A session read `matchCount: 0` and nothing beside it, and reported that the
changelog says nothing about the backend entry point. The re-query the same miss
had already computed returns the one entry its review turned on.

## Evidence

- `feedback/2026-08-03-144349`, re-run on 2026-08-03 from
  `/home/benji/projects/typo3-cms`, which ships 15.0. `typo3_changelog_lookup`
  with `query: "typo3 directory backend entry point"` still returns nothing, and
  the text carries the whole narrowing: five per-word counts, then
  `"typo3 backend entry point" reaches 1 entry`.
- The data half carries none of it. `query`, `matchCount: 0`, `tags: []`,
  `entries: []`, 55 `versions` and `answeredBy: "packages"` — the six fields the
  feedback quotes, and no field for what the miss worked out.
- The narrowing was there when the session ran. `perTermCounts` reached this
  answer on 2026-08-01 in `40557f5`, the subsets on 2026-08-02 in `cf6bcdb`, the
  filter sentence at 09:47 on 2026-08-03 in `c410db4`. The feedback is stamped
  16:43 that day.
- The offered subset answers the review. `typo3 backend entry point` returns
  `13.0 Deprecation: TYPO3 backend entry point script deprecated (#87889)`,
  alone, in one call, matched by name.
- What #87889 says is what the review was for. Read in the same checkout: the
  deprecated thing is the script `/typo3/index.php`, "it is still in place", and
  the route path becomes configurable through
  `$GLOBALS['TYPO3_CONF_VARS']['BE']['entryPoint']`. Its affected installations
  are "all installations using the TYPO3 backend `/typo3`".
- The URL prefix is still there on this branch. `DefaultConfiguration.php:1637`
  reads `'entryPoint' => '/typo3'` and `UriBuilder.php:199` still constructs
  `new RequestContext('/typo3/')` — the line the session found by grep, after
  the changelog.
- The miss names no tool. Its closing line offers the core repository and
  docs.typo3.org, and only for a version this installation does not ship.
- `bin/cli feedback:list` on 2026-08-03: 16 open, 8 of them from this core
  checkout, five written by one review session. Its sibling
  `feedback/2026-08-03-144457` reports the same ending — the question settled by
  grep — for the same question.

## Decided

- **Step 2 of the ladder, delivery.** The narrowing exists, is correct, reaches
  the entry, and did not reach the session. Nothing about the computation is at
  fault, so nothing about it changes.
- **The decision `D-ANS-016` declined is taken here.** Its last **Since then**
  named this gap and left it: "a miss whose whole guidance is text is that gap
  already — `matchCount: 0` is the entire structured answer. That is one
  decision about the shape of a miss rather than three, and this entry does not
  take it."
- **Queued rather than closed on the spot.** A field is added to a declared
  `outputSchema` and the answer is `src/`, which
  [judging.md](../../documentation/records/judging.rst) puts on the far side of
  the autonomous line.
- **`normal`, and what set it is the corpus rather than this report.** The same
  ending — an empty lookup, then grep — is in `feedback/2026-08-01-115112`, in
  the three `2026-07-31-1745` reports `D-ANS-010` was decided on, and in this
  one's own sibling. Four sessions from two checkouts, none of them `low`.
- **The feedback's second suggestion is taken in the other order.** It asks the
  zero to say that a changelog records change events and to send the caller to
  `typo3_documentation_lookup`. Here the changelog did carry the entry, one
  subset away, so a sentence naming the manual first would have routed this
  session away from it. The query that reaches comes first, and the corpus is
  what to ask once that comes back empty too.
- **No new requirement.** `R-ANS-002` already says the reason is in the data and
  `R-ANS-018` that an answer names the tool for what it says is absent. Both are
  `held`, by cases over `typo3_server_scope` and `typo3_project_describe`. What
  is missing is the path, not the rule.

## Assumed

- That a caller composing on `structuredContent` reads a field it did not ask
  for. Nothing measures that. The other reading of this feedback — a session
  that had the text and quoted only the data — leaves the same lever, so the
  assumption decides the size of the win and not whether there is one.
- That the manual answers what the changelog's silence leaves. `D-ANS-010`
  assumed the same and verified one shape of it, and
  `feedback/2026-08-03-144457` doubts it for this very question. That card is in
  hand elsewhere.
- That a sentence in the miss reaches a session the same sentence in
  `skills/base.md` did not. This feedback is the evidence for it: the session
  quotes the base.md routing back and did not follow it.

## Wrong if

- A later feedback quotes the new field and goes to grep anyway. Then the
  delivery was not what was missing, and what a miss says is worth less than
  `D-ANS-016` and this entry both assume.
- The corpus sentence is followed and the manual does not say whether a
  mechanism still holds. That is `D-ANS-010`'s first **Wrong if**, and it fires
  here on the same words.
- A client reports the miss as too long to act on. It already carries up to five
  sentences, and one more is the cost of the offer being read at all.
- A subset the data half now promises returns entries a caller cannot use,
  because the field was filled where the text withholds it — under a `tag`,
  where `D-ANS-016` established that a subset promises what the same call does
  not return.

## Since then

Built on 2026-08-03. The miss branch held every value already and declared none
of them, so what this cost is three fields in `outputSchema()` and three lines
beside `matchCount`: `termCounts`, `termCountsWithoutTheNarrowing` and
`termSubsets`. Each is present where it was computed and absent where the text
withholds it. Re-run from `/home/benji/projects/typo3-cms`, which ships 15.0:
`typo3 directory backend entry point` returns the five per-word counts and
`{"terms": ["typo3", "backend", "entry", "point"], "matchCount": 1}` as data.
That subset still returns `13.0` #87889 alone.

Which of the two count fields carries a number is what says where it was taken,
rather than a marker inside one of them. The pair is what a narrowed miss
computes anyway, and a caller reading `termCounts` alone reads counts that are
true inside the narrowing it asked for. The words reaching nothing are in that
field as well, which the sentence leaves out of its list: a zero is the word to
drop, and in data it costs no reading.

The corpus sentence follows the offered subset and stands nowhere else. "That"
in "where that comes back empty too" is the re-query, so a miss offering none
has nothing for the sentence to follow — under a `tag`, or where no two words of
the query meet in one entry. Whether a session that reads it follows it is the
third **Assumed** above, and this settles nothing about it.

## Since then

The branch that paragraph reserved is where a session lost its question.
`feedback/2026-08-24-183536` asked `typo3_changelog_lookup` whether widening a
method from protected to public owes an entry, read `matchCount: 0`, and swept
1405 commits by hand. Judged on 2026-08-25.

Re-run the same day from this checkout against `.checkouts/main`, which ships
15.0, through `bin/typo3-dev-companion` over JSON-RPC: `visibility public` at
`limit: 25` returns three lines — the miss, the two per-word counts ending "ask
again with the one that narrows best", and what the installation ships. The data
carries `termCounts` and no `termSubsets`. No tool is named anywhere in either
half.

The branch is larger than the paragraph reserving it reads. `Subsets` skips a
carried set of one because the per-term counts already say it, and one of the
full size because a query that hits is not answered with itself — so a query of
exactly two words can produce no subset at all, whatever the corpus holds. The
shortest queries are the ones that end with no route out, and they are not an
edge of this branch but the whole of it.

`R-ANS-018` is what that leaves unheld. It demands that an answer saying
something is not here names the tool that has it, and it is `held` by
`PackageSourcesTest::aMissThatOffersARequeryNamesTheCorpusToAskNext` — a case
over the offering branch alone. The requirement is guarded exactly where the
caller still has something to ask, and unguarded where it has nothing.

The objection this entry raised against naming a corpus first does not reach
here. It was written against routing a session away from a re-query that
answers, and a miss offering none has no such answer to be routed away from.

What the branch should name is not settled. `D-ANS-010` routes "does this still
hold" to `typo3_documentation_lookup`, and the question reported here is a
different shape — whether a kind of change owes an entry, which is a rule and is
`typo3_rule_lookup`. One sentence carrying both is a paragraph on every miss, so
which of the two the branch names, or whether it names both, is the card's first
step rather than this reading's.

Queued at `normal` rather than closed on the spot: the answer is
`src/Tool/ChangelogLookup.php`. The grounds are the ones this entry already set
for the same surface — the same ending, an empty lookup and then grep, from a
sixth session — and one report is what keeps it off `high`.

The feedback's other half is answered and trimmed off. It asked that
`typo3_rule_lookup(query "breaking change")` state where a widened visibility
falls; `D-KNW-123` decided that on 2026-08-25 and `5578ee26` landed it, and the
re-run above returns the bullet 4496 characters into a 10520-character answer,
so the section carrying it is not truncated away. Naming the precedent commit
was refused there, which is an answer rather than a remainder.

The session also reported reading the review skill's "list the kind before you
search for words" and querying with words anyway. That is the same lever from
the other side, and `D-ANS-010` established it before this: a sentence read
before the task does not fire at the moment the silence arrives, and the miss is
where the caller is standing then. It stays on the feedback rather than becoming
a second card.
