---
id: D-VER-005
title: A document section declares the majors it holds for
date: 2026-08-04
status: open
coveredBy:
  - KnowledgeTest::aBoundSectionIsKeptOnTheMajorItHoldsFor
  - KnowledgeTest::theBindingDoesNotReachTheCallerAsPartOfWhatItBinds
  - KnowledgeTest::aDeclarationBelowTheFirstLineOfContentBindsNothing
  - KnowledgeTest::noProseDocumentDatesAStatementInItsSentence
  - KnowledgeTest::noProseDocumentNamesACheckOnlySomeBranchesHave
  - VersionsTest::proseSaysWhereARangeItDoesNotCarryLives
---

# D-VER-005 — A document section declares the majors it holds for

**A knowledge document section declares the majors it holds for, and every prose
answer is filtered by the caller's target instead of labelled as unfiltered.**

`D-VER-002` refused a binding mechanism for prose and had the better of the
argument for the corpus it was written about. What it did not have in front of
it is a section whose body is a file the caller writes out verbatim, where the
document is the only place the range could be stated.

## Evidence

- `D-VER-002` rests on two properties. The corpus is read whole rather than
  filtered, and the same statements are bound where a caller acts on them. A
  shipped file has neither: the document is where the caller acts, and no hint
  carries the file for the range to sit on.
- Its confirmation on 2026-08-02 is what the cost of not binding looks like.
  `typo3_script_lookup` handed a 12.4 contributor `-s checkIntegrityXliff`,
  `-s normalizeXliff` and `-s build`, none of which that branch has. The remedy
  was to take the commands out of the prose, because their range already lived
  on the suite. A file has nowhere to be taken out to.
- The divergence a skeleton has to survive is measured. Between
  `typo3/testing-framework` lines 8 and 9 both PHPUnit XML files differ in the
  schema URL, `10.1` against `11.2`, and in
  `beStrictAboutTestsThatDoNotTestAnything`; line 9 and `main` are identical.
- Nothing new has to be invented one layer down. `Versions::holds()` answers
  whether a range covers a major, `Versions::target()` and `Versions::targets()`
  resolve what the caller is on, and `Hints::forVersion()` is the filter this
  mirrors.

## Decided

- The binding is declared per section, directly under its heading, and read by
  `Documents::sections()`. A section that declares nothing holds on every
  covered major, the same as a hint statement carrying no `since` or `until`, so
  the corpus as it stands today changes in no answer.
- Prose::NOT_VERSION_BOUND goes. A prose answer states the range of the section
  it returns, which is what the constant used to say was impossible.
- The three tools rendering this corpus take an optional `targetVersion`
  together — `typo3_rule_lookup`, `typo3_script_lookup`, `typo3_task_guide` —
  because a caller that learns one of these answers has to find the next one
  built the same way. Adding it to one of them is the split that makes the
  corpus answer two ways.
- The declaration is data, so the two guards in `KnowledgeTest` are rewritten
  around the new premise rather than dropped. A version written into a sentence
  stays wrong, and a suite only some branches carry stays wrong, because neither
  is something a filter can read.
- Per-paragraph binding stays refused. The section is the unit, which is what
  `D-VER-002` called per-bullet metadata and what this does not introduce.

## Assumed

- A section is a fine enough unit for prose that changed inside the covered
  range. Nothing measures that; the case that disproves it is a section half of
  which holds on one major.
- A caller on no installation, stating no target, is served by getting every
  variant with its range named. That is what the corpus does everywhere else,
  and for a file it means picking one rather than reading both.

## Wrong if

- A caller with no target is handed two variants of one file with nothing in the
  answer saying which to write out.
- A binding appears on a section whose body is prose rather than a file, so the
  corpus starts filtering the long form of a subject that the hints already
  carry bound.
- The declaration reaches a caller as part of the file it binds.
- A section is bound and the range it declares is never read, because the tool
  the caller used passes no target. Nothing runs over that one: which tools
  render this corpus is read rather than counted, and a fourth would have to be
  given the target by whoever adds it.
