---
id: D-ANS-028
date: 2026-08-02
status: open
---

# D-ANS-028 — A two-letter query word is searched for, and the stopword list is what keeps the others out

**`TermSearch::terms()` admits a word of two characters, and the two-letter
words that say nothing about a subject are named in `STOPWORDS` rather than cut
off by a length.**

The floor was three characters and nothing said why. What decides whether a
short word is noise is not its length but how it is matched, and
`PREFIX_FROM_LENGTH` already answers that: below four characters a term is
matched as a whole word. The prefix noise its docblock records — `fal` reaching
seven hints through "fallback" and "false", `ist` deciding a German query by
occurring in no hint as a word at all — is a property of prefix matching and
cannot reach a term that is matched whole.

What the floor was doing instead was the stopword list's work, silently, for
every word that short. Moving it means the list has to do its own.

## Evidence

- The miss the floor caused, and it is not marginal. The ViewHelper reference
  titles a page after the tag, so `Global/If.html` is called "if" and the only
  word of `f:if` that can reach it is two characters long.
  `TermSearch::terms('f:if')` returned nothing at all, and the lookup answered
  `empty` — [`D-ANS-023`](ans-023-no-viewhelper-is-documented-in-any-manual-this-lookup-indexes.md)
  measured that after indexing the book and left it here.
- Both corpora go through it, so both were measured before and after, over the
  41 scenario prompts — the only corpus of real phrasings this repository has.
  The hints: `bin/cli hints:coverage` prints the same page byte for byte, and
  the hints returned for all 41 prompts are unchanged. The prose sections:
  unchanged for all 41.
- The manuals, live at 14.3, 1419 pages over the four books. 2 of the 41
  prompts changed, and both gained the same page: `SKILL-05` and `SKILL-08` each
  ask for checks that "run locally and in CI", and CI/CD Automation is now
  reachable by the word they used.
- The floor moved on its own, before the stopword list was completed, cost 7 of
  the 41. Five of them gained Setting up backend user groups, on the word "up"
  of "set it up from scratch" — and one of those five reordered its hints as
  well. That is the measurement that says the two halves are one change.
- What the change buys on its own query: `f:if` is answered rather than empty,
  and `Global/If.html` is in the ranked index at 14.3. It is not first. Ten of
  the 1419 pages carry `if` as a whole word — two TypoScript function pages,
  `security.ifAuthenticated`, `mfa.ifHasState`, `ShouldUseCachedPageDataIfAvailableEvent`
  and the rest — and all ten score exactly 198, so the order among them is the
  order the index was built in and `Global/If.html` is eighth.
  `f:if f:then f:else condition ViewHelper` puts it tenth of 204.
- The lever for that tie was measured and is not this one. The manual weighs a
  field by `UNDILUTED_WORDS = 12` and no title it has is that long, so a page
  titled "if" and a page titled Should Use Cached Page Data If Available Event
  are worth the same. At a reference of 3 the page titled "if" is fourth for
  both queries — and the six results of all 41 prompts change. That is a
  decision about `Documentation`, with its own before and after.

## Decided

- Two characters, not one. A single letter is a whole word in the corpus as
  readily as in the query, so the `f` of `f:if` is the `f` of every other tag
  written out and it separates nothing. `a` and `i` came out of `STOPWORDS` in
  the same commit: the floor drops them first, so they were entries nothing
  could reach.
- `am`, `go`, `he`, `no`, `so`, `up`, `us` and `we` go in, beside the fifteen
  two-letter stopwords that were already there. They are the English function
  words the floor had been absorbing.
- `if` deliberately stays out. It is an English keyword and a ViewHelper and a
  TypoScript function, and it is the whole of what a caller asking about `f:if`
  has left after the tokenizer.
- `be` stays in, against the same argument and the other way round. It is the
  backend namespace on 17 pages of the ViewHelper reference, and it is also the
  verb in every sentence saying what something should be — so admitting it
  would put those 17 pages into every query that used the word.
- Not the tie in the manual's ranking, and not `f:or` or `f:then`. The first is
  `Documentation`'s dilution reference and moves every manual answer; the second
  needs `or` and `then` out of a list that both corpora share, which would make
  `Global/Or.html` a candidate for every English "or". Both stay in the todo.

## Assumed

- The eight words added are the ones that had to be. They are what the 41
  prompts turned up; a two-letter function word nobody has written in a prompt
  yet is not in the list and nothing will notice until it wins something.
- The two-letter words left out carry signal here — `id`, `fe`, `db`, `ui`, and
  the version numbers `12` and `13` a prompt names. None of them moved an
  answer in the sweep, which is weaker evidence than it looks: it says they cost
  nothing, not that they earn anything.
- What was measured on one afternoon holds for questions nobody asked that day.
  The manual half of it was measured against 14.3 as published that day.

## Wrong if

- The stopword list has to grow every time somebody phrases a prompt with a
  two-letter word nobody wrote down. Five of the 41 prompts turned on "up"
  alone, so the next one is a word rather than a class, and there is no floor
  left to catch it.
- A two-letter word that does carry signal starts winning questions that are not
  about it. `if` is where that would show first: it is a keyword in one book and
  a conjunction in every English sentence, and `how do I check if a page is
  hidden` already answers with ShouldUseCachedPageDataIfAvailableEvent where it
  used to answer with the TCA checkbox pages.
- The hint and prose corpora turn out to have been unchanged only because their
  fields are long. A term nothing carries counts as if the square root of the
  corpus held it, so an admitted word the corpus has no answer for lowers the
  coverage of everything beside it — `record id uid field` returns three hints
  where it returned five.

## Covered by

- `TermSearchTest::aTwoLetterWordIsATerm`
- `TermSearchTest::oneLetterIsNot`
- `TermSearchTest::aTwoLetterWordThatSaysNothingAboutTheSubjectIsStillDropped`
- `TermSearchTest::aShortTermIsCarriedAsAWholeWordAndNotAsAPrefix`
- `DocumentationTest::aViewHelperNamedAfterAKeywordIsReachedByItsOwnName`
