---
id: R-DOC-003
status: held
restsOn: [D-ANS-026]
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
admits a two-letter word, so `f:if` reaches `Global/If.html`. What is left is a
tie: the page titled "if" scores exactly what the ten other pages whose titles
carry the word score, and it is eighth of them by the order the index was
built. `f:or` and `f:then` are a second case of the same shape, unreachable by
name because `or` and `then` are stopwords. Both carry a todo of their own.

## From

`feedback/2026-08-01-003000`, judged as `D-ANS-023` on 2026-08-02. One session
lost a task to three Fluid mistakes; three manuals were searched, none of them
documents a ViewHelper, and `f:if f:then f:else condition ViewHelper` came back
with Developing a custom ViewHelper and the Translate ViewHelper.

## Held by

- `DocumentationTest::aViewHelperQuestionReachesTheManualPublishedOutsideTheCoreCollection`
- `DocumentationTest::aPageOfThatManualIsReadBackAtItsOwnBase`
