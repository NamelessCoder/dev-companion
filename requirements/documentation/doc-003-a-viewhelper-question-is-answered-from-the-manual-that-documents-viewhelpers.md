---
id: R-DOC-003
status: held
restsOn: [D-ANS-026, D-ANS-032]
---

# R-DOC-003 — A ViewHelper question is answered from the manual that documents ViewHelpers

**What a ViewHelper does is answered from the reference that documents
ViewHelpers, at the version it was asked for.**

`R-DOC-001` says the manuals searched are the ones a question can be about.
This names the one that was missing and what carrying it costs. The Fluid
ViewHelper Reference is published under `/other/` rather than the `/m/` the
three manuals of the core sit in, so where a manual is is the collection it is
published in and not one base built for all of them. A base built wrong is
silent: the root does not answer, the book is simply absent from the index, and
the question comes back answered from whichever of the other manuals carries
the word. So what has to hold is that its pages are in the index, that they are
there under its own base, and that a URL handed back from it is one the same
call takes back.

What this does not promise is where in the answer that page comes. A name too
short to be searched for is no longer the obstacle — `TermSearch::terms()`
admits a two-letter word, so `f:if` reaches `Global/If.html` — and neither is
the ranking constant: `D-ANS-032` weighs a title by its length and the page is
fourth of the ten rather than eighth. What is left is what neither reaches.
Three of those ten are titled `if` — the two TypoScript function pages and this
one — and a fourth is `security.ifAuthenticated`, which is three words and so
undiluted as well. All four score the same, no field weight separates them
because all four matched in the title, and the order among them is the order the
index was built in. What does separate them is the book, which the query names
by its `f:` prefix and nothing in `Documentation` reads. `f:or` and
`f:then` do not get that far: `or` and `then` are stopwords, so those queries
have no term left at all. Both carry a todo of their own.

## From

`feedback/2026-08-01-003000`, judged as `D-ANS-023` on 2026-08-02. One session
lost a task to three Fluid mistakes; three manuals were searched, none of them
documents a ViewHelper, and `f:if f:then f:else condition ViewHelper` came back
with Developing a custom ViewHelper and the Translate ViewHelper.

## Held by

- `DocumentationTest::aViewHelperQuestionReachesTheManualPublishedOutsideTheCoreCollection`
- `DocumentationTest::aPageOfThatManualIsReadBackAtItsOwnBase`
