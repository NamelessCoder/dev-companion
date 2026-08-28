---
date: 2026-08-28T07:39:54+00:00
category: idea
status: closed
closed: 2026-08-28
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_task_guide
directory: /home/benji/projects/bootstrap_package
---

# the guides list is read past, and its two relevant procedures were re-derived by hand

## Observation

Task: review pull request #1613 against bootstrap_package — one changed line in a Fluid partial of the table content element — then fix it and cover it with a test.

typo3_project_describe returned a `guides` array of 21 documents. My client renders tool results in full, so the list was visible to me. I opened none of them. Two of the 21 were exactly the procedures I then worked out for myself:

1. `extension/compatibility/running-on-a-declared-major-that-is-not-installed`. The package declares `^13.4 || ^14.3`; only 14.3 is installed. I had to establish that the proposed condition also fails on 13.4. I improvised: `composer require typo3fluid/fluid:^4.3` in a scratch directory, then ran the same render script against Fluid 4.6.1. That document's `when` line describes this situation exactly, and its stated content ("the Composer root of its own that stands the other major up beside the installation") is what I built. Cost: 4 round trips, plus a `gh api` call to TYPO3/typo3 composer.json@13.4 to learn which fluid constraint 13.4 carries.

2. `core/testing/proving-a-rendering`. I needed a functional test asserting what the content element renders. I tried a site-set-based setup first, it failed twice, then arrived at importing the element's TypoScript directly. Cost: 3 failed test runs at roughly 8s each plus four TypoScript and Fluid files read.

The structural point: the guides arrive as one flat array at the end of a large JSON answer, after `commands`, `patches` and `extensions`, all 21 at equal weight, and nothing later in my working order pointed back at it. typo3_task_guide's checklist did state the obligation — "Judge it on every major the package's Composer constraint declares and not only on the installed one" — but that checklist entry does not name the document carrying the how. So the obligation and its procedure arrived in two different answers, minutes apart, and I acted on the first without going back for the second.

This is the difference between a document that is not there and one that exists and stayed shut. Both of these existed, and both were listed to me.

## Query

typo3_project_describe (no arguments) — returned guides[] with 21 entries, including extension/compatibility/running-on-a-declared-major-that-is-not-installed and core/testing/proving-a-rendering.

typo3_task_guide task="Review an incoming pull request that changes an f:if condition in a Fluid partial of the table content element", changeType="audit", paths=["Resources/Private/Partials/ContentElements/Table/Columns.html"] — returned a checklist naming the multi-major obligation but no documentId.

## Suggestion

Make the obligation and its procedure travel together. Where typo3_task_guide emits a checklist entry whose procedure is one of the bundled documents, carry that documentId on the entry itself, so the sentence creating the duty also names the page discharging it.

Failing that, have typo3_project_describe mark the subset of guides the current repository state actually activates. Here, `coreConstraint: ^13.4 || ^14.3` against `typo3Version: 14.3.6` makes both compatibility documents relevant and most of the other 19 irrelevant; a `relevant: true` flag, or a short `becauseOf` naming the field that triggered it, would have made them hard to scroll past.
