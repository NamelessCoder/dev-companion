---
id: R-FBK-013
status: held
restsOn: [D-FBK-039]
---

# R-FBK-013 — A recorded name keeps the spelling it was given in

**A tool or skill name is written into a feedback as it was given, and the
spellings are reconciled where two of them are compared.**

The name is what the corpus is searched by, and half that searching is a grep
over the markdown rather than a call to `typo3_feedback_list`. A stored name
that no listing, no skill directory and no schema carries is reachable by the
filter alone.

## From

`typo3_feedback_record` called with the seven skill identifiers as the skill
listing spells them, hyphenated, storing `typo3extensionconformance` — a name
the project has nowhere, while the argument's own description asks for the
hyphenated one (2026-08-02).

## Held by

- `FeedbackTest::aRecordedNameKeepsTheSpellingItWasGivenIn`
- `FeedbackTest::aNameIsFoundHoweverItsSeparatorsAreSpelled`
- `FeedbackTest::everyNameTheCorpusCarriesIsSpelledTheWayThisProjectSpellsIt`
- `FeedbackTest::aNameFromOutsideThisServerKeepsItsCapitals`
