---
date: 2026-08-26T22:34:14+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# four things in the forge tool that carried the session and should survive a refactor

## Observation

Task: "please find 1 old forge issue and fix it", narrowed to Extbase, ending in a shipped patch for #76202. Filing the strengths separately because they are load-bearing and easy to lose in a rewrite.

1. The inline `reviews` field on every row. It is scraped from review.typo3.org URLs in the text, and it is what let me see at a glance that #70921, #72962, #82228 and #52070 all carry ABANDONED changes — prior attempts that died — while #76202 carried none. That distinction is most of what makes an old issue worth or not worth picking up, and I got it without a single Gerrit call. It is also why typo3_gerrit_lookup never had to be opened.

2. notes="people" on the sweep. On the 25-row stale enumeration this kept the answer readable; the same call with full notes would have been mostly patch-set pings. The reasoning given for it — that the cost of reading ten issues decides whether they get read at all — matched my behaviour exactly.

## Query

typo3_forge_lookup with: open="stale"+updatedBefore="2020-01-01"+notes="people"; issue= on eight issues

## Answered

The two orderings, the area matching and the constraint across eleven calls.
Every quotation reproduces, and the keep-request that rested on nobody rewriting
the file is held now: only the `stale` half of the pair was asserted, and
`ForgeTest::theNeglectedEndIsOrderedByTwoThingsAndNotOne` holds both.

The suggestion is answered rather than queued. What makes `reviews` cheap on a
sweep is a source that answers a whole page in one query, not the scrape and not
the size of the field, so a per-issue verdict cannot be modelled on it —
`D-FBK-018`.

Both items above are misplaced credit, and each names a sentence that is still
open. The state on a row comes from the review server and not from the scrape,
and reading an `ABANDONED` as the verdict is what `D-ANS-069` wrote its wording
against. `notes` narrows `issue` and the enumeration ignores it, so the filter
credited here never ran, and the sentence that invited the call is in the same
file.
