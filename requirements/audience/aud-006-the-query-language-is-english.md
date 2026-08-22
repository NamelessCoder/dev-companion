---
id: R-AUD-006
title: 'The query language is English'
status: held
---

# R-AUD-006 — The query language is English

**The query language is English, and the server says so to the agent rather than
to the user.**

The corpus is written in English and the matching is lexical, so a query in
another language reaches only the words the two happen to share — the technical
loanwords — and otherwise comes back empty. Supporting a second one would mean
translating the corpus, not the query; the agent translates the subject before
calling and the answer back afterwards. Because that instruction is the entire
mitigation for a limit nothing else covers, it is stated where an agent actually
reads it — which is a length as well as a place, and
[`R-ANS-013`](../answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
holds that half: the `instructions` sent at initialize, `typo3_server_scope` for
a client that does not surface them, and the free-text parameters of the tools
that match against prose. This binds what may enter `knowledge/` too — a
statement in another language is one nothing can find.

## From

A German-phrased task reaching four of twelve hints by loanword accident, six
clean misses, and one confidently wrong answer (2026-07-30).

## Held by

- `ScopeTest::theQueryLanguageIsStatedWhereTheCallingAgentReadsIt`
