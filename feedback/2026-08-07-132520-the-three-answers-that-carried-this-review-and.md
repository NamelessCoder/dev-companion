---
date: 2026-08-07T13:25:20+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_test_run_guide, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# the three answers that carried this review, and why -d postgres in particular must not be dropped

## Observation

Task: critically review a local TYPO3 core commit on Extbase empty-date handling, then rework it and amend. Filing what worked, so it is not broken later.

typo3_project_describe, one call, opened the session: kind=core-checkout, typo3Version 15.0.0-dev, corePhpConstraint ^8.5, environment via ddev at php 8.5, extensions []. Three of those were load-bearing. main being v15 decided how I read the "Releases: main, 14.3, 13.4" trailer — I had no other way to know the branch I was standing on was not v14. extensions:[] correctly closed out the typo3_extension_describe step in the prescribed order, and it said so rather than leaving me to infer it. And naming the ddev PHP as a different interpreter from my shell's is why I did not trust a host-side lint.

typo3_test_run_guide, one call with the changed paths, returned the targeted invocation "-s functional -d sqlite -- <path>", the option list including -d sqlite|mariadb|mysql|postgres and -b docker, and the CI=true note. Roughly fifteen runTests.sh invocations followed and every one used all three. Two things I would protect specifically. First, -b docker: the answer says podman is the default and docker the fallback, and this host has only docker — without that line I would have failed on the first run and blamed the checkout. Second, -d postgres: two of the strongest findings in the review exist only because switching DBMS was one flag away. I could show that equals($property, null) on a native non-nullable datetime column raises SQLSTATE[22008] "date/time field value out of range: 0000-00-00" on postgres 10 while returning 0 rows on mariadb 10.4 — a measured regression instead of an argued one. A guide that returned only the suite name would have left that as "this would presumably throw".

typo3_commit_message_guide, two calls with workflow="core". Passing releases ["main","14.3","13.4"] and getting no error back is what let me confirm the trailer without counting Releases: lines in git log — which the Release Targets rule explicitly warns is the wrong method. It also returned breaking-not-assessed rather than silently assuming the classification, which is the right shape for a tool that never sees the diff. The second call, on my rewritten message, was worth its round trip too.

## Query

typo3_project_describe (no args); typo3_test_run_guide paths=[4 extbase Classes paths, 2 Tests paths] query="functional tests, phpstan, cgl for an extbase persistence change"; typo3_commit_message_guide workflow="core" changeType="BUGFIX" issue="109572" releases=["main","14.3","13.4"] message=&lt;the commit message&gt;

## Suggestion

Keep all three as they are. In particular keep typo3_test_run_guide returning the DBMS options and the container-runtime option alongside the targeted invocation rather than only the suite name: a reviewer who can run the same test file on three databases finds defects a reviewer who cannot will not, and in this session that difference produced the finding that stopped the patch. Keep typo3_project_describe answering extensions:[] explicitly for a core checkout instead of an empty response, and keep typo3_commit_message_guide validating the releases list rather than only formatting the message.
