---
date: 2026-08-24T18:32:09+00:00
category: wrong-answer
status: open
model: claude-opus-5
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# breaking-not-assessed calls a widened protected member breaking; the core's own history does not

## Observation

Task: review open Gerrit change 91127 ("[BUGFIX] Reset page renderer when performing subrequest") against a current core checkout at 15.0.0-dev, patch set 8 carried onto main.

The diff's only API-shaped change is PageRenderer::reset() going from protected to public, with an added @internal docblock line.

typo3_commit_message_guide with workflow="core" returned check `breaking-not-assessed`, which states: "a removed, narrowed or **widened** public or protected member makes the change breaking ... A breaking change owes [!!!], a Breaking changelog entry and an extension scanner matcher."

Read literally that makes this patch breaking and unsubmittable as written. I nearly reported it as a blocker. What stopped me was the review skill's checklist telling me to find precedent before proposing anything, so I swept the core's own history instead:

- git log --since=2025-01-01 over typo3/sysext/core/Classes and typo3/sysext/backend/Classes gave 1405 commits; filtering for diffs that both remove a "protected function" line and add the same name as "public function" found many.
- 343e93a97826 "[TASK] Promote generateRandomBase64String to public API" is exactly this shape: Classes/Crypto/Random.php plus call sites and tests, four files, no Documentation/Changelog file in the diff, no [!!!], no extension scanner matcher.
- typo3_changelog_lookup finds no entry documenting a protected-to-public widening at any covered version.

So the core does widen visibility in plain [TASK] and [BUGFIX] commits without any of the three obligations the check names.

The hazard the check reaches for is real and I verified it with a throwaway probe: PHP fatals with "Access level to Child::reset() must be public (as in class Parent)" when a subclass overrides with the narrower visibility, and PageRenderer is neither final nor @internal at class level. But "real hazard" and "owes [!!!] plus a Breaking entry plus a scanner matcher" are different claims, and only the second is what the check asserts.

Widening and narrowing are also collapsed into one sentence. Narrowing breaks every caller; widening breaks only subclasses that re-declare the member, a much smaller and differently shaped set. The core evidently treats them differently.

## Query

typo3_commit_message_guide with workflow="core" and the full commit message of Gerrit change 91127 (Change-Id Ibb426e12fe37d89471c4b7fa8cb11fade77ba5f3), isBreaking omitted. The diff it describes changes PageRenderer::reset() from protected to public and adds @internal, plus three lines in PageContentErrorHandler::sendSubRequest(). Re-run and read the breaking-not-assessed check text.

## Suggestion

Split the sentence. Removal and narrowing owe [!!!], a Breaking entry and a scanner matcher — that is well supported. Widening should be stated as what it is: not breaking in the core's practice, but a hazard for subclasses that re-declare the member, worth a line in the commit body rather than a changelog entry. Naming 343e93a97826 (or any equivalent) as the precedent would settle it in the answer instead of costing a reviewer a 1405-commit sweep.

Separately: the check fires on every call that omits isBreaking, with the same paragraph every time. A reviewer holding a diff cannot pass isBreaking honestly without having already done the enumeration the text asks for, so it reads as a demand rather than a finding. Consider pointing it at typo3_rule_lookup(query "breaking change") for the obligations and keeping the check itself to naming which member kinds to enumerate.
