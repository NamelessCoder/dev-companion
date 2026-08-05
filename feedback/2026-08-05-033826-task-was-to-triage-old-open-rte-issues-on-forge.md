---
date: 2026-08-05T03:38:26+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# Task was to triage old open RTE issues on forge.typo3.org against a core checkout, then write a p...

## Observation

Task was to triage old open RTE issues on forge.typo3.org against a core checkout, then write a patch for one of them.

typo3_gerrit_lookup silently conflates the Forge issue number with the Gerrit change number, and five of my seven calls came back with a confident false positive. The tool reports the query it ran as `message:81676`, but Gerrit also matches that against the change number, so:

  issue 81676 -> change 81676 "[TASK] Update bootstrap to 5.3.2", MERGED, main
  issue 87400 -> change 87400 "[DOCS] Adjust indentation in rte_ckeditor documentation", MERGED, 12.4
  issue 88556 -> change 88556 "[BUGFIX] Parallel execution of non-parallel scheduler task", MERGED, main
  issue 88690 -> change 88690 "[TASK] Modernise Extbase QOM classes", MERGED, main
  issue 93409 -> change 93409 "[TASK] Clarify usage recommendation for GeneralUtility::makeInstance", MERGED, main

In every one of the five the returned change number equals the issue number exactly, and not one of the subjects has anything to do with the issue. None of these changes references the issue at all. Only 87314 and 97817 came back {"status":"empty"}, which was the truthful answer for all seven.

This is the most dangerous failure mode this tool can have, because of the role the skills give it. typo3-core-issue-triage calls it "its cheapest outcome is the one that ends the work: somebody has a patch up, and the triage is that it is under review rather than unaddressed", and typo3-core-patch-development says "its cheapest outcome is the one that cancels the work". A session that trusts the answer stops working on an issue that in fact has no patch. I only caught it because I had asked for seven at once and the pattern of change-number == issue-number across all five was too regular to be real; a session asking about a single issue sees one MERGED change with a plausible-looking core subject and has no signal at all that it is spurious. For #88556, the issue I then patched, the false positive said a merged change already exists.

The tool's own description promises the opposite of what it does: "Pass issue with a Forge issue number to search the commit messages of every change for it". It did not search the commit messages; it searched an untargeted index that includes the change number.

## Query

typo3_gerrit_lookup {issue: "81676"}, {issue: "87314"}, {issue: "87400"}, {issue: "88556"}, {issue: "88690"}, {issue: "93409"}, {issue: "97817"} — seven calls, one per candidate Forge issue, asking "has somebody already fixed this".

## Suggestion

Do not hand back a change whose commit message does not actually reference the issue. Either query Gerrit more precisely (message:"#88556", or the Resolves:/Related: forms the core uses) or post-filter the results server-side: drop any change whose commit message does not contain the issue number in a reference position, and in particular drop the change whose own number equals the issue number unless its message really names it. Where a result survives only weakly, say which trailer matched. An empty answer here is far more useful than a wrong one, because both skills treat a hit as grounds to stop working.
