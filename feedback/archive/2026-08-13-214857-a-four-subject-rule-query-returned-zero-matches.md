---
date: 2026-08-13T21:48:57+00:00
category: missing-knowledge
status: closed
closed: 2026-08-13
model: claude-opus-5[1m]
tool: typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# a four-subject rule query returned zero matches while the same subjects answered when one documen...

## Observation

Task: review Gerrit change 93319; I needed to establish whether a test-only [TASK] owes a changelog entry, and what the rules say about e2e coverage and review readiness.

typo3-core-patch-review explicitly instructs bundling: "Obligations that share a document are one call. `breaking change changelog entry` returns both sections whole, and asking the two separately returns the same pair twice." I followed that and asked for four subjects at once:

  typo3_rule_lookup(query: "changelog entry testing review readiness", targetVersion: "15.0")
  -> matchCount: 0, matches: []

Zero. Not a partial answer, not a low-coverage one — nothing. All four subjects are real headings in core/contribution/rules ("Testing", "Documentation", "Review Readiness") and the changelog obligations are headings in core/contribution/commit-messages. The follow-up call with documentId "core/contribution/rules" returned the document whole and answered every one of them.

So the guidance and the matcher disagree. The skill says bundle subjects; the matcher appears to require a match to carry every word of the query at once, which makes a bundled query strictly less likely to match than any of its parts. The skill even anticipates the shape of this — "Coverage is measured against the query's own words, so a query naming four obligations dropped the deprecation section that one subject alone returns" — but it describes degraded coverage, not a total miss, and it presents bundling as the efficient move. In practice the bundle cost me a wasted round trip and the recovery was to abandon querying entirely and read a document by id.

The zero-match answer did do one thing right: it listed every document with its topics, and "Testing" and "Review Readiness" under core/contribution/rules were visible in that listing, which is how I picked the documentId. That fallback is what kept the cost to one extra call instead of several.

## Query

typo3_rule_lookup(query: "changelog entry testing review readiness", targetVersion: "15.0") — 0 matches; then typo3_rule_lookup(documentId: "core/contribution/rules") — answered all of it

## Suggestion

Either make the matcher fall back to per-term matching when the conjunction returns nothing — returning the union of sections that matched any subject, each labelled with which — or change the skill's instruction, because as written it recommends the query shape most likely to return zero.

If the conjunction is deliberate, say so in the tool description in those words: a query matches only sections carrying every word, so name one subject per call. That is the opposite of what the review skill currently teaches, and one of the two has to move.

The document listing that came back with the empty result is the right behaviour and should stay — it turned a dead end into a documentId in one step.
