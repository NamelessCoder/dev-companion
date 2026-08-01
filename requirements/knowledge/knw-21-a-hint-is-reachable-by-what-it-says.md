---
id: R-KNW-21
status: held
---

# R-KNW-21 — A hint is reachable by what it says

**A hint is reachable by what it says, not only by the words it was indexed
under.**

`appliesTo` is a curator's guess at how the subject will be asked about, and a
hint's own statements are where the symptom is written down — «the failure
is a service-not-found at request time» is the sentence a caller arrives with
and was the one thing the matcher could not see. Both are scored, the curated
vocabulary above the prose, so a phrasing somebody anticipated still decides.
This does not withdraw
[`R-KNW-2`](knw-2-a-hint-carries-the-words-its-subject-is-asked-about-in.md);
it removes its cost, which was that every phrasing had to be foreseen at
authoring time. Because the corollary of matching more is answering everything,
the other half is held too: a term the corpus does not carry lowers what any
answer can cover, so a query about a subject nobody wrote down still misses and
is answered by the index
[`R-ANS-6`](../answers/ans-6-a-miss-says-what-there-would-have-been-to-find.md)
requires.

**From:** a measurement of the matcher on 2026-07-30 — 57 hints and 11,501
words of hint body reachable through 9.3 keywords each; of eighteen realistic
queries, seven reached nothing, two of them the `dependency-injection-services`
hint that names the symptom outright.

**Held by:**
`HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay` — the
measurement itself, both halves of it: the queries that reached nothing before
and the two the corpus has no answer for, which is what says the matcher did
not simply start answering everything. `HintsTest::theCuratedVocabularyStillDecidesWhereItWasWritten`
holds the ranking the sweep only holds the membership of.

A sweep is still a sample, so `bin/cli hints coverage` is what says how much of
the corpus is reachable at all: which hints their own title does not reach,
which no scenario prompt reaches, and which prompts reach nothing.
