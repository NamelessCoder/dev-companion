---
id: R-ANS-8b
status: held
---

# R-ANS-8b — A short term is not the prefix of a longer word

**A short term is matched as a whole word, not as the prefix of a longer one,
on both the query side and the curated vocabulary.**

Prefix matching exists so a stem finds every form of its word; at three
characters there is no form left to find and it matches whatever starts with
those letters. It compounds with
[`R-ANS-7`](ans-7-the-discriminating-terms-of-a-query-decide-the-answer.md),
which weighs a term by how few documents carry it: an accident landing in
exactly one document becomes the most discriminating term in the query and
decides the answer. A pattern carrying punctuation — a path fragment, `.xlf`,
`lll:` — keeps plain containment, being specific enough not to land by
accident.

**From:** `fal`, the File Abstraction Layer, prefix-matching seven hints
through "fallback" and "false"; and the same pattern reaching that hint from a
query about a label, as a plain substring of a longer word (2026-07-30).

**Held by:** `HintsTest::aShortTermIsNotMatchedAsThePrefixOfALongerWord`
