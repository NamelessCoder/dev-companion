---
date: 2026-08-11T05:51:57+00:00
category: idea
status: closed
closed: 2026-08-12
model: claude-opus-5
tool: typo3_gerrit_lookup, typo3_forge_lookup, typo3_rule_lookup, typo3_test_run_guide, typo3_commit_message_guide, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# Four lookups each removed a specific wrong path from a core patch review; keep them as they are

## Observation

Task: review Gerrit change 94686 (RecordBreadcrumbProvider, EXT:backend) and then, once the user asked, finish it. Recording what worked, concretely, because these are the answers that must not regress.

1. typo3_gerrit_lookup(change "94686") returned patchSet 3 with commit 90955192c19a2ccb6d4f7114d6a1214693c95649. `git rev-parse HEAD` in the checkout was 02dbc51a82c. The mismatch was visible in one call and told me I was not looking at the patch at all; I fetched refs/changes/86/94686/3 (the ref came back in the same answer, so no second lookup) before reading a line of the diff. Without it I would have reviewed main and reported nothing.

2. typo3_forge_lookup(issue "110123") returned the relations, including #108072 "Module action is not kept in breadcrumb" (Closed). Following that to typo3_forge_lookup(issue "108072") gave the change 91586, which is commit d0479c32af804f38e49fa76a4b37c9c9f7c7ce2c in the checkout — the commit the patch under review partially reverts. That reframed the entire review: the patch is not a plain bugfix but a narrowing of an earlier merged fix, which decided the severity of two findings and produced a "Related: #108072" recommendation. Nothing in the diff or in the commit message says this. I would not have found it from the code.

3. typo3_rule_lookup(query "changelog entry bugfix code style review readiness") returned the "Changelog Files" section, which states that a BUGFIX owes no entry and that demanding one of a BUGFIX that removes nothing public is itself a review defect. I had the finding half-formed. It stopped a false finding before I wrote it.

4. typo3_test_run_guide with the changed path returned the targeted invocation `CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path>` plus the note that everything after `--` is passed through. Every test run I made in the session used that form and all of them worked first try; I never guessed at the runner.

5. typo3_commit_message_guide(workflow "core") flagged the author's 63-character subject against the preferred 52 and returned the body reflowed at 72 characters. I handed the reflowed output to the user verbatim three times as the message to amend with. The `breaking-not-assessed` note also made me go back to the diff and check explicitly that the added parameter sat on a private method of an @internal class rather than assume it.

The common property: each of these answered something the checkout cannot answer, or answered it before I could form a wrong view rather than after.

## Query

Task: "bitte review mir folgenden patch 94686" (review Gerrit change 94686 against a typo3-cms core checkout on main). Calls in order: typo3_project_describe; typo3_gerrit_lookup(change "94686"); typo3_forge_lookup(issue "110123"); typo3_forge_lookup(issue "108072"); typo3_test_run_guide(paths ["typo3/sysext/backend/Classes/Breadcrumb/RecordBreadcrumbProvider.php"]); typo3_rule_lookup(query "changelog entry bugfix code style review readiness"); typo3_commit_message_guide(workflow "core", message, releases ["main","14.3"], issue "110123").

## Suggestion

No change requested. Treat the pairing of typo3_gerrit_lookup's `patchSet`+`commit`+`fetch.ref` in one answer, and typo3_forge_lookup's `relations` carrying subject and status, as load-bearing for review work: both saved a round trip and both changed what the review concluded. The rule_lookup section that names demanding a changelog of a BUGFIX as a review defect is worth keeping worded exactly that way — it is phrased as a prohibition on the reviewer, which is what made it act on me.
