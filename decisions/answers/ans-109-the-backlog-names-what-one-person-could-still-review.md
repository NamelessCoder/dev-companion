---
id: D-ANS-109
title: The backlog names what one person could still review
date: 2026-08-25
status: open
coveredBy:
  - GerritTest::aPageThatLeftSomebodyOutSaysWhatAMisspeltNameLooksLike
  - GerritTest::theChangesAPersonHasNotTouchedAreOneQuery
---

# D-ANS-109 — The backlog names what one person could still review

**`typo3_gerrit_lookup` takes `reviewableBy`, a person whose own changes and own
votes are out of the enumeration, which is `-owner:` with `-reviewedby:` under
an affirmative name.**

`D-ANS-107` built three person filters and every one of them selects. The
question the reporting session was actually given is the complement of all
three: open work that is not mine and that I have not already judged.

## Evidence

- `feedback/2026-08-24-205050` was told to finish almost-ready open reviews and
  was told twice which changes were not to be looked at — the ones the user had
  voted on, and the ones of their own waiting on votes. It wrote
  `-owner:<email> -reviewedby:<email>` by hand, and every other operator in that
  query is an argument today.
- `involving` is the union of `owner` and `reviewedBy` taken positively, which
  is exactly the set this filter removes. No combination of the three reaches
  the complement, because the schema has no way to negate an argument.
- Measured anonymously against review.typo3.org on 2026-08-25, on
  `project:"Packages/TYPO3.CMS" status:open -is:wip`: 444 open changes, of which
  25 are `benjamin.kott@outlook.com`'s own and 5 carry a vote of theirs. The
  negated pair answers 414, so the union those 30 rows are is what comes out.
- The same pair with the almost-ready filters — `label:Code-Review>=1`, no
  negative Code-Review and no negative Verified — answers 81 of those 444, which
  is the shortlist the reporting session's whole task was about.
- A negated person resolves the way a selected one does:
  `-owner:"Benjamin Kott"` and `-owner:"benjamin.kott@outlook.com"` both answer
  the same 414.
- A name the review server cannot place excludes nothing.
  `-owner:"zzzznotauser" -reviewedby:"zzzznotauser"` answered all 444 rows,
  where the same misspelling passed to `owner` answers none.

## Decided

- The argument is `reviewableBy` and it takes a person, beside `owner`,
  `reviewedBy` and `involving`. It says what the answer is rather than what was
  taken out of it, which is what `AGENTS.md` asks of a name — and every obvious
  spelling of the operator is a negation a reader has to read twice: `notOwner`,
  `notInvolving`, `excluding`, `withoutPerson`, `unreviewedBy`.
- It is a filter and not a claim about permission. Nothing here reads the review
  server's access control, so what the name means is that this person's own
  changes and own votes are out — a change anybody may review is one anybody may
  review either way.
- Both operators go in together and neither is an argument of its own. Half of
  it — everybody else's changes, mine among the ones I have voted on — is a set
  no report has asked for, and either half alone is one more name for a question
  `owner` and `reviewedBy` already answer from the other side.
- It composes with the three that select rather than replacing them.
  `owner: A reviewableBy: B` is A's changes that B could review, which is a
  question a maintainer reading somebody's queue has; the same person on
  `involving` and here answers nothing, and the description says so rather than
  the code refusing it.
- The empty-backlog caveat gains the inverted trap. A misspelt name passed here
  widens the answer instead of emptying it, so the caveat that fires on an empty
  answer cannot be where it is stated — it is said with the filters instead.

## Assumed

- That "not mine and not already judged by me" is the whole of what a reviewer
  with time excludes. The report named those two and no third.
- That the anonymous search index keeps taking a negated person predicate, which
  is `D-ANS-107`'s first assumption on the operators it selects with.
- That a caller who misspells the name notices the answer is the whole backlog.
  Nothing here can tell that from a person with nothing open on either side.

## Wrong if

- Callers pass it and get an answer they still have to filter by hand, which
  would say the exclusion is not the two operators but something about the
  change itself.
- A second exclusion arrives — a person's comments, a branch, an area — and each
  one wants its own argument. That is a negation vocabulary growing where one
  filter was decided, and it is where a general "excluding" shape would have to
  be reconsidered.
- `reviewableBy` is read as a permission and a caller reports a change as
  unreviewable because it is not in the answer.
- The wide answer a misspelt name produces is acted on as the backlog. That is
  the failure `D-ANS-107`'s empty-answer caveat names, inverted, and nothing on
  this side separates it from a person nobody has crossed.
