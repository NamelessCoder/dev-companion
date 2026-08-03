---
date: 2026-08-03T14:43:24+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# Task: review core commit 9f6c6eb9093 "[TASK] Resolve file sources in ResourceFactory" and report ...

## Observation

Task: review core commit 9f6c6eb9093 "[TASK] Resolve file sources in ResourceFactory" and report what blocks it.

The typo3-core-patch-review skill instructs: "Ask it per obligation and not once: the sections it returns are named by subject, and a query that names two reaches neither." I followed that and made two calls, one for the changelog obligation and one for the breaking-change obligation.

The two answers overlapped almost entirely. Both returned the same two sections of typo3-commit-messages verbatim — "Breaking Changes" (full body, identical text) and "Changelog Files" (full body, identical text) — differing only in score (31/31 for "changelog entry", 86/31 for "breaking change") and in the two lower-ranked matches. "changelog entry" additionally returned typo3-contribution-sources and typo3-core-scripts; "breaking change" additionally returned typo3-core-rules "Review Readiness" and typo3-commit-messages "Summary Line".

So the second call cost a round trip and returned roughly 80% restatement. The two extra sections it did surface were both useful — "Review Readiness" is what I cited for the empty-issue-description finding, and "Summary Line" is where the [!!!] rule lives — but I got them as a by-product of a query whose main hits I already had.

This is not a wrong answer; the ranking is doing its job. It is a mismatch between the skill's advice and the corpus's granularity: changelog obligations and breaking-change obligations are not separate documents, they are two sections of one document that both queries reach. The advice "ask per obligation" is right for genuinely distinct subjects (testing vs. review vs. code style) and produces a near-duplicate for these two.

Both calls were worth making in the end. But if I ran this review again I would make one call for the pair and a separate one only for a genuinely different subject.

## Query

typo3_rule_lookup(query "changelog entry") then typo3_rule_lookup(query "breaking change"), issued in the same turn during a core patch review of commit 9f6c6eb9093 (#110359).

## Suggestion

Either (a) have typo3_rule_lookup suppress or collapse a section body it is about to return when the same section was the top hit of a query in the same session, returning "already returned for query X" plus only the sections new to this query — so the second call costs a round trip but not a re-read; or (b) adjust the typo3-core-patch-review skill's wording so it does not imply that "changelog entry" and "breaking change" are separate obligations to ask separately, since they resolve to the same two sections. A cheap third option: have the answer for either query name the sibling sections of the same document that the other query would surface ("Summary Line", "Review Readiness"), so one call reaches the whole obligation cluster.
