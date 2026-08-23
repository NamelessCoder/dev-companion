---
id: D-ANS-042
title: An identifier reaches the changelog entries whose body names it
date: 2026-08-03
status: open
coveredBy:
  - ChangelogLookupTest::aQueryTheNamesAnswerIsNotWidenedByTheBodies
  - ChangelogLookupTest::aRemovedMethodReachesTheEntriesNamingItInTheirBody
  - ChangelogLookupTest::aWordThatIsAlsoWrittenAsCodeIsNotAnIdentifier
  - ChangelogLookupTest::anIdentifierIsReachedInEverySpellingACallerHasIt
---

# D-ANS-042 — An identifier reaches the changelog entries whose body names it

**`typo3_changelog_lookup` reads the entry bodies where the file names carried
nothing of the query, and a class, method or constant a body writes reaches that
entry whole.**

A caller after a removed method types the method name, and the name is in the
body: `getTemporaryImageWithText` is in three entries and in no file name, which
is the half of `D-ANS-030` the title alone does not answer.

## Evidence

- Measured on 2026-08-03 against `.checkouts/main`, which ships 3793 entries in
  `7.0` through `15.0`.
- Indexing the `:php:` roles the feedback asks for reaches one of the three
  entries. `13.0/Breaking-101955` writes the method as a role;
  `8.0/Breaking-72426` and `7.1/Deprecation-46770` write it in single backticks,
  because the role postdates them. Every inline literal, whatever markup it is
  written in, reaches all three.
- A whole role is not a searchable word. The class path is part of it, so
  splitting one leaves `CMS` reaching 1080 entries and `Core` 530.
- Restricted to the words carrying a hump or an underscore, the index is 8561
  names across 3037 entries. 4885 of them reach exactly one entry and 96% reach
  at most five, and none of `image`, `form`, `core`, `backend`, `text`, `page`,
  `file`, `preview`, `placeholder`, `request`, `true`, `false`, `null`, `this`,
  `and`, `default`, `content` or `event` is in it.
- What the read costs: 39 ms warm to open all 3793 entries and 29 ms to take the
  identifiers out of them. Through the tool, the unfiltered miss that pays it is
  112 ms against the 44 ms a query the names answer costs; narrowed to `13` it
  is 20 ms.
- What the tool answers with it. `getTemporaryImageWithText` returns the three
  entries above; `image generation` returns the one entry it returned before;
  `GraphicalFunctions` returns the 6 entries named after it and not the 18 whose
  bodies write it.

## Decided

- **The names answer and the bodies are read only where they answered nothing.**
  A call that hits pays no read, nothing that matched before stops matching, and
  the first **Wrong if** of `D-ANS-030` is what the order keeps out: the query
  for one class is answered by the entries named after it.
- **An identifier is a word an inline literal writes with a hump or an
  underscore.** That rule alone is what keeps the words a query is made of out
  of an index a term is compared to whole — `Request`, `Event` and `File` are
  class names spelled exactly like what a caller means by them. Rejected: the
  `:php:` roles alone, which reach one entry of three; the Removed-lists the
  feedback names, which are narrower again; and every word of a literal, which
  brings the ordinary words back.
- **A term is compared to the name it ends in, whole.** A caller types the
  identifier at whichever specificity they have it, and the last segment of
  `\TYPO3\CMS\Core\Imaging\GraphicalFunctions->getTemporaryImageWithText()` is
  what the query is about. Rejected: containment, which is how `image` would
  reach every entry naming `imageCreateFromFile`.
- **The answer says which of the two carried the query**, in the text and as
  `matchedIn`, because a body match names the identifier without being about it.
- Left alone: the miss advice. The per-term counts and the largest reaching
  subsets still run over the names, so a miss that mixes an identifier with a
  word it cannot place says nothing about the identifier.

## Assumed

- That a caller after a removed method types the identifier rather than a
  description of it. The feedback says so in its own suggestion, and it is still
  one session.
- That an entry naming an identifier is worth returning where nothing is named
  after it. Nothing measured here says how much of the index is a class an entry
  merely mentions, and `E_USER_DEPRECATED` reaches 329 entries.
- That the read stays affordable as the changelog grows. It is per file and on a
  miss, so an unfiltered miss pays it whole.

## Wrong if

- A query naming an identifier comes back with entries about another subject.
  The order keeps that out for a name some entry is named after and not for one
  nothing is: `E_USER_DEPRECATED` answers with 329 entries, and almost none of
  them is about it.
- The read shows up as latency where a miss used to be cheap. The unfiltered
  miss is 94 ms here, against the 48 ms the names alone cost.
- A caller types a method spelled as one lowercase word — `crop`, `scale`,
  `output` — and reaches nothing, because the index carries no word without a
  hump.
- A caller reads a body match as the entry being about the identifier, despite
  the sentence beside it saying that it is not.
