---
id: D-KNW-091
title: A PHP version is the payload a hint may state, and a TYPO3 version is not
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch
  - HintsTest::eachCoveredLineCarriesItsOwnFloorAndTestedRange
---

# D-KNW-091 — A PHP version is the payload a hint may state, and a TYPO3 version is not

**A hint may write a PHP version where it writes the word `PHP` in front of it,
and every other `<major>.<minor>` stays out of the corpus.**

`HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch` refused every
`\d+\.\d+` in a hint, and what it is looking for is a statement silently tied to
one branch. The interpreter a covered version requires is the opposite case: it
is what the caller asked for, `since` and `until` say which branch it holds on,
and `bin/cli checkouts:update` re-reads the file it came from. The exemption is
the shape the guard already uses for `HTTP 404`, `doktype 254` and `PSR-14` —
the number carries its word, so exempting it exempts nothing else.

## Evidence

- The guard's three patterns are a version number, a concrete changelog file and
  a count, and its own comment says a number naming something other than a
  branch is exemptible where it is written with its word in front.
- `php-versions` needs eight of them: `PHP ^8.1`, `PHP ^8.2`, `PHP ^8.5`, three
  `config.platform.php` pins and both ends of two tested ranges. Written bare,
  every one of them trips the guard; written with the word, none does.
- The count pattern is untouched and still fires: `\b\d{2,}\b` never matches a
  PHP version, so nothing this exemption admits can be a count.

## Decided

- **The word is required on both ends of a range.** "PHP 8.2 through PHP 8.6"
  and not "PHP 8.2 through 8.6", which is the repetition the rule costs. A bare
  second number would need an exemption for the range itself, and that one would
  admit `13.4` as readily as `8.6`.
- **The caret is inside the exemption, the word still in front.** `PHP ^8.1`
  passes and a bare `^8.1` does not, because `^13.4` is exactly the statement
  the guard exists to refuse and no pattern can tell the two apart by shape.
- **Stated in the test rather than in a rule nobody reads.** The next author
  finds it where the assertion fails, which is the only place the convention has
  to be legible.

## Assumed

- **That PHP is the only foreign version scheme the corpus will want.** A Fluid,
  a Composer or a DDEV version would need the same argument made again, and
  making it once per product is the point: each is a decision rather than a
  widening of the pattern.

## Wrong if

- A hint writes `PHP 8.4` about a mechanism that is not the interpreter — a
  released feature, a syntax level — and dates itself through the exemption. The
  guard would pass it; nothing else would catch it.
- A caller reads a tested range as a support statement despite the sentence
  saying it is the core testing itself. Then the numbers were the wrong payload
  and the answer belongs in the manual lookup instead.
