---
date: 2026-08-03T14:43:16+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_gerrit_lookup, typo3_changelog_lookup, typo3_rule_lookup, typo3_test_run_guide, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# This is what worked in a core-patch review and must not be broken later. Full transcript availabl...

## Observation

This is what worked in a core-patch review and must not be broken later. Full transcript available — this session did not begin at a summary.

Four answers each produced a finding I could not have produced from the checkout, and one produced a finding I would have got wrong without it.

1. typo3_forge_lookup(110359) — one round trip. Returned subject "Avoid calling ImageService methods - part 2", status Under Review, and an EMPTY description with a single note (the automated Gerrit push notification). Two findings came out of that single call: (a) the review-readiness finding, because the contribution rule "the change should be reproducible from the issue or task description" fails against an empty description, and every justification the patch has lives in the commit body alone; (b) the whole framing of the review, because "part 2" says the patch may not be meant to stand alone, which decides every finding about what it leaves behind. Neither fact is visible from git.

2. typo3_gerrit_lookup, both directions, one round trip each. By Change-Id: change 95070, patch set 1, status NEW, branch main — established that I was reading the same patch set that exists on the server and that no reviewer comment was outstanding, which the checkout cannot report. By issue: exactly one change for #110359, so no part 3 has been pushed. That second call is what made findings about the leftover ImageService API legitimate against THIS patch rather than deferrable to a later one in the series. Both directions were needed; neither restated the other.

3. typo3_changelog_lookup(query "ResourceFactory") — one round trip, four entries, and the single highest-value call of the session. Feature-72904-AddPreProcessStorageSignalToResourceFactory gave me the precedent that adding to ResourceFactory has earned a Feature entry before, which turned "a changelog is owed" from taste into an argument. Important-107735-InternalMethodsRemovedFromResourceFactory (14.0) gave me the opposite direction — the core removed methods from ResourceFactory in 14.0 explicitly for separation of concerns toward StorageRepository — which is exactly the design question a patch adding a broad loosely-typed resolver back onto that class has to answer. I reported it as an open placement question with the entry cited. Without this call I would have had neither the precedent nor the counter-direction.

4. typo3_rule_lookup(query "breaking change") — one round trip, and it CORRECTED me before I acted. The patch removes a protected method (ImageService::getImageFromSourceString) from a non-final, non-@internal class. I was about to demand an extension-scanner matcher entry. The rule's enumeration of matcher kinds — MethodCallMatcher for an instance method, MethodCallStaticMatcher, PropertyPublicMatcher for a removed public property, PropertyProtectedMatcher for a public property that became protected, ClassNameMatcher for a class — has no kind that covers a removed protected method, and the rule adds that the scanner reads PHP and what it cannot find leaves an entry PartiallyScanned or NotScanned. So I reported the changelog obligation but explicitly said the matcher cannot exist for a protected member and the entry would be NotScanned/PartiallyScanned. That is a wrong demand I did not make.

5. typo3_test_run_guide(the seven changed paths, targetVersion 15.0) — one round trip, and every invocation it returned ran verbatim. "-s functional -d sqlite -- <path>" (16 tests OK, then 111 tests OK across three ViewHelper files), "-s unit -- <path>" (7 OK), "-s cgl -n" (0 of 6279), "-s phpstan" (No errors), "-s checkIntegrityPhp" (OK). I never opened Build/Scripts/runTests.sh. It also returned the suites I did NOT run, which is what let me name them in the report instead of letting six green suites read as a finished verification. The cglGit caveat about git worktrees was returned unprompted and is the kind of thing only the server knows.

The shape that made this work: the skill fixed the or

## Query

Task: "Review the current changes in this TYPO3 core checkout. Tell me what is wrong, missing, or not ready for review, in priority order. Do not change files." Core checkout at 15.0.0-dev, single commit 9f6c6eb9093 "[TASK] Resolve file sources in ResourceFactory", Resolves: #110359, Change-Id: Id53f1068d3866128320487df9ec59530d94a4aa1. Calls that carried the review: typo3_forge_lookup(issue "110359"); typo3_gerrit_lookup(change "Id53f1068d3866128320487df9ec59530d94a4aa1"); typo3_gerrit_lookup(issue "110359"); typo3_changelog_lookup(query "ResourceFactory"); typo3_rule_lookup(query "breaking change"); typo3_test_run_guide(paths = the seven changed files, targetVersion "15.0").

## Suggestion

Keep all five behaviours exactly as they are. Specifically: keep typo3_forge_lookup returning the note list and an empty description as an empty description rather than eliding it — the absence IS the finding. Keep typo3_gerrit_lookup's two directions separate; issue-search and Change-Id lookup answered different questions in the same review. Keep typo3_changelog_lookup's plain class-name query working without a type or version filter — "ResourceFactory" alone returned both the precedent and the counter-direction, and narrowing it would have hidden one of them. Keep the matcher-kind enumeration in the Breaking Changes rule body verbatim; its value was in what it does NOT list. Keep typo3_test_run_guide returning the full suite list rather than only the narrowed ones, because naming the unrun suites is a review obligation.
